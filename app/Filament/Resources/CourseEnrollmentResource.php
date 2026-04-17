<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseEnrollmentResource\Pages;
use App\Models\CourseEnrollment;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseEnrollmentResource extends Resource
{
    protected static ?string $model = CourseEnrollment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت آموزش';

    protected static ?string $modelLabel = 'درخواست ثبت نام';

    protected static ?string $pluralModelLabel = 'درخواست های ثبت نام';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('user_id')->label('کاربر')->relationship('user', 'name')->disabled(),
            Forms\Components\Select::make('course_id')->label('دوره')->relationship('course', 'title')->disabled(),
            Forms\Components\Select::make('status')
                ->label('وضعیت')
                ->options([
                    CourseEnrollment::STATUS_PENDING => 'در انتظار بررسی',
                    CourseEnrollment::STATUS_APPROVED => 'تایید شده',
                    CourseEnrollment::STATUS_REJECTED => 'رد شده',
                ])
                ->required(),
            Forms\Components\TextInput::make('amount')->label('مبلغ پرداختی')->numeric()->disabled(),
            Forms\Components\TextInput::make('receipt_reference')->label('شماره پیگیری')->disabled(),
            Forms\Components\DateTimePicker::make('paid_at')->label('تاریخ پرداخت')->disabled(),
            Forms\Components\Placeholder::make('receipt_preview')
                ->label('تصویر فیش')
                ->content(fn (?CourseEnrollment $record) => $record?->receipt_image_url ? new \Illuminate\Support\HtmlString('<a href="' . $record->receipt_image_url . '" target="_blank">مشاهده فیش پرداخت</a>') : 'فایلی ثبت نشده است')
                ->columnSpanFull(),
            Forms\Components\Textarea::make('user_note')->label('توضیح کاربر')->disabled()->rows(3)->columnSpanFull(),
            Forms\Components\Textarea::make('admin_note')->label('توضیح مدیر')->rows(4)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('کاربر')->searchable(),
                Tables\Columns\TextColumn::make('course.title')->label('دوره')->searchable(),
                Tables\Columns\TextColumn::make('amount')->label('مبلغ')->formatStateUsing(fn ($state) => $state ? number_format((int) $state) . ' تومان' : '-'),
                Tables\Columns\TextColumn::make('status')->label('وضعیت')->badge()->formatStateUsing(fn (string $state) => match ($state) {
                    CourseEnrollment::STATUS_PENDING => 'در انتظار بررسی',
                    CourseEnrollment::STATUS_APPROVED => 'تایید شده',
                    default => 'رد شده',
                }),
                Tables\Columns\TextColumn::make('paid_at')->label('پرداخت')->since(),
                Tables\Columns\TextColumn::make('reviewer.name')->label('بررسی توسط')->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        CourseEnrollment::STATUS_PENDING => 'در انتظار بررسی',
                        CourseEnrollment::STATUS_APPROVED => 'تایید شده',
                        CourseEnrollment::STATUS_REJECTED => 'رد شده',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseEnrollments::route('/'),
            'edit' => Pages\EditCourseEnrollment::route('/{record}/edit'),
        ];
    }
}
