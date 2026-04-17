<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseCategoryResource\Pages;
use App\Models\CourseCategory;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CourseCategoryResource extends Resource
{
    protected static ?string $model = CourseCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت آموزش';

    protected static ?string $modelLabel = 'دسته بندی دوره';

    protected static ?string $pluralModelLabel = 'دسته بندی های دوره';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')->label('عنوان')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->label('اسلاگ')->required()->unique(ignoreRecord: true)->maxLength(255),
            Forms\Components\Textarea::make('description')->label('توضیحات')->rows(4)->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('فعال')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('عنوان')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('اسلاگ')->searchable(),
                Tables\Columns\TextColumn::make('courses_count')->counts('courses')->label('تعداد دوره ها'),
                Tables\Columns\IconColumn::make('is_active')->label('وضعیت')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('آخرین بروزرسانی')->since(),
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
            'index' => Pages\ListCourseCategories::route('/'),
            'create' => Pages\CreateCourseCategory::route('/create'),
            'edit' => Pages\EditCourseCategory::route('/{record}/edit'),
        ];
    }
}
