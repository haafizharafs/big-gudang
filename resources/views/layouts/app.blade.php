<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}" />
    <link rel="icon" type="image/png" href="{{ asset('img/logos/big-warna.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>BIG Man</title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Data Tables -->
    <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>

    <!-- Font Awesome CSS -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>

    <!-- CSS Files -->

    {{-- <link href="{{ asset('build/assets/argon-dashboard-bef17899.css') }}" rel="stylesheet" /> --}}
    @vite(['resources/scss/argon-dashboard.scss', 'resources/js/app.js'])
    <style>
        .navbar-vertical.navbar-expand-xs .navbar-collapse {
            height: calc(100% - 32px - 24px - 24px - 1px) !important;
        }
    </style>
    @stack('css')
</head>

<body class="{{ $class ?? '' }}">
    @guest
        @yield('content')
    @endguest
    @auth

        <div class="min-height-300 bg-danger position-fixed top-0 w-100"></div>

        <div class="position-relative overflow-hidden d-flex vh-100 w-100">
            @include('layouts.navbars.auth.sidenav')
            <main class="position-relative main-content overflow-auto w-100">
                @yield('content')
                @include('layouts.footers.auth.footer')
            </main>

        </div>
        <button id="btn-to-top"
            class="btn btn-dark position-fixed opacity-5 d-flex align-items-center justify-content-center"
            style="z-index: 100; bottom: -6rem;right: 4rem;width: 3rem;height: 3rem;"><i
                class="fa-solid fa-chevron-up"></i></button>
    @endauth
    @stack('modal')

    {{-- <script src="{{ asset('build/assets/app-dca2c067.js ') }}"></script> --}}

    <script src="{{ asset('assets/plugins/Buttons/buttons.js') }}"></script>
    <script>
        // global variables
        const appUrl = "{{ env('APP_URL') }}";
        @auth
        const authUserId = '{{ auth()->user()->id }}';
        @endauth

        // global function
        // $('.main-content').on('scroll', e => {
        //     if (e.target.scrollTop > 300) {
        //         document.getElementById('btn-to-top').style.bottom = "4rem";
        //     } else {
        //         document.getElementById('btn-to-top').style.bottom = "-6rem";
        //     }
        // });

        // $('#btn-to-top').on('click', () => {
        //     $('.main-content')[0].scrollTop = 0
        // })


        function debounce(func, delay) {
            let timeoutId;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(context, args);
                }, delay);
            };
        }

        function resetValidationInputs(parent) {
            document.querySelector(parent).querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
                if (!el) return
                if (el.type == 'file') el.previousElementSibling.classList.replace('border-danger', 'border-light')
            })
        }

        function invalidateInput(id, errors) {
            const el = document.getElementById(id)
            const fEl = document.getElementById(id + 'Feedback')
            el.classList.add('is-invalid');
            if (el.type == 'file') el.previousElementSibling.classList.replace('border-light', 'border-danger')
            fEl.innerHTML = errors;
        }

        function showPassword(e) {
            let isShow = e.previousElementSibling.getAttribute('type') == 'text'
            if (isShow) {
                e.previousElementSibling.setAttribute('type', 'password')
                e.innerHTML = '<i class="fa-solid fa-eye"></i>'
            } else {
                e.previousElementSibling.setAttribute('type', 'text')
                e.innerHTML = '<i class="fa-solid fa-eye-slash"></i>'
            }
        }

        function getRandomPassword(length) {
            const charset = "abcdefghijklmnopqrstuvwxyz0123456789";
            let password = "";
            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * charset.length);
                password += charset[randomIndex];
            }
            return password;
        }

        function getBadgeAbsen(Status) {
            let status, bg;
            switch (Status) {
                case 1:
                    status = 'Hadir';
                    bg = 'success';
                    break;
                case 2:
                    status = 'Terlambat';
                    bg = 'warning';
                    break;
                case 3:
                    status = 'Tidak Hadir';
                    bg = 'danger';
                    break;
                default:
                    status = 'Tidak Hadir';
                    bg = 'danger';
                    break;
            }
            return `<span class="badge bg-gradient-${bg}">
                ${status}
            </span>`;
        }


        function previewOnLightbox(el) {
            const link = el.tagName === 'IMG' ? el.src : el.style.backgroundImage.match(/url\("([^"]+)"\)/)[1];
            const lightboxModal = `<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true" style="z-index:1051" >
                <div style="max-width: unset" class="modal-dialog d-flex position-relative w-100 justify-content-center align-items-center h-100 p-0 m-0">
                    <div class="modal-content h-100 w-100 rounded-0 border-0"  style="background-color: #0008 !important">
                        <div class="modal-body border-0" data-bs-dismiss="modal" >
                            <a href="javascript:;"data-bs-dismiss="modal" class="ms-auto position-absolute" aria-label="Close" style="top: 1rem; right:1rem"><i class="fa-solid fa-xmark fa-xl" style="color:#fffa; text-shadow: 0 0 3px #000a;"></i></a>
                            <div class="w-100 h-100" style="
                            background-image: url('${link}');
                            background-repeat: no-repeat;
                            background-position: center center;
                            background-size:contain;"></div>
                        </div>
                    </div>
                </div>
            </div>`
            document.body.insertAdjacentHTML('beforeend', lightboxModal);
            const modalLightboxEl = document.getElementById('lightboxModal')
            const ModalLightbox = new bootstrap.Modal(modalLightboxEl);
            ModalLightbox.show()

            modalLightboxEl.addEventListener('hidden.bs.modal', e => {
                modalLightboxEl.remove()
            })
        }


        const imgUploaderTrigger = (id) => {
            return `<div id="btnImgUploader${id}" class="cursor-pointer card card-body bg-transparent d-flex border border-light align-items-center justify-content-center"
                style="height: 15rem" onclick="document.getElementById('${id}').click()">
                <div class=""><i class="fa-solid fa-camera fa-xl"></i></div>
                <div class="">Tambah Foto</div>
            </div>`
        }

        const imgUploaderPreview = (data) => {
            return ` <div class="position-relative" id="imgUploaderPreview${data.id}">
                <div class="img-trigger rounded-3 overflow-hidden" style="height: 15rem;background-image:url('${data.url}')" onclick="previewOnLightbox(this)"></div>
                <button type="button" class="btn btn-light position-absolute" style="bottom:.5rem;right:.5rem" onclick="document.getElementById('${data.id}').click()"><i class="fa-solid fa-pen-to-square me-1"></i>Ubah Foto</button>
            </div>`
        }

        const imgUploaderLoader = `<div class="img-processor cursor-pointer card card-body bg-transparent d-flex border border-light align-items-center justify-content-center" style="height: 15rem">
            <div class=""><i class="fa-solid fa-spin fa-spinner fa-xl"></i></div>
            <div class="">Memproses Gambar...</div>
        </div>`


        const imgUploader = (id, url = null) => {
            let element = url ? imgUploaderPreview({
                id: id,
                url: url
            }) : imgUploaderTrigger(id)
            return `<div class="form-group">
                ${element}

                <input type="file" id="${id}" class="d-none" accept="image/*" onchange="drawPreview(event.srcElement)">
                <div class="invalid-feedback text-xs" id="${id}Feedback"></div>
            </div>`
        }

        function drawPreview(el) {
            el.disabled = true
            if (document.getElementById('btnImgUploader' + el.id)) {
                document.getElementById('btnImgUploader' + el.id).remove()
            }
            if (document.getElementById('imgUploaderPreview' + el.id)) {
                document.getElementById('imgUploaderPreview' + el.id).remove()
            }
            el.insertAdjacentHTML('beforebegin', imgUploaderLoader)
            if (el.files && el.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    el.disabled = false
                    document.querySelector('.img-processor').remove()
                    const data = {
                        id: el.id,
                        url: e.target.result,
                    }
                    el.insertAdjacentHTML('beforebegin', imgUploaderPreview(data))
                }
                reader.readAsDataURL(el.files[0]);
            }
        }

        const geocodeErrMsg = (error) => {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    return 'Izinkan akses GPS.';
                case error.POSITION_UNAVAILABLE:
                    return 'Informasi GPS tidak tersedia.';
                case error.TIMEOUT:
                    return 'Waktu permintaan untuk mendapatkan lokasi pengguna habis.';
                case error.UNKNOWN_ERROR:
                    return 'Terjadi kesalahan yang tidak diketahui.';
            }
        }

        const aktivitassEl = (data = '') => {
            if (!data) {
                return `<div class="w-100 bg-light rounded-3 d-flex align-items-center justify-content-center">
                    <div class="text-sm text-muted">Tidak ada data absen</div>
            </div>`
            }
            return `<div class="w-100 border-bottom-md-0 border-bottom pb-3 pb-md-0">
                <div class="lightbox-trigger overflow-hidden rounded-3" onclick="previewOnLightbox(this)" style="background-image: url('${appUrl}/storage/${ data ?data.foto: "profile/dummy.png"}');">
                </div>
                <div class="form-check">
                    <input class="form-check-input" id="check${data.id}" type="checkbox"checked onclick="return false;">
                    <label class="form-check-label m-0" for="check${data.id}">${data.time}</label>
                </div>
                <div class="small mt-1">${data.aktivitas }</div>
                <a href="https://www.google.com/maps/?q=${data.koordinat}" target="blank" class="text-xs my-1 cursor-pointer"><i class="fa-solid fa-location-dot me-1"></i>${data.koordinat}</a>
                <div class="text-xs">${data.alamat }</div>
            </div>`;
        };
    </script>

    @stack('js')
    <!-- jQuery -->
    <script src='https://code.jquery.com/jquery-3.7.0.js'></script>
    <!-- Data Table JS -->
    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>
</body>

</html>
