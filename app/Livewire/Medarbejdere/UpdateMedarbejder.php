<?php

namespace App\Livewire\Medarbejdere;
use Livewire\Component;
use App\Models\Medarbejdere;
use App\Livewire\forms\MedarbejderForm;
use App\Models\User;

class UpdateMedarbejder extends Component
{
    public ?Medarbejdere $medarbejder;
    public MedarbejderForm $form;
    public $message = "Rediger denne medarbejder";

    public function mount(Medarbejdere $medarbejder)
    {
        $this->medarbejder = $medarbejder;

        $this->form->setMedarbejder($medarbejder); // ✅ FIXED
    }

    public function save()
    {
        $this->form->update();

        $this->dispatch('toast', message: 'Medarbejder gemt!');
    }

    public function createUserForMedarbejder()
    {
        $email = strtolower(str_replace(' ', '.', $this->medarbejder->navn)) . '@example.com';

        if (User::where('email', $email)->exists()) {
            $this->dispatch('toast', message: 'Bruger findes allerede!');
            return;
        }

        $user = User::create([
            'name' => $this->medarbejder->navn,
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('Medarbejder');

        // ✅ correct relation approach (pivot)
        $this->medarbejder->users()->sync([$user->id]);

        $this->dispatch('toast', message: 'Bruger oprettet!');
    }

    public function render()
    {
        return view('livewire.medarbejdere.update-medarbejder', [ // ✅ FIX FILE NAME
            'medarbejder' => $this->medarbejder, // ✅ PASS SINGLE
            'users' => User::role('Medarbejder')->get(),
        ]);
    }
}