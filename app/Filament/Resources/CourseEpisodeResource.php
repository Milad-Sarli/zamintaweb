<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseEpisodeResource\Pages;
use App\Models\CourseEpisode;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CourseEpisodeResource extends Resource
{
    protected static ?string $model = CourseEpisode::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-play-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت آموزش';

    protected static ?string $modelLabel = 'جلسه دوره';

    protected static ?string $pluralModelLabel = 'جلسات دوره';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('course_id')
                ->label('دوره')
                ->relationship('course', 'title')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('title')->label('عنوان جلسه')->required()->maxLength(255),
            Forms\Components\TextInput::make('duration')->label('مدت زمان')->maxLength(255),
            Forms\Components\TextInput::make('video_url')->label('لینک ویدیو')->maxLength(255)->columnSpanFull(),
            Forms\Components\Textarea::make('description')->label('توضیحات جلسه')->rows(4)->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0),
            Forms\Components\Toggle::make('is_preview')->label('نمایش رایگان')->default(false),
            Forms\Components\Toggle::make('is_published')->label('منتشر شده')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.title')->label('دوره')->searchable(),
                Tables\Columns\TextColumn::make('title')->label('عنوان جلسه')->searchable(),
                Tables\Columns\TextColumn::make('duration')->label('مدت زمان')->placeholder('-'),
                Tables\Columns\IconColumn::make('is_preview')->label('پیش نمایش')->boolean(),
                Tables\Columns\IconColumn::make('is_published')->label('انتشار')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseEpisodes::route('/'),
            'create' => Pages\CreateCourseEpisode::route('/create'),
            'edit' => Pages\EditCourseEpisode::route('/{record}/edit'),
        ];
    }
}
