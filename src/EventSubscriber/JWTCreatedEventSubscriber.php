<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [Events::JWT_CREATED => 'onJWTCreated'];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var \App\Entity\User $user */
        $user = $event->getUser();

        $payload = $event->getData();
        $payload = $this->overloadJwtPayload($payload, $user);

        $event->setData($payload);
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,mixed>
     */
    private function overloadJwtPayload(array $payload, \App\Entity\User $user): array
    {
        $payload["roles"] = $user->getRoles();

        return $payload;
    }
}
