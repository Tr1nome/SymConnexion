<?php
namespace App\EventListener;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RegistrationConfirmedListener implements EventSubscriberInterface
{
    private $router;
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRM => ['onRegistrationConfirmed']
        );
    }
    public function onRegistrationConfirmed(GetResponseUserEvent $event)
    {
        $event->setResponse(new RedirectResponse($this->router->generate('redirectTo')));
    }
}