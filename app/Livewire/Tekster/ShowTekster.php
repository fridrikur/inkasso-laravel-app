<?php

namespace App\Livewire\Tekster;

use Livewire\Component;
use App\Models\Status;
use App\Models\Autotekster;

class ShowTekster extends Component
{
    public string $tab = 'status';

    public string $search = '';

    public bool $showModal = false;
    public bool $showDeleteModal = false;

    public ?int $editingId = null;

    /**
     * status | autotekst
     */
    public string $type = 'status';

    public ?int $deleteId = null;
    public ?string $deleteType = null;

    public array $form = [
        'tekst' => '',
        'forkortelse' => '',
        'dato' => null,
    ];

    protected function rules(): array
    {
        if ($this->type === 'status') {

            return [
                'form.tekst' => ['required', 'string', 'max:255'],
                'form.forkortelse' => ['required', 'string', 'max:50'],
            ];
        }

        return [
            'form.tekst' => ['required', 'string'],
            'form.dato' => ['nullable', 'date'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $this->resetForm();

        $this->type = $this->tab;

        $this->showModal = true;
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT STATUS
    |--------------------------------------------------------------------------
    */

    public function editStatus(int $id)
    {
        $status = Status::findOrFail($id);

        $this->editingId = $status->id;

        $this->type = 'status';

        $this->form = [
            'tekst' => $status->tekst,
            'forkortelse' => $status->forkortelse,
            'dato' => null,
        ];

        $this->showModal = true;
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT AUTOTEKST
    |--------------------------------------------------------------------------
    */

    public function editAutotekst(int $id)
    {
        $autotekst = Autotekster::findOrFail($id);

        $this->editingId = $autotekst->id;

        $this->type = 'autotekst';

        $this->form = [
            'tekst' => $autotekst->tekst,
            'forkortelse' => '',
            'dato' => $autotekst->dato,
        ];

        $this->showModal = true;
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */

    public function save()
    {
        $this->validate();

        if ($this->type === 'status') {

            if ($this->editingId) {

                Status::findOrFail($this->editingId)
                    ->update([
                        'tekst' => $this->form['tekst'],
                        'forkortelse' => $this->form['forkortelse'],
                    ]);

                session()->flash('success', 'Status opdateret.');
            } else {

                Status::create([
                    'tekst' => $this->form['tekst'],
                    'forkortelse' => $this->form['forkortelse'],
                ]);

                session()->flash('success', 'Status oprettet.');
            }
        }

        if ($this->type === 'autotekst') {

            if ($this->editingId) {

                Autotekster::findOrFail($this->editingId)
                    ->update([
                        'tekst' => $this->form['tekst'],
                        'dato' => $this->form['dato'],
                    ]);

                session()->flash('success', 'Autotekst opdateret.');
            } else {

                Autotekster::create([
                    'tekst' => $this->form['tekst'],
                    'dato' => $this->form['dato'],
                ]);

                session()->flash('success', 'Autotekst oprettet.');
            }
        }

        $this->showModal = false;

        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function confirmDelete(string $type, int $id)
    {
        $this->deleteType = $type;
        $this->deleteId = $id;

        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deleteType === 'status') {

            $status = Status::findOrFail($this->deleteId);

            /*
             * Prevent deletion if used on cases
             */
            if ($status->sagerStatus()->exists()) {

                session()->flash(
                    'error',
                    'Status bruges på sager og kan ikke slettes.'
                );

                $this->showDeleteModal = false;

                return;
            }

            $status->delete();
        }

        if ($this->deleteType === 'autotekst') {

            Autotekster::findOrFail($this->deleteId)->delete();
        }

        $this->showDeleteModal = false;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    protected function resetForm()
    {
        $this->editingId = null;

        $this->form = [
            'tekst' => '',
            'forkortelse' => '',
            'dato' => now()->format('Y-m-d'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $statuses = Status::query()
            ->when(
                $this->search,
                fn ($q) => $q->where(
                    'tekst',
                    'like',
                    '%' . $this->search . '%'
                )
                ->orWhere(
                    'forkortelse',
                    'like',
                    '%' . $this->search . '%'
                )
            )
            ->orderBy('tekst')
            ->get();

        $autotekster = Autotekster::query()
            ->when(
                $this->search,
                fn ($q) => $q->where(
                    'tekst',
                    'like',
                    '%' . $this->search . '%'
                )
            )
            ->latest('id')
            ->get();

        return view('livewire.tekster.show-tekster', [
            'statuses' => $statuses,
            'autotekster' => $autotekster,
        ]);
    }
}