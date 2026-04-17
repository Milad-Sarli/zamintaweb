<?php

namespace App\Filament\Resources\CourseEpisodeResource\Pages;

use App\Filament\Resources\CourseEpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseEpisode extends EditRecord
{
    protected static string $resource = CourseEpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
