<?php

namespace RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources\PostResource;
use RobertoPorceddu\ResourceTranslator\Traits\Resources\HasTranslations;

class CreatePost extends CreateRecord
{
    use HasTranslations;

    protected static string $resource = PostResource::class;
}

