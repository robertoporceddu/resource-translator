<?php

namespace RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources\PostResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources\PostResource;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

