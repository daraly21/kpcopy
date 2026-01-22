<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Section: Update Profile Information -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Section: Update Profile Picture -->
            <div class="p-6 sm:p-8 bg-white shadow-lg rounded-xl">
                <div class="max-w-4xl mx-auto flex flex-col sm:flex-row gap-8 items-center">
                    <!-- Form untuk Upload Gambar Profil -->
                    <div class="flex-1">
                        <h3 class="text-2xl font-semibold text-gray-800">Perbarui Foto Profil</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Perbarui foto profil Anda agar lebih personal.</p>

                        <form action="{{ route('profile.picture.update') }}" method="POST"
                            enctype="multipart/form-data" class="mt-6">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Input File -->
                                <div>
                                    <label for="profile_picture"
                                        class="block text-sm font-medium text-gray-700 mb-2">Pilih foto profil
                                        baru</label>
                                    <div class="relative">
                                        <input type="file" name="profile_picture" id="profile_picture"
                                            accept="image/*" class="hidden" onchange="updateFileName()">
                                        <div class="flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg w-full max-w-sm cursor-pointer hover:bg-gray-200 transition"
                                            onclick="document.getElementById('profile_picture').click()">
                                            <span id="file-name" class="text-sm text-gray-500 truncate flex-1">Tidak ada
                                                file dipilih</span>
                                            <span
                                                class="px-4 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition">Pilih
                                                File</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Submit Hitam -->
                                <div>
                                    <button type="submit"
                                        class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150'>
                                        Unggah & Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tampilkan Gambar Profil Saat Ini -->
                    @if (auth()->user()->profile_picture)
                        <div class="mr-10">
                            <h4 class="text-sm font-medium text-gray-700 mb-3 text-center sm:text-left">
                                Foto Profil Saat Ini</h4>
                            <div
                                class="w-40 h-40 rounded-full overflow-hidden shadow-md transform hover:scale-105 transition">
                                <img src="{{ route('profile.picture.show') }}" alt="Profile Picture"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Script untuk Update Nama File -->
            <script>
                function updateFileName() {
                    const input = document.getElementById('profile_picture');
                    const fileName = document.getElementById('file-name');
                    fileName.textContent = input.files.length > 0 ? input.files[0].name : "Tidak ada file dipilih";
                }
            </script>

            <!-- Script untuk Update Nama File -->
            <script>
                function updateFileName() {
                    const input = document.getElementById('profile_picture');
                    const fileName = document.getElementById('file-name');
                    fileName.textContent = input.files.length > 0 ? input.files[0].name : "Tidak ada file dipilih";
                }
            </script>


            <!-- Section: Update Password -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
