<?php

namespace Database\Factories;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamMember>
 */
class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'user_id' => User::factory(),
            'role' => fake()->randomElement([
                TeamRole::Admin->value,
                TeamRole::Member->value,
                TeamRole::Viewer->value,
            ]),
        ];
    }

    public function asOwner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => TeamRole::Owner->value,
        ]);
    }

    public function asAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => TeamRole::Admin->value,
        ]);
    }

    public function asMember(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => TeamRole::Member->value,
        ]);
    }

    public function asViewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => TeamRole::Viewer->value,
        ]);
    }
}
