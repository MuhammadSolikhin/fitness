<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center justify-content-center">
        <img src="{{ asset('assets/Foto/Logo Kurnia.png') }}" alt="Logo Kurnia" class="brand-image"
            style="opacity: .8; width: 100%; height: auto; max-height: 70px; object-fit: contain;">
    </a>


    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route(auth()->user()->role . '.dashboard') }}"
                        class="nav-link {{ request()->routeIs(auth()->user()->role . '.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @if(auth()->user()->role == 'admin')
                    <!-- Menu untuk Admin -->
                    <li class="nav-item">
                        <a href="{{ route('admin.classes.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-chalkboard"></i>
                            <p>Manajemen Kelas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.schedules.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Manajemen Jadwal</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview {{ request()->routeIs('admin.payments.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>
                                Pembayaran
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.payments.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Verifikasi Pembayaran</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.payments.history') }}"
                                    class="nav-link {{ request()->routeIs('admin.payments.history') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>History Pembayaran</p>
                                </a>
                            </li>
                        </ul>
                    </li>


                @elseif(auth()->user()->role == 'pelatih')
                    <!-- Menu untuk Pelatih -->
                    <li class="nav-item">
                        <a href="{{ route('pelatih.kelas-saya') }}" class="nav-link">
                            <i class="nav-icon fas fa-chalkboard-teacher"></i>
                            <p>Kelas Saya</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pelatih.jadwal') }}" class="nav-link">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Jadwal Mengajar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pelatih.payments.index') }}"
                            class="nav-link {{ request()->routeIs('pelatih.payments.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>Manajemen Pembayaran</p>
                        </a>
                    </li>

                @elseif(auth()->user()->role == 'user')
                    <!-- Menu untuk User -->
                    <li class="nav-item">
                        <a href="{{ route('user.classes.available') }}"
                            class="nav-link {{ request()->routeIs('user.classes.available') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Kelas Tersedia</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.classes.my') }}"
                            class="nav-link {{ request()->routeIs('user.classes.my') || request()->routeIs('user.classes.show') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-graduate"></i>
                            <p>Kelas Saya</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.membership') }}"
                            class="nav-link {{ request()->routeIs('user.membership') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>Membership</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.payments.history') }}"
                            class="nav-link {{ request()->routeIs('user.payments.history') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-receipt"></i>
                            <p>Riwayat Pembayaran</p>
                        </a>
                    </li>

                @endif

                <li class="nav-item">
                    <a href="#" class="nav-link" id="logoutButton">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>

                    <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display: none;">
                        @csrf
                    </form>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>