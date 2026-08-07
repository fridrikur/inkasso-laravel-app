<div class="min-h-screen bg-slate-100">

    <div class="max-w-5xl mx-auto py-10 px-6">

        {{-- HERO --}}
        <div class="mb-8">

            <div class="flex items-center gap-4">

                <div
                    class="h-14 w-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow"
                >
                    +
                </div>

                <div>

                    <h1 class="text-3xl font-bold text-slate-900">
                        Opret bruger
                    </h1>

                    <p class="text-slate-500">
                        Registrér en ny bruger i systemet
                    </p>

                </div>

            </div>

        </div>

        {{-- ROLE TABS --}}
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-3 mb-6">

            <div class="flex gap-2">

                <button
                    type="button"
                    wire:click="$set('role', 'Medarbejder')"
                    class="px-5 py-3 rounded-2xl font-medium transition
                    {{ $role === 'Medarbejder'
                        ? 'bg-blue-600 text-white shadow'
                        : 'hover:bg-slate-100'
                    }}"
                >
                    Medarbejder
                </button>

                <button
                    type="button"
                    wire:click="$set('role', 'Kreditor')"
                    class="px-5 py-3 rounded-2xl font-medium transition
                    {{ $role === 'Kreditor'
                        ? 'bg-blue-600 text-white shadow'
                        : 'hover:bg-slate-100'
                    }}"
                >
                    Kreditor
                </button>

                <button
                    type="button"
                    wire:click="$set('role', 'Admin')"
                    class="px-5 py-3 rounded-2xl font-medium transition
                    {{ $role === 'Admin'
                        ? 'bg-blue-600 text-white shadow'
                        : 'hover:bg-slate-100'
                    }}"
                >
                    Admin
                </button>

            </div>

        </div>

        {{-- MAIN CARD --}}
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">

            {{-- HEADER --}}
            <div class="px-8 py-6 border-b bg-slate-50">

                <h2 class="font-semibold text-lg">
                    Stamdata
                </h2>

            </div>

            {{-- FORM --}}
            <form wire:submit="save">

                <div class="p-8 space-y-8">

                    {{-- BASIC INFO --}}
                    <div class="grid md:grid-cols-2 gap-6">

                        <div>

                            <label class="block text-sm font-medium mb-2">
                                Navn
                            </label>

                            <input
                                type="text"
                                wire:model.blur="name"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3"
                                placeholder="Indtast navn"
                            >

                            @error('name')
                                <p class="text-red-600 text-sm mt-2">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        <div>

                            <label class="block text-sm font-medium mb-2">
                                E-mail
                            </label>

                            <input
                                type="email"
                                wire:model.blur="email"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3"
                                placeholder="mail@firma.dk"
                            >

                            @error('email')
                                <p class="text-red-600 text-sm mt-2">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                    {{-- PASSWORDS --}}
                    <div class="grid md:grid-cols-2 gap-6">

                        <div>

                            <label class="block text-sm font-medium mb-2">
                                Password
                            </label>

                            <input
                                type="password"
                                wire:model.defer="password"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3"
                            >

                            @error('password')
                                <p class="text-red-600 text-sm mt-2">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        <div>

                            <label class="block text-sm font-medium mb-2">
                                Bekræft password
                            </label>

                            <input
                                type="password"
                                wire:model.defer="password_confirmation"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3"
                            >

                        </div>

                    </div>

                    {{-- ROLE INFO --}}
                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-6">

                        @if($role === 'Admin')

                            <div class="font-semibold text-slate-900">
                                Administrator
                            </div>

                            <p class="text-slate-600 mt-1">
                                Administratoren har adgang til alle funktioner i systemet.
                            </p>

                        @elseif($role === 'Medarbejder')

                            <div class="font-semibold text-slate-900">
                                Medarbejder
                            </div>

                            <p class="text-slate-600 mt-1">
                                Medarbejderen får adgang til sagsbehandling og dagligt arbejde.
                            </p>

                        @elseif($role === 'Kreditor')

                            <div class="font-semibold text-slate-900">
                                Kreditor
                            </div>

                            <p class="text-slate-600 mt-1">
                                Kreditorbrugere skal tilknyttes præcis én virksomhed.
                            </p>

                        @endif

                    </div>

                    {{-- KREDITOR SELECTOR --}}
                    @if($role === 'Kreditor')

                        <div>
                            <div>

                                <label class="block text-sm font-medium mb-2">
                                    Kreditor
                                </label>

                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="kreditorSearch"
                                    class="w-full rounded-xl border px-4 py-3"
                                    placeholder="Søg kreditor..."
                                >

                            </div>

                            <div class="mb-4">

                                <h3 class="font-semibold text-lg">
                                    Vælg kreditor
                                </h3>

                                <p class="text-slate-500 text-sm">
                                    Brugeren tilknyttes én virksomhed.
                                </p>

                            </div>

                            <div class="bg-slate-50 rounded-2xl p-2 flex gap-2 overflow-x-auto">

                                @foreach($kreditorer as $kreditor)

                                    <button
                                        type="button"
                                        wire:click="$set('kreditor_id', {{ $kreditor->id }})"
                                        class="
                                            whitespace-nowrap
                                            px-4 py-2 rounded-xl transition

                                            {{ $kreditor_id == $kreditor->id
                                                ? 'bg-blue-600 text-white'
                                                : 'bg-white hover:bg-slate-100'
                                            }}
                                        "
                                    >
                                        {{ $kreditor->navn }}
                                    </button>

                                @endforeach

                            </div>

                            @error('kreditor_id')
                                <p class="text-red-600 text-sm mt-3">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    @endif

                    {{-- ACTIONS --}}
                    <div class="flex justify-end gap-3 pt-4">

                        <a
                            href="{{ route('users.manage-users') }}"
                            class="px-5 py-3 rounded-xl border border-slate-300"
                        >
                            Annullér
                        </a>

                        <button
                            type="submit"
                            class="px-6 py-3 rounded-xl bg-blue-600 text-white font-medium shadow hover:bg-blue-700"
                        >
                            Opret bruger
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>