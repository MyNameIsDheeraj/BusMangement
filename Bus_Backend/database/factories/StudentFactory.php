<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // Will be created separately to avoid circular dependencies
            'class_id' => \App\Models\ClassModel::factory(), // Assumes ClassModel factory exists or you'll create it
            'admission_no' => $this->faker->unique()->numerify('STU####'),
            'dob' => $this->faker->date('Y-m-d', '-5 years'),
            'address' => $this->faker->address(),
            'pickup_stop_id' => \App\Models\Stop::factory(),
            'drop_stop_id' => \App\Models\Stop::factory(),
            'bus_service_active' => $this->faker->boolean(),
            'academic_year' => $this->faker->regexify('/^\d{4}-\d{4}$/'), // Format like 2024-2025
        ];
    }
}