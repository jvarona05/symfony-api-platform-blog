<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Email\Mailer;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistrationSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        TokenGenerator $tokenGenerator,
        Mailer $mailer
    ){
        $this->encoder = $encoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['registrationUser', EventPriorities::PRE_WRITE]
        ];
    }

    public function registrationUser(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$user instanceof User || $method !== Request::METHOD_POST){
            return;
        }

        //it is an user, we need to hash the password
        $user->setPassword(
            $this->encoder->encodePassword($user, $user->getPassword())
        );

        $confirmationToken = $this->tokenGenerator->getRandomSecureToken();

        //create a confirmation token
        $user->setConfirmationToken($confirmationToken);

        //send email
        $this->mailer->sendConfirmationEmail($user);
    }
}
