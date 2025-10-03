<?php

namespace Vormkracht10\FilamentMails\Resources;

use Filament\Facades\Filament;
use Filament\Forms\Components\TagsInput;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Vormkracht10\FilamentMails\Resources\MailResource\Pages\ListMails;
use Vormkracht10\FilamentMails\Resources\MailResource\Pages\ViewMail;
use Vormkracht10\FilamentMails\Resources\MailResource\Widgets\MailStatsWidget;
use Vormkracht10\Mails\Actions\ResendMail;
use Vormkracht10\Mails\Enums\EventType;
use Vormkracht10\Mails\Models\Mail;
use Vormkracht10\Mails\Models\MailEvent;

class MailResource extends Resource
{
    protected static ?string $slug = 'mails';

    protected static ?string $recordTitleAttribute = 'subject';

    protected static bool $isScopedToTenant = false;

    protected static bool $shouldRegisterNavigation = true;

    public static function getModel(): string
    {
        return config('mails.models.mail');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-mails.navigation.group') ?? 'Emails');
    }

    public static function getNavigationLabel(): string
    {
        return __(config('filament-mails.navigation.label') ?? 'Emails');
    }

    public static function getLabel(): ?string
    {
        return __(config('filament-mails.label') ?? 'Email');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-mails.navigation.icon') ?? 'heroicon-o-envelope';
    }

    public function getTitle(): string
    {
        return __(config('filament-mails.title') ?? 'Emails');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('General')
                    ->icon('heroicon-o-envelope')
                    ->compact()
                    ->collapsible()
                    ->schema([
                        Tabs::make('')
                            ->schema([
                                Tab::make(__('Sender Information'))
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('subject')
                                                    ->columnSpanFull()
                                                    ->label(__('Subject')),
                                                TextEntry::make('from')
                                                    ->label(__('From'))
                                                    ->getStateUsing(fn (Mail $record) => self::formatMailState($record->from)),
                                                TextEntry::make('to')
                                                    ->label(__('Recipient(s)'))
                                                    ->getStateUsing(fn (Mail $record) => self::formatMailState($record->to)),
                                                TextEntry::make('cc')
                                                    ->label(__('CC'))
                                                    ->default('-')
                                                    ->getStateUsing(fn (Mail $record) => self::formatMailState($record->cc ?? [])),
                                                TextEntry::make('bcc')
                                                    ->label(__('BCC'))
                                                    ->default('-')
                                                    ->getStateUsing(fn (Mail $record) => self::formatMailState($record->bcc ?? [])),
                                                TextEntry::make('reply_to')
                                                    ->default('-')
                                                    ->label(__('Reply To'))
                                                    ->getStateUsing(fn (Mail $record) => self::formatMailState($record->reply_to ?? [])),
                                            ]),
                                    ]),
                                Tab::make(__('Statistics'))
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('opens')
                                                    ->label(__('Opens')),
                                                TextEntry::make('clicks')
                                                    ->label(__('Clicks')),
                                                TextEntry::make('sent_at')
                                                    ->label(__('Sent At'))
                                                    ->default(__('Never'))
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === __('Never') ? $state : Carbon::parse($state)->format('d-m-Y H:i');
                                                    }),
                                                TextEntry::make('resent_at')
                                                    ->label(__('Resent At'))
                                                    ->default(__('Never'))
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === __('Never') ? $state : Carbon::parse($state)->format('d-m-Y H:i');
                                                    }),
                                                TextEntry::make('delivered_at')
                                                    ->label(__('Delivered At'))
                                                    ->default(__('Never'))
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === __('Never') ? $state : Carbon::parse($state)->format('d-m-Y H:i');
                                                    }),
                                                TextEntry::make('last_opened_at')
                                                    ->label(__('Last Opened At'))
                                                    ->default(__('Never'))
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === __('Never') ? $state : Carbon::parse($state)->format('d-m-Y H:i');
                                                    }),
                                                TextEntry::make('last_clicked_at')
                                                    ->label(__('Last Clicked At'))
                                                    ->default(__('Never'))
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === __('Never') ? $state : Carbon::parse($state)->format('d-m-Y H:i');
                                                    }),
                                                TextEntry::make('complained_at')
                                                    ->label(__('Complained At'))
                                                    ->default(__('Never'))
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === __('Never') ? $state : Carbon::parse($state)->format('d-m-Y H:i');
                                                    }),
                                                TextEntry::make('soft_bounced_at')
                                                    ->label(__('Soft Bounced At'))
                                                    ->default(__('Never'))
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === __('Never') ? $state : Carbon::parse($state)->format('d-m-Y H:i');
                                                    }),
                                                TextEntry::make('hard_bounced_at')
                                                    ->label(__('Hard Bounced At'))
                                                    ->default(__('Never'))
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === __('Never') ? $state : Carbon::parse($state)->format('d-m-Y H:i');
                                                    }),
                                            ]),
                                    ]),
                                Tab::make(__('Events'))
                                    ->schema([
                                        RepeatableEntry::make('events')
                                            ->state(function (Mail $record) {
                                                return $record->events->sortByDesc('occurred_at');
                                            })
                                            ->hiddenLabel()
                                            ->schema([
                                                TextEntry::make('type')
                                                    ->label(__('Type'))
                                                    ->badge()
                                                    ->url(function (MailEvent $record) {
                                                        $panel = Filament::getCurrentPanel();
                                                        $tenant = Filament::getTenant();

                                                        if (! $panel || ! $tenant) {
                                                            return null;
                                                        }

                                                        return route('filament.' . $panel->getId() . '.resources.mails.events.view', [
                                                            'record' => $record,
                                                            'tenant' => $tenant->getKey(),
                                                        ]);
                                                    })
                                                    ->color(fn (EventType $state): string => match ($state) {
                                                        EventType::DELIVERED => 'success',
                                                        EventType::CLICKED => 'clicked',
                                                        EventType::OPENED => 'info',
                                                        EventType::SOFT_BOUNCED => 'danger',
                                                        EventType::HARD_BOUNCED => 'danger',
                                                        EventType::COMPLAINED => 'warning',
                                                        EventType::UNSUBSCRIBED => 'danger',
                                                        EventType::ACCEPTED => 'success',
                                                    })
                                                    ->formatStateUsing(function (EventType $state) {
                                                        return ucfirst($state->value);
                                                    }),
                                                TextEntry::make('occurred_at')
                                                    ->url(function (MailEvent $record) {
                                                        $panel = Filament::getCurrentPanel();
                                                        $tenant = Filament::getTenant();

                                                        if (! $panel || ! $tenant) {
                                                            return null;
                                                        }

                                                        return route('filament.' . $panel->getId() . '.resources.mails.view', [
                                                            'record' => $record,
                                                            'tenant' => $tenant->getKey(),
                                                        ]);
                                                    })
                                                    ->since()
                                                    ->dateTimeTooltip('d-m-Y H:i')
                                                    ->label(__('Occurred At')),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),

                    ]),
                Section::make('Content')
                    ->icon('heroicon-o-document')
                    ->collapsible()
                    ->compact()
                    ->schema([
                        Tabs::make('Content')
                            ->label(__('Content'))
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'w-full max-w-full'])
                            ->tabs([
                                Tab::make('Preview')
                                    ->extraAttributes(['class' => 'w-full max-w-full'])
                                    ->schema([
                                        TextEntry::make('html')
                                            ->hiddenLabel()
                                            ->label(__('HTML Content'))
                                            ->extraAttributes(['class' => 'overflow-x-auto'])
                                            ->formatStateUsing(fn (string $state, Mail $record): View => view(
                                                'filament-mails::mails.preview',
                                                ['html' => $state, 'mail' => $record],
                                            )),
                                    ]),
                                Tab::make('HTML')
                                    ->schema([
                                        TextEntry::make('html')
                                            ->hiddenLabel()
                                            ->extraAttributes(['class' => 'overflow-x-auto'])
                                            ->formatStateUsing(fn (string $state, Mail $record): View => view(
                                                'filament-mails::mails.html',
                                                ['html' => $state, 'mail' => $record],
                                            ))
                                            ->copyable()
                                            ->copyMessage('Copied!')
                                            ->copyMessageDuration(1500)
                                            ->label(__('HTML Content'))
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make('Text')
                                    ->schema([
                                        TextEntry::make('text')
                                            ->hiddenLabel()
                                            ->copyable()
                                            ->copyMessage('Copied!')
                                            ->copyMessageDuration(1500)
                                            ->label(__('Text Content'))
                                            ->formatStateUsing(fn (string $state): HtmlString => new HtmlString(nl2br(e($state))))
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Attachments')
                    ->icon('heroicon-o-paper-clip')
                    ->compact()
                    ->collapsible()
                    ->schema([
                        TextEntry::make('attachments')
                            ->hiddenLabel()
                            ->label(__('Attachments'))
                            ->visible(fn (Mail $record) => $record->attachments->count() == 0)
                            ->default(__('Email has no attachments')),
                        RepeatableEntry::make('attachments')
                            ->hiddenLabel()
                            ->label(__('Attachments'))
                            ->visible(fn (Mail $record) => $record->attachments->count() > 0)
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('filename')
                                            ->label(__('Name')),
                                        TextEntry::make('size')
                                            ->label(__('Size')),
                                        TextEntry::make('mime')
                                            ->label(__('Mime Type')),
                                        ViewEntry::make('uuid')
                                            ->label(__('Download'))
                                            ->getStateUsing(fn ($record) => $record)
                                            ->view('filament-mails::mails.download'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction('view')
            ->recordUrl(null)
            ->defaultSort('created_at', 'desc')
            ->paginated([50, 100, 'all'])
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->sortable()
                    ->searchable(false)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        __('Soft Bounced') => 'warning',
                        __('Hard Bounced') => 'danger',
                        __('Complained') => 'danger',
                        __('Clicked') => 'clicked',
                        __('Opened') => 'info',
                        __('Delivered') => 'success',
                        __('Sent') => 'info',
                        __('Resent') => 'info',
                        __('Unsent') => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->limit(35)
                    ->sortable()
                    ->searchable(['subject', 'html', 'text']),
                Tables\Columns\IconColumn::make('attachments')
                    ->label('')
                    ->alignLeft()
                    ->searchable(false)
                    ->getStateUsing(fn (Mail $record) => $record->attachments->count() > 0)
                    ->icon(fn (string $state): string => $state ? 'heroicon-o-paper-clip' : ''),
                Tables\Columns\TextColumn::make('to')
                    ->label(__('Recipient(s)'))
                    ->limit(50)
                    ->getStateUsing(fn (Mail $record) => self::formatMailState(emails: $record->to, mailOnly: true))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('opens')
                    ->label(__('Opens'))
                    ->tooltip(fn (Mail $record) => __('Last opened at :date', ['date' => $record->last_opened_at?->format('d-m-Y H:i')]))
                    ->sortable(),
                Tables\Columns\TextColumn::make('clicks')
                    ->label(__('Clicks'))
                    ->tooltip(fn (Mail $record) => __('Last clicked at :date', ['date' => $record->last_clicked_at?->format('d-m-Y H:i')]))
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label(__('Sent At'))
                    ->dateTime('d-m-Y H:i')
                    ->since()
                    ->tooltip(fn (Mail $record) => $record->sent_at?->format('d-m-Y H:i'))
                    ->sortable()
                    ->searchable(),
            ])
            ->modifyQueryUsing(
                fn (Builder $query) => $query->with('attachments')
            )
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    // ->url(null)
                    ->modal()
                    ->slideOver()
                    ->label(__('View'))
                    ->hiddenLabel()
                    ->tooltip(__('View')),
                Action::make('resend')
                    ->label(__('Resend'))
                    ->icon('heroicon-o-arrow-uturn-right')
                    ->requiresConfirmation()
                    ->modalDescription(__('Are you sure you want to resend this mail?'))
                    ->hiddenLabel()
                    ->tooltip(__('Resend'))
                    ->form(self::getResendForm())
                    ->fillForm(function (Mail $record) {
                        return [
                            'to' => array_keys($record->to ?: []),
                            'cc' => array_keys($record->cc ?: []),
                            'bcc' => array_keys($record->bcc ?: []),
                        ];
                    })
                    ->action(function (Mail $record, array $data) {
                        (new ResendMail)->handle($record, $data['to'], $data['cc'] ?? [], $data['bcc'] ?? []);

                        Notification::make()
                            ->title(__('Mail will be resent in the background'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('resend')
                        ->label(__('Resend'))
                        ->icon('heroicon-o-arrow-uturn-right')
                        ->requiresConfirmation()
                        ->modalDescription(__('Are you sure you want to resend the selected mails?'))
                        ->form(fn ($records) => self::getBulkResendForm($records))
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                (new ResendMail)->handle($record, $data['to'], $data['cc'] ?? [], $data['bcc'] ?? []);
                            }

                            Notification::make()
                                ->title(__('Mail will be resent in the background'))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function getResendForm(): array
    {
        return [
            TagsInput::make('to')
                ->placeholder(__('Recipient(s)'))
                ->label(__('To'))
                ->required()
                ->nestedRecursiveRules(['email:rfc,dns']),
            TagsInput::make('cc')
                ->placeholder(__('Recipient(s)'))
                ->label(__('CC'))
                ->nestedRecursiveRules(['nullable', 'email:rfc,dns']),
            TagsInput::make('bcc')
                ->placeholder(__('Recipient(s)'))
                ->label(__('BCC'))
                ->nestedRecursiveRules(['nullable', 'email:rfc,dns']),
        ];
    }

    private static function getBulkResendForm($records): array
    {
        $extractEmails = function ($records, $field) {
            return collect($records)
                ->map(fn ($record) => array_keys($record->{$field} ?? []))
                ->flatten()
                ->unique()
                ->toArray();
        };

        $toEmails = $extractEmails($records, 'to');
        $ccEmails = $extractEmails($records, 'cc');
        $bccEmails = $extractEmails($records, 'bcc');

        return [
            TagsInput::make('to')
                ->placeholder(__('Recipient(s)'))
                ->label(__('Recipient(s)'))
                ->default($toEmails)
                ->required()
                ->nestedRecursiveRules(['email:rfc,dns']),

            TagsInput::make('cc')
                ->placeholder(__('CC'))
                ->label(__('CC'))
                ->default($ccEmails)
                ->nestedRecursiveRules(['nullable', 'email:rfc,dns']),

            TagsInput::make('bcc')
                ->placeholder(__('BCC'))
                ->label(__('BCC'))
                ->default($bccEmails)
                ->nestedRecursiveRules(['nullable', 'email:rfc,dns']),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMails::route('/'),
            'view' => ViewMail::route('/{record}/view'),
        ];
    }

    private static function formatMailState(array $emails, bool $mailOnly = false): string
    {
        return collect($emails)
            ->mapWithKeys(fn ($value, $key) => [$key => $value ?? $key])
            ->map(fn ($value, $key) => $mailOnly ? $key : ($value == null ? $key : ($value !== $key ? "$value <$key>" : $key)))
            ->implode(', ');
    }

    public static function getWidgets(): array
    {
        return [
            MailStatsWidget::class,
        ];
    }
}
