<x-app-layout title="Kirim Notifikasi">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifikasi') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                    <span class="iconify mr-2" data-icon="mdi:cellphone-message" style="color: #4f46e5;"></span>
                    Notifikasi Orang Tua
                </h4>
            </div>

            <!-- Filter Section -->
            <div class="p-6 border-b">
                <form method="GET" action="{{ route('notifications.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Subject Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                        <select name="subject_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 h-10 pl-3 pr-8">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Task Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Tugas</label>
                        <select name="task_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 h-10 pl-3 pr-8">
                            <option value="">Semua Tugas</option>
                            @foreach ($taskNames as $task)
                                <option value="{{ $task }}" {{ request('task_name') == $task ? 'selected' : '' }}>
                                    {{ $task }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center">
                            <span class="iconify mr-2" data-icon="mdi:filter"></span>
                            Filter
                        </button>
                    </div>

                    <!-- Active Filter Display -->
                    <div class="flex items-end">
                        <div class="w-full bg-gray-50 p-3 rounded-md border border-gray-200">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Filter Aktif:</span>
                                <span>
                                    @if(request('subject_id'))
                                        {{ $subjects->firstWhere('id', request('subject_id'))->name ?? 'Semua Mata Pelajaran' }}
                                        @if(request('task_name')) - {{ request('task_name') }} @endif
                                    @else
                                        Semua Data
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Results Section -->
            <div class="p-6">
                <!-- Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor HP Orang Tua</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($students as $student)
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
                                
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}
                                        <input type="hidden" name="students[]" value="{{ $student->id }}">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $student->parent_phone ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        @if($filteredTask)
                                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                                                {{ $filteredTask->score >= 90 ? 'bg-green-100 text-green-800' : 
                                                   ($filteredTask->score >= 75 ? 'bg-blue-100 text-blue-800' : 
                                                   ($filteredTask->score >= 60 ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-red-100 text-red-800')) }}">
                                                {{ $filteredTask->score }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        @if ($notification && $notification->sent_at)
                                            <span class="inline-flex items-center text-green-600">
                                                <span class="iconify mr-1" data-icon="mdi:check-circle-outline"></span>
                                                Terkirim
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-gray-500">
                                                <span class="iconify mr-1" data-icon="mdi:clock-outline"></span>
                                                Belum dikirim
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
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
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($students->hasPages())
                    <div class="mt-4">
                        {{ $students->appends(request()->except('page'))->links() }}
                    </div>
                @endif

                <!-- Bulk Actions -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="button" onclick="sendNotificationToAll()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="iconify mr-2" data-icon="mdi:send-outline"></span>
                        Kirim Semua Notifikasi
                    </button>
                    
                    <button type="button" onclick="resetNotificationStatus()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <span class="iconify mr-2" data-icon="mdi:refresh"></span>
                        Reset Status Pengiriman
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sendNotificationToAll() {
            // Ambil semua ID siswa yang ada di tabel
            let allStudentIds = Array.from(document.querySelectorAll('input[name="students[]"]')).map(cb => cb.value);

            if (allStudentIds.length === 0) {
                alert('Tidak ada siswa yang dapat dikirim notifikasi!');
                return;
            }

            // Konfirmasi pengiriman
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
                            location.reload(); // Refresh halaman untuk memperbarui status
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
            // Tentukan siswa yang akan direset
            let studentIds = specificStudentIds ||
                Array.from(document.querySelectorAll('input[name="students[]"]:checked')).map(cb => cb.value);

            // Konfirmasi reset
            let confirmMessage = studentIds.length > 0 ?
                `Yakin ingin reset status pengiriman untuk ${studentIds.length} siswa terpilih?` :
                'Yakin ingin reset status pengiriman untuk semua siswa yang ditampilkan?';

            if (!confirm(confirmMessage)) {
                return;
            }

            // Kirim request reset
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