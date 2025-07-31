<p align="center">
    <a href="https://packagist.org/packages/robertoporceddu/resource-translator"><img src="https://img.shields.io/packagist/v/robertoporceddu/resource-translator.svg" alt="Latest Version on Packagist"></a>
    <a href="https://github.com/robertoporceddu/resource-translator/blob/main/LICENSE.md"><img src="https://img.shields.io/github/license/robertoporceddu/resource-translator.svg" alt="License"></a>
</p>

---

# Resource Translator for Filament

A Filament v3 plugin to add multi-language (i18n) support to your Eloquent models and Filament resources, with an intuitive UI for managing translations.

---

## Requirements

- [kenepa/translation-manager](https://github.com/kenepa/translation-manager) (used for managing translation files and locales)
- PHP 8.1+
- Filament v3

---

## Features

- Effortless translation management for Eloquent models
- Seamless integration with Filament v3 resources and pages
- Easy switching and editing of translations per resource
- Simple database structure for translatable resources
- Compatible with existing Filament workflows

---

## Quick Start

```bash
# Install the resource translator plugin
composer require robertoporceddu/resource-translator:dev-main
```

1. **Add the trait to your Eloquent model:**
    ```php
    use RobertoPorceddu\ResourceTranslator\Traits\Models\HasTranslations;

    class Product extends Model
    {
        use HasFactory, HasTranslations;
        // ...
    }
    ```
2. **Add the trait to your Filament Edit Page:**
    ```php
    use RobertoPorceddu\ResourceTranslator\Traits\Resources\HasTranslations;

    class EditProduct extends EditRecord
    {
        use HasTranslations;
        // ...
    }
    ```
3. **Add the translation action to your Edit Page:**
    ```php
    use RobertoPorceddu\ResourceTranslator\Filament\Actions\ChangeTranslationAction;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            ChangeTranslationAction::make(),
        ];
    }
    ```
4. **Register the plugin in your Filament panel provider:**
    ```php
    use RobertoPorceddu\ResourceTranslator\ResourceTranslatorPlugin;

    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->plugin(ResourceTranslatorPlugin::make());
    }
    ```
5. **Update your migration:**
    ```php
    $table->string('translation_group_uuid')->index();
    $table->string('locale', 5)->index();
    ```

---

## Example

```php
// Migration
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('translation_group_uuid')->index();
    $table->string('locale', 5)->index();
    $table->string('name');
    $table->timestamps();
});

// Model
use RobertoPorceddu\ResourceTranslator\Traits\Models\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations;
    // ...
}

// Filament Edit Page
use RobertoPorceddu\ResourceTranslator\Traits\Resources\HasTranslations;
use RobertoPorceddu\ResourceTranslator\Filament\Actions\ChangeTranslationAction;

class EditProduct extends EditRecord
{
    use HasTranslations;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            ChangeTranslationAction::make(),
        ];
    }
}
```

---

Happy translating! 🎉
