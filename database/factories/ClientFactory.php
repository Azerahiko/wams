<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /** Define the model's default state. */
    public function definition(): array
    {
        return [
            'name' => 'PT. '.ucwords(Str::lower('acme-corp')),
            'contact_name' => 'John Doe',
            'email' => 'contact@acme.com',
            'phone' => '0812345678',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'notes' => 'Klien penting',
            'status' => 'active',
        ];
    }
}
