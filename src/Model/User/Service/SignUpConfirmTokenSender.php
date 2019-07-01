<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use RuntimeException;
use Swift_Message;
use Twig\Environment;

class SignUpConfirmTokenSender
{
    private $mailer;
    private $twig;
    private $from;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, array $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }
    
    public function send(Email $email, string $token): void
    {
        $message = (new Swift_Message('Sign Up Conformation'))
            ->setFrom($this->from)
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/signup.html.twig', [
                'token' => $token
            ]), 'text/html');

        $success = !$this->mailer->send($message);

        if (!$success) {
            throw new RuntimeException('Unable to send email');
        }
    }
}
