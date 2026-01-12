<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use RobertoPorceddu\ResourceTranslator\Http\Middleware\TranslationGroupMiddleware;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Post;
use Symfony\Component\HttpFoundation\Response;

describe('TranslationGroupMiddleware Redirects', function () {
    beforeEach(function () {
        $this->middleware = new TranslationGroupMiddleware();
        $this->passedThrough = false;
        $this->nextCallback = function ($request) {
            $this->passedThrough = true;
            return new Response('passed');
        };
    });

    describe('redirect scenarios', function () {
        beforeEach(function () {
            $this->uuid = Str::uuid()->toString();

            // Create posts in different locales
            app()->setLocale('en');
            Post::create([
                'title' => 'English Post',
                'content' => 'English content',
                'translation_group_uuid' => $this->uuid,
            ]);

            app()->setLocale('it');
            $this->italianPost = Post::create([
                'title' => 'Post Italiano',
                'content' => 'Contenuto italiano',
                'translation_group_uuid' => $this->uuid,
            ]);

            app()->setLocale('es');
            Post::create([
                'title' => 'Post en Español',
                'content' => 'Contenido en español',
                'translation_group_uuid' => $this->uuid,
            ]);

            // Reset locale
            app()->setLocale('en');
        });

        it('passes through when action is not edit', function () {
            $request = Request::create('/posts', 'GET');
            $route = new Route('GET', '/posts', ['as' => 'posts.index']);
            $route->bind($request);
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
        });

        it('passes through when controller is missing', function () {
            $request = Request::create('/posts/1/edit', 'GET');
            $route = new Route('GET', '/posts/{record}/edit', [
                'as' => 'posts.edit',
            ]);
            $route->bind($request);
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
        });
    });

    describe('model without translations', function () {
        it('passes through for models without translations method', function () {
            // Create a mock resource that returns a model without translations
            $regularModelClass = new class {
                public static function getModel()
                {
                    return \RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\RegularModel::class;
                }
            };

            $controllerClass = new class($regularModelClass) {
                private static $resource;
                public function __construct($resource) {
                    self::$resource = $resource;
                }
                public static function getResource()
                {
                    return self::$resource;
                }
            };

            $request = Request::create('/admin/regular/1/edit', 'GET');
            $route = new Route('GET', '/admin/regular/{record}/edit', [
                'as' => 'filament.admin.resources.regular.edit',
                'controller' => get_class($controllerClass),
            ]);
            $route->bind($request);
            $route->setParameter('record', '1');
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
        });
    });
});
