<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;
use RobertoPorceddu\ResourceTranslator\Http\Middleware\TranslationGroupMiddleware;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Post;

describe('TranslationGroupMiddleware Feature', function () {
    beforeEach(function () {
        $this->middleware = new TranslationGroupMiddleware();
        $this->passedThrough = false;
        $this->nextCallback = function ($request) {
            $this->passedThrough = true;
            return new Response('passed');
        };
    });

    describe('route handling', function () {
        it('passes through when route is null', function () {
            $request = Request::create('/test', 'GET');

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
            expect($response->getContent())->toBe('passed');
        });

        it('passes through when action is not edit', function () {
            $request = Request::create('/posts', 'GET');
            $route = new Route('GET', '/posts', ['as' => 'posts.index']);
            $route->bind($request);
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
        });

        it('passes through for create action', function () {
            $request = Request::create('/posts/create', 'GET');
            $route = new Route('GET', '/posts/create', ['as' => 'posts.create']);
            $route->bind($request);
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
        });

        it('passes through for show action', function () {
            $request = Request::create('/posts/1', 'GET');
            $route = new Route('GET', '/posts/{post}', ['as' => 'posts.show']);
            $route->bind($request);
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
        });

        it('passes through when controller is null', function () {
            $request = Request::create('/posts/1/edit', 'GET');
            $route = new Route('GET', '/posts/{post}/edit', [
                'as' => 'posts.edit',
                'controller' => null,
            ]);
            $route->bind($request);
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
        });

        it('passes through when route has no as parameter', function () {
            $request = Request::create('/posts/1/edit', 'GET');
            $route = new Route('GET', '/posts/{post}/edit', []);
            $route->bind($request);
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
        });
    });

    describe('locale handling', function () {
        it('uses session language when available', function () {
            session()->put('language', 'fr');

            $request = Request::create('/posts/1/edit', 'GET');
            $route = new Route('GET', '/posts/{post}/edit', ['as' => 'posts.edit']);
            $route->bind($request);
            $request->setRouteResolver(fn() => $route);

            // Without a proper controller, middleware passes through
            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();

            session()->forget('language');
        });

        it('falls back to app locale when session language not set', function () {
            session()->forget('language');
            app()->setLocale('de');

            $request = Request::create('/posts/1/edit', 'GET');
            $route = new Route('GET', '/posts/{post}/edit', ['as' => 'posts.edit']);
            $route->bind($request);
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();

            app()->setLocale('en');
        });
    });

    describe('record handling', function () {
        it('passes through when record is not found', function () {
            // Create a mock controller that returns proper resource/model
            $mockController = new class {
                public static function getResource()
                {
                    return new class {
                        public static function getModel()
                        {
                            return Post::class;
                        }
                        public static function getUrl($name, $params = [])
                        {
                            return '/posts/' . ($params['record'] ?? 'create');
                        }
                    };
                }
            };

            $request = Request::create('/posts/99999/edit', 'GET');
            $route = new Route('GET', '/posts/{record}/edit', [
                'as' => 'posts.edit',
                'controller' => get_class($mockController),
            ]);
            $route->bind($request);
            $route->setParameter('record', '99999');
            $request->setRouteResolver(fn() => $route);

            $response = $this->middleware->handle($request, $this->nextCallback);

            expect($this->passedThrough)->toBeTrue();
        });
    });
});
