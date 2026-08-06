<div class="p-6 max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Tildel Medarbejder Rolle</h1>

    @if(session()->has('message'))
        <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if($users->isEmpty())
        <div class="p-4 bg-gray-100 rounded">Ingen brugere uden rolle fundet.</div>
    @else
        <button wire:click="assignMedarbejderToAll"
                class="mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
            Tildel Medarbejder til alle
        </button>

        <table class="min-w-full border border-gray-200 rounded">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Navn</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Handling</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <button wire:click="assignMedarbejder({{ $user->id }})"
                                    class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
                                Giv Medarbejder
                            </button>
                            <button wire:click="deleteUser({{ $user->id }})"
                                    class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 font-medium">
                                Slet
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
