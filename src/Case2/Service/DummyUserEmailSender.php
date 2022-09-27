<?php

namespace App\Case2\Service;


use App\Case2\Exception\EmailSendException;
use App\Case2\Interfaces\UserEmailSenderInterface;

class DummyUserEmailSender implements UserEmailSenderInterface
{

    /**
     * @throws EmailSendException
     */
    public function sendEmailChangedNotification(string $oldEmail, string $newEmail): void
    {
        if ($newEmail === "troll@ya.ru") {
            throw new EmailSendException("EmailSendException");
        }

        printf("%s -> %s\n", $oldEmail, $newEmail);
    }
}
