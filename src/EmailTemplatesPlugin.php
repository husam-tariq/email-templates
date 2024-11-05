<?php

namespace Visualbuilder\EmailTemplates;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Visualbuilder\EmailTemplates\Resources\EmailTemplateResource;
use Visualbuilder\EmailTemplates\Resources\EmailTemplateThemeResource;

class EmailTemplatesPlugin implements Plugin
{
    use EvaluatesClosures;

    public string $navigationGroup;

    protected bool|Closure $navigation = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function getId(): string
    {
        return 'filament-email-templates';
    }

    public function enableNavigation(bool|Closure $callback = true): static
    {
        $this->navigation = $callback;

        return $this;
    }

    public function shouldRegisterNavigation(): bool
    {
        return $this->evaluate($this->navigation) === true ?? config('filament-email-templates.navigation.enabled',
            true);
    }

    public function navigationGroup($navigationGroup): static
    {
        $this->navigationGroup = $navigationGroup;
        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? config('filament-email-templates.navigation.templates.group');
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            EmailTemplateResource::class,
            EmailTemplateThemeResource::class,
        ]);

    }

    public function boot(Panel $panel): void
    {
        //
    }
}
