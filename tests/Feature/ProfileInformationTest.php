<?php

use App\Models\User;
use Laravel\Jetstream\Http\liveWire\UpdateProfileInformationForm;
use Livewire\liveWire;

test('current profile information is available', function () {
    $this->actingAs($user = User::factory()->create());

    $component = liveWire::test(UpdateProfileInformationForm::class);

    expect($component->state['name'])->toEqual($user->name);
    expect($component->state['email'])->toEqual($user->email);
});

test('profile information can be updated', function () {
    $this->actingAs($user = User::factory()->create());

    liveWire::test(UpdateProfileInformationForm::class)
        ->set('state', ['name' => 'Test Name', 'email' => 'test@example.com'])
        ->call('updateProfileInformation');

    expect($user->fresh())
        ->name->toEqual('Test Name')
        ->email->toEqual('test@example.com');
});
