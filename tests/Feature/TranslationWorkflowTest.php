<?php

use Illuminate\Support\Str;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Post;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Article;

describe('Translation Workflow Feature', function () {
    describe('complete translation workflow', function () {
        it('can create a resource and add translations', function () {
            // Step 1: Create original post in English
            app()->setLocale('en');
            $englishPost = Post::create([
                'title' => 'My First Post',
                'content' => 'This is the English content.',
            ]);

            expect($englishPost->locale)->toBe('en');
            expect($englishPost->translation_group_uuid)->toBeUuid();

            $groupUuid = $englishPost->translation_group_uuid;

            // Step 2: Create Italian translation
            app()->setLocale('it');
            $italianPost = Post::create([
                'title' => 'Il Mio Primo Post',
                'content' => 'Questo è il contenuto italiano.',
                'translation_group_uuid' => $groupUuid,
            ]);

            expect($italianPost->locale)->toBe('it');
            expect($italianPost->translation_group_uuid)->toBe($groupUuid);

            // Step 3: Create Spanish translation
            app()->setLocale('es');
            $spanishPost = Post::create([
                'title' => 'Mi Primera Publicación',
                'content' => 'Este es el contenido en español.',
                'translation_group_uuid' => $groupUuid,
            ]);

            expect($spanishPost->locale)->toBe('es');
            expect($spanishPost->translation_group_uuid)->toBe($groupUuid);

            // Step 4: Verify all translations are linked
            $translations = $englishPost->translations;

            expect($translations)->toHaveCount(3);
            expect($translations->pluck('locale')->toArray())->toEqualCanonicalizing(['en', 'it', 'es']);
        });

        it('can update a translation without affecting others', function () {
            $groupUuid = Str::uuid()->toString();

            // Create translations
            app()->setLocale('en');
            $english = Post::create([
                'title' => 'Original English',
                'translation_group_uuid' => $groupUuid,
            ]);

            app()->setLocale('it');
            $italian = Post::create([
                'title' => 'Originale Italiano',
                'translation_group_uuid' => $groupUuid,
            ]);

            // Update English
            $english->title = 'Updated English';
            $english->save();

            // Refresh Italian
            $italian->refresh();

            expect($english->title)->toBe('Updated English');
            expect($italian->title)->toBe('Originale Italiano');
            expect($english->translation_group_uuid)->toBe($italian->translation_group_uuid);
        });

        it('can delete a translation without affecting others', function () {
            $groupUuid = Str::uuid()->toString();

            // Create translations
            app()->setLocale('en');
            Post::create([
                'title' => 'English',
                'translation_group_uuid' => $groupUuid,
            ]);

            app()->setLocale('it');
            $italian = Post::create([
                'title' => 'Italiano',
                'translation_group_uuid' => $groupUuid,
            ]);

            app()->setLocale('es');
            Post::create([
                'title' => 'Español',
                'translation_group_uuid' => $groupUuid,
            ]);

            // Delete Italian
            $italian->delete();

            // Check remaining translations
            $remaining = Post::withoutGlobalScopes()
                ->where('translation_group_uuid', $groupUuid)
                ->get();

            expect($remaining)->toHaveCount(2);
            expect($remaining->pluck('locale')->toArray())->toEqualCanonicalizing(['en', 'es']);
        });
    });

    describe('translation group integrity', function () {
        it('maintains translation group across multiple saves', function () {
            $post = createPost();
            $originalUuid = $post->translation_group_uuid;

            // Multiple saves
            for ($i = 0; $i < 5; $i++) {
                $post->title = "Update {$i}";
                $post->save();
            }

            expect($post->translation_group_uuid)->toBe($originalUuid);
        });

        it('creates unique groups for unrelated posts', function () {
            $uuids = [];

            for ($i = 0; $i < 10; $i++) {
                $post = createPost(['title' => "Post {$i}"]);
                $uuids[] = $post->translation_group_uuid;
            }

            // All UUIDs should be unique
            expect(array_unique($uuids))->toHaveCount(10);
        });
    });

    describe('locale switching scenarios', function () {
        it('handles rapid locale switching', function () {
            $locales = ['en', 'it', 'es', 'fr'];
            $groupUuid = Str::uuid()->toString();

            foreach ($locales as $locale) {
                app()->setLocale($locale);
                Post::create([
                    'title' => "Title in {$locale}",
                    'translation_group_uuid' => $groupUuid,
                ]);
            }

            $translations = Post::withoutGlobalScopes()
                ->where('translation_group_uuid', $groupUuid)
                ->get();

            expect($translations)->toHaveCount(4);
        });

        it('correctly associates translations created in different sessions', function () {
            $groupUuid = Str::uuid()->toString();

            // Session 1: Create English
            app()->setLocale('en');
            $english = Post::create([
                'title' => 'English from session 1',
                'translation_group_uuid' => $groupUuid,
            ]);

            // Simulate new session by clearing and setting different locale
            app()->setLocale('it');
            $italian = Post::create([
                'title' => 'Italian from session 2',
                'translation_group_uuid' => $groupUuid,
            ]);

            // Both should be linked
            $englishTranslations = $english->translations;
            $italianTranslations = $italian->translations;

            expect($englishTranslations)->toHaveCount(2);
            expect($italianTranslations)->toHaveCount(2);
        });
    });

    describe('soft deletes workflow', function () {
        it('soft deleted articles are excluded from translations', function () {
            $groupUuid = Str::uuid()->toString();

            app()->setLocale('en');
            $english = Article::create([
                'title' => 'English Article',
                'body' => 'Body',
                'translation_group_uuid' => $groupUuid,
            ]);

            app()->setLocale('it');
            $italian = Article::create([
                'title' => 'Italian Article',
                'body' => 'Corpo',
                'translation_group_uuid' => $groupUuid,
            ]);

            // Soft delete Italian
            $italian->delete();

            // English translations should only show English
            expect($english->translations)->toHaveCount(1);
            expect($english->translations->first()->locale)->toBe('en');
        });

        it('can restore soft deleted translation and see it again', function () {
            $groupUuid = Str::uuid()->toString();

            app()->setLocale('en');
            $english = Article::create([
                'title' => 'English Article',
                'body' => 'Body',
                'translation_group_uuid' => $groupUuid,
            ]);

            app()->setLocale('it');
            $italian = Article::create([
                'title' => 'Italian Article',
                'body' => 'Corpo',
                'translation_group_uuid' => $groupUuid,
            ]);

            // Soft delete and then restore
            $italian->delete();
            expect($english->translations)->toHaveCount(1);

            $italian->restore();
            
            // Need to get fresh translations
            expect($english->translations()->get())->toHaveCount(2);
        });
    });

    describe('edge cases', function () {
        it('handles post with no other translations', function () {
            $post = createPost();

            expect($post->translations)->toHaveCount(1);
            expect($post->translations->first()->id)->toBe($post->id);
        });

        it('handles very long UUIDs gracefully', function () {
            $post = createPost();

            expect(strlen($post->translation_group_uuid))->toBeLessThanOrEqual(36);
        });

        it('handles posts with empty content', function () {
            $post = Post::create([
                'title' => 'Title Only',
                'content' => '',
            ]);

            expect($post->locale)->toBe('en');
            expect($post->translation_group_uuid)->toBeUuid();
        });

        it('handles posts with null content', function () {
            $post = Post::create([
                'title' => 'Title Only',
                'content' => null,
            ]);

            expect($post->locale)->toBe('en');
            expect($post->translation_group_uuid)->toBeUuid();
        });
    });
});
