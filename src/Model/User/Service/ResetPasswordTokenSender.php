<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetPasswordToken;
use RuntimeException;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class ResetPasswordTokenSender
{
    private $mailer;
    private $twig;
    private $from;

    public function __construct(Swift_Mailer $mailer, Environment $twig, array $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }

    public function send(Email $email, ResetPasswordToken $token): void
    {
        $message = (new Swift_Message('Reset your password'))
            ->setFrom($this->from)
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/reset-password.html.twig', [
                'token' => $token->getToken()
            ]), 'text/html');

        $success = $this->mailer->send($message);

        if (!$success) {
            throw new RuntimeException('Unable to send email');
        }
    }
}
