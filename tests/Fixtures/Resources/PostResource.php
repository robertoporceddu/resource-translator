<?php

namespace RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Post;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources\PostResource\Pages\CreatePost;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources\PostResource\Pages\EditPost;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Resources\PostResource\Pages\ListPosts;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('content')
                    ->rows(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('locale')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}

