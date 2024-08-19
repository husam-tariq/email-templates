<?php

namespace Visualbuilder\EmailTemplates;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Visualbuilder\EmailTemplates\Commands\InstallCommand;
use Visualbuilder\EmailTemplates\Contracts\CreateMailableInterface;
use Visualbuilder\EmailTemplates\Contracts\FormHelperInterface;

use Visualbuilder\EmailTemplates\Helpers\CreateMailableHelper;
use Visualbuilder\EmailTemplates\Helpers\FormHelper;

class EmailTemplatesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name("filament-email-templates")
            ->hasMigrations(['create_email_templates_themes_table','create_email_templates_table','update_email_templates_table'])
            ->hasConfigFile(['filament-email-templates', 'filament-tiptap-editor'])
            ->hasAssets()
            ->hasViews('vb-email-templates')
            ->runsMigrations()
            ->hasCommands([
                InstallCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton(CreateMailableInterface::class, CreateMailableHelper::class);
        $this->app->singleton(FormHelperInterface::class, FormHelper::class);
        $this->app->register(EmailTemplatesEventServiceProvider::class);

        // Add the binding for TokenReplacementInterface
        $this->app->bind(
            \Visualbuilder\EmailTemplates\Contracts\TokenReplacementInterface::class,
            config('filament-email-templates.tokenHelperClass')
        );
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        FilamentAsset::register(
            $this->getAssets()
        );

        if($this->app->runningInConsole()) {
            $this->publishResources();
        }

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'vb-email-templates');
    }

    protected function publishResources()
    {
        $this->publishes([
                            __DIR__
                            .'/../database/seeders/EmailTemplateSeeder.php' => database_path('seeders/EmailTemplateSeeder.php'),
                            __DIR__.'/../database/seeders/EmailTemplateThemeSeeder.php' => database_path('seeders/EmailTemplateThemeSeeder.php'),
                        ], 'filament-email-templates-seeds');

        $this->publishes([
                            __DIR__.'/../media/' => public_path('media/email-templates'),
                            __DIR__.'/../assets/flag-icons/flags' => public_path('css/flags'),
                            __DIR__.'/../resources/views' => resource_path('views/vendor/vb-email-templates'),
                        ], 'filament-email-templates-assets');
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            Css::make('vb-email-templates-styles', __DIR__.'/../assets/flag-icons/css/flag-icon.min.css'),
        ];
    }
}
