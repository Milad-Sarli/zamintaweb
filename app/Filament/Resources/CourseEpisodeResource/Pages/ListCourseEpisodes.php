<?php

namespace App\Filament\Resources\CourseEpisodeResource\Pages;

use App\Filament\Resources\CourseEpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseEpisodes extends ListRecords
{
    protected static string $resource = CourseEpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('جلسه جدید'),
        ];
    }
}
