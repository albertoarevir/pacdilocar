<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\FuelType;
use App\Models\FundingOrigin;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $brands = ['Toyota', 'Ford', 'Nissan', 'Chevrolet', 'Hyundai', 'Kia', 'Mitsubishi', 'Volkswagen'];
        $models = ['Hilux', 'Ranger', 'Transit', 'Navara', 'D-MAX', 'Accent', 'Sprinter', 'Amarok'];

        return [
            'patente'            => strtoupper($this->faker->unique()->bothify('??-####')),
            'vehicle_type_id'    => VehicleType::inRandomOrder()->first()?->id
                                    ?? VehicleType::factory()->create()->id,
            'brand'              => $this->faker->randomElement($brands),
            'model'              => $this->faker->randomElement($models),
            'color_id'           => Color::inRandomOrder()->first()?->id,
            'year'               => $this->faker->numberBetween(2015, 2024),
            'service_start_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'fuel_type_id'       => FuelType::inRandomOrder()->first()?->id,
            'engine_number'      => strtoupper($this->faker->bothify('??######')),
            'chassis_number'     => strtoupper($this->faker->bothify('?####??####')),
            'funding_origin_id'  => FundingOrigin::inRandomOrder()->first()?->id,
            'status'             => $this->faker->randomElement([
                'OPERATIVO', 'OPERATIVO', 'OPERATIVO', // ponderado hacia operativo
                'PANNE', 'MANTENIMIENTO', 'BAJA',
            ]),
            'is_aggregated'      => $this->faker->boolean(20),
            'observations'       => $this->faker->optional()->sentence(),
        ];
    }

    public function operativo(): static
    {
        return $this->state(['status' => 'OPERATIVO']);
    }

    public function enPanne(): static
    {
        return $this->state(['status' => 'PANNE']);
    }

    public function enMantenimiento(): static
    {
        return $this->state(['status' => 'MANTENIMIENTO']);
    }
}
