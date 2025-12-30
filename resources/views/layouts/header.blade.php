<header class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-6 py-5 bg-white dark:bg-[#1a202c] border-b border-[#dbe0e6] dark:border-gray-700 sticky top-0 z-10">
    <div class="flex flex-col gap-1">
        <h2 class="text-[#111418] dark:text-white text-2xl font-black leading-tight tracking-tight">
            @yield('header-title', 'Dashboard')
        </h2>
        <p class="text-[#617289] dark:text-gray-400 text-sm font-medium">
            @yield('header-subtitle', 'Overview sistem inventory.')
        </p>
    </div>
    <div class="flex items-center gap-4">
        
        {{-- Tombol Notifikasi Khusus Manajer --}}
        @if(auth()->user()->role === 'procurement_manager')
            <div class="relative inline-block text-left">
                <button onclick="toggleDropdown('notifDropdown')" class="relative bg-[#f0f2f4] dark:bg-gray-800 text-[#111418] dark:text-white rounded-full p-2 hover:bg-gray-200 transition">
                    <span class="material-symbols-outlined" style="font-size: 24px;">notifications</span>
                    {{-- Badge angka notifikasi unread --}}
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 text-[10px] text-white font-bold flex items-center justify-center border-2 border-white">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                {{-- Dropdown Data Notifikasi --}}
                <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-[#dbe0e6] dark:border-gray-700 rounded-xl shadow-xl overflow-hidden z-50">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h4 class="font-bold text-sm dark:text-white">Pemberitahuan Stok</h4>
                        <a href="#" class="text-xs text-primary font-bold">Tandai Semua Dibaca</a>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <div class="p-4 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <p class="text-sm font-bold text-red-600 mb-1">{{ $notification->data['message'] }}</p>
                                <p class="text-xs text-gray-500">Stok saat ini: {{ $notification->data['current_stock'] }} (Min: {{ $notification->data['min_stock'] }})</p>
                                <span class="text-[10px] text-gray-400 mt-2 block">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400 italic text-sm">Tidak ada notifikasi baru.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <button class="lg:hidden bg-[#f0f2f4] dark:bg-gray-800 text-[#111418] dark:text-white rounded-full p-2">
            <span class="material-symbols-outlined" style="font-size: 24px;">menu</span>
        </button>
    </div>
</header>

<script>
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        dropdown.classList.toggle('hidden');
    }

    // Menutup dropdown saat klik di luar
    window.onclick = function(event) {
        if (!event.target.closest('button')) {
            document.getElementById('notifDropdown')?.classList.add('hidden');
        }
    }
</script>