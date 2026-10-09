<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extend Factory<Project> */
class ProjectFactory extends Factory
{
    /** @var array<string> */
    protected $statuses = [
        'planning',
        'in_progress',
        'on_hold',
        'completed',
        'cancelled',
    ];

    /**
     * Handle creating a when no clients exist.
     */
    public function creating(Factory $factory, array $attributes): void
    {
        if (! Client::exists()) {
            Client::create(['name' => 'Test Client']);
        }
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clientIds = Client::pluck('id')->toArray();

        return [
            'client_id' => $clientIds ? $clientIds[array_rand($clientIds)] : 1,
            'name' => 'Proyek '.Str::random(5),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement($this->statuses),
            'budget' => fake()->randomNumber(5),
            'start_date' => fake()->date('2024-01-01', '2024-12-31'),
            'due_date' => fake()->date('2024-01-01', '2024-12-31'),
        ];
    }
}
