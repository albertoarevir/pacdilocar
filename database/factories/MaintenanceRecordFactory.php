<?php

namespace Database\Factories;

use App\Models\MaintenanceCategory;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceRecordFactory extends Factory
{
    protected $model = MaintenanceRecord::class;

    public function definition(): array
    {
        $fechaIngreso = $this->faker->dateTimeBetween('-2 years', '-1 day');
        $cerrado      = $this->faker->boolean(60);
        $fechaSalida  = $cerrado
            ? $this->faker->dateTimeBetween($fechaIngreso, 'now')
            : null;

        $diasParalizado = $fechaSalida
            ? (int) \Carbon\Carbon::parse($fechaIngreso)->diffInDays($fechaSalida)
            : null;

        return [
            'vehiculo_id'          => Vehicle::inRandomOrder()->first()?->id
                                      ?? Vehicle::factory()->create()->id,
            'categoria_id'         => MaintenanceCategory::inRandomOrder()->first()?->id,
            'taller_id'            => Workshop::inRandomOrder()->first()?->id,
            'fecha_ingreso'        => $fechaIngreso,
            'fecha_salida'         => $fechaSalida,
            'dias_paralizado'      => $diasParalizado,
            'estado'               => $cerrado
                ? 'Cerrado'
                : $this->faker->randomElement(['Abierto', 'En Diagnóstico']),
            'tipo_mantenimiento'   => $this->faker->randomElement([
                'Correctivo', 'Correctivo', 'Preventivo', 'Emergencia',
            ]),
            'descripcion_tecnica'  => $this->faker->optional()->sentence(10),
            'costo_total'          => $this->faker->numberBetween(50000, 2000000),
            'kilometraje_ingreso'  => $this->faker->optional()->numberBetween(5000, 200000),
            'numero_orden'         => 'OT-' . $this->faker->unique()->numerify('####-###'),
            'observaciones'        => $this->faker->optional()->sentence(),
        ];
    }

    public function abierto(): static
    {
        return $this->state(['estado' => 'Abierto', 'fecha_salida' => null, 'dias_paralizado' => null]);
    }

    public function cerrado(): static
    {
        return $this->afterMaking(function (MaintenanceRecord $registro) {
            $salida = $this->faker->dateTimeBetween($registro->fecha_ingreso, 'now');
            $registro->fecha_salida    = $salida;
            $registro->dias_paralizado = (int) \Carbon\Carbon::parse($registro->fecha_ingreso)->diffInDays($salida);
            $registro->estado          = 'Cerrado';
        });
    }
}
