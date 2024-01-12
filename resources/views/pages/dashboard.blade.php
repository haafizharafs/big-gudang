@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])
    <div class="container-fluid px-0 px-2 px-sm-4 py-4">
        <div class="d-flex gap-4 flex-column-reverse flex-sm-row">
            <div class="w-100">
                <div class="d-flex flex-column gap-3" id="aktivitasContainer">
                    <div class="card card-body p-3 placeholder-glow d-flex flex-column gap-1 aktivitas-loader w-100">
                        <div class="d-flex gap-2 mb-2">
                            <div class="rounded-3 placeholder d-inline-block" style="height:35px;width:35px"></div>
                            <div class="d-flex flex-column gap-1 flex-grow-1">
                                <div class="lh-sm placeholder col-6"></div>
                                <div class="lh-sm placeholder col-5 placeholder-sm"></div>
                            </div>
                        </div>

                        <div class="rounded-3 placeholder" style="height: 20rem"></div>
                        <div class="lh-1 mt-2 placeholder col-12"></div>
                        <div class="lh-1 placeholder col-12"></div>
                        <div class="lh-1 placeholder col-12"></div>
                        <div class="lh-1 placeholder col-6"></div>
                    </div>
                    <div class="card card-body p-3 placeholder-glow d-flex flex-column gap-1 aktivitas-loader">
                        <div class="d-flex gap-2 mb-2">
                            <div class="rounded-3 placeholder d-inline-block" style="height:35px;width:35px"></div>
                            <div class="d-flex flex-column gap-1 flex-grow-1">
                                <div class="lh-sm placeholder col-6"></div>
                                <div class="lh-sm placeholder col-5 placeholder-sm"></div>
                            </div>
                        </div>

                        <div class="rounded-3 placeholder" style="height: 20rem"></div>
                        <div class="lh-1 mt-2 placeholder col-12"></div>
                        <div class="lh-1 placeholder col-12"></div>
                        <div class="lh-1 placeholder col-12"></div>
                        <div class="lh-1 placeholder col-6"></div>
                    </div>
                </div>
            </div>
            <div class="w-100">
                <div class="card card-body p-0 overflow-hidden">
                    <div class="list-group list-group-flush" id="recapContainer">
                        <div class="list-group-item p-3 placeholder-glow"><div class="placeholder placeholder-lg w-100"></div></div>
                        <div class="list-group-item p-3 placeholder-glow"><div class="placeholder placeholder-lg w-100"></div></div>
                        <div class="list-group-item p-3 placeholder-glow"><div class="placeholder placeholder-lg w-100"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const aktivitasContainer = document.getElementById('aktivitasContainer')
        const recapContainer = document.getElementById('recapContainer')

        document.addEventListener('DOMContentLoaded', () => {
            loadAktivitas()
            loadRecap()
        });

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
                <div class="d-flex align-items-center mb-2">
                    <div class="d-flex  align-items-center gap-2">
                        <img class="rounded-3" onclick="previewOnLightbox(this)"
                            src="${appUrl}/storage/${data.foto_profil}"
                            alt="foto profil" height="35">
                        <div class="d-flex flex-column">
                            <div class="lh-sm">${data.nama}</div>
                            <div class="lh-sm text-xs ">${data.speciality}</div>
                        </div>
                    </div>
                </div>
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
            axios.get(`${appUrl}/api/dashboard/aktivitas?offset=${tryingFetch}`)
                .then(response => {
                    tryingFetch++;
                    [...document.querySelectorAll('.aktivitas-loader')].map(loader => {
                        loader.remove()
                    })
                    if (response.data.length == 0) {
                        if(tryingFetch == 1){
                            aktivitasContainer.insertAdjacentHTML('beforeend', aktivitasEmpty)
                            return;
                        }else{
                            return;
                        }
                    }
                    beenUnder = false;
                    response.data.forEach(data => {
                        aktivitasContainer.insertAdjacentHTML('beforeend', aktivitasCard(data))
                    })
                })
        }
        const recapsEl = (data) =>{
            let recapCollapseItem = '';
            data.users.forEach(user =>{
                recapCollapseItem += `<li class="list-group-item">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <img class="rounded-3" onclick="previewOnLightbox(this)" src="${appUrl}/storage/${user.foto_profil}" alt="foto profil" height="35">
                            <div class="lh-sm">${user.nama}</div>
                        </div>
                        <div class="d-flex ms-auto">
                            <div class="form-check"><input type="checkbox" class="form-check-input" ${user.aktivitass[0] != '' ? 'checked' : ''}></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" ${user.aktivitass[1] != '' ? 'checked' : ''}></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" ${user.aktivitass[2] != '' ? 'checked' : ''}></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" ${user.aktivitass[3] != '' ? 'checked' : ''}></div>
                        </div>
                    </div>
                </li>`
            })

            return `<div class="list-group-item-action cursor-pointer p-3 d-flex text-uppercase text-sm" data-bs-toggle="collapse" data-bs-target="#${data.nama_wilayah}Collapse">
                ${data.nama_wilayah} ${data.hadir}/${data.all} Hadir
                <span class="ms-auto"><i class="fa-solid fa-chevron-down" style="transition: 300ms"></i></span>
            </div>
            <div class="collapse ${window.innerWidth >= 576 ? 'show' : ''}" id="${data.nama_wilayah}Collapse">
                <ul class="list-group list-group-flush">
                    ${recapCollapseItem}
                </ul>
            </div>`
            }

        function loadRecap() {
            axios.get(`${appUrl}/api/dashboard/recap`)
            .then(response => {
                recapContainer.innerHTML = ''
                response.data.forEach(data => {
                    recapContainer.insertAdjacentHTML('beforeend',recapsEl(data))
                });
            })
        }
    </script>

@endpush
