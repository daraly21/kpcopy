<x-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-medium">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Role</label>
            <select name="role" id="role" required class="w-full border rounded p-2">
                <option value="" disabled>Pilih Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="class-section" style="{{ $user->role_id == 2 ? '' : 'display: none;' }}">
            <label class="block font-medium">Kelas (khusus Wali Kelas)</label>
            <select name="class_id" class="w-full border rounded p-2">
                <option value="">Pilih Kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" {{ $user->class_id == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan Perubahan</button>
    </form>

    <script>
        document.getElementById('role').addEventListener('change', function () {
            const classSection = document.getElementById('class-section');
            if (this.value == 2) {
                classSection.style.display = 'block';
            } else {
                classSection.style.display = 'none';
            }
        });
    </script>
</x-layout>
