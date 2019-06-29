<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetPasswordToken;

interface ResetPasswordTokenSender
{
    public function send(Email $email, ResetPasswordToken $token): void;
}
