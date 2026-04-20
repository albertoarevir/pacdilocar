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
        $entryDate = $this->faker->dateTimeBetween('-2 years', '-1 day');
        $isClosed  = $this->faker->boolean(60);
        $exitDate  = $isClosed
            ? $this->faker->dateTimeBetween($entryDate, 'now')
            : null;

        $downtimeDays = $exitDate
            ? (int) \Carbon\Carbon::parse($entryDate)->diffInDays($exitDate)
            : null;

        return [
            'vehicle_id'              => Vehicle::inRandomOrder()->first()?->id
                                         ?? Vehicle::factory()->create()->id,
            'maintenance_category_id' => MaintenanceCategory::inRandomOrder()->first()?->id,
            'workshop_id'             => Workshop::inRandomOrder()->first()?->id,
            'entry_date'              => $entryDate,
            'exit_date'               => $exitDate,
            'downtime_days'           => $downtimeDays,
            'record_status'           => $isClosed
                ? 'Cerrado'
                : $this->faker->randomElement(['Abierto', 'En Diagnóstico']),
            'maintenance_type'        => $this->faker->randomElement([
                'Correctivo', 'Correctivo', 'Preventivo', 'Emergencia',
            ]),
            'technical_description'   => $this->faker->optional()->sentence(10),
            'total_cost'              => $this->faker->numberBetween(50000, 2000000),
            'mileage_entry'           => $this->faker->optional()->numberBetween(5000, 200000),
            'work_order_number'       => 'OT-' . $this->faker->unique()->numerify('####-###'),
            'observations'            => $this->faker->optional()->sentence(),
        ];
    }

    public function abierto(): static
    {
        return $this->state(['record_status' => 'Abierto', 'exit_date' => null, 'downtime_days' => null]);
    }

    public function cerrado(): static
    {
        return $this->afterMaking(function (MaintenanceRecord $record) {
            $exit = $this->faker->dateTimeBetween($record->entry_date, 'now');
            $record->exit_date    = $exit;
            $record->downtime_days = (int) \Carbon\Carbon::parse($record->entry_date)->diffInDays($exit);
            $record->record_status = 'Cerrado';
        });
    }
}
