@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/ijaboCropTool/ijaboCropTool.min.css') }}">
@endpush
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Your Profile'])


    <div class="container-fluid py-4 ">
        <div class="card shadow-lg mb-4 card-profile-bottom">
            <div class="card-body p-3 pe-4">
                <div class="d-flex gap-4 align-items-center">
                    <div class="avatar avatar-xl position-relative">
                        <img src="{{ route('storage.private', $user->foto_profil) }}" alt="profile_image"
                            class="w-100 border-radius-lg shadow-sm img-previewer">
                    </div>
                    <div class="w-100">
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="m-0">
                                {{ $user->nama }}
                            </h5>
                            <span
                                class="badge bg-gradient-primary align-self-start ms-auto">{{ App\Models\Wilayah::find($user->wilayah_id)->nama_wilayah }}</span>
                        </div>
                        <p class="mb-0 font-weight-bold text-sm">
                            {{ $user->speciality }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
        <div class="row flex-column-reverse flex-md-row">
            <div class="mb-4 col-md-7 ">
                <div class="card">
                    <div class="card-body">
                        <div class="p-0 m-0">
                            <div class="d-flex align-items-center">
                                <div class="text-uppercase text-sm">Aktivitas</div>
                            </div>
                            <div class="aktivitas-body"></div>
                            <div class="pt-3 spinner">
                                <i class="fa-solid fa-spinner fa-spin me-2"></i>
                                Mendapatkan data...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4 col-md-5">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex mb-3 align-items-center">
                            <div class="text-uppercase text-sm">
                                Informasi Akun
                            </div>

                        </div>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class="small text-muted">Nama</span>
                                <div class="fw-bold">{{$user->nama}}</div>
                            </li>
                            <li class="list-group-item">
                                <span class="small text-muted">Jabatan</span>
                                <div class="fw-bold">{{$user->speciality}}</div>
                            </li>
                            <li class="list-group-item">
                                <span class="small text-muted">No. Telp/Whatsapp</span>
                                <div class="fw-bold">
                                    {{str_replace('62','0',$user->no_telp)}}
                                    <span style="cursor: pointer" class="text-primary ms-1" onclick="copyText('body','Nomor','{{str_replace('62','0',$user->no_telp)}}')">
                                        <i class="fas fa-copy"></i>
                                    </span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <span class="small text-muted">Email</span>
                                <div class="fw-bold">
                                    {{$user->email}}
                                    <span style="cursor: pointer" class="text-primary ms-1" onclick="copyText('body','Nomor','{{$user->email}}')">
                                        <i class="fas fa-copy"></i>
                                    </span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <span class="small text-muted">Wilayah</span>
                                <div class="fw-bold">{{$user->wilayah->nama_wilayah}}</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('layouts.footers.auth.footer')
    </div>
    @push('modal')
        @include('components.lightbox.modal')
    @endpush
@endsection


@push('js')
    <script>
        let last = 18446744073709551615;
        let isReachBottom = true;
        let percobaan = 0;
        $(document).ready(() => {
            $('#ijabo-cropper-cropBtn').addClass('btn');
            drawActivity()
        })

        function drawActivity() {
            $('.spinner').removeClass('d-none')
            axios.get(`${appUrl}/api/profile/${last}`)
                .then(response => {
                    data = response.data
                    if (data.length > 0) {
                        data.forEach((e, i) => {
                            isReachBottom = false
                            $('.aktivitas-body').append(`<div id="aktivitas${e.id}" class="aktivitas-item py-3 border-bottom ">
                                <div>${e.aktivitas}</div>
                                <div class="text-xs">${e.dateFormat}</div>
                                <div class="text-xs mb-2"><i class="fas fa-location-dot me-1"></i> ${e.alamat}</div>
                                <img class="rounded-3 w-100 " src="${appUrl}/storage/private/${e.foto}" alt="foto aktivitas" style="cursor: pointer;" onclick="$('#lightbox img').attr('src',this.src); $('#lightbox').modal('show')">
                            </div>`)
                            last = e.id
                        })
                    } else {
                        isReachBottom = true
                        $('.aktivitas-body').append(`<div class="pt-3">Tidak ada data lagi</div>`)
                    }
                })
                .catch(error => {
                    percobaan++;
                    if (percobaan < 5) {
                        drawActivity()
                    } else {
                        $('.aktivitas-body').append(`<div class="pt-3 text-danger">Gagal mendapatkan data</div>`)
                    }
                })
                .finally(() => {
                    $('.spinner').addClass('d-none')
                })
        }



        $('.main-content').scroll(function() {
            if (!isReachBottom) {
                if ($(`#aktivitas${last}`).isInViewport()) {
                    isReachBottom = true
                    drawActivity()
                }
            }
        });
    </script>
@endpush
