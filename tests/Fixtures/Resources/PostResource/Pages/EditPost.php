<?php

namespace RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources\PostResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use RobertoPorceddu\ResourceTranslator\Filament\Actions\ChangeTranslationAction;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources\PostResource;
use RobertoPorceddu\ResourceTranslator\Traits\Resources\HasTranslations;

class EditPost extends EditRecord
{
    use HasTranslations;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ChangeTranslationAction::make(),
        ];
    }
}

