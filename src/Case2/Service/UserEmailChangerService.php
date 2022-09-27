<?php

namespace App\Case2\Service;

use App\Case2\Exception\EmailSendException;
use App\Case2\Interfaces\UserEmailSenderInterface;
use PDO;
use PDOException;

class UserEmailChangerService
{
    private PDO $db;

    private UserEmailSenderInterface $emailSender;

    public function __construct(PDO $db, UserEmailSenderInterface $emailSender)
    {
        $this->db = $db;
        $this->emailSender = $emailSender;
    }

    /**
     * @param int    $userId
     * @param string $email
     *
     * @return void
     *
     * @throws PDOException
     */
    public function changeEmail(int $userId, string $email): void
    {
        $this->db->beginTransaction();

        $statement = $this->db->prepare("SELECT email from users WHERE id = :id");
        $statement->bindParam(':id', $userId, PDO::PARAM_INT);
        $statement->execute();

        $oldEmail = $statement->fetchAll(PDO::FETCH_ASSOC)[0]['email'] ?? null;

        if (!$oldEmail) {
            return;
        }

        if ($oldEmail === $email) {
            return;
        }

        $statement = $this->db->prepare("UPDATE users SET email = :email WHERE id = :id AND email = :old");
        $statement->bindParam(':id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':old', $oldEmail);

        try {
            $statement->execute();

            if ($statement->rowCount() === 0) {
                $this->db->rollBack();
                throw new PDOException("Data old");
            }
        } catch (PDOException $exception) {
            if ($exception->getCode() == 23000) {
                throw new PDOException("Email already exists");
            }
        }

        try {
            $this->emailSender->sendEmailChangedNotification($oldEmail, $email);
        } catch (EmailSendException $throwable) {
            $this->db->rollBack();
            throw new PDOException("Email transport error");
        }

        if ($this->db->inTransaction()) {
            $this->db->commit();
        }
    }
}
