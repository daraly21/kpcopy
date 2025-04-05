<x-modal name="delete-user-modal" focusable>
    <form id="deleteUserForm" method="POST" class="p-6">
        @csrf
        @method('DELETE')
        <input type="hidden" id="deleteUserId" name="user_id">
        
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Konfirmasi Hapus') }}
        </h2>

        <div class="mt-6">
            <div class="flex justify-center text-red-500 mb-4">
                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <p class="text-center text-gray-700">
                Apakah Anda yakin ingin menghapus user <span id="deleteUserName" class="font-semibold"></span>?
            </p>
            
            <p class="text-center text-red-600 mt-2 text-sm">
                Tindakan ini tidak dapat dibatalkan!
            </p>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Batal') }}
            </x-secondary-button>

            <x-danger-button class="ml-3">
                {{ __('Hapus') }}
            </x-danger-button>
        </div>
    </form>
</x-modal>