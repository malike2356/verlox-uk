<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /** MIS admin (can access `/mis/*`). */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
            'mis_role' => null,
        ]);
    }

    /** MIS finance role (not admin); cannot use `mis.super` routes. */
    public function misFinance(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => false,
            'mis_role' => 'finance',
        ]);
    }

    /** MIS VA-only: CRM routes redirect to VA dashboard. */
    public function misVa(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => false,
            'mis_role' => 'va',
        ]);
    }

    /** Logged-in user with no MIS access (no admin, no finance/va role). */
    public function withoutMisAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => false,
            'mis_role' => null,
        ]);
    }
}
