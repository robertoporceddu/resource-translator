<?php

namespace RobertoPorceddu\ResourceTranslator\Traits\Resources;

use Filament\Forms\Form;
use Filament\Forms\Components\Hidden;

trait HasTranslations
{
    public $changeTranslation = null;

    public function updatedChangeTranslation($value)
    {
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
