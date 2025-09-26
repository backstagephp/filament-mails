<?php

use Backstage\FilamentMails\Resources\EventResource;
use Backstage\FilamentMails\Resources\MailResource;
use Backstage\FilamentMails\Resources\SuppressionResource;

return [
    'resources' => [
        'mail' => MailResource::class,
        'event' => EventResource::class,
        'suppression' => SuppressionResource::class,
    ],

    'navigation' => [
        'group' => null,
        'sort' => null,
    ],
];
