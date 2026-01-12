<?php

namespace RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use RobertoPorceddu\ResourceTranslator\Traits\Models\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'content',
        'locale',
        'translation_group_uuid',
    ];
}

