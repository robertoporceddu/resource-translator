<?php

namespace RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RobertoPorceddu\ResourceTranslator\Traits\Models\HasTranslations;

class Article extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'locale',
        'translation_group_uuid',
    ];
}

