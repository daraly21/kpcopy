<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:account-tie"></span>
            Kelola Guru
        </h2>
    </x-slot>

    <div class="py-6" x-data="teacherManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-700 flex items-center">
                    <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:account-group"></span>
                    Daftar Guru
                </h3>
                @can('kelola guru')
                <button @click="openAddModal()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-md shadow-sm transition-all duration-200 flex items-center border border-indigo-700">
                    <span class="iconify text-xl mr-2" data-icon="mdi:plus"></span>
                    Tambah Guru
                </button>
                @endcan
            </div>

            {{-- Filter Section (hanya Status Kerja) --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
                <div class="p-4">
                    <div class="flex items-center gap-4">
                        <label class="text-sm font-medium text-gray-700">Filter Status Kerja:</label>
                        <select name="status_kerja" id="statusFilter" onchange="filterByStatus(this.value)"
                                class="border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all" {{ request('status_kerja') == 'all' || !request('status_kerja') ? 'selected' : '' }}></option>
                            <option value="PPPK" {{ request('status_kerja') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            <option value="Honorer" {{ request('status_kerja') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                        </select>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <span class="iconify h-5 w-5 mr-3 text-green-600" data-icon="mdi:check-circle"></span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Guru</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP / NUPTK</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wali Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel Khusus</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($teachers as $i => $t)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ($teachers->currentPage()-1)*$teachers->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $t->nama_lengkap }}</div>
                                        @if($t->contact_email)
                                            <div class="text-xs text-gray-500">{{ $t->contact_email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @if($t->nip) <div>NIP: {{ $t->nip }}</div> @endif
                                        @if($t->nuptk) <div>NUPTK: {{ $t->nuptk }}</div> @endif
                                        @if(!$t->nip && !$t->nuptk) <span class="text-gray-400">-</span> @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $t->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($t->status_kerja === 'PPPK')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                PPPK
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Honorer
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $t->class?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $t->subject?->name ?? '-' }}
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                                        @can('kelola guru')
                                        <div class="flex justify-center space-x-2">
                                            <button @click="openEditModal({
                                                id: {{ $t->id }},
                                                user_id: '{{ $t->user_id }}',
                                                nip: '{{ addslashes($t->nip) }}',
                                                nuptk: '{{ addslashes($t->nuptk) }}',
                                                nama_lengkap: '{{ addslashes($t->nama_lengkap) }}',
                                                jenis_kelamin: '{{ $t->jenis_kelamin }}',
                                                tempat_lahir: '{{ addslashes($t->tempat_lahir) }}',
                                                tanggal_lahir: '{{ $t->tanggal_lahir }}',
                                                contact_email: '{{ addslashes($t->contact_email) }}',
                                                class_id: '{{ $t->class_id }}',
                                                subject_id: '{{ $t->subject_id }}'
                                            })"
                                                class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <span class="iconify text-xl" data-icon="mdi:pencil"></span>
                                            </button>

                                            <form action="{{ route('admin.teachers.destroy', $t->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Hapus guru ini?')"
                                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <span class="iconify text-xl" data-icon="mdi:trash-can"></span>
                                                </button>
                                            </form>
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <span class="iconify text-gray-400 text-4xl mb-2" data-icon="mdi:account-off"></span>
                                            <span>Tidak ada data guru ditemukan.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                {{ $teachers->withQueryString()->links() }}
            </div>
        </div>

        {{-- Modal Tambah/Edit dengan Step --}}
        @can('kelola guru')
        <div x-show="openModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4" x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col" @click.away="openModal = false">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold flex items-center">
                            <span class="iconify text-indigo-600 text-2xl mr-2" :data-icon="isEdit ? 'mdi:pencil' : 'mdi:plus'"></span>
                            <span x-text="isEdit ? 'Edit Guru' : 'Tambah Guru'"></span>
                        </h3>
                        <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">
                            <span class="iconify text-2xl" data-icon="mdi:close"></span>
                        </button>
                    </div>
                </div>

                {{-- Step Navigation --}}
                <div class="px-6 py-3 border-b border-gray-200 flex-shrink-0">
                    <div class="flex space-x-4">
                        <button @click="currentStep = 1" 
                                :class="currentStep === 1 ? 'text-indigo-600 border-indigo-600' : 'text-gray-500 border-transparent'"
                                class="pb-2 border-b-2 font-medium text-sm transition-colors">
                            <span class="iconify mr-1" data-icon="mdi:account"></span>
                            Data Pribadi
                        </button>
                        <button @click="currentStep = 2" 
                                :class="currentStep === 2 ? 'text-indigo-600 border-indigo-600' : 'text-gray-500 border-transparent'"
                                class="pb-2 border-b-2 font-medium text-sm transition-colors">
                            <span class="iconify mr-1" data-icon="mdi:briefcase"></span>
                            Data Kepegawaian
                        </button>
                        <button @click="currentStep = 3" 
                                :class="currentStep === 3 ? 'text-indigo-600 border-indigo-600' : 'text-gray-500 border-transparent'"
                                class="pb-2 border-b-2 font-medium text-sm transition-colors">
                            <span class="iconify mr-1" data-icon="mdi:school"></span>
                            Tugas Mengajar
                        </button>
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="flex-1 overflow-y-auto">
                    <form :action="formAction" method="POST" @submit="validateAndSubmit" class="p-6">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="isEdit ? 'PUT' : 'POST'">
                        <input type="hidden" name="id" x-model="form.id">

                        {{-- Step 1: Data Pribadi --}}
                        <div x-show="currentStep === 1" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_lengkap" x-model="form.nama_lengkap"
                                        class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <div x-show="errors.nama_lengkap" class="text-red-500 text-xs mt-1" x-text="errors.nama_lengkap"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                                    <select name="jenis_kelamin" x-model="form.jenis_kelamin"
                                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="">- Pilih Gender -</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    <div x-show="errors.jenis_kelamin" class="text-red-500 text-xs mt-1" x-text="errors.jenis_kelamin"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" x-model="form.tempat_lahir"
                                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" x-model="form.tanggal_lahir"
                                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                                    <input type="email" name="contact_email" x-model="form.contact_email"
                                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Data Kepegawaian --}}
                        <div x-show="currentStep === 2" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Link User (Opsional)</label>
                                    <select name="user_id" x-model="form.user_id"
                                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">- Pilih User -</option>
                                        @foreach($availableUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="text-xs text-gray-500 mt-1">Hubungkan dengan akun user yang sudah ada</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                                    <input type="text" name="nip" x-model="form.nip"
                                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan NIP">
                                    <div class="text-xs text-gray-500 mt-1">Otomatis PPPK jika diisi</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">NUPTK</label>
                                    <input type="text" name="nuptk" x-model="form.nuptk"
                                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan NUPTK">
                                    <div class="text-xs text-gray-500 mt-1">Otomatis PPPK jika diisi</div>
                                </div>
                            </div>

                            <div class="bg-amber-50 border border-amber-200 rounded-md p-4">
                                <div class="flex items-start">
                                    <span class="iconify text-amber-600 text-xl mr-2 flex-shrink-0 mt-0.5" data-icon="mdi:information"></span>
                                    <div class="text-amber-800 text-sm">
                                        <p class="font-medium mb-1">Status Kerja Otomatis:</p>
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li>Jika <strong>NIP</strong> atau <strong>NUPTK</strong> diisi → Status: <strong>PPPK</strong></li>
                                            <li>Jika keduanya kosong → Status: <strong>Honorer</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3: Tugas Mengajar --}}
                        <div x-show="currentStep === 3" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas (Opsional)</label>
                                    <select name="class_id" x-model="form.class_id"
                                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">- Tidak menjadi wali -</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <span class="iconify mr-1" data-icon="mdi:information"></span>
                                        Satu kelas hanya boleh 1 wali. Sistem akan menolak jika kelas sudah punya wali.
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mapel Khusus (Opsional)</label>
                                    <select name="subject_id" x-model="form.subject_id"
                                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">- Bukan guru mapel khusus -</option>
                                        @foreach($allowedSubjects as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <span class="iconify mr-1" data-icon="mdi:information"></span>
                                        Hanya untuk mata pelajaran Agama atau PJOK.
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex items-start">
                                    <span class="iconify text-blue-600 text-xl mr-2 flex-shrink-0 mt-0.5" data-icon="mdi:lightbulb"></span>
                                    <div class="text-blue-800 text-sm">
                                        <p class="font-medium mb-1">Informasi Tugas Mengajar:</p>
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li><strong>Wali Kelas:</strong> Guru yang bertanggung jawab terhadap satu kelas tertentu</li>
                                            <li><strong>Guru Mapel Khusus:</strong> Guru yang mengajar mata pelajaran khusus seperti Agama atau PJOK</li>
                                            <li>Seorang guru bisa menjadi wali kelas sekaligus guru mapel khusus</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Navigation Buttons --}}
                        <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200">
                            <button type="button" 
                                    x-show="currentStep > 1"
                                    @click="currentStep--"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-all duration-200 flex items-center">
                                <span class="iconify mr-2" data-icon="mdi:chevron-left"></span>
                                Sebelumnya
                            </button>
                            
                            <div class="flex gap-3">
                                <button type="button" @click="openModal = false"
                                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-all duration-200">
                                    Batal
                                </button>
                                
                                <button type="button" 
                                        x-show="currentStep < 3"
                                        @click="currentStep++"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-all duration-200 flex items-center">
                                    Selanjutnya
                                    <span class="iconify ml-2" data-icon="mdi:chevron-right"></span>
                                </button>
                                
                                <button type="submit" 
                                        x-show="currentStep === 3"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-all duration-200 flex items-center">
                                    <span class="iconify mr-2" data-icon="mdi:content-save"></span>
                                    Simpan Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <script>
    // Filter function untuk auto-filter status kerja
    function filterByStatus(status) {
        const url = new URL(window.location);
        if (status === 'all') {
            url.searchParams.delete('status_kerja');
        } else {
            url.searchParams.set('status_kerja', status);
        }
        window.location.href = url.toString();
    }

    function teacherManager() {
        return {
            openModal: false,
            isEdit: false,
            currentStep: 1,
            formAction: '{{ route('admin.teachers.store') }}',
            form: {
                id: '',
                user_id: '',
                nip: '',
                nuptk: '',
                nama_lengkap: '',
                jenis_kelamin: '',
                tempat_lahir: '',
                tanggal_lahir: '',
                contact_email: '',
                class_id: '',
                subject_id: '',
            },
            errors: {
                nama_lengkap: '',
                jenis_kelamin: '',
            },

            openAddModal() {
                this.isEdit = false;
                this.currentStep = 1;
                this.formAction = '{{ route('admin.teachers.store') }}';
                this.form = {
                    id: '', user_id: '', nip: '', nuptk: '', nama_lengkap: '',
                    jenis_kelamin: '', tempat_lahir: '', tanggal_lahir: '',
                    contact_email: '', class_id: '', subject_id: ''
                };
                this.errors = { nama_lengkap: '', jenis_kelamin: '' };
                this.openModal = true;
            },

            openEditModal(row) {
                this.isEdit = true;
                this.currentStep = 1;
                this.formAction = '{{ url('admin/teachers') }}' + '/' + row.id;
                this.form = {
                    id: row.id ?? '',
                    user_id: row.user_id ?? '',
                    nip: row.nip ?? '',
                    nuptk: row.nuptk ?? '',
                    nama_lengkap: row.nama_lengkap ?? '',
                    jenis_kelamin: row.jenis_kelamin ?? '',
                    tempat_lahir: row.tempat_lahir ?? '',
                    tanggal_lahir: row.tanggal_lahir ?? '',
                    contact_email: row.contact_email ?? '',
                    class_id: row.class_id ?? '',
                    subject_id: row.subject_id ?? '',
                };
                this.errors = { nama_lengkap: '', jenis_kelamin: '' };
                this.openModal = true;
            },

            validateAndSubmit(e) {
                let ok = true;
                this.errors = { nama_lengkap: '', jenis_kelamin: '' };

                if (!this.form.nama_lengkap || this.form.nama_lengkap.trim() === '') {
                    this.errors.nama_lengkap = 'Nama guru wajib diisi';
                    ok = false;
                }
                if (!this.form.jenis_kelamin) {
                    this.errors.jenis_kelamin = 'Jenis kelamin wajib dipilih';
                    ok = false;
                }

                if (!ok) {
                    e.preventDefault();
                    // Pindah ke step yang ada error
                    if (this.errors.nama_lengkap || this.errors.jenis_kelamin) {
                        this.currentStep = 1;
                    }
                }
            }
        }
    }
    </script>
</x-app-layout>