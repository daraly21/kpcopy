<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Siswa Kelas {{ $class->name }}</h2>
    </x-slot>

    <div class="py-6" x-data="walikelas()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-700">Daftar Siswa</h3>
                <div class="text-sm text-gray-600 bg-gray-100 p-2 rounded-md">
                    <span class="font-medium">Petunjuk:</span> Sebagai Wali Kelas, Anda dapat mengedit nomor telepon orang tua siswa.
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex">
                        <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orang Tua</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP Orang Tua</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($students as $i => $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $i + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->nis }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($student->gender == 'L')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Laki-laki</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">Perempuan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->parent_name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->parent_phone ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="openEditModal({{ json_encode($student) }})" class="text-indigo-600 hover:text-indigo-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            Edit No HP
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal Edit Nomor Telepon --}}
        <div x-show="openModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50" x-cloak>
            <div class="bg-white p-6 rounded-lg w-full max-w-md relative" @click.away="openModal = false">
                <h3 class="text-lg font-bold mb-4">Edit Nomor HP Orang Tua</h3>
                <p class="mb-4 text-gray-600">Siswa: <span x-text="selectedStudent.name" class="font-medium"></span></p>

                <form :action="'/siswa/' + selectedStudent.id + '/update-phone'" method="POST" @submit="validateAndSubmit">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP Orang Tua <span class="text-red-500">*</span></label>
                        <input type="text" name="parent_phone" class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" x-model="selectedStudent.parent_phone" placeholder="083113355381" @input="formatPhoneNumber" required>
                        <div x-show="phoneError" class="text-red-500 text-xs mt-1" x-text="phoneError"></div>
                        <span class="text-xs text-gray-500 mt-1 block">Format: Tulis nomor tanpa awalan +62 (contoh: 083113355381)</span>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="openModal = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md transition">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function walikelas() {
            return {
                openModal: false,
                selectedStudent: {
                    id: '',
                    name: '',
                    parent_phone: ''
                },
                phoneError: '',
                rawPhoneInput: '', // Untuk menyimpan input asli user
                
                openEditModal(student) {
                    this.selectedStudent = { ...student };
                    // Convert 62 to 0 for display
                    this.rawPhoneInput = this.selectedStudent.parent_phone?.startsWith('62') 
                        ? '0' + this.selectedStudent.parent_phone.slice(2) 
                        : this.selectedStudent.parent_phone || '';
                    this.phoneError = '';
                    this.openModal = true;
                },
                
                handlePhoneInput() {
                    // Simpan input asli user
                    this.rawPhoneInput = this.$refs.phoneInput.value;
                    
                    // Konversi ke format 62 untuk disimpan
                    let phoneNumber = this.rawPhoneInput.replace(/\D/g, '');
                    if (phoneNumber.startsWith('0')) {
                        this.selectedStudent.parent_phone = '62' + phoneNumber.substring(1);
                    } else if (!phoneNumber.startsWith('62') && phoneNumber) {
                        this.selectedStudent.parent_phone = '62' + phoneNumber;
                    } else {
                        this.selectedStudent.parent_phone = phoneNumber;
                    }
                },
                
                validateAndSubmit(e) {
                    this.handlePhoneInput(); // Pastikan konversi terakhir
                    
                    if (!this.selectedStudent.parent_phone) {
                        this.phoneError = 'Nomor HP tidak boleh kosong';
                        e.preventDefault();
                    } else if (!this.selectedStudent.parent_phone.startsWith('62')) {
                        this.phoneError = 'Format nomor harus diawali 0 atau 62';
                        e.preventDefault();
                    } else if (this.selectedStudent.parent_phone.length < 11) {
                        this.phoneError = 'Nomor terlalu pendek';
                        e.preventDefault();
                    }
                }
            }
        }
    </script>
</x-app-layout>