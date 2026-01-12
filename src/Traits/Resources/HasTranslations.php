<?php

namespace RobertoPorceddu\ResourceTranslator\Traits\Resources;

use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Form;

trait HasTranslations
{
    public ?string $changeTranslation = null;

    public function updatedChangeTranslation($value)
    {
        if ($value === null) {
            return;
        }

        $this->redirect(route('translation-manager.switch', [ 'code' => $value ]));
    }

    public function form(Form $form): Form
    {
        $originalSchema = $form->getComponents();
        $schema = collect($originalSchema)->push(
            Hidden::make('translation_group_uuid')
                ->default(request()->get('translation_group_uuid')),
        );

        return $form->schema($schema->toArray());
    }
}
