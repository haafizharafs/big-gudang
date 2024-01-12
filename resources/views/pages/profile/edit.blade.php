@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/Cropper/cropper.min.css') }}">
@endpush
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Your Profile'])
    <div class="container-fluid d-flex justify-content-center px-0 px-2 px-sm-4 py-4">
        <div class="w-sm-50 w-100" style="max-width: 32rem">
            <div class="card card-body p-0">
                <div class="text-sm text-uppercase p-3">PENGATURAN AKUN</div>
                <div class="d-flex px-3 justify-content-center">
                    <div class="card card-body p-0 position-relative d-inline-block flex-grow-0" style="width:  !important;">
                        <img onclick="previewOnLightbox(this)" src="{{ url('storage/' . auth()->user()->foto_profil) }}" class="rounded-3 foto_profil" style="max-width: 10rem; max-height:10rem">
                        <input type="file" class="d-none" id="foto" onchange="handleChange(this)">
                        <button class="btn btn-light btn-circle position-absolute"
                            style="bottom: -.5rem; right: -.5rem; width: 2.5rem !important; height: 2.5rem !important"
                            onclick="document.getElementById('foto').click()">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </div>
                </div>
                <div class="list-group p-3 " id="listGroupSettings">
                    <div class="list-group-item p-0">
                        <div class="d-flex text-sm align-items-center py-2 px-3 cursor-pointer text-uppercase"
                            data-bs-toggle="collapse" data-bs-target="#settingCollapse1">
                            INFORMASI PRIBADI
                            <i class="ms-auto fa-solid fa-chevron-down fa-sm"></i>
                        </div>
                        <div class="collapse" data-bs-parent="#listGroupSettings" id="settingCollapse1">
                            <div class="p-3">
                                <form onsubmit="event.preventDefault();handleSubmit(this)" novalidate data-context=""
                                    id="settingsForm1">
                                    <div class="form-group">
                                        <label for="nama">Nama Lengkap Tanpa Gelar</label>
                                        <input type="text" class="form-control" id="nama"
                                            value="{{ auth()->user()->nama }}" placeholder="cth: John Doe"
                                            autocomplete="name">
                                        <div class="invalid-feedback text-xs ps-2" id="namaFeedback"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" id="email"
                                            value="{{ auth()->user()->email }}" placeholder="cth: johndoe@big.com"
                                            autocomplete="email">
                                        <div class="invalid-feedback text-xs ps-2" id="emailFeedback"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_telp">Nomor Telepon/Whatsapp</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text">+</span>
                                            <input type="number" class="form-control" id="no_telp"
                                                placeholder="cth: 6281234567890" value="{{ auth()->user()->no_telp }}"
                                                autocomplete="tel">
                                            <div class="invalid-feedback text-xs ps-2" id="no_telpFeedback"></div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item p-0">
                        <div class="d-flex text-sm align-items-center py-2 px-3 cursor-pointer text-uppercase"
                            data-bs-toggle="collapse" data-bs-target="#settingCollapse2">
                            Ubah Password
                            <i class="ms-auto fa-solid fa-chevron-down fa-sm"></i>
                        </div>
                        <div class="collapse" data-bs-parent="#listGroupSettings" id="settingCollapse2">
                            <div class="p-3 pt-0">
                                <form onsubmit="event.preventDefault();handleSubmit(this)" novalidate id="settingsForm2"
                                    data-context="/change-password">
                                    <div class="form-group">
                                        <label for="Nama">Password lama</label>
                                        <div class="input-group has-validation">
                                            <input type="password" class="form-control" id="password_lama">
                                            <span onmousedown="event.preventDefault();showPassword(this)"
                                                class="input-group-text"><i class="fa-solid fa-eye"></i></span>
                                            <div class="invalid-feedback text-xs ps-2" id="password_lamaFeedback"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password baru</label>
                                        <div class="input-group has-validation">
                                            <input type="password" class="form-control" id="password">
                                            <span onmousedown="event.preventDefault();showPassword(this)"
                                                class="input-group-text"><i class="fa-solid fa-eye"></i></span>
                                            <div class="invalid-feedback text-xs ps-2" id="passwordFeedback"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                                        <div class="input-group has-validation">
                                            <input type="password" class="form-control" id="password_confirmation">
                                            <span onmousedown="event.preventDefault();showPassword(this)"
                                                class="input-group-text"><i class="fa-solid fa-eye"></i></span>
                                            <div class="invalid-feedback text-xs ps-2" id="password_confirmationFeedback">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('modal')
    <div class="modal fade" id="CropModal" tabindex="-1" aria-labelledby="CropModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down ">
            <div class="modal-content">
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="handleCrop(this)">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endpush
@push('js')
    <script>
        const fotoProfilsEl = document.querySelectorAll('.foto_profil')
        const fotoEl = document.getElementById('foto')
        const CropModalEl = document.getElementById('CropModal')
        const CropModalBodyEl = CropModalEl.querySelector('.modal-body')
        let CropModal, cropper;

        document.addEventListener('DOMContentLoaded', () => {
            CropModal = new bootstrap.Modal(CropModalEl);
        })


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

        function handleSubmit(el) {
            const settingInputs = el.querySelectorAll('input, textarea, select');
            const settingBtn = el.querySelector('button');
            settingBtn.disabled = true
            settingBtn.innerHTML = '<i class="fa-solid fa-spin fa-spinner me-1"></i>Loading...'
            let data = {}
            settingInputs.forEach(input => {
                data[input.id] = input.value
            })
            axios.patch(`${appUrl}/api/profile${el.dataset.context}`, data)
                .then(response => {
                    Success.fire(response.data.message)
                    resetValidationInputs(('#' + el.id))
                })
                .catch(error => {
                    if (error.response.status == 422) {
                        const errors = error.response.data.errors
                        resetValidationInputs(('#' + el.id))
                        Object.keys(errors).forEach(key => {
                            invalidateInput(key, errors[key])
                        });
                    } else {
                        Failed.fire(error.response.data.message)
                    }
                })
                .finally(() => {
                    settingBtn.disabled = false
                    settingBtn.innerHTML = 'Simpan'
                })
        }
    </script>
@endpush
