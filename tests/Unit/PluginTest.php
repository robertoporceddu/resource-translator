<?php

use Filament\Contracts\Plugin;
use RobertoPorceddu\ResourceTranslator\ResourceTranslatorPlugin;

describe('ResourceTranslatorPlugin', function () {
    it('can be instantiated using make method', function () {
        $plugin = ResourceTranslatorPlugin::make();

        expect($plugin)->toBeInstanceOf(ResourceTranslatorPlugin::class);
    });

    it('implements the Filament Plugin interface', function () {
        $plugin = ResourceTranslatorPlugin::make();

        expect($plugin)->toBeInstanceOf(Plugin::class);
    });

    it('has the correct plugin id', function () {
        $plugin = ResourceTranslatorPlugin::make();

        expect($plugin->getId())->toBe('resource-translator');
    });

    it('getId returns a non-empty string', function () {
        $plugin = ResourceTranslatorPlugin::make();
        $id = $plugin->getId();

        expect($id)
            ->toBeString()
            ->not->toBeEmpty();
    });
});
