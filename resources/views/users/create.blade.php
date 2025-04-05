<x-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah User') }}
        </h2>
    </x-slot>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium">Nama</label>
            <input type="text" name="name" required class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Email</label>
            <input type="email" name="email" required class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Password</label>
            <input type="password" name="password" required class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Role</label>
            <select name="role" required class="w-full border rounded p-2">
                <option value="" disabled selected>Pilih Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <div id="class-section">
            <label class="block font-medium">Kelas (khusus Wali Kelas)</label>
            <select name="class_id" class="w-full border rounded p-2">
                <option value="">Pilih Kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
    </form>
</x-layout>
