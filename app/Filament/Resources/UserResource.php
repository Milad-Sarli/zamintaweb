<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت کاربران';

    protected static ?string $modelLabel = 'کاربر';

    protected static ?string $pluralModelLabel = 'کاربران';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('name')->label('نام نمایشی')->required()->maxLength(255),
            Forms\Components\TextInput::make('first_name')->label('نام')->maxLength(255),
            Forms\Components\TextInput::make('last_name')->label('نام خانوادگی')->maxLength(255),
            Forms\Components\TextInput::make('phone')->label('شماره موبایل')->maxLength(255),
            Forms\Components\TextInput::make('email')->label('ایمیل')->email()->maxLength(255),
            Forms\Components\TextInput::make('national_id')->label('کد ملی')->maxLength(255),
            Forms\Components\Toggle::make('admin_panel_access')->label('دسترسی ادمین')->default(false),
            Forms\Components\DateTimePicker::make('email_verified_at')->label('تایید ایمیل'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')->label('نام')->searchable(['name', 'first_name', 'last_name']),
                Tables\Columns\TextColumn::make('phone')->label('موبایل')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('ایمیل')->searchable(),
                Tables\Columns\TextColumn::make('course_enrollments_count')->counts('courseEnrollments')->label('درخواست ها'),
                Tables\Columns\IconColumn::make('admin_panel_access')->label('ادمین')->boolean(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
