@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/Cropper/cropper.min.css') }}">
@endpush
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Your Profile'])
    <div class="container-fluid px-0 px-2 px-sm-4 py-4">
        <div class="d-flex flex-sm-row flex-column-reverse gap-4 align-items-start">
            <div id="aktivitasContainer" class="w-100 d-flex flex-column gap-4">
                <div class="card card-body p-3 placeholder-glow d-flex flex-column gap-1 aktivitas-loader w-100">
                    <div class="rounded-3 placeholder" style="height: 20rem"></div>
                    <div class="lh-1 mt-2 placeholder col-12"></div>
                    <div class="lh-1 placeholder col-12"></div>
                    <div class="lh-1 placeholder col-12"></div>
                    <div class="lh-1 placeholder col-6"></div>
                </div>
                <div class="card card-body p-3 placeholder-glow d-flex flex-column gap-1 aktivitas-loader w-100">
                    <div class="rounded-3 placeholder" style="height: 20rem"></div>
                    <div class="lh-1 mt-2 placeholder col-12"></div>
                    <div class="lh-1 placeholder col-12"></div>
                    <div class="lh-1 placeholder col-12"></div>
                    <div class="lh-1 placeholder col-6"></div>
                </div>
            </div>

            <div class="card card-body w-100">
                <div class="d-flex gap-2 align-items-center flex-column">
                    <img onclick="previewOnLightbox(this)" src="{{ url('storage/' . auth()->user()->foto_profil) }}"
                        class="rounded-3" style="max-width: 10rem;max-height:10rem">
                    <h4 class="m-0 lh-1 text-center">
                        {{ auth()->user()->nama }}
                    </h4>
                    <div class="">
                        <span class="badge bg-gradient-primary">
                            {{ App\Models\Wilayah::find(auth()->user()->wilayah_id)->nama_wilayah }}
                        </span>
                        <span class="text-muted text-sm">
                            ({{ auth()->user()->speciality }})
                        </span>
                    </div>
                    <div class="d-flex gap-3 mt-1">
                        <a href="https://wa.me/{{ auth()->user()->no_telp }}" class="text-sm">
                            <i class="fa-solid fa-mobile-screen me-1"></i>{{ auth()->user()->no_telp }}
                        </a>
                        <a href="mailto:/{{ auth()->user()->email }}" class="text-sm">
                            <i class="fa-solid fa-envelope me-1"></i>{{ auth()->user()->email }}
                        </a>
                    </div>
                    <a href="{{ url('profile/edit') }}" class="btn btn-light">
                        <i class="fa-solid fa-gear me-1"></i>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        const aktivitasContainer = document.getElementById('aktivitasContainer')
        document.addEventListener('DOMContentLoaded', () => {
            loadAktivitas()
        })

        let beenUnder = false;
        document.querySelector('.main-content').addEventListener('scroll', e => {
            if (e.target.scrollTop + e.target.offsetHeight >= (e.target.scrollHeight - 100) && !beenUnder) {
                beenUnder = true;
                loadAktivitas()
            }
        });

        const aktivitasLoader = `<span class="btn-circle bg-white align-self-center aktivitas-loader">
            <i class="fa-solid fa-spin fa-spinner fa-lg"></i>
        </span>`

        const aktivitasEmpty = `<div class="card card-body p-3 d-flex align-items-center justify-content-center" style="height:20rem">
            <span class="opacity-5"><i class="fa-solid fa-ghost fa-2xl"></i></span>
            <div class="opacity-5">Belum ada aktivitas</div>
        </div>`

        const aktivitasCard = (data) => {
            return ` <div class="card card-body p-3 d-flex flex-column gap-1">
                    <div class="img-trigger rounded-3" onclick="previewOnLightbox(this)" style="height: 20rem; background-image: url('${appUrl}/storage/${data.foto}')"></div>
                    <div class="lh-1 mt-2">${data.aktivitas}</div>
                    <div class="text-sm">
                        <i class="fa-regular fa-clock me-1"></i>
                        ${data.time}
                    </div>
                    <a href="https://www.google.com/maps/?q=${data.koordinat}" target="blank" class="text-sm">
                        <i class="fa-solid fa-location-dot me-1"></i>
                        ${data.koordinat}
                    </a>
                    <div class="text-sm">
                        ${data.alamat}
                    </div>
                </div>`
        }

        let tryingFetch = 0;

        function loadAktivitas() {
            if (tryingFetch > 0) aktivitasContainer.insertAdjacentHTML('beforeend', aktivitasLoader)
            axios.get(`${appUrl}/api/profile?offset=${tryingFetch}`)
                .then(response => {
                    tryingFetch++;
                    [...document.querySelectorAll('.aktivitas-loader')].map(loader => {
                        loader.remove()
                    })
                    if (response.data.length == 0) {
                        if (tryingFetch == 1) {
                            aktivitasContainer.insertAdjacentHTML('beforeend', aktivitasEmpty)
                            return;
                        } else {
                            return;
                        }
                    }
                    beenUnder = false;
                    response.data.forEach(data => {
                        aktivitasContainer.insertAdjacentHTML('beforeend', aktivitasCard(data))
                    })
                })
        }

        CropModalEl.addEventListener('shown.bs.modal', () => {
            const reader = new FileReader();
            reader.onload = e => {
                const image = document.getElementById('image')
                image.src = e.target.result
                cropper = new Cropper(image, {
                    aspectRatio: 1 / 1,
                    viewMode: 3,
                    minContainerWidth: document.querySelector('#imageContainer').offsetWidth,
                    minContainerHeight: document.querySelector('#imageContainer').offsetHeight,
                    minCanvasWidth: document.querySelector('#imageContainer').offsetWidth,
                    minCanvasHeight: document.querySelector('#imageContainer').offsetHeight,
                    autoCropArea: 1,
                });
            }
            reader.readAsDataURL(fotoEl.files[0])
        })

        CropModalEl.addEventListener('hidden.bs.modal', () => {
            fotoEl.value = null
        })


        function handleChange(el) {
            CropModalBodyEl.innerHTML = `<div id="imageContainer">
                <img src="" id="image" class="d-block" style="max-width: 100%">
            </div>`
            CropModal.show()
        }

        function handleCrop(el) {
            const tempBtnText = el.innerHTML
            el.innerHTML = '<i class="fa-solid fa-spin fa-spinner me-1"></i>Memproses gambar...'
            el.disabled = true;
            const croppedCanvas = cropper.getCroppedCanvas();
            croppedCanvas.toBlob((blob) => {
                const data = new FormData();
                data.append('file', blob, 'cropped-image.png');
                axios.post(`${appUrl}/api/profile/change-picture`, data)
                    .then((response) => {
                        Success.fire(response.data.message)
                        fotoProfilsEl.forEach((fotoProfil) => {
                            fotoProfil.src = `${appUrl}/storage/${response.data.foto_profil}`
                        })
                        CropModal.hide();
                    })
                    .catch((error) => {
                        Failed.fire(error.response.data.message)
                    })
                    .finally(() => {
                        el.innerHTML = tempBtnText
                        el.disabled = false;
                    });
            });
        }
    </script>
@endpush
