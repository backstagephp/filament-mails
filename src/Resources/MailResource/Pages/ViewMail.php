<?php

namespace Backstage\FilamentMails\Resources\MailResource\Pages;

use Backstage\FilamentMails\Resources\MailResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMail extends ViewRecord
{
    public static function getResource(): string
    {
        return config('filament-mails.resources.mail', MailResource::class);
    }
}
