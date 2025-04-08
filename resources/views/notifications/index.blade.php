<x-app-layout title="Kirim Notifikasi">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:bell"></span>
                {{ __('Notifikasi') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
      
            <!-- Filter Section -->
            <div class="p-5 border-b border-gray-200">
                <form method="GET" action="{{ route('notifications.index') }}"
                      class="grid grid-cols-1 md:grid-cols-3 gap-4 relative">
                    <!-- Subject Filter -->
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                        <select name="subject_id"
                                class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-10 pl-3 pr-8 transition-colors duration-150">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}"
                                        {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Active Filter Display (diletakkan di bawah Subject Filter) -->
                        <div class="mt-5 w-full bg-gray-50 p-3 rounded-md border border-gray-200 shadow-sm">
                            <p class="text-sm text-gray-600 max-w-full break-words">
                                <span class="font-medium">Filter Aktif:</span>
                                <span>
                                    @if (request('subject_id'))
                                        {{ $subjects->firstWhere('id', request('subject_id'))->name ?? 'Semua Mata Pelajaran' }}
                                        @if (request('task_name'))
                                            - {{ request('task_name') }}
                                        @endif
                                    @else
                                        Semua Data
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Task Filter -->
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Tugas</label>
                        <select name="task_name"
                                class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-10 pl-3 pr-8 transition-colors duration-150">
                            <option value="">Semua Tugas</option>
                            @foreach ($taskNames as $task)
                                <option value="{{ $task }}"
                                        {{ request('task_name') == $task ? 'selected' : '' }}>
                                    {{ $task }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Button (sejajar dengan Subject dan Task Filter, di ujung kanan) -->
                    <div class="md:col-span-1 flex items-start justify-end mt-5">
                        <button type="submit"
                                class="h-10 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center border border-indigo-700 transition-all duration-200 hover:shadow-md">
                            <span class="iconify mr-2 text-xl" data-icon="mdi:filter"></span>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
            <!-- Results Section -->
            <div class="p-6">
                <!-- Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12 border border-gray-200">No</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-56 border border-gray-200">Nama Siswa</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40 border border-gray-200">Nomor HP Orang Tua</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20 border border-gray-200">Nilai</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32 border border-gray-200">Status</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-56 border border-gray-200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($students as $student)
                                @php
                                    $filteredTask = $student->gradeTasks
                                        ->where('subject_id', request('subject_id'))
                                        ->where('task_name', request('task_name'))
                                        ->first();

                                    $notification = $student->notifications
                                        ->where('subject_id', request('subject_id'))
                                        ->where('task_name', request('task_name'))
                                        ->first();
                                @endphp

                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border border-gray-200">
                                        {{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}
                                        <input type="hidden" name="students[]" value="{{ $student->id }}">
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 max-w-56 break-words border border-gray-200">{{ $student->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 max-w-40 break-words border border-gray-200">
                                        {{ $student->parent_phone ? '0'.substr($student->parent_phone, 2) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border border-gray-200">
                                        @if ($filteredTask)
                                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                                {{ $filteredTask->score >= 90
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($filteredTask->score >= 75
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : ($filteredTask->score >= 60
                                                            ? 'bg-yellow-100 text-yellow-800'
                                                            : 'bg-red-100 text-red-800')) }}">
                                                {{ $filteredTask->score }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 max-w-32 break-words border border-gray-200">
                                        @if ($notification && $notification->sent_at)
                                            <span class="inline-flex items-center text-green-600">
                                                <span class="iconify mr-1 text-xl" data-icon="mdi:check-circle-outline"></span>
                                                Terkirim
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-gray-500">
                                                <span class="iconify mr-1 text-xl" data-icon="mdi:clock-outline"></span>
                                                Belum dikirim
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium border border-gray-200">
                                        <div class="flex space-x-2">
                                            @if ($notification && $notification->sent_at)
                                                <button onclick="sendNotification([{{ $student->id }}])"
                                                        class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 px-3 py-1 rounded-md text-sm flex items-center">
                                                    <span class="iconify mr-1" data-icon="mdi:send-outline"></span>
                                                    Kirim Ulang
                                                </button>
                                                <button onclick="resetNotificationStatus([{{ $student->id }}])"
                                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-sm flex items-center">
                                                    <span class="iconify mr-1" data-icon="mdi:refresh"></span>
                                                    Reset
                                                </button>
                                            @else
                                                <button onclick="sendNotification([{{ $student->id }}])"
                                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-sm flex items-center">
                                                    <span class="iconify mr-1" data-icon="mdi:send-outline"></span>
                                                    Kirim
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500 border border-gray-200">
                                        Tidak ada data siswa yang tersedia
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($students->hasPages())
                    <div class="mt-4">
                        {{ $students->appends(request()->except('page'))->links() }}
                    </div>
                @endif

                <!-- Bulk Actions -->
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="sendNotificationToAll()"
                            class="inline-flex items-center px-4 py-2 border border-indigo-700 text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <span class="iconify mr-2 text-xl" data-icon="mdi:send-outline"></span>
                        Kirim Semua Notifikasi
                    </button>

                    <button type="button" onclick="resetNotificationStatus()"
                            class="inline-flex items-center px-4 py-2 border border-red-700 text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                        <span class="iconify mr-2 text-xl" data-icon="mdi:refresh"></span>
                        Reset Status Pengiriman
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sendNotificationToAll() {
            let allStudentIds = Array.from(document.querySelectorAll('input[name="students[]"]')).map(cb => cb.value);

            if (allStudentIds.length === 0) {
                alert('Tidak ada siswa yang dapat dikirim notifikasi!');
                return;
            }

            if (confirm(`Yakin ingin mengirim notifikasi ke ${allStudentIds.length} siswa?`)) {
                fetch("{{ route('notifications.send') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            students: allStudentIds,
                            subject_id: "{{ request('subject_id') }}",
                            task_name: "{{ request('task_name') }}"
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Notifikasi berhasil dikirim ke semua siswa!');
                            location.reload();
                        } else {
                            alert('Terjadi kesalahan saat mengirim notifikasi.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengirim notifikasi.');
                    });
            }
        }

        function resetNotificationStatus(specificStudentIds = null) {
            let studentIds = specificStudentIds ||
                Array.from(document.querySelectorAll('input[name="students[]"]')).map(cb => cb.value);

            let confirmMessage = studentIds.length > 0 ?
                `Yakin ingin reset status pengiriman untuk ${studentIds.length} siswa?` :
                'Yakin ingin reset status pengiriman untuk semua siswa yang ditampilkan?';

            if (!confirm(confirmMessage)) {
                return;
            }

            fetch("{{ route('notifications.reset') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        students: studentIds,
                        subject_id: "{{ request('subject_id') }}",
                        task_name: "{{ request('task_name') }}"
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert(`Status pengiriman berhasil direset! (${data.updated_count} notifikasi)`);
                        location.reload();
                    } else {
                        alert('Terjadi kesalahan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mereset status: ' + error.message);
                    });
        }

        function sendNotification(studentIds) {
            if (studentIds.length === 0) {
                alert('Pilih minimal satu siswa!');
                return;
            }

            fetch("{{ route('notifications.send') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        students: studentIds,
                        subject_id: "{{ request('subject_id') }}",
                        task_name: "{{ request('task_name') }}"
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Notifikasi berhasil dikirim!');
                        location.reload();
                    } else {
                        alert('Gagal mengirim notifikasi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengirim notifikasi.');
                });
        }
    </script>
</x-app-layout>