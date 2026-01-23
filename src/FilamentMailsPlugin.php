<?php

namespace Backstage\FilamentMails;

use Backstage\FilamentMails\Resources\EventResource;
use Backstage\FilamentMails\Resources\MailResource;
use Backstage\FilamentMails\Resources\SuppressionResource;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentMailsPlugin implements Plugin
{
    use EvaluatesClosures;

    public bool | Closure $canManageMails = true;

    public function getId(): string
    {
        return 'filament-mails';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->colors([
                'clicked' => Color::Purple,
            ])
            ->resources([
                config('filament-mails.resources.mail', MailResource::class),
                config('filament-mails.resources.event', EventResource::class),
                config('filament-mails.resources.suppression', SuppressionResource::class),
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function canManageMails(bool | Closure $canManageMails = true): static
    {
        $this->canManageMails = $canManageMails;

        return $this;
    }

    public function userCanManageMails(): bool
    {
        return $this->evaluate($this->canManageMails);
    }
}
