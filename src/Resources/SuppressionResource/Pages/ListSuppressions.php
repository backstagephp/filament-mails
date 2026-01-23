<?php

namespace Backstage\FilamentMails\Resources\SuppressionResource\Pages;

use Backstage\FilamentMails\FilamentMailsPlugin;
use Backstage\FilamentMails\Resources\SuppressionResource;
use Filament\Resources\Pages\ListRecords;

class ListSuppressions extends ListRecords
{
    protected static string $resource = SuppressionResource::class;

    public function getTitle(): string
    {
        return __('Suppressions');
    }

    public static function canAccess(array $parameters = []): bool
    {
        return FilamentMailsPlugin::get()->userCanManageMails();
    }
}
