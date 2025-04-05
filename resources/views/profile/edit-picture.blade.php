<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Profile Picture') }}
        </h2>
    </x-slot>

    <div class="container">
        <h1>Update Profile Picture</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="profile_picture">Upload Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Update Profile Picture</button>
        </form>

        @if(auth()->user()->profile_picture)
            <div class="mt-4">
                <h3>Current Profile Picture:</h3>
                <img src="{{ auth()->user()->profile_picture }}" alt="Profile Picture" style="width: 150px; height: 150px;">
            </div>
        @endif
    </div>
</x-app-layout>