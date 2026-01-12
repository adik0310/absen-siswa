<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="icon" type="image/png" href="{{ asset('image/logo_ma.png') }}">
    <title>@yield('title', 'Dashboard Guru') - Sistem Absensi</title>

    {{-- Bootstrap 5.3 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
:root{
    --main-green: #00D452;
    --main-blue: #0072A8;
    --body-bg: #f8f9fa;
    --muted: #6c757d;
    --pad-xs: 0.9rem;
    --pad-sm: 1.2rem;
    --pad-lg: 10rem;
}

body{
    font-family: "Poppins", sans-serif;
    background: var(--body-bg);
    min-height:100vh;
    display:flex;
    flex-direction:column;
    color:#212529;
}

/* =========================================================
   NAVBAR FULL GREEN
========================================================= */
.navbar-wrapper{
    background: var(--main-green) !important;
    box-shadow: 0 6px 24px rgba(0,0,0,0.1);
}
.navbar-wrapper .container-fluid{
    padding-left: var(--pad-lg);
    padding-right: var(--pad-lg);
}
@media(max-width:991.98px){
    .navbar-wrapper .container-fluid{ padding-left: var(--pad-sm); padding-right: var(--pad-sm); }
}
@media(max-width:575.98px){
    .navbar-wrapper .container-fluid{ padding-left: var(--pad-xs); padding-right: var(--pad-xs); }
}

.nav-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:18px 0 10px;
}

/* BRAND */
.brand{
    display:flex;
    align-items:center;
    color:white;
    text-decoration:none;
    gap:12px;
}
.brand img{
    width:56px;
    height:56px;
    object-fit:contain;
    border-radius:10px;
    background:rgba(255,255,255,0.10);
    padding:6px;
}

/* PROFILE */
.top-profile{
    display:flex;
    align-items:center;
    gap:10px;
}
.profile-icon{
    background:rgba(255,255,255,0.24);
    padding:6px;
    border-radius:50%;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:.15s;
    color:white;
}
.profile-icon:hover{
    background:rgba(255,255,255,0.35);
    transform:translateY(-1px);
}
.profile-icon img{
    width:42px;
    height:42px;
    border-radius:50%;
    object-fit:cover;
    border:1px solid rgba(255,255,255,0.45);
}

/* DROPDOWN */
.dropdown-menu{
    border:none;
    border-radius:14px;
    min-width:260px;
    box-shadow:0 12px 30px rgba(0,0,0,.18);
}
.dropdown-user{
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px 18px;
}
.dropdown-user img{
    width:48px;
    height:48px;
    border-radius:50%;
    object-fit:cover;
}
.dropdown-item{
    padding:10px 18px;
    display:flex;
    align-items:center;
    gap:10px;
}
.dropdown-item:hover{
    background:rgba(0,0,0,.05);
    color:#000;
}
.dropdown-item.text-danger:hover{
    background:rgba(220,53,69,.1);
    color:#dc3545;
}

/* MENU */
.nav-bottom{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:10px 0 12px;
    border-top:1px solid rgba(255,255,255,0.20);
}
.menu-left{
    display:flex;
    align-items:center;
    gap:6px;
}
.menu-left .nav-link{
    color:white !important;
    font-weight:600;
    padding:8px 14px 12px;
    border-radius:8px;
    transition:.15s;
    position:relative;
    white-space:nowrap;
}
.menu-left .nav-link:hover{
    background:rgba(255,255,255,.18);
    transform:translateY(-2px);
}
.menu-left .nav-link::after{
    content:"";
    position:absolute;
    left:50%;
    transform:translateX(-50%);
    bottom:4px;
    width:0;
    height:3px;
    background:white;
    border-radius:6px;
    opacity:0;
    transition:.2s;
}
.menu-left .nav-link:hover::after,
.menu-left .nav-link.active::after{
    width:56%;
    opacity:1;
    bottom:6px;
}

/* FOOTER */
.footer-new{
    background:var(--main-green);
    color:white;
    padding:18px 0;
    margin-top:auto;
    text-align:center;
    font-weight:500;
}
</style>

@stack('head')
</head>

<body>
@php
    use App\Models\Kelas;
    use Illuminate\Support\Facades\Storage;

    $kelasList = Kelas::orderBy('nama_kelas')->get();
    $user = auth()->user();
    $firstKelasId = $kelasList->first()->id_kelas ?? null;

    $foto = ($user->foto && Storage::disk('public')->exists($user->foto))
        ? asset('storage/'.$user->foto)
        : null;
@endphp

<header class="sticky-top">
<div class="navbar-wrapper">
    <div class="container-fluid">

        {{-- TOP --}}
        <div class="nav-top">
            <a class="brand" href="{{ route('guru.dashboard') }}">
                <img src="/image/logo_ma.png">
                <div>
                    <div class="fw-bold">System Presensi</div>
                    <small>MA NURUL IMAN</small>
                </div>
            </a>
            {{-- PROFILE --}}
            <div class="top-profile">
                <div class="dropdown">
                    <a class="profile-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        @if($foto)
                            <img src="{{ $foto }}" alt="Foto Profil">
                        @else
                            <i class="bi bi-person"></i>
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-user">
                            @if($foto)
                                <img src="{{ $foto }}" alt="Foto Profil">
                            @else
                                <div class="profile-icon text-dark">
                                    <i class="bi bi-person"></i>
                                </div>
                            @endif
                            <div>
                                <strong>{{ $user->nama ?? 'Guru' }}</strong><br>
                                <small>{{ $user->email ?? '-' }}</small>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-pencil-square"></i> Edit Profil
                            </a>
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- MENU --}}
        <div class="nav-bottom">
            <nav class="menu-left">
                <a class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}"
                   href="{{ route('guru.dashboard') }}">Home</a>

                <a class="nav-link {{ request()->routeIs('guru.jadwal.index') ? 'active' : '' }}"
                   href="{{ route('guru.jadwal.index') }}">Kelas</a>

                @if($firstKelasId)
                    <a class="nav-link {{ request()->routeIs('guru.absensi.rekap') ? 'active' : '' }}"
                       href="{{ route('guru.absensi.rekap', ['id_kelas'=>$firstKelasId,'year'=>now()->year,'month'=>now()->month]) }}">
                       Rekap Absensi
                    </a>
                @else
                    <a class="nav-link disabled">Rekap Absensi</a>
                @endif
            </nav>
        </div>

    </div>
</div>
</header>

<main class="container-fluid" style="max-width:1200px;padding:30px 15px 40px;">
    @yield('content')
</main>

<footer class="footer-new">
    &copy; {{ date('Y') }} MA NURUL IMAN — Sistem Absensi
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
