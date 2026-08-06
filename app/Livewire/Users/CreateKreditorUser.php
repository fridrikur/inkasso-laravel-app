<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Livewire\forms\UserForm;
use App\Models\Kreditorer;

class Createkreditoruser extends Component
{
    public UserForm $form;         // liveWire 3 Form object
    public $kreditor_id;
    public $kreditornavn;

    public function mount()
    {
        $this->kreditor_id = request()->kreditor_id;
        $kreditor = Kreditorer::find($this->kreditor_id);
        $this->kreditornavn = $kreditor->navn ?? '';

        // Force role to Kreditor
        $this->form->role = 'Kreditor';
    }

    public function save()
    {
        // Validate the form
        $this->form->validate();

        // Role is required
        if (!$this->form->role) {
            $this->addError('form.role', 'Du skal vælge en rolle');
            return;
        }

        // Create the user
        $user = $this->form->create();

        // Attach Kreditor pivot
        $user->kreditorer()->attach($this->kreditor_id);

        // Flash message
        session()->flash('message', 'Bruger oprettet for ' . $this->kreditornavn . '!');

        // Reset form
        $this->form->reset();

        // Redirect
        $this->redirect('/users');
    }

    public function render()
    {
        return view('liveWire.users.create-kreditor-user', [
            'kreditornavn' => $this->kreditornavn
        ]);
    }
}
