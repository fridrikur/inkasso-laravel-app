<div>

    <h1 class="font-bold text-2xl mb-4">Medarbejdere</h1>

    <!-- 🔥 Create medarbejder (preselect role) -->
    <button
        onclick="window.location.href='/users?role=Medarbejder&create=1'"
        class="flex items-center justify-center gap-2 w-full px-4 py-2 rounded-md 
            bg-indigo-600 text-white text-sm font-semibold 
            hover:bg-indigo-700 transition">

        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4v16m8-8H4" />
        </svg>

        Opret ny bruger
    </button>
    

    <ul class="mt-4 space-y-2">
        @foreach ($users as $user)
            <li class="flex justify-between items-center border-b py-2">
                <div>
                    <strong>{{ $user->name }}</strong>
                    <span class="text-gray-600 text-sm">{{ $user->email }}</span>
                </div>

                <a href="{{ url('/users?role=Medarbejder&edit=' . $user->id) }}"
                class="text-blue-600 hover:underline">
                    Rediger
                </a>
            </li>
        @endforeach
    </ul>
</div>