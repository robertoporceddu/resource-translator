<?php

use RobertoPorceddu\ResourceTranslator\Filament\Actions\ChangeTranslationAction;

describe('ChangeTranslationAction', function () {
    it('has the correct default name', function () {
        expect(ChangeTranslationAction::getDefaultName())->toBe('changeTranslation');
    });

    it('default name is not null', function () {
        expect(ChangeTranslationAction::getDefaultName())->not->toBeNull();
    });

    it('default name is a string', function () {
        expect(ChangeTranslationAction::getDefaultName())->toBeString();
    });

    it('class exists', function () {
        expect(class_exists(ChangeTranslationAction::class))->toBeTrue();
    });
});
