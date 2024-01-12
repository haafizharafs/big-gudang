<aside
    class="sidenav overflow-hidden bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ url('dashboard') }}" target="_blank">
            <img src="{{ asset('img/logos/big-warna.png') }}" class="navbar-brand-img h-100" alt="main_logo" />
            <span class="ms-1 font-weight-bold">BIG Net Manajemen</span>
        </a>
    </div>
    <hr class="horizontal dark my-0" />
    <div class="collapse navbar-collapse position-relative w-100" id="sidenav-collapse-main">
        <ul class="navbar-nav d-flex py-2 flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('*dashboard*') ? 'active' : '' }}" href="{{ url('dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            @if (auth()->user()->role == 1)
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">
                        Absensi
                    </h6>
                </li>
            @endif

            <li class="nav-item">
                <a class="nav-link {{ request()->is('*karyawan/absen*') ? 'active' : '' }}"
                    href="{{ url('karyawan/absen') }}">
                    <div
                        class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-badge text-info text-sm opacity-10"></i>
                    </div>
                    Absen
                </a>
            </li>

            @if (auth()->user()->role == 1)
                <li class="nav-item nav-collapse">
                    <a class="nav-link {{ request()->is('*admin/absen*') ? 'active' : '' }} nav-collapse"
                        href="javascript:;" data-bs-target="#absensiKaryawanCollapse" data-bs-toggle="collapse">
                        <div
                            class="nav-collapse icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="nav-collapse ni ni-badge text-warning text-sm opacity-10"></i>
                        </div>
                        Absensi Karyawan
                    </a>
                    <div class="collapse {{ request()->is('*admin/absen*') ? 'show' : '' }} nav-collapse"
                        id="absensiKaryawanCollapse">
                        <ul class="navbar-nav nav-collapse d-flex ps-4 flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('*admin/absen') ? 'active' : '' }}"
                                    href="{{ url('admin/absen') }}">
                                    <div
                                        class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="ni ni-fat-delete text-muted text-sm opacity-10"></i>
                                    </div>
                                    Rekap Harian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('*admin/absen/bulanan') ? 'active' : '' }}"
                                    href="{{ url('admin/absen/bulanan') }}">
                                    <div
                                        class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="ni ni-fat-delete text-muted text-sm opacity-10"></i>
                                    </div>
                                    Rekap Bulanan
                                </a>
                            </li>
                        </ul>
                    </div>

                </li>



                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">
                        Kelola pengguna
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('*admin/karyawan') ? 'active' : '' }}"
                        href="{{ url('admin/karyawan') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-single-02 text-warning text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Karyawan</span>
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">
                        Kelola Gudang
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('*admin/gudang') ? 'active' : '' }}"
                        href="{{ url('admin/gudang') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-box-2 text-secondary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Gudang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('*admin/barang') ? 'active' : '' }}"
                        href="{{ url('admin/barang') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-bag-17 text-success text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Barang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('*admin/mutasi') ? 'active' : '' }}"
                        href="{{ url('admin/mutasi') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-archive-2 text-info text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Mutasi</span>
                    </a>
                </li>


                <hr class="horizontal dark my-3" />

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('*admin/settings') ? 'active' : '' }}"
                        href="{{ url('admin/settings') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-settings-gear-65 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Pengaturan</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</aside>
