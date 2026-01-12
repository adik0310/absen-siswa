{{-- resources/views/profile/edit.blade.php --}}
@php
    $layout = auth()->user()->id_role == 1 ? 'layouts.admin' : 'layouts.guru';
    $themeColor = auth()->user()->id_role == 1 ? '#198754' : '#10b981';
@endphp

@extends($layout)

@section('title', 'Pengaturan Akun')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    .container-profile {
        padding-top: 2rem;
        padding-bottom: 3rem;
    }
    .card-profile {
        max-width: 900px;
        margin: auto;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        background: #fff;
        overflow: hidden;
    }
    .sidebar-profile {
        background: #f8fafc;
        border-right: 1px solid #edf2f7;
    }
    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }
    .avatar {
        width: 130px;
        height: 130px;
        border-radius: 20px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .avatar-initial {
        width: 130px;
        height: 130px;
        border-radius: 20px;
        background: linear-gradient(135deg, {{ $themeColor }}, #064e3b);
        color: #fff;
        font-size: 40px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #4a5568;
    }
    .form-control {
        border-radius: 10px;
        padding: 0.6rem 1rem;
        border: 1px solid #e2e8f0;
    }
    .form-control:focus {
        border-color: {{ $themeColor }};
        box-shadow: 0 0 0 3px {{ $themeColor }}15;
    }
    .password-toggle {
        cursor: pointer;
        background: transparent;
        border-left: none;
        color: #a0aec0;
    }
    .btn-save {
        background-color: {{ $themeColor }};
        border: none;
        border-radius: 10px;
        padding: 0.7rem 2rem;
        font-weight: 700;
        transition: all 0.3s;
    }
    .btn-save:hover {
        filter: brightness(90%);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $user = $user ?? auth()->user();
    $initial = Str::of($user->nama)->explode(' ')->take(2)->map(fn ($w) => Str::substr($w, 0, 1))->join('');
@endphp

<div class="container container-profile">
    <div class="card card-profile">
        <div class="row g-0">

            {{-- SIDEBAR INFO --}}
            <div class="col-md-4 sidebar-profile p-4 text-center d-flex flex-column align-items-center justify-content-center">
                <div class="avatar-wrapper mb-3">
                    @if($user->foto && Storage::disk('public')->exists($user->foto))
                        <img src="{{ url('storage/'.$user->foto) }}" class="avatar" alt="Foto Profil">
                    @else
                        <div class="avatar-initial">{{ $initial }}</div>
                    @endif
                </div>

                <h5 class="fw-bold mb-1 text-dark">{{ $user->nama }}</h5>
                <p class="text-muted small mb-4">{{ $user->email }}</p>

                <div class="w-100 border-top pt-4 mt-2">
                    <p class="small text-muted mb-3">Login sebagai:</p>
                    <span class="badge rounded-pill px-3 py-2 fw-bold" 
                          style="background: {{ $themeColor }}15; color: {{ $themeColor }};">
                        <i class="bi bi-person-badge me-1"></i>
                        {{ $user->id_role == 1 ? 'Administrator' : 'Pengajar / Guru' }}
                    </span>
                </div>

                <div class="mt-auto pt-5">
                    <a href="{{ $user->id_role == 1 ? route('admin.dashboard') : route('guru.dashboard') }}"
                       class="btn btn-link text-decoration-none text-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Dashboard
                    </a>
                </div>
            </div>

            {{-- FORM EDIT --}}
            <div class="col-md-8 p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark mb-0">Pengaturan Akun</h4>
                    <i class="bi bi-gear-fill text-muted opacity-25 fs-3"></i>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3">
                        <ul class="mb-0 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" 
                                   value="{{ old('nama', $user->nama) }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="col-md-12 mb-4">
                            <label class="form-label">Ganti Foto Profil</label>
                            <input type="file" name="foto" class="form-control">
                            <div class="form-text text-muted small">Format: JPG, PNG. Maksimal 2MB.</div>
                        </div>

                        <div class="col-12 py-2">
                            <div class="border-top mb-4"></div>
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-lock me-2"></i>Keamanan</h6>
                            <p class="text-muted small mb-4">Kosongkan jika tidak ingin mengubah password.</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" 
                                       autocomplete="new-password">
                                <button type="button" class="input-group-text password-toggle" 
                                        onclick="togglePassword('password')">
                                    <i class="bi bi-eye" id="eye-password"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control" autocomplete="new-password">
                                <button type="button" class="input-group-text password-toggle" 
                                        onclick="togglePassword('password_confirmation')">
                                    <i class="bi bi-eye" id="eye-password_confirmation"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary btn-save w-100 shadow-sm text-white">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = document.getElementById('eye-' + id);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
@endpush