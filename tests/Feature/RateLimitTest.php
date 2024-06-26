<?php

use App\Models\Event;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('rate limiting on attendees endpoint', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    // Send requests up to the limit
    for ($i = 0; $i < 60; $i++) {
        $response = $this->postJson("/api/events/{$event->id}/attendees", [
            'user_id' => $user->id
        ]);
        $response->assertStatus(201);  // Assuming 201 is returned for successful creation

        // Check rate limit headers
        expect($response->headers->has('X-RateLimit-Remaining'))->toBeTrue();
        expect((int) $response->headers->get('X-RateLimit-Remaining'))->toBe(59 - $i);
    }

    // The next request should be rate limited
    $response = $this->postJson("/api/events/{$event->id}/attendees", [
        'user_id' => $user->id
    ]);
    $response->assertStatus(429); // Too Many Requests

    // Check the retry-after header
    expect($response->headers->has('Retry-After'))->toBeTrue();
});