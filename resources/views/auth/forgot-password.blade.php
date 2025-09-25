<x-guest-layout>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl border border-gray-100">
            <!-- Left side decorative element -->
            <div class="h-2 bg-gradient-to-r from-blue-800 to-indigo-600"></div>
            
            <div class="p-8">
                <!-- Logo and Header -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-5">
                        <img src="https://ucarecdn.com/140db37d-4117-4b98-bc00-20e8d0147903/WhatsApp_Image_20250430_at_105328_AM__1_removebgpreview.png"
                            alt="Logo SDN Cijedil" class="h-12 w-auto">
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Lupa Kata Sandi?</h2>
                    <p class="mt-2 text-sm text-gray-500 max-w-xs mx-auto leading-relaxed">
                        {{ __('Hubungi dapodik jika anda lupa password dan email.') }}
                    </p>
                </div>
                
                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-center text-sm font-medium text-green-600" :status="session('status')" />
                
                <!-- Form -->
                {{-- <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Email Input -->
                    <div class="space-y-2">
                        <label for="email" class="block text-xs font-medium text-gray-700">Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="appearance-none block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm bg-gray-50 transition-all group-hover:bg-white"
                                placeholder="nama@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-600" />
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit"
                            class="w-full py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-800 to-indigo-600 hover:from-blue-700 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-sm hover:shadow transform hover:-translate-y-0.5">
                            {{ __('Kirim Tautan Reset') }}
                        </button>
                    </div>
                </form> --}}
                
                <!-- Divider -->
                <div class="relative mt-8 mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-400">atau</span>
                    </div>
                </div>
                
                <!-- Back to Login Link -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center text-sm text-blue-700 hover:text-blue-800 font-medium transition duration-300">
                        <i class="fas fa-arrow-left mr-2 text-xs"></i>
                        {{ __('Kembali ke Halaman Login') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>