<?php

namespace RobertoPorceddu\ResourceTranslator\Filament\Actions;

use Filament\Actions\SelectAction;
use Illuminate\Database\Eloquent\Model;

class ChangeTranslationAction extends SelectAction
{
    public static function getDefaultName(): ?string
    {
        return 'changeTranslation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(function(Model $record) {
            $availableTranslations = $record->translations->pluck('locale')->toArray();
            $availableLocales = collect(config('translation-manager.available_locales'));

            return $availableLocales
                ->map(fn($locale) => [
                    $locale['code'] => $locale['name'] . (in_array($locale['code'], $availableTranslations) ? ' (1)' : ' (0)')
                ])
                ->filter(fn($locale) => !($locale[$record->locale] ?? false))
                ->collapse();
        });

        $this->placeholder('Available Translations');
    }
}
