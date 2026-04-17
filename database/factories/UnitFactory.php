<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $units = [
            ['name' => 'عدد', 'short_name' => 'عدد'],
            ['name' => 'بسته', 'short_name' => 'بسته'],
            ['name' => 'کیلوگرم', 'short_name' => 'ک.گ'],
            ['name' => 'لیتر', 'short_name' => 'لیتر'],
        ];
        $unit = $this->faker->randomElement($units);

        return [
            'name' => $unit['name'],
            'short_name' => $unit['short_name'],
        ];
    }
}
