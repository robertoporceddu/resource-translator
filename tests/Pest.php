<?php

use RobertoPorceddu\ResourceTranslator\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeUuid', function () {
    return $this->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i');
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

function createPost(array $attributes = []): \RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Post
{
    return \RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Post::create(array_merge([
        'title' => 'Test Post',
        'content' => 'Test content',
    ], $attributes));
}

function createArticle(array $attributes = []): \RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Article
{
    return \RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Article::create(array_merge([
        'title' => 'Test Article',
        'body' => 'Test body',
    ], $attributes));
}
