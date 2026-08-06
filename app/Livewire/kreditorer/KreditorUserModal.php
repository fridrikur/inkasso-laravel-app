<?php

namespace App\Livewire\Kreditorer;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class kreditorerModal extends Component
{
    public bool $show = false;

    public ?User $user = null;
    public ?int $kreditorId = null;

    // 🔹 Form fields
    public string $name = '';
    public string $email = '';
    public ?string $password = null;

    #[On('open-user-modal')]
    public function open(array $payload): void
    {
        $this->kreditorId = $payload['kreditorId'] ?? null;

        if (!empty($payload['userId'])) {
            $this->user = User::find($payload['userId']);

            if ($this->user) {
                $this->name = $this->user->name;
                $this->email = $this->user->email;
                $this->password = null;
            }
        } else {
            $this->resetFields();
        }

        $this->show = true;
    }

    public function close(): void
    {
        $this->resetFields();
        $this->show = false;
    }

    private function resetFields(): void
    {
        $this->user = null;
        $this->name = '';
        $this->email = '';
        $this->password = null;
        $this->resetValidation();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                $this->user 
                    ? Rule::unique('users', 'email')->ignore($this->user->id)
                    : Rule::unique('users', 'email'),
            ],
            'password' => [$this->user ? 'nullable' : 'required', 'min:8'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        } else {
            unset($data['password']);
        }

        $user = $this->user
            ? tap($this->user)->update($data)
            : User::create($data);

        // Attach to kreditor
        $user->kreditorer()->syncWithoutDetaching([$this->kreditorId]);

        // Ensure role = Kreditor
        $user->syncRoles(['Kreditor']);

        $this->close();

        // Refresh ShowKreditor
        $this->dispatch('kreditorer.show-kreditor', '$refresh');
    }

    public function render()
    {
        return view('liveWire.kreditorer.kreditor-user-modal');
    }
}
