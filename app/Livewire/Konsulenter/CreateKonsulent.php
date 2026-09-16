<?php

namespace App\Livewire\Konsulenter;

use App\Models\Konsulenter;
use App\Services\KonsulentService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateKonsulent extends Component
{
    public string $navn = '';
    public string $email = '';
    public string $tlf = '';
    public string $titel = 'Konsulent';
    public bool $aktiv = true;

    // 🟢 Konsulent roller
    public bool $is_hoved = false;
    public bool $is_skjult = false;
    public bool $is_notifikation = false;

    protected function rules(): array
    {
        return [
            // Tilføjet unique-regel på navn her, så den fanges i valideringen før databasen
            'navn'  => ['required', 'string', 'max:255', Rule::unique('konsulenters', 'navn')],
            'email' => ['required', 'email', 'max:255', Rule::unique('konsulenters', 'email')],
            'tlf'   => ['nullable', 'string', 'max:30', Rule::unique('konsulenters', 'tlf')],
            'titel' => 'nullable|string|max:100',
            'aktiv' => 'boolean',
            'is_hoved' => 'boolean',
            'is_skjult' => 'boolean',
            'is_notifikation' => 'boolean',
        ];
    }

    public function save(KonsulentService $service)
    {
        $this->validate();

        try {
            DB::transaction(function () use ($service) {
                // 1. Opret konsulenten
                $konsulent = Konsulenter::create([
                    'navn'  => $this->navn,
                    'email' => $this->email,
                    'tlf'   => !empty(trim($this->tlf)) ? trim($this->tlf) : null,
                    'titel' => !empty(trim($this->titel)) ? trim($this->titel) : null,
                    'aktiv' => $this->aktiv,
                ]);

                // 2. Synkroniser konsulentens roller via KonsulentService
                $service->syncRoles($konsulent, [
                    'hoved'        => $this->is_hoved,
                    'skjult'       => $this->is_skjult,
                    'notifikation' => $this->is_notifikation,
                ]);
            });

            session()->flash('toast', [
                'message' => 'Konsulent blev oprettet succesfuldt med valgte roller!',
                'type'    => 'success',
            ]);

            return redirect()->route('konsulenter.index');

        } catch (QueryException $e) {
            // Fanger MySQL duplicate entry (fejlkode 1062) hvis den mod forventning smutter igennem valideringen
            if ($e->errorInfo[1] === 1062) {
                $errorMessage = $e->getMessage();

                if (str_contains($errorMessage, 'konsulenters_navn_unique')) {
                    $this->addError('navn', 'Der findes allerede en konsulent med dette navn.');
                } elseif (str_contains($errorMessage, 'konsulenters_email_unique')) {
                    $this->addError('email', 'Denne e-mailadresse er allerede i brug.');
                } elseif (str_contains($errorMessage, 'konsulenters_tlf_unique')) {
                    $this->addError('tlf', 'Dette telefonnummer er allerede i brug.');
                } else {
                    $this->addError('navn', 'Der findes allerede en post med disse oplysninger.');
                }

                $this->dispatch('toast', [
                    'message' => 'Kunne ikke oprette konsulent. Kontroller felterne for fejl.',
                    'type'    => 'error',
                ]);
                
                return;
            }

            // Kaster andre uventede databasefejl videre
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.konsulenter.create-konsulent');
    }
}