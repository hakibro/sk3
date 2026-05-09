<x-app-layout>
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fa-solid fa-user-shield text-indigo-600 mr-2"></i>Panel Kontrol Admin
            </h2>
            <p class="text-sm text-gray-500">Kelola akses pengurus asrama dan pusat serta konfigurasi sistem.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="bg-blue-100 p-4 rounded-xl text-blue-600">
                <i class="fa-solid fa-users-gear text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pengurus</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $users->count() }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="bg-amber-100 p-4 rounded-xl text-amber-600">
                <i class="fa-solid fa-tags text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Master Alasan</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $alasans->count() }}</h3>
            </div>
        </div>
    </div>

    <div x-data="{ tab: 'users' }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex border-b">
            <button @click="tab = 'users'"
                :class="tab === 'users' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/30' :
                    'border-transparent text-gray-500 hover:text-gray-700'"
                class="flex-1 py-4 px-6 text-sm font-bold border-b-2 transition-all flex items-center justify-center">
                <i class="fa-solid fa-user-group mr-2"></i> MANAJEMEN PENGURUS
            </button>
            <button @click="tab = 'alasan'"
                :class="tab === 'alasan' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/30' :
                    'border-transparent text-gray-500 hover:text-gray-700'"
                class="flex-1 py-4 px-6 text-sm font-bold border-b-2 transition-all flex items-center justify-center">
                <i class="fa-solid fa-list-check mr-2"></i> MASTER ALASAN BOYONG
            </button>
        </div>

        <div class="p-6">
            <div x-show="tab === 'users'" x-transition>
                <div class="flex justify-between items-center mb-6">
                    <h4 class="font-bold text-gray-700">Daftar Akun Pengurus</h4>
                    <button @click="$dispatch('open-modal', 'add-user-modal')"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah Pengurus
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Nama / Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Wilayah/Lembaga</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-gray-800">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'pengurus_pusat' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                            <i
                                                class="fa-solid {{ $user->role === 'pengurus_pusat' ? 'fa-building-columns' : 'fa-house-user' }} mr-1.5"></i>
                                            {{ str_replace('_', ' ', strtoupper($user->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        {{ $user->lembaga ?? 'Seluruh Unit' }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus pengurus ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-400 hover:text-red-600 transition">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="tab === 'alasan'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="bg-gray-50 p-6 rounded-xl border border-dashed border-gray-300">
                        <h4 class="font-bold text-gray-700 mb-4">Tambah Alasan</h4>
                        <form action="{{ route('admin.alasan.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <x-input-label for="nama_alasan" value="Nama Alasan Boyong" />
                                <x-text-input name="nama_alasan" class="w-full mt-1"
                                    placeholder="Contoh: Lulus / Wisuda" required />
                            </div>
                            <x-primary-button class="w-full justify-center py-3">
                                <i class="fa-solid fa-save mr-2"></i> Simpan Alasan
                            </x-primary-button>
                        </form>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($alasans as $alasan)
                                <div
                                    class="flex justify-between items-center p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:border-indigo-300 transition">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                                        <span class="font-medium text-gray-700">{{ $alasan->nama_alasan }}</span>
                                    </div>
                                    <form action="{{ route('admin.alasan.destroy', $alasan->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-300 hover:text-red-500 transition">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="add-user-modal" focusable>
        <form method="post" action="{{ route('admin.users.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Registrasi Pengurus Baru</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label value="Nama Lengkap" />
                    <x-text-input name="name" class="w-full mt-1" required />
                </div>
                <div>
                    <x-input-label value="Email" />
                    <x-text-input name="email" type="email" class="w-full mt-1" required />
                </div>
                <div>
                    <x-input-label value="Role Akses" />
                    <select name="role"
                        class="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="pengurus_asrama">PENGURUS ASRAMA</option>
                        <option value="pengurus_pusat">PENGURUS PUSAT</option>
                    </select>
                </div>
                <div>
                    <x-input-label value="Lembaga/Asrama" />
                    <x-text-input name="lembaga" class="w-full mt-1" placeholder="Nama asrama..." />
                </div>
                <div>
                    <x-input-label value="Password Akun" />
                    <x-text-input name="password" type="password" class="w-full mt-1" required />
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button>Simpan Sekarang</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
