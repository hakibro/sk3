<x-app-layout>
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <x-heroicon-o-shield-check class="h-6 w-6 inline-block text-indigo-600 mr-2 -mt-1" />Panel Kontrol Admin
            </h2>
            <p class="text-sm text-gray-500">Kelola akses pengurus asrama dan pusat serta konfigurasi sistem.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="bg-blue-100 p-4 rounded-xl text-blue-600">
                <x-heroicon-o-users class="h-6 w-6" />
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pengurus</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $users->count() }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="bg-amber-100 p-4 rounded-xl text-amber-600">
                <x-heroicon-o-tag class="h-6 w-6" />
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Master Alasan</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $alasans->count() }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="bg-emerald-100 p-4 rounded-xl text-emerald-600">
                <x-heroicon-o-document-check class="h-6 w-6" />
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Kop Surat</p>
                <h3 class="text-base font-bold text-gray-800">{{ $kopSurat['nama_pesantren'] }}</h3>
            </div>
        </div>
    </div>

    <div x-data="{ tab: 'users' }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex border-b">
            <button @click="tab = 'users'"
                :class="tab === 'users' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/30' :
                    'border-transparent text-gray-500 hover:text-gray-700'"
                class="flex-1 py-4 px-6 text-sm font-bold border-b-2 transition-all flex items-center justify-center">
                <x-heroicon-o-user-group class="h-5 w-5 mr-2" /> MANAJEMEN PENGURUS
            </button>
            <button @click="tab = 'alasan'"
                :class="tab === 'alasan' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/30' :
                    'border-transparent text-gray-500 hover:text-gray-700'"
                class="flex-1 py-4 px-6 text-sm font-bold border-b-2 transition-all flex items-center justify-center">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5 mr-2" /> MASTER ALASAN BOYONG
            </button>
            <button @click="tab = 'kop'"
                :class="tab === 'kop' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/30' :
                    'border-transparent text-gray-500 hover:text-gray-700'"
                class="flex-1 py-4 px-6 text-sm font-bold border-b-2 transition-all flex items-center justify-center">
                <x-heroicon-o-document-text class="h-5 w-5 mr-2" /> KOP SURAT
            </button>
            <button @click="tab = 'cutoff'"
                :class="tab === 'cutoff' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/30' :
                    'border-transparent text-gray-500 hover:text-gray-700'"
                class="flex-1 py-4 px-6 text-sm font-bold border-b-2 transition-all flex items-center justify-center">
                <x-heroicon-o-scissors class="h-5 w-5 mr-2" /> CUT-OFF
            </button>
        </div>

        <div class="p-6">
            <div x-show="tab === 'users'" x-transition>
                <div class="flex justify-between items-center mb-6">
                    <h4 class="font-bold text-gray-700">Daftar Akun Pengurus</h4>
                    <button @click="$dispatch('open-modal', 'add-user-modal')"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                        <x-heroicon-o-plus class="h-4 w-4 mr-1" /> Tambah Pengurus
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
                                            @if ($user->role === 'pengurus_pusat')
                                                <x-heroicon-o-building-library class="h-3.5 w-3.5 mr-1.5" />
                                            @else
                                                <x-heroicon-o-home-modern class="h-3.5 w-3.5 mr-1.5" />
                                            @endif
                                            {{ str_replace('_', ' ', strtoupper($user->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        {{ $user->lembaga ?? 'Seluruh Unit' }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button
                                                @click="$dispatch('open-edit-user', { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}', email: '{{ $user->email }}', role: '{{ $user->role }}', lembaga: '{{ $user->lembaga }}' })"
                                                class="text-indigo-400 hover:text-indigo-600 transition" title="Edit pengurus">
                                                <x-heroicon-o-pencil-square class="h-5 w-5" />
                                            </button>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus pengurus ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-400 hover:text-red-600 transition" title="Hapus pengurus">
                                                    <x-heroicon-o-trash class="h-5 w-5" />
                                                </button>
                                            </form>
                                        </div>
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
                                <x-heroicon-o-check class="h-4 w-4 mr-2" /> Simpan Alasan
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
                                            <x-heroicon-o-x-circle class="h-5 w-5" />
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'kop'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <h4 class="font-bold text-gray-700 mb-4">Pengaturan Kop Surat SK3</h4>
                        <form action="{{ route('admin.kop-surat.update') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="nama_pesantren" value="Nama Pesantren" />
                                <x-text-input id="nama_pesantren" name="nama_pesantren" class="w-full mt-1"
                                    value="{{ old('nama_pesantren', $kopSurat['nama_pesantren']) }}" required />
                                <x-input-error :messages="$errors->get('nama_pesantren')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nspp" value="NSPP" />
                                <x-text-input id="nspp" name="nspp" class="w-full mt-1"
                                    value="{{ old('nspp', $kopSurat['nspp']) }}" />
                                <x-input-error :messages="$errors->get('nspp')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="alamat" value="Alamat" />
                                <textarea id="alamat" name="alamat" rows="3" required
                                    class="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat', $kopSurat['alamat']) }}</textarea>
                                <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                            </div>

                            <x-primary-button>
                                <x-heroicon-o-check class="h-4 w-4 mr-2" /> Simpan Kop Surat
                            </x-primary-button>
                        </form>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                        <h4 class="font-bold text-gray-700 mb-4">Pratinjau</h4>
                        <div class="rounded-lg bg-white p-4 text-center shadow-sm">
                            <img src="{{ asset('assets/logo-ppn-smaller.png') }}" alt="Logo pesantren"
                                class="mx-auto h-14 w-14 object-contain">
                            <h3 class="mt-3 text-sm font-bold uppercase text-gray-800">{{ $kopSurat['nama_pesantren'] }}</h3>
                            @if ($kopSurat['nspp'])
                                <p class="text-xs font-semibold text-gray-600">NSPP: {{ $kopSurat['nspp'] }}</p>
                            @endif
                            <p class="mt-1 text-xs leading-relaxed text-gray-500">{{ $kopSurat['alamat'] }}</p>
                            <div class="mt-3 border-t-2 border-gray-800"></div>
                            <p class="mt-2 text-xs font-bold text-gray-700">SURAT KETERANGAN KEMBALI KE RUMAH (SK3)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'cutoff'" x-transition>
                <div class="max-w-2xl">
                    <h4 class="font-bold text-gray-700 mb-1">Pengaturan Cut-off Pembayaran Boyong</h4>
                    <p class="text-sm text-gray-500 mb-4">Batas hari (dalam bulan) penentu aturan SPP bulan berjalan saat proses cut-off SK3.</p>
                    <form action="{{ route('admin.cutoff.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="cut_off_tanggal_max" value="Batas Tanggal" />
                            <x-text-input id="cut_off_tanggal_max" name="cut_off_tanggal_max" type="number" min="1" max="31"
                                class="w-full mt-1" value="{{ old('cut_off_tanggal_max', $cutOffTanggalMax) }}" required />
                            <p class="mt-1 text-xs text-gray-500">
                                Bila tanggal boyong melebihi batas ini, SPP (asrama) bulan berjalan tetap dihitung full (tidak di-cut).
                            </p>
                            <x-input-error :messages="$errors->get('cut_off_tanggal_max')" class="mt-2" />
                        </div>

                        <x-primary-button>
                            <x-heroicon-o-check class="h-4 w-4 mr-2" /> Simpan Pengaturan
                        </x-primary-button>
                    </form>
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
                    <select name="lembaga"
                        class="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Pilih asrama untuk pengurus asrama</option>
                        @foreach ($asramas as $asrama)
                            <option value="{{ $asrama }}">{{ $asrama }}</option>
                        @endforeach
                    </select>
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

    <x-modal name="edit-user-modal" focusable>
        <div x-data="{
            id: null,
            form: {
                name: '',
                email: '',
                role: 'pengurus_asrama',
                lembaga: '',
                password: '',
            },
            open(user) {
                this.id = user.id;
                this.form.name = user.name;
                this.form.email = user.email;
                this.form.role = user.role;
                this.form.lembaga = user.lembaga || '';
                this.form.password = '';
                this.$nextTick(() => this.$dispatch('open-modal', 'edit-user-modal'));
            }
        }" x-on:open-edit-user.window="open($event.detail)">
            <form x-bind:action="'/admin/users/' + id" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Edit Pengurus</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label value="Nama Lengkap" />
                        <x-text-input x-model="form.name" name="name" class="w-full mt-1" required />
                    </div>
                    <div>
                        <x-input-label value="Email" />
                        <x-text-input x-model="form.email" name="email" type="email" class="w-full mt-1" required />
                    </div>
                    <div>
                        <x-input-label value="Role Akses" />
                        <select x-model="form.role" name="role"
                            class="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="pengurus_asrama">PENGURUS ASRAMA</option>
                            <option value="pengurus_pusat">PENGURUS PUSAT</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Lembaga/Asrama" />
                        <select x-model="form.lembaga" name="lembaga"
                            class="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Pilih asrama untuk pengurus asrama</option>
                            @foreach ($asramas as $asrama)
                                <option value="{{ $asrama }}">{{ $asrama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Password Baru (opsional)" />
                        <x-text-input x-model="form.password" name="password" type="password" class="w-full mt-1"
                            placeholder="Kosongkan jika tidak diubah" autocomplete="new-password" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                    <x-primary-button>Simpan Perubahan</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
