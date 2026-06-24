<?php

namespace App\Http\Resources;

use App\Entities\Subscriber;
use DateTimeInterface;

class SubscriberResource
{
    public static function toArray(Subscriber $subscriber): array
    {
        return [
            'uuid'    => $subscriber->getUuid(),
            'email'   => $subscriber->getEmail(),
            'name'    => $subscriber->getName(),
            'created' => $subscriber->getCreated()->format(DateTimeInterface::ATOM),
            'updated' => $subscriber->getUpdated()?->format(DateTimeInterface::ATOM),
        ];
    }
}
