<?php

namespace AppBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'clearSessionCookie',
        ];
    }

    public function clearSessionCookie(FilterResponseEvent $event)
    {
        if ($event->isMasterRequest() && ($session = $event->getRequest()->getSession()) !== null) {
            $event->getResponse()->headers->clearCookie($session->getName());
        }
    }
}
