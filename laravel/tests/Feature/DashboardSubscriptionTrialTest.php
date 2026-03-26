<?php

use App\Models\User;

test('authenticated user can enable pro trial from dashboard', function () {
    $user = User::factory()->create([
        'subscription_plan' => 'free',
    ]);

    $response = $this->actingAs($user)->post(route('dashboard.subscription.trial-toggle'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $response->assertSessionHas('success');

    expect($user->fresh()->subscription_plan)->toBe('pro');
});

test('authenticated user can disable pro trial from dashboard', function () {
    $user = User::factory()->create([
        'subscription_plan' => 'pro',
    ]);

    $response = $this->actingAs($user)->post(route('dashboard.subscription.trial-toggle'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $response->assertSessionHas('success');

    expect($user->fresh()->subscription_plan)->toBe('free');
});

test('guest cannot toggle pro trial from dashboard', function () {
    $response = $this->post(route('dashboard.subscription.trial-toggle'));

    $response->assertRedirect(route('login', absolute: false));
});
