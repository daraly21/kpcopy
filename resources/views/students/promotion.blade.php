<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Naik Kelas - {{ $class->name }}</h2>
    </x-slot>

    <div class="py-6 container mx-auto px-4">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('admin.siswa.list', ['class' => $class->id, 'academic_year_id' => $currentYear->id]) }}"
                class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2">
                <span class="iconify" data-icon="mdi:arrow-left"></span> Kembali ke Daftar Siswa
            </a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <span class="iconify text-green-600 mr-2 text-2xl" data-icon="mdi:arrow-up-bold-box-outline"></span>
                Form Naik Kelas
            </h3>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-sm text-blue-700">
                    Anda sedang memproses kenaikan kelas untuk siswa kelas <strong>{{ $class->name }}</strong>
                    pada tahun ajaran <strong>{{ $currentYear->name }}</strong>.
                </p>
            </div>

            <form action="{{ route('admin.siswa.process-promotion') }}" method="POST">
                @csrf
                <input type="hidden" name="from_year_id" value="{{ $currentYear->id }}">
                <input type="hidden" name="from_class_id" value="{{ $class->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Current Info (Read only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Asal</label>
                        <input type="text" value="{{ $currentYear->name }}" disabled
                            class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 text-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kelas Asal</label>
                        <input type="text" value="{{ $class->name }}" disabled
                            class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 text-gray-500">
                    </div>

                    <!-- Target Info -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Tujuan <span
                                class="text-red-500">*</span></label>
                        <select name="to_year_id" required
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach ($academicYears as $year)
                                @if ($year->id != $currentYear->id)
                                    <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kelas Tujuan <span
                                class="text-red-500">*</span></label>
                        <select name="target_class_id" required
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Kelas Tujuan --</option>
                            @foreach ($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                            <option value="graduated" class="font-bold text-green-600">Lulus / Selesai</option>
                        </select>
                    </div>
                </div>

                <!-- Student List -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Siswa yang Akan Dinaikkan:</label>
                    <div class="border rounded-md overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                        <input type="checkbox" id="selectAll"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            checked>
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NIS</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Siswa</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($students as $student)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                class="student-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                checked>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $student->nis }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $student->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3"
                                            class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">Tidak
                                            ada siswa di kelas ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transform transition hover:scale-105 duration-200 flex items-center">
                        <span class="iconify mr-2" data-icon="mdi:check-circle"></span>
                        Proses Naik Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.student-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        </script>
    @endpush
</x-app-layout>
