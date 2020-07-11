<?php

namespace App\Email;

use App\Entity\User;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $templating;

    public function __construct(
        \Swift_Mailer $mailer,
        Environment $templating
    )
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendConfirmationEmail(User $user)
    {
        $body = $this->templating->render(
            'email/confirmation.html.twig',
            [
                'user' => $user
            ]
        );

        $message = (new Swift_Message('Please confirm your account!'))
            ->setFrom('api-platform@api.com')
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}