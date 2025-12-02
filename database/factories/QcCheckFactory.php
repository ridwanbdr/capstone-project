<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QcCheck>
 */
class QcCheckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'qc_id' => 'QC' . str_pad($this->faker->unique()->numberBetween(1000, 9999), 6, '0', STR_PAD_LEFT),
            'product_name' => $this->faker->randomElement(['Product A', 'Product B', 'Product C']),
            'qty_passed' => $this->faker->numberBetween(50, 200),
            'qty_reject' => $this->faker->numberBetween(0, 20),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'qc_checker' => 'USR000001',
            'qc_label' => $this->faker->randomElement(['PASS', 'FAIL', 'PENDING']),
        ];
    }
}
