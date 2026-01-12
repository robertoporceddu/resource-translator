<?php

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use RobertoPorceddu\ResourceTranslator\Http\Middleware\TranslationGroupMiddleware;

describe('TranslationGroupMiddleware', function () {
    it('can be instantiated', function () {
        $middleware = new TranslationGroupMiddleware();

        expect($middleware)->toBeInstanceOf(TranslationGroupMiddleware::class);
    });

    it('passes through when route is null', function () {
        $middleware = new TranslationGroupMiddleware();
        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, function ($request) {
            return new Response('passed');
        });

        expect($response->getContent())->toBe('passed');
    });

    it('has a handle method', function () {
        $middleware = new TranslationGroupMiddleware();

        expect(method_exists($middleware, 'handle'))->toBeTrue();
    });

    it('has a redirectToCreate method', function () {
        $middleware = new TranslationGroupMiddleware();

        expect(method_exists($middleware, 'redirectToCreate'))->toBeTrue();
    });

    it('handle method accepts Request and Closure', function () {
        $middleware = new TranslationGroupMiddleware();
        $reflectionMethod = new ReflectionMethod($middleware, 'handle');
        $parameters = $reflectionMethod->getParameters();

        expect($parameters)->toHaveCount(2);
        expect($parameters[0]->getName())->toBe('request');
        expect($parameters[1]->getName())->toBe('next');
    });

    it('handle method returns Response', function () {
        $middleware = new TranslationGroupMiddleware();
        $reflectionMethod = new ReflectionMethod($middleware, 'handle');

        expect($reflectionMethod->getReturnType()->getName())->toBe(Response::class);
    });

    it('redirectToCreate method returns RedirectResponse', function () {
        $middleware = new TranslationGroupMiddleware();
        $reflectionMethod = new ReflectionMethod($middleware, 'redirectToCreate');

        expect($reflectionMethod->getReturnType()->getName())->toBe(RedirectResponse::class);
    });
});

