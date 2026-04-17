<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Closure;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت آموزش';

    protected static ?string $modelLabel = 'دوره';

    protected static ?string $pluralModelLabel = 'دوره ها';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('course_category_id')
                ->label('دسته بندی')
                ->relationship('category', 'title')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('title')->label('عنوان دوره')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->label('اسلاگ')->required()->unique(ignoreRecord: true)->maxLength(255),
            Forms\Components\TextInput::make('instructor_name')->label('مدرس')->maxLength(255),
            Forms\Components\TextInput::make('level')->label('سطح دوره')->maxLength(255),
            Forms\Components\TextInput::make('duration')->label('مدت زمان کل')->maxLength(255),
            Forms\Components\TextInput::make('support_channel')->label('راه ارتباطی پشتیبانی')->maxLength(255),
            Forms\Components\TextInput::make('price')
                ->label('قیمت کل (تومان)')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->required()
                ->live(),
            Forms\Components\TextInput::make('prepayment_amount')
                ->label('پیش پرداخت (تومان)')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->maxValue(fn (Get $get): int => (int) ($get('price') ?? 0))
                ->live()
                ->rule(function (Get $get): Closure {
                    return function (string $attribute, $value, Closure $fail) use ($get): void {
                        $price = (int) ($get('price') ?? 0);
                        $prepaymentAmount = (int) ($value ?? 0);

                        if ($prepaymentAmount > $price) {
                            $fail('پیش پرداخت نمی‌تواند بیشتر از قیمت کل دوره باشد.');
                        }
                    };
                }),
            Forms\Components\Placeholder::make('remaining_amount')
                ->label('باقی‌مانده پس از پیش پرداخت')
                ->content(fn (Get $get): string => number_format(max((int) ($get('price') ?? 0) - (int) ($get('prepayment_amount') ?? 0), 0)) . ' تومان'),
            Forms\Components\TextInput::make('installment_months')
                ->label('تعداد ماه اقساط')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->maxValue(36)
                ->live()
                ->rule(function (Get $get): Closure {
                    return function (string $attribute, $value, Closure $fail) use ($get): void {
                        $months = (int) ($value ?? 0);
                        $monthlyAmount = (int) ($get('installment_monthly_amount') ?? 0);
                        $remainingAmount = max((int) ($get('price') ?? 0) - (int) ($get('prepayment_amount') ?? 0), 0);

                        if ($remainingAmount === 0 && $months > 0) {
                            $fail('وقتی باقی‌مانده صفر است، تعداد ماه اقساط باید صفر باشد.');
                        }

                        if ($months > 0 && $monthlyAmount <= 0) {
                            $fail('برای قسط‌بندی، مبلغ هر قسط را وارد کنید.');
                        }
                    };
                }),
            Forms\Components\TextInput::make('installment_monthly_amount')
                ->label('مبلغ هر قسط ماهانه (تومان)')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->live()
                ->rule(function (Get $get): Closure {
                    return function (string $attribute, $value, Closure $fail) use ($get): void {
                        $months = (int) ($get('installment_months') ?? 0);
                        $monthlyAmount = (int) ($value ?? 0);
                        $remainingAmount = max((int) ($get('price') ?? 0) - (int) ($get('prepayment_amount') ?? 0), 0);

                        if ($remainingAmount === 0 && $monthlyAmount > 0) {
                            $fail('وقتی باقی‌مانده صفر است، مبلغ قسط باید صفر باشد.');
                        }

                        if ($months === 0 && $monthlyAmount > 0) {
                            $fail('برای ثبت مبلغ قسط، تعداد ماه اقساط را هم وارد کنید.');
                        }

                        if ($months > 0 && $monthlyAmount <= 0) {
                            $fail('برای قسط‌بندی، مبلغ هر قسط باید بیشتر از صفر باشد.');
                        }

                        if ($months > 0 && ($months * $monthlyAmount) !== $remainingAmount) {
                            $fail('جمع اقساط باید دقیقاً برابر باقی‌مانده قیمت باشد.');
                        }
                    };
                }),
            Forms\Components\Placeholder::make('installment_total_amount')
                ->label('جمع اقساط')
                ->content(fn (Get $get): string => number_format((int) ($get('installment_months') ?? 0) * (int) ($get('installment_monthly_amount') ?? 0)) . ' تومان'),
            Forms\Components\TextInput::make('teaser_video_url')->label('لینک ویدیوی معرفی')->maxLength(255),
            Forms\Components\FileUpload::make('cover_image')->label('تصویر دوره')->directory('courses')->image(),
            Forms\Components\Textarea::make('short_description')->label('خلاصه توضیح')->rows(3)->columnSpanFull(),
            Forms\Components\RichEditor::make('description')->label('توضیحات کامل')->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')->label('ترتیب نمایش')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->label('منتشر شده')->default(true),
            Forms\Components\Toggle::make('is_featured')->label('نمایش ویژه')->default(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->label('تصویر')->disk('public')->square(),
                Tables\Columns\TextColumn::make('title')->label('عنوان')->searchable(),
                Tables\Columns\TextColumn::make('category.title')->label('دسته بندی')->placeholder('-'),
                Tables\Columns\TextColumn::make('price')->label('قیمت')->formatStateUsing(fn ($state) => number_format((int) $state) . ' تومان'),
                Tables\Columns\TextColumn::make('prepayment_amount')->label('پیش پرداخت')->formatStateUsing(fn ($state) => number_format((int) $state) . ' تومان'),
                Tables\Columns\TextColumn::make('installment_plan')
                    ->label('طرح اقساط')
                    ->state(fn (Course $record): string => $record->installment_months > 0 && $record->installment_monthly_amount > 0
                        ? $record->installment_months . ' ماه × ' . number_format((int) $record->installment_monthly_amount) . ' تومان'
                        : '-'),
                Tables\Columns\TextColumn::make('episodes_count')->counts('episodes')->label('جلسات'),
                Tables\Columns\IconColumn::make('is_featured')->label('ویژه')->boolean(),
                Tables\Columns\IconColumn::make('is_published')->label('انتشار')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Actions\EditAction::make(),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
