<?php

namespace App\Case2\Interfaces;

use App\Case2\EmailSendException;

interface UserEmailSenderInterface
{
    /**
     * @param string $oldEmail
     * @param string $newEmail
     *
     * @return void
     * @throws EmailSendException
     */
    public function sendEmailChangedNotification(string $oldEmail, string $newEmail): void;
}
