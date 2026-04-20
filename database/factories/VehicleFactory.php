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
        $marcas  = ['Toyota', 'Ford', 'Nissan', 'Chevrolet', 'Hyundai', 'Kia', 'Mitsubishi', 'Volkswagen'];
        $modelos = ['Hilux', 'Ranger', 'Transit', 'Navara', 'D-MAX', 'Accent', 'Sprinter', 'Amarok'];

        return [
            'patente'               => strtoupper($this->faker->unique()->bothify('??-####')),
            'tipo_vehiculo_id'      => VehicleType::inRandomOrder()->first()?->id
                                       ?? VehicleType::factory()->create()->id,
            'marca'                 => $this->faker->randomElement($marcas),
            'modelo'                => $this->faker->randomElement($modelos),
            'color_id'              => Color::inRandomOrder()->first()?->id,
            'anio'                  => $this->faker->numberBetween(2015, 2024),
            'fecha_inicio_servicio' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'tipo_combustible_id'   => FuelType::inRandomOrder()->first()?->id,
            'numero_motor'          => strtoupper($this->faker->bothify('??######')),
            'numero_chasis'         => strtoupper($this->faker->bothify('?####??####')),
            'origen_financiamiento_id' => FundingOrigin::inRandomOrder()->first()?->id,
            'estado'                => $this->faker->randomElement([
                'OPERATIVO', 'OPERATIVO', 'OPERATIVO',
                'PANNE', 'MANTENIMIENTO', 'BAJA',
            ]),
            'es_agregado'           => $this->faker->boolean(20),
            'observaciones'         => $this->faker->optional()->sentence(),
        ];
    }

    public function operativo(): static
    {
        return $this->state(['estado' => 'OPERATIVO']);
    }

    public function enPanne(): static
    {
        return $this->state(['estado' => 'PANNE']);
    }

    public function enMantenimiento(): static
    {
        return $this->state(['estado' => 'MANTENIMIENTO']);
    }
}
