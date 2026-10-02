<?php

use function Livewire\Volt\{state, computed, rules};
use App\Models\DataRt;
use App\Models\KartuKeluarga;
use App\Models\Warga;
use App\Models\Approval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

state([
    'currentStep' => 1,
    // Step 1: Wilayah
    'id_rt' => '',
    // Step 2: Data Keluarga & Identitas
    'id_kk' => '',
    'nik' => '',
    'nama_lengkap' => '',
    'tgl_lahir' => '',
    'no_telp' => '',
    'alamat' => '',
    // Step 3: Kredensial
    'username' => '',
    'password' => '',
    'password_confirmation' => '',
    'showPassword' => false,
    'isSubmitting' => false,
]);

$rtOptions = computed(function () {
    return DataRt::orderBy('nomor_rw')->orderBy('nomor_rt')->get();
});

$toggleShowPassword = function () {
    $this->showPassword = !$this->showPassword;
};

$validateStep1 = function () {
    $this->validate([
        'id_rt' => 'required|exists:DATA_RT,ID_RT',
    ], [
        'id_rt.required' => 'Silakan pilih RT / RW domisili tempat tinggal Anda.',
        'id_rt.exists' => 'Data RT yang dipilih tidak valid di sistem.',
    ]);
};

$validateStep2 = function () {
    $this->validate([
        'id_kk' => 'required|digits:16',
        'nik' => [
            'required',
            'digits:16',
            function ($attribute, $value, $fail) {
                if (Warga::withoutTenant()->where('NIK', $value)->exists()) {
                    $fail('Nomor Induk Kependudukan (NIK) ini sudah terdaftar di sistem.');
                }
            },
        ],
        'nama_lengkap' => 'required|string|min:3|max:150',
        'tgl_lahir' => 'required|date|before:today',
        'no_telp' => 'required|string|min:8|max:25',
        'alamat' => 'required|string|min:5',
    ], [
        'id_kk.required' => 'Nomor Kartu Keluarga wajib diisi.',
        'id_kk.digits' => 'Nomor Kartu Keluarga harus berjumlah 16 digit angka.',
        'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
        'nik.digits' => 'NIK harus berjumlah 16 digit angka.',
        'nama_lengkap.required' => 'Nama lengkap wajib diisi sesuai KTP.',
        'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter.',
        'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
        'tgl_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
        'no_telp.required' => 'Nomor WhatsApp / Telepon aktif wajib diisi.',
        'no_telp.min' => 'Nomor telepon minimal 8 karakter.',
        'alamat.required' => 'Alamat domisili lengkap wajib diisi.',
        'alamat.min' => 'Alamat domisili minimal 5 karakter.',
    ]);
};

$validateStep3 = function () {
    $this->validate([
        'username' => [
            'required',
            'alpha_dash',
            'min:3',
            'max:50',
            function ($attribute, $value, $fail) {
                $low = strtolower(trim($value));
                if (Warga::withoutTenant()->whereRaw('LOWER(Username) = ?', [$low])->exists() ||
                    \App\Models\Admin::whereRaw('LOWER(Username) = ?', [$low])->exists()) {
                    $fail('Username ini sudah digunakan. Silakan pilih username lain.');
                }
            },
        ],
        'password' => 'required|string|min:8|same:password_confirmation',
        'password_confirmation' => 'required|string|min:8',
    ], [
        'username.required' => 'Username akun wajib diisi.',
        'username.alpha_dash' => 'Username hanya boleh memuat huruf, angka, strip (-), dan garis bawah (_).',
        'username.min' => 'Username minimal 3 karakter.',
        'password.required' => 'Kata sandi wajib diisi.',
        'password.min' => 'Kata sandi minimal 8 karakter.',
        'password.same' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.',
        'password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.',
    ]);
};

$nextStep = function () {
    if ($this->currentStep === 1) {
        $this->validateStep1();
        $this->currentStep = 2;
    } elseif ($this->currentStep === 2) {
        $this->validateStep2();
        $this->currentStep = 3;
    }
};

$prevStep = function () {
    if ($this->currentStep > 1) {
        $this->currentStep--;
    }
};

$register = function () {
    $this->validateStep1();
    $this->validateStep2();
    $this->validateStep3();

    $this->isSubmitting = true;

    try {
        DB::transaction(function () {
            // a. Ensure KARTU_KELUARGA record exists for the given ID_KK; if not, create it
            KartuKeluarga::firstOrCreate(
                ['ID_KK' => trim($this->id_kk)],
                ['Alamat' => trim($this->alamat)]
            );

            // b. Insert new citizen record into WARGA with role='warga' and hashed password
            Warga::create([
                'NIK' => trim($this->nik),
                'ID_KK' => trim($this->id_kk),
                'ID_RT' => $this->id_rt,
                'Nama_Lengkap' => trim($this->nama_lengkap),
                'no_telp' => trim($this->no_telp),
                'Username' => strtolower(trim($this->username)),
                'Password_hash' => Hash::make($this->password),
                'tgl_lahir' => $this->tgl_lahir,
                'role' => 'warga',
            ]);

            // c. Automatically insert an entry into the APPROVAL table
            Approval::create([
                'Admin_ID' => null,
                'tabel_sumber' => 'WARGA',
                'id_referensi' => trim($this->nik),
                'status' => 'pending',
                'catatan' => 'Pendaftaran mandiri akun warga baru',
                'tgl_approval' => null,
            ]);
        });

        session()->flash('success', 'Registrasi berhasil! Akun Anda telah terdaftar dan menunggu verifikasi dari Sekretaris RT. Silakan login.');
        return redirect()->route('login');
    } catch (\Throwable $e) {
        $this->isSubmitting = false;
        $this->addError('general', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
};

?>

<div class="w-full flex flex-col items-center justify-center">

  <!-- ========================================================================
       CENTERED FLOATING REGISTER CARD (Max-width: 520px)
       Pure Light Theme: #FFFFFF container, #F8FAFC bg, #0F172A text
       ======================================================================== -->
  <div class="w-full max-w-[520px] bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 sm:p-9 relative transition-all">

    <!-- Header Section -->
    <div class="text-center mb-6">
      <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 mb-2.5 group focus:outline-none">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-blue-500 flex items-center justify-center text-white shadow-md shadow-blue-500/25 group-hover:scale-105 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <line x1="19" y1="8" x2="19" y2="14"/>
            <line x1="22" y1="11" x2="16" y2="11"/>
          </svg>
        </div>
        <div class="text-left">
          <div class="font-black text-slate-900 text-lg tracking-tight leading-none">
            Ruang<span class="text-blue-600">Warga</span>
          </div>
          <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mt-0.5">REGISTRASI WARGA</span>
        </div>
      </a>

      <h1 class="text-base font-extrabold text-slate-900 tracking-tight">Pendaftaran Akun Warga Baru</h1>
      <p class="text-xs font-semibold text-slate-500 max-w-sm mx-auto leading-relaxed mt-0.5">
        Layanan mandiri kependudukan, pengajuan surat, & iuran kas lingkungan RT
      </p>
    </div>

    <!-- Step Progress Indicator -->
    <div class="mb-7">
      <div class="flex items-center justify-between relative">
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 w-full bg-slate-200 -z-0"></div>
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-blue-600 transition-all duration-300 -z-0"
             style="width: {{ $currentStep === 1 ? '0%' : ($currentStep === 2 ? '50%' : '100%') }}"></div>

        <!-- Step 1 Indicator -->
        <div class="relative z-10 flex flex-col items-center">
          <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all {{ $currentStep >= 1 ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
            @if($currentStep > 1)
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            @else
              1
            @endif
          </div>
          <span class="text-[10px] font-bold mt-1.5 {{ $currentStep === 1 ? 'text-blue-600 font-extrabold' : 'text-slate-500' }}">Wilayah</span>
        </div>

        <!-- Step 2 Indicator -->
        <div class="relative z-10 flex flex-col items-center">
          <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all {{ $currentStep >= 2 ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
            @if($currentStep > 2)
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            @else
              2
            @endif
          </div>
          <span class="text-[10px] font-bold mt-1.5 {{ $currentStep === 2 ? 'text-blue-600 font-extrabold' : 'text-slate-500' }}">Data Diri</span>
        </div>

        <!-- Step 3 Indicator -->
        <div class="relative z-10 flex flex-col items-center">
          <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all {{ $currentStep === 3 ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
            3
          </div>
          <span class="text-[10px] font-bold mt-1.5 {{ $currentStep === 3 ? 'text-blue-600 font-extrabold' : 'text-slate-500' }}">Kredensial</span>
        </div>
      </div>
    </div>

    <!-- General Error Banner -->
    @error('general')
      <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2">
        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke-width="2"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
        </svg>
        <span>{{ $message }}</span>
      </div>
    @enderror

    <!-- Form Container -->
    <form wire:submit.prevent="{{ $currentStep === 3 ? 'register' : 'nextStep' }}" class="space-y-4">

      <!-- ====================================================================
           STEP 1: VALIDASI WILAYAH DOMISILI
           ==================================================================== -->
      @if($currentStep === 1)
        <div class="space-y-4 animate-fadeIn">
          <div class="p-3.5 bg-blue-50/70 border border-blue-200/80 rounded-xl text-xs text-blue-900 leading-relaxed">
            <div class="font-bold flex items-center gap-1.5 mb-1">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <span>Langkah 1: Wilayah Domisili RT</span>
            </div>
            <p class="text-blue-800/90 text-[11px]">
              Pilih rukun tetangga tempat tinggal Anda. Akun Anda akan diverifikasi oleh pengurus RT yang berwenang di wilayah tersebut.
            </p>
          </div>

          <div>
            <label for="selectRt" class="block text-xs font-bold text-slate-700 mb-1.5">
              Pilih RT / RW Domisili <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
              <select
                wire:model="id_rt"
                id="selectRt"
                class="w-full px-3.5 py-2.5 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('id_rt') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all cursor-pointer"
                required
              >
                <option value="">-- Pilih Rukun Tetangga (RT) --</option>
                @foreach($this->rtOptions as $rt)
                  <option value="{{ $rt->ID_RT }}">
                    RT {{ $rt->nomor_rt }} / RW {{ $rt->nomor_rw }} — Kel. {{ $rt->kelurahan }} (Ketua: {{ $rt->nama_ketua_rt }})
                  </option>
                @endforeach
              </select>
            </div>
            @error('id_rt')
              <p class="text-[11px] text-rose-600 mt-1 font-semibold flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ $message }}</span>
              </p>
            @enderror
          </div>

          <!-- Action Buttons -->
          <div class="pt-3">
            <button
              type="button"
              wire:click="nextStep"
              class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <span>Lanjutkan ke Data Identitas</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
      @endif

      <!-- ====================================================================
           STEP 2: DATA KELUARGA & IDENTITAS
           ==================================================================== -->
      @if($currentStep === 2)
        <div class="space-y-3.5 animate-fadeIn">
          <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
            <div class="font-bold flex items-center gap-1.5 mb-0.5">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
              <span>Langkah 2: Data Keluarga & Identitas</span>
            </div>
            <p class="text-slate-500 text-[11px]">Masukkan nomor KK dan NIK resmi sesuai dokumen KTP/KK Anda.</p>
          </div>

          <!-- No KK & NIK Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Nomor Kartu Keluarga (ID_KK) -->
            <div>
              <label for="inputKk" class="block text-[11px] font-bold text-slate-700 mb-1">
                Nomor KK (16 Digit) <span class="text-rose-500">*</span>
              </label>
              <input
                wire:model="id_kk"
                id="inputKk"
                type="text"
                maxlength="16"
                placeholder="320105xxxxxxxxxx"
                class="w-full px-3 py-2 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('id_kk') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-mono font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all"
                required
              />
              @error('id_kk')
                <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
              @enderror
            </div>

            <!-- NIK -->
            <div>
              <label for="inputNik" class="block text-[11px] font-bold text-slate-700 mb-1">
                NIK KTP (16 Digit) <span class="text-rose-500">*</span>
              </label>
              <input
                wire:model="nik"
                id="inputNik"
                type="text"
                maxlength="16"
                placeholder="3201xxxxxxxxxxxx"
                class="w-full px-3 py-2 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('nik') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-mono font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all"
                required
              />
              @error('nik')
                <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <!-- Nama Lengkap -->
          <div>
            <label for="inputNama" class="block text-[11px] font-bold text-slate-700 mb-1">
              Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span>
            </label>
            <input
              wire:model="nama_lengkap"
              id="inputNama"
              type="text"
              placeholder="Contoh: Hendra Wijaya"
              class="w-full px-3 py-2 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('nama_lengkap') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all"
              required
            />
            @error('nama_lengkap')
              <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
            @enderror
          </div>

          <!-- Tanggal Lahir & Telepon Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label for="inputTglLahir" class="block text-[11px] font-bold text-slate-700 mb-1">
                Tanggal Lahir <span class="text-rose-500">*</span>
              </label>
              <input
                wire:model="tgl_lahir"
                id="inputTglLahir"
                type="date"
                class="w-full px-3 py-2 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('tgl_lahir') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all"
                required
              />
              @error('tgl_lahir')
                <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="inputTelp" class="block text-[11px] font-bold text-slate-700 mb-1">
                No. WhatsApp / Telepon <span class="text-rose-500">*</span>
              </label>
              <input
                wire:model="no_telp"
                id="inputTelp"
                type="text"
                placeholder="0812-3456-7890"
                class="w-full px-3 py-2 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('no_telp') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all"
                required
              />
              @error('no_telp')
                <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <!-- Alamat Domisili -->
          <div>
            <label for="inputAlamat" class="block text-[11px] font-bold text-slate-700 mb-1">
              Alamat Domisili Lengkap <span class="text-rose-500">*</span>
            </label>
            <textarea
              wire:model="alamat"
              id="inputAlamat"
              rows="2"
              placeholder="Contoh: Jl. Harmoni Raya Blok B No. 12"
              class="w-full px-3 py-2 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('alamat') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all resize-none"
              required
            ></textarea>
            @error('alamat')
              <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
            @enderror
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-2 pt-2">
            <button
              type="button"
              wire:click="prevStep"
              class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition-all cursor-pointer"
            >
              Kembali
            </button>
            <button
              type="button"
              wire:click="nextStep"
              class="flex-1 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <span>Lanjutkan ke Akun & Sandi</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
      @endif

      <!-- ====================================================================
           STEP 3: KREDENSIAL AKUN
           ==================================================================== -->
      @if($currentStep === 3)
        <div class="space-y-3.5 animate-fadeIn">
          <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
            <div class="font-bold flex items-center gap-1.5 mb-0.5">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <span>Langkah 3: Kredensial Akun Warga</span>
            </div>
            <p class="text-slate-500 text-[11px]">Tentukan username dan kata sandi yang akan digunakan saat login portal mandiri.</p>
          </div>

          <!-- Username -->
          <div>
            <label for="inputUsername" class="block text-[11px] font-bold text-slate-700 mb-1">
              Username Akun <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-bold">@</div>
              <input
                wire:model="username"
                id="inputUsername"
                type="text"
                placeholder="nama_pengguna (huruf & angka)"
                class="w-full pl-8 pr-3 py-2 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('username') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all"
                required
              />
            </div>
            @error('username')
              <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
            @enderror
          </div>

          <!-- Password & Konfirmasi Password Grid -->
          <div x-data="{ show: false }" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label for="inputRegPassword" class="block text-[11px] font-bold text-slate-700 mb-1">
                Kata Sandi (Min. 8 Karakter) <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <input
                  wire:model="password"
                  id="inputRegPassword"
                  :type="show ? 'text' : 'password'"
                  type="password"
                  placeholder="Minimal 8 karakter..."
                  class="w-full px-3 pr-8 py-2 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('password') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all"
                  required
                />
                <button
                  type="button"
                  @click="show = !show"
                  class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-blue-600 cursor-pointer"
                  tabindex="-1"
                >
                  <svg x-show="!show" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                  <svg x-show="show" x-cloak class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                </button>
              </div>
              @error('password')
                <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="inputRegPasswordConfirm" class="block text-[11px] font-bold text-slate-700 mb-1">
                Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
              </label>
              <input
                wire:model="password_confirmation"
                id="inputRegPasswordConfirm"
                :type="show ? 'text' : 'password'"
                type="password"
                placeholder="Ketik ulang kata sandi..."
                class="w-full px-3 py-2 bg-slate-100/80 hover:bg-slate-100 focus:bg-white border {{ $errors->has('password_confirmation') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-xl text-xs text-slate-900 font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all"
                required
              />
              @error('password_confirmation')
                <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <!-- Audit Notice -->
          <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-[11px] text-slate-600 space-y-1">
            <span class="font-bold text-slate-800 flex items-center gap-1">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
              <span>Verifikasi Berkas RT:</span>
            </span>
            <p>
              Setelah submit, data Anda akan diverifikasi oleh Sekretaris RT. Anda dapat login dengan NIK / Username dan kata sandi setelah data tercatat di sistem.
            </p>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-2 pt-2">
            <button
              type="button"
              wire:click="prevStep"
              class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition-all cursor-pointer"
            >
              Kembali
            </button>
            <button
              type="submit"
              wire:loading.attr="disabled"
              class="flex-1 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
            >
              <span wire:loading.remove wire:target="register">Selesaikan Pendaftaran Warga</span>
              <span wire:loading wire:target="register" class="flex items-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor" stroke-dasharray="32" stroke-linecap="round"/>
                </svg>
                <span>Mendaftarkan Data Warga...</span>
              </span>
            </button>
          </div>
        </div>
      @endif

      <!-- Divider line -->
      <div class="relative my-4">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-slate-200"></div>
        </div>
        <div class="relative flex justify-center text-xs">
          <span class="bg-white px-3 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">sudah punya akun?</span>
        </div>
      </div>

      <!-- Back to Login Link: "Sudah memiliki akun? Masuk di sini" -->
      <div class="text-center pt-0.5">
        <a
          href="{{ route('login') }}"
          class="text-xs font-semibold text-slate-600 hover:text-blue-600 transition-colors"
        >
          Sudah memiliki akun? <strong class="text-blue-600 font-bold hover:underline">Masuk di sini</strong>
        </a>
      </div>
    </form>
  </div>

  <!-- Footer Notice -->
  <div class="mt-6 text-center text-xs text-slate-400">
    <p class="text-[11px] font-medium">© 2026 RuangWarga Platform • Pelayanan Mandiri RT</p>
  </div>
</div>
