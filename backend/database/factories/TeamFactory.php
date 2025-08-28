<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'owner_id' => User::factory(),
            'personal_team' => false,
            'settings' => [
                'allow_invitations' => fake()->boolean(80),
                'visibility' => fake()->randomElement(['public', 'private']),
            ],
        ];
    }

    public function personal(): static
    {
        return $this->state(fn (array $attributes) => [
            'personal_team' => true,
            'settings' => [
                'allow_invitations' => false,
                'visibility' => 'private',
            ],
        ])->afterMaking(function (Team $team) {
            if ($team->owner_id) {
                $owner = User::find($team->owner_id);
                if ($owner) {
                    $team->name = $owner->name."'s Team";
                    $team->slug = Str::slug($owner->name.'-team');
                }
            }
        })->afterCreating(function (Team $team) {
            if ($team->owner_id) {
                $owner = User::find($team->owner_id);
                if ($owner && !str_contains($team->name, "'s Team")) {
                    $team->update([
                        'name' => $owner->name."'s Team",
                        'slug' => Str::slug($owner->name.'-team'),
                    ]);
                }
            }
        });
    }

    /**
     * Set custom settings for the team.
     *
     * @param  array<string, mixed>  $settings
     */
    public function withSettings(array $settings): static
    {
        return $this->state(fn (array $attributes) => [
            'settings' => array_merge($attributes['settings'] ?? [], $settings),
        ]);
    }
}
