<?php

use RobertoPorceddu\ResourceTranslator\Traits\Resources\HasTranslations;

describe('Resource HasTranslations Form Integration', function () {
    describe('changeTranslation property', function () {
        it('is initialized as null', function () {
            $pageClass = new class {
                use HasTranslations;
            };

            expect($pageClass->changeTranslation)->toBeNull();
        });

        it('can be set to a string value', function () {
            $pageClass = new class {
                use HasTranslations;
            };

            $pageClass->changeTranslation = 'it';

            expect($pageClass->changeTranslation)->toBe('it');
        });

        it('property is nullable string type', function () {
            $pageClass = new class {
                use HasTranslations;
            };

            $reflection = new ReflectionProperty($pageClass, 'changeTranslation');

            expect($reflection->getType()->allowsNull())->toBeTrue();
        });
    });

    describe('updatedChangeTranslation method', function () {
        it('returns null when value is null', function () {
            $pageClass = new class {
                use HasTranslations;

                public function redirect($url)
                {
                    return $url;
                }
            };

            $result = $pageClass->updatedChangeTranslation(null);

            expect($result)->toBeNull();
        });

        it('exists and is callable', function () {
            $pageClass = new class {
                use HasTranslations;
            };

            expect(method_exists($pageClass, 'updatedChangeTranslation'))->toBeTrue();
        });
    });

    describe('form method', function () {
        it('exists in trait', function () {
            $pageClass = new class {
                use HasTranslations;
            };

            expect(method_exists($pageClass, 'form'))->toBeTrue();
        });

        it('accepts Form parameter', function () {
            $pageClass = new class {
                use HasTranslations;
            };

            $reflectionMethod = new ReflectionMethod($pageClass, 'form');
            $parameters = $reflectionMethod->getParameters();

            expect($parameters)->toHaveCount(1);
            expect($parameters[0]->getName())->toBe('form');
        });
    });
});
