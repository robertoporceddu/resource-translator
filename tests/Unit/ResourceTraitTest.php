<?php

use RobertoPorceddu\ResourceTranslator\Traits\Resources\HasTranslations;

describe('HasTranslations Resource Trait', function () {
    describe('trait properties', function () {
        it('has changeTranslation property', function () {
            // Create an anonymous class using the trait
            $class = new class {
                use HasTranslations;
            };

            expect(property_exists($class, 'changeTranslation'))->toBeTrue();
        });

        it('changeTranslation property is nullable string', function () {
            $class = new class {
                use HasTranslations;
            };

            expect($class->changeTranslation)->toBeNull();
        });
    });

    describe('trait methods', function () {
        it('has updatedChangeTranslation method', function () {
            expect(method_exists(
                new class { use HasTranslations; },
                'updatedChangeTranslation'
            ))->toBeTrue();
        });

        it('has form method', function () {
            expect(method_exists(
                new class { use HasTranslations; },
                'form'
            ))->toBeTrue();
        });

        it('updatedChangeTranslation returns null when value is null', function () {
            $class = new class {
                use HasTranslations;

                public function redirect($url)
                {
                    return $url;
                }
            };

            $result = $class->updatedChangeTranslation(null);

            expect($result)->toBeNull();
        });
    });

    describe('method signatures', function () {
        it('updatedChangeTranslation accepts a value parameter', function () {
            $class = new class { use HasTranslations; };
            $reflectionMethod = new ReflectionMethod($class, 'updatedChangeTranslation');
            $parameters = $reflectionMethod->getParameters();

            expect($parameters)->toHaveCount(1);
            expect($parameters[0]->getName())->toBe('value');
        });

        it('form method accepts Form parameter', function () {
            $class = new class { use HasTranslations; };
            $reflectionMethod = new ReflectionMethod($class, 'form');
            $parameters = $reflectionMethod->getParameters();

            expect($parameters)->toHaveCount(1);
            expect($parameters[0]->getName())->toBe('form');
        });
    });
});

