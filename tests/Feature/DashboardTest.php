<?php

use App\Models\User;

it('shows issue tracker navigation on the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Projects')
        ->assertSee('Issues')
        ->assertSee('Tags')
        ->assertSee(route('projects.index', absolute: false))
        ->assertSee(route('issues.index', absolute: false))
        ->assertSee(route('tags.index', absolute: false));
});
