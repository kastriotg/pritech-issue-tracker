<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
        ];
    }

    /**
     * Attach a random number of issues to the created project.
     *
     * @param int $minimum The minimum number of issues to attach.
     * @param int $maximum The maximum number of issues to attach.
     * @return static
     */
    public function withRandomIssues(int $minimum = 1, int $maximum = 5): static
    {
        return $this->afterCreating(function (Project $project) use ($minimum, $maximum): void {
            Issue::factory()
                ->count(fake()->numberBetween($minimum, $maximum))
                ->for($project)
                ->create();
        });
    }
}
