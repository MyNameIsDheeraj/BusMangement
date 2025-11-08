<?php

namespace Database\Factories;

use App\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassModelFactory extends Factory
{
    protected $model = ClassModel::class;

    public function definition(): array
    {
        return [
            'class_name' => $this->faker->words(2, true) . ' - ' . $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'class_teacher_id' => null, // Will be set separately to avoid circular dependencies
            'academic_year' => $this->faker->regexify('/^\d{4}-\d{4}$/'),
        ];
    }
}