<div class="max-w-7xl mx-auto p-6 space-y-6">

    <h1 class="text-3xl font-bold mb-6">Roller</h1>

    @if(session()->has('message'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- ========================= --}}
    {{-- TABS --}}
    {{-- ========================= --}}
    <div class="flex space-x-3 mb-6 border-b pb-2">

        <button wire:click="$set('tab', 'roles')"
                class="px-4 py-2 rounded-t-lg
                {{ $tab === 'roles' ? 'bg-white shadow font-semibold' : 'text-gray-500' }}">
            Roles
        </button>

        <button wire:click="$set('tab', 'users')"
                class="px-4 py-2 rounded-t-lg
                {{ $tab === 'users' ? 'bg-white shadow font-semibold' : 'text-gray-500' }}">
            Users
        </button>

        <button wire:click="$set('tab', 'security')"
                class="px-4 py-2 rounded-t-lg
                {{ $tab === 'security' ? 'bg-white shadow font-semibold' : 'text-gray-500' }}">
            Security Policies
        </button>

    </div>

    {{-- ========================= --}}
    {{-- ROLES TAB --}}
    {{-- ========================= --}}
    @if($tab === 'roles')

        <div class="flex space-x-3 mb-6">
            @foreach($roles as $role)
                <button wire:click="selectRole({{ $role->id }})"
                        class="px-4 py-2 rounded
                        @if($selectedRoleId === $role->id)
                            bg-indigo-600 text-white
                        @else
                            bg-gray-200 text-gray-700
                        @endif
                        hover:bg-indigo-500 hover:text-white transition">
                    {{ $role->name }} ({{ $role->users->count() }})
                </button>
            @endforeach
        </div>

        @if($selectedRoleId)

            @php $selectedRole = $roles->firstWhere('id', $selectedRoleId); @endphp

            <div class="bg-white shadow rounded-lg p-4">

                <div class="flex justify-between mb-4">
                    <h2 class="text-xl font-semibold">
                        {{ $selectedRole->name }} Users
                    </h2>

                    <div class="flex space-x-2">

                        <button wire:click="editRole({{ $selectedRoleId }})"
                                class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Edit Role
                        </button>

                        <button wire:click="addUserToRole({{ $selectedRoleId }})"
                                class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                            Add User
                        </button>

                    </div>
                </div>

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                Name
                            </th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                Email
                            </th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">

                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-2 text-sm">{{ $user['name'] }}</td>
                                <td class="px-6 py-2 text-sm text-gray-500">{{ $user['email'] }}</td>
                                <td class="px-6 py-2 text-right text-sm">
                                    <button wire:click="removeUserFromRole({{ $selectedRoleId }}, {{ $user['id'] }})"
                                            class="text-red-600 font-semibold">
                                        Fjern
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        @else
            <div class="text-gray-500">
                Select a role to see its users.
            </div>
        @endif

    @endif

    {{-- ========================= --}}
    {{-- USERS TAB (simple overview placeholder) --}}
    {{-- ========================= --}}
    @if($tab === 'users')

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-2">User Overview</h2>

            <p class="text-gray-500">
                Select a role in the Roles tab to manage users.
            </p>
        </div>

    @endif

    {{-- ========================= --}}
    {{-- SECURITY POLICIES TAB --}}
    {{-- ========================= --}}
    @if($tab === 'security')

        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-lg font-semibold mb-4">
                Security Policies (2FA per Role)
            </h2>

            <div class="space-y-4">

                @foreach($roles as $role)

                    <div class="flex items-center justify-between border-b pb-3">

                        <div>
                            <div class="font-semibold">
                                {{ $role->name }}
                            </div>

                            <div class="text-sm text-gray-500">
                                Require 2FA for this role
                            </div>
                        </div>

                        <button
                            wire:click="toggleTwoFactor({{ $role->id }})"
                            class="px-4 py-2 rounded text-sm
                            {{ $role->requires_two_factor
                                ? 'bg-green-600 text-white'
                                : 'bg-gray-200' }}"
                        >
                            {{ $role->requires_two_factor ? 'Enabled' : 'Disabled' }}
                        </button>

                    </div>

                @endforeach

            </div>
        </div>

    @endif

    {{-- ========================= --}}
    {{-- MODALS --}}
    {{-- ========================= --}}

    @if($showEditRoleModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-white rounded-lg w-96 p-6">

                <h2 class="text-xl font-bold mb-4">Edit Role</h2>

                <form wire:submit.prevent="updateRole">

                    <input type="text"
                           wire:model.defer="roleName"
                           class="w-full border rounded p-2 mb-4">

                    <div class="flex justify-end space-x-3">

                        <button type="button"
                                wire:click="$set('showEditRoleModal', false)"
                                class="px-4 py-2 bg-gray-300 rounded">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded">
                            Save
                        </button>

                    </div>

                </form>

            </div>
        </div>
    @endif

    @if($showAddUserModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-white rounded-lg w-96 p-6">

                <h2 class="text-xl font-bold mb-4">Add User to Role</h2>

                <form wire:submit.prevent="saveUserToRole">

                    <select wire:model="selectedUserId"
                            class="w-full border rounded p-2 mb-4">

                        <option value="">Select User</option>

                        @foreach($usersList as $userOption)
                            <option value="{{ $userOption->id }}">
                                {{ $userOption->name }} ({{ $userOption->email }})
                            </option>
                        @endforeach

                    </select>

                    <div class="flex justify-end space-x-3">

                        <button type="button"
                                wire:click="$set('showAddUserModal', false)"
                                class="px-4 py-2 bg-gray-300 rounded">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded">
                            Add User
                        </button>

                    </div>

                </form>

            </div>
        </div>
    @endif

</div>