<?php

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateCourseImages extends Command
{
    protected $signature = 'courses:generate-images';
    protected $description = 'Generate course cover images and update database';

    private array $palettes = [
        'frontend' => [['#667eea', '#764ba2'], ['#f093fb', '#f5576c']],
        'laravel' => [['#4facfe', '#00f2fe'], ['#fa709a', '#fee140']],
        'react' => [['#00c6fb', '#005bea'], ['#36d1dc', '#5b86e5']],
        'svelte' => [['#f857a6', '#ff5858'], ['#ff758c', '#ff7eb3']],
        'git' => [['#f12711', '#f5af19'], ['#e65c00', '#f9d423']],
        'database' => [['#11998e', '#38ef7d'], ['#0575e6', '#021b79']],
        'fundamentals' => [['#8e2de2', '#4a00e0'], ['#fc5c7d', '#6a82fb']],
    ];

    public function handle(): int
    {
        $this->info('Generating course cover images...');
        $dir = 'courses';

        $courses = Course::with('category')->get();
        $bar = $this->output->createProgressBar(count($courses));
        $bar->start();

        foreach ($courses as $course) {
            $catSlug = $course->category?->slug ?? 'frontend';
            $palette = $this->palettes[$catSlug] ?? $this->palettes['frontend'];
            $colors = $palette[array_rand($palette)];

            $filename = "course_{$course->id}.webp";
            $path = storage_path("app/public/{$dir}/{$filename}");

            $this->generateImage($path, $colors, $course->title, 800, 500);

            $course->update(['cover_image' => "{$dir}/{$filename}"]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done! ' . count($courses) . ' course images generated.');
        return Command::SUCCESS;
    }

    private function generateImage(string $path, array $colors, string $text, int $w, int $h): void
    {
        $img = imagecreatetruecolor($w, $h);
        imageantialias($img, true);

        // Gradient background
        $r1 = hexdec(substr($colors[0], 1, 2));
        $g1 = hexdec(substr($colors[0], 3, 2));
        $b1 = hexdec(substr($colors[0], 5, 2));
        $r2 = hexdec(substr($colors[1], 1, 2));
        $g2 = hexdec(substr($colors[1], 3, 2));
        $b2 = hexdec(substr($colors[1], 5, 2));

        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            $r = (int) ($r1 + ($r2 - $r1) * $ratio);
            $g = (int) ($g1 + ($g2 - $g1) * $ratio);
            $b = (int) ($b1 + ($b2 - $b1) * $ratio);
            $color = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $w, $y, $color);
        }

        // Decorative circles
        for ($i = 0; $i < 3; $i++) {
            $cx = rand(50, $w - 50);
            $cy = rand(30, $h - 30);
            $radius = rand(40, 120);
            $alpha = rand(10, 25);
            $color = imagecolorallocatealpha($img, 255, 255, 255, $alpha);
            imagefilledellipse($img, $cx, $cy, $radius * 2, $radius * 2, $color);
        }

        // Overlay dark gradient at bottom for text readability
        for ($y = (int)($h * 0.6); $y < $h; $y++) {
            $ratio = ($y - $h * 0.6) / ($h * 0.4);
            $alpha = (int) (80 * $ratio);
            $color = imagecolorallocatealpha($img, 0, 0, 0, $alpha);
            imageline($img, 0, $y, $w, $y, $color);
        }

        // Add text
        $fontSize = min(28, (int) ($w / mb_strlen($text) * 2.2));
        $fontSize = max(18, $fontSize);
        $fontFile = '/System/Library/Fonts/Supplemental/Arial Bold.ttf';

        if (file_exists($fontFile)) {
            $white = imagecolorallocate($img, 255, 255, 255);
            $black = imagecolorallocatealpha($img, 0, 0, 0, 40);
            $textX = 40;
            $textY = $h - 60;

            // Shadow
            imagettftext($img, $fontSize, 0, $textX + 2, $textY + 2, $black, $fontFile, $text);

            try {
                $chars = mb_strlen($text);
                if ($chars > 30) {
                    $lines = explode(' ', wordwrap($text, 30, "\n"));
                    $lineHeight = $fontSize + 8;
                    foreach ($lines as $i => $line) {
                        imagettftext($img, $fontSize, 0, $textX, $textY + ($i * $lineHeight), $white, $fontFile, $line);
                    }
                } else {
                    imagettftext($img, $fontSize, 0, $textX, $textY, $white, $fontFile, $text);
                }
            } catch (\Throwable $e) {
                imagestring($img, 5, $textX, $textY - 20, $text, $white);
            }
        } else {
            $white = imagecolorallocate($img, 255, 255, 255);
            imagestring($img, 5, 40, $h - 60, $text, $white);
        }

        imagewebp($img, $path, 85);
        imagedestroy($img);
    }
}
