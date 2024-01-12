@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Your Profile'])
    <div class="container-fluid px-0 px-2 px-sm-4 py-4">
        <div class="row justify-content-center">
            <div class="card card-body p-0" style="max-width: 32rem">
                <div class="text-sm text-uppercase p-3">PENGATURAN APLIKASI</div>
                <div class="list-group list-group-flush">
                    <a href="{{ url('/admin/settings/absen') }}"
                        class="list-group-item p-3 list-group-item-action d-flex align-items-center justify-content-between">
                        Absensi
                        <i class="fa-solid fa-chevron-right fa-xs "></i>
                    </a>
                    <a href="{{ url('/admin/settings/wilayah') }}"
                        class="list-group-item p-3 list-group-item-action d-flex align-items-center justify-content-between">
                        Wilayah
                        <i class="fa-solid fa-chevron-right fa-xs "></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
@endsection


@push('js')
@endpush
