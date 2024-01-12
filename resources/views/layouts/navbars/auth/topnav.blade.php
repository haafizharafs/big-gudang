<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg  px-0 px-sm-5  sticky-top  pt-0 pt-sm-3 pb-0 shadow-none"
    style="z-index:999">
    <div class="container-fluid d-block py-2 border-radius-xl shadow-sm"
        style="background-color: #f5365cdd; backdrop-filter: blur(3px)">
        <div class="d-flex align-items-center justify-content-between flex-wrap w-100">

            @include('components.breadcrumbs')

            <div class="navbar-collapse flex-shrink-0" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                </div>
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center" id="iconNavbarSidenav">
                        <span style="cursor: pointer;" class="nav-link text-white p-0 me-3">
                            <div class="sidenav-toggler-inner nav-toggler">
                                <i class="sidenav-toggler-line bg-white nav-toggler"></i>
                                <i class="sidenav-toggler-line bg-white nav-toggler"></i>
                                <i class="sidenav-toggler-line bg-white nav-toggler"></i>
                            </div>
                        </span>
                    </li>
                    <li class="nav-item align-self-center d-none d-sm-block">
                        <span class="text-white me-2">{{ auth()->user()->nama }}</span>
                    </li>

                    <li class="nav-item dropdown d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-white p-0" id="dropdownProfile"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" class="avatar avatar-sm foto_profil"
                                alt="foto profil">
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end  p-2 me-sm-n4" aria-labelledby="dropdownProfile">
                            <li>
                                <a class="dropdown-item border-radius-md" href="{{ url('profile') }}">
                                    <div class="d-flex py-1">
                                        <div class="my-auto">
                                            <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"class="avatar avatar-sm  me-3 ">
                                        </div>
                                        <h6 class="text-sm font-weight-normal align-self-center m-0">
                                            Lihat Profil
                                        </h6>
                                    </div>
                                </a>
                            </li>
                            <hr class="horizontal dark my-2">
                            <li>
                                <form class="m-0" role="form" method="post" action="{{ url('logout') }}"
                                    id="logout-form">
                                    @csrf
                                    <span id="btn-logout" class="dropdown-item border-radius-md text-danger"
                                        type="submit">
                                        <div class="d-flex py-1 align-items-center ">
                                            <i class="fa-solid fa-right-from-bracket me-3"></i>
                                            <h6 class="text-sm font-weight-normal mb-1 ">
                                                Keluar
                                            </h6>
                                        </div>
                                    </span>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
