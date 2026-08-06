<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\Status;

class StatusForm extends Form
{
    #[Validate('required')]
    public $tekst = '';

    #[Validate('required|min:1')] 
    public $forkortelse = '';

    public ?Status $status = null;

    public function setStatus(Status $status): void
    {
        $this->status = $status;
        $this->tekst = $status->tekst;
        $this->forkortelse = $status->forkortelse;
    }

    public function create(): Status
    {
        return Status::create($this->all());
    }

    public function update(): void
    {
        $this->validate();
        $this->status?->update($this->all());
    }
}
