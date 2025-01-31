<?php

namespace App\EventSubscriber;

use App\Service\Logger\AuditLogService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AuthenticationSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly AuditLogService $service
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
            LoginFailureEvent::class => 'onLoginFailureEvent',
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }


    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $this->service->logSuccess($event->getUser()->getUserIdentifier(),$event->getRequest()->getClientIp());
    }

    public function onLoginFailureEvent(LoginFailureEvent $event): void
    {
        dd($event);
        $message = $event->getException()->getMessage();

        if($event->getException() instanceof TooManyLoginAttemptsAuthenticationException){
            $message = "Too many login attempts";
        }


        $this->service->logFailure(
            $event->getRequest()->get('_username'),
            $message,
            $event->getRequest()->getClientIp(),
        );
    }

    public function onLogoutEvent(LogoutEvent $event): void
    {
        dd($event);
    }


}
