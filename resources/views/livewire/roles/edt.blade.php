<div class="max-w-md mx-auto p-4">
    <h1>Edit Role</h1>

    <form method="PUT" action="{{ route('roles.update', $role) }}">
        @csrf
        @method('PUT')

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ $role->name }}">

        <button type="submit">Update</button>
    </form>
</div>