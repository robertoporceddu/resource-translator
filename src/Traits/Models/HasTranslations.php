<?php

namespace RobertoPorceddu\ResourceTranslator\Traits\Models;

use Illuminate\Support\Str;

trait HasTranslations
{
    const LOCALE_COLUMN = 'locale';

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public static function booted(): void
    {
        if(Filament::isServing()) {
            static::addGlobalScope('return_current_locale_records', function ($builder) {
                $builder->where(static::LOCALE_COLUMN, app()->getLocale());
            });
        }
    }

    /**
     * Handle the saving of the model setting the locale.
     */
    public function save(array $options = []): bool
    {
        $this->locale = app()->getLocale();

        if(session()->has('translation_group_uuid')) {
            $this->translation_group_uuid = session()->get('translation_group_uuid');
        } else {
            $this->translation_group_uuid = Str::uuid()->toString();
        }

        $result = parent::save($options);

        return $result;
    }

    public function translations()
    {
        return $this::withoutGlobalScopes()->where('translation_group_uuid', $this->translation_group_uuid);
    }

    public function getTranslationsAttribute()
    {
        return $this->translations()->get();
    }
}
