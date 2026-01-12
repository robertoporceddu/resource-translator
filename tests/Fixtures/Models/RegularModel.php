<?php

namespace RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A regular model without the HasTranslations trait for testing.
 */
class RegularModel extends Model
{
    protected $table = 'posts';
    
    protected $fillable = [
        'title',
        'content',
        'locale',
        'translation_group_uuid',
    ];
}

