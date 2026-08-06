<?php

use App\Models\User;
use Laravel\Jetstream\Http\liveWire\LogoutOtherBrowserSessionsForm;
use Livewire\liveWire;

test('other browser sessions can be logged out', function () {
    $this->actingAs(User::factory()->create());

    liveWire::test(LogoutOtherBrowserSessionsForm::class)
        ->set('password', 'password')
        ->call('logoutOtherBrowserSessions')
        ->assertSuccessful();
});
