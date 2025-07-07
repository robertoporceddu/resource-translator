<?php

namespace RobertoPorceddu\ResourceTranslator;

use Filament\Panel;
use Filament\Contracts\Plugin;
use RobertoPorceddu\ResourceTranslator\Http\Middleware\TranslationGroupMiddleware;

class ResourceTranslatorPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'resource-translator';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                //
            ])
            ->pages([
                //
            ]);

        if (config('translation-manager.language_switcher')) {
            $panel->authMiddleware([
                TranslationGroupMiddleware::class,
            ]);
        }

    }

    public function boot(Panel $panel): void
    {
        //
    }
}
