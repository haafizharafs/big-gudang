@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@push('css')
    <style>
        th,
        td {
            width: unset !important;
        }

        th {
            padding-left: .25rem !important;
            padding-right: .25rem !important
        }

        td {
            vertical-align: middle;
            border: none;
        }

        tbody tr:not(.no-hover),
        tbody tr:not(.no-hover) * {
            cursor: pointer;
        }

        tr.no-hover:hover,
        tr.no-hover {
            --bs-table-hover-color: unset;
            --bs-table-hover-bg: transparent;
        }
    </style>
@endpush
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Absensi Karyawan'])
    <div class="container-fluid px-0 px-sm-4 py-3">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-1">
                <div class="d-flex align-items-sm-center flex-column flex-sm-row gap-1">
                    <h6 class="m-0 lh-1">Rekap Absen Tanggal</h6>
                    <input type="date" id="tanggal" class="form-control" style="width: unset" value="{{ date('Y-m-d') }}"
                        onchange="loadDaily()">
                </div>
                <button class="btn btn-light" onclick="exportDaily(this)">
                    <i class="fa-solid fa-file me-1"></i>
                    <span class="d-none d-sm-inline-block">Generate Report</span>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="px-4 d-flex gap-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="search" class="form-control" id="cariKaryawan" placeholder="Cari karyawan...">
                    </div>
                    <select id="dailyWilayah" class="form-select flex-grow-sm-0 flex-grow-1 pe-5" style="width: unset"
                        onchange="loadDaily()">
                        <option value="">Wilayah</option>
                        @foreach (\App\Models\Wilayah::all() as $wilayah)
                            <option value="{{ $wilayah->id }}">{{ $wilayah->nama_wilayah }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="table-responsive">
                    <table class="table table-hover m-0" id="table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th
                                    class="d-none d-md-table-cell text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pe-2">
                                    No.
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Karyawan
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    1
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    2
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    3
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    4
                                </th>
                                <th
                                    class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pe-4">
                                    <span class="">S</span>
                                    <span class="d-none d-sm-inline">tatus</span>
                                </th>
                            </tr>

                        </thead>
                        <tbody id="dailyContainer">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    <div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
            <div class="modal-content">

            </div>
        </div>
    </div>
@endpush
@push('js')
    <script>
        let detailsDaily = {};
        const AbsenSettedTime = @json(\App\Models\Absen::$settedTime);
        const AbsenMax = '{{ \App\Models\Absen::$max }}'

        const dailyContainer = document.getElementById('dailyContainer');
        const dailyDate = document.getElementById('tanggal');
        dailyDate.addEventListener('change', () => {
            detailsDaily = {}
            loadDaily()
        });

        const dailySearch = document.getElementById('cariKaryawan');
        dailySearch.addEventListener('input', debounce(loadDaily, 300));
        const dailyWilayah = document.getElementById('dailyWilayah');
        const modalContent = document.querySelector('#Modal .modal-content');

        let Modal;
        document.addEventListener('DOMContentLoaded', () => {
            Modal = new bootstrap.Modal(document.getElementById('Modal'))
            loadDaily();
        });

        const dailyLoader = `<tr>
            <td colspan="7" class="text-center">
                <i class="fa-solid fa-spin fa-spinner me-1"></i>
                Mendapatkan data...
            </td>
        </tr>`

        const dailyEmpty = `<tr>
            <td colspan="7" class="text-center">
                Tidak ada data
            </td>
        </tr>`

        const absenElement = time => {
            return `<td class="text-center">
                <div class="form-check p-0 justify-content-center flex-wrap m-0 d-inline-flex gap-2">
                    <input class="form-check-input m-0 " type="checkbox" onclick="return false" ${ time ? 'checked' : ''}>
                    ${time ? '<label class="custom-control-label m-0">'+time+'</label>':''}
                </div>
            </td>`
        }

        const dailyRow = (data, loop) => {
            let absenElements = '';
            data.absens.forEach(absen => {
                absenElements += absenElement(absen);
            });

            return `<tr onclick="showDetailDaily(${data.id})" data-bs-toggle="collapse" data-bs-target="#dailyDetail${data.id}">
                <td class="ps-4 d-none d-md-table-cell w-0 text-uppercase text-secondary text-xxs font-weight-bolder">
                    ${loop +1}.
                </td>
                <td class="ps-4">
                    <div class="d-inline-flex align-items-start align-items-sm-center flex-column flex-sm-row gap-2">
                        <img class="rounded-3" src="${appUrl}/storage/${data.foto_profil}" alt="foto profil" height="35">
                        <div style="white-space: initial" class="lh-sm text-sm">
                            ${data.nama}
                        </div>
                    </div>
                </td>
                ${absenElements}
                <td class="pe-4 text-end">
                    ${getBadgeAbsenR(data.status)}
                </td>
            </tr>
            <tr class="no-hover bg-light">
                <td colspan="7" class="p-0 border-bottom">
                    <div class="collapse" id="dailyDetail${data.id}" data-bs-parent="#dailyContainer">
                        <div class="p-4">
                            <div class="lightbox-trigger rounded-3 placeholder-glow overflow-hidden">
                                <div class="placeholder col-12 h-100"></div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>`
        }

        const dailyError = `<tr>
            <td colspan="7" class="text-center" onclick="loadDaily()">
                Tidak Dapat Memperoleh Data, Tekan Untuk Memuat Ulang!
            </td>
        </tr>`


        let tryingDaily = 0;
        let isDailyFetching = false;

        function loadDaily() {
            if (isDailyFetching) return;
            isDailyFetching = true;
            dailyContainer.innerHTML = dailyLoader;
            axios.get(
                    `${appUrl}/api/admin/absen?date=${dailyDate.value}&nama=${dailySearch.value}&wilayah=${dailyWilayah.value}`
                )
                .then(response => {
                    const data = response.data;
                    if (data.length > 0) {
                        let dailyRows = '';
                        data.forEach((data, loop) => {
                            dailyRows += dailyRow(data, loop);
                        });
                        dailyContainer.innerHTML = dailyRows;
                    } else {
                        dailyContainer.innerHTML = dailyEmpty;
                    }
                })
                .catch(error => {
                    console.error(error);
                    if (error.response.status === 500 && tryingDaily < 10) {
                        loadDaily()
                        tryingDaily++;
                    } else {
                        tryingDaily = 0;
                    }
                    dailyContainer.innerHTML = dailyError;
                })
                .finally(() => {
                    isDailyFetching = false;
                    console.log(detailsDaily);
                })
        }

        const detailDailyLoader = `<div class="lightbox-trigger rounded-3 placeholder-glow overflow-hidden">
            <div class="placeholder col-12 h-100"></div>
        </div>`

        const detailDailyEl = (data) => {
            let aktivitassEl = ''
            data.absen.aktivitass.forEach((aktivitas, i) => {
                const btnDelete = i == 0 ? '' :
                    ` <button class="btn btn-danger" onclick="deleteAktivitas(${aktivitas.id})"><i class="fa-solid fa-trash"></i></button>`
                if (!aktivitas) {
                    aktivitassEl += `<div data-bs-toggle="modal" data-bs-target="#Modal" class="card bg-transparent border border-2 card-body p-3 cursor-pointer align-self-stretch align-items-center justify-content-center text-center d-flex w-100" data-id="${data.absen.id}" data-nama="${data.nama}" data-xabsen="${i}" onclick="createAktivitas(this)">
                            <span><i class="fa-solid fa-plus-circle fa-xl"></i></span>
                            <div style="white-space:initial">Tekan Untuk Menambah Aktivitas</div>
                        </div>`
                } else {
                    aktivitassEl += `<div class="card card-body p-3 w-100">
                            <div class="d-flex gap-3 flex-column">
                                <div class="img-trigger w-100 rounded-3"
                                    style="background-image: url('${appUrl}/storage/${aktivitas.foto}');height: 15rem;"
                                    onclick="previewOnLightbox(this)"></div>
                                <div class="">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" checked onclick="return false;">
                                        <label class="form-check-label m-0">${aktivitas.time}</label>
                                    </div>
                                    <div class="small mt-1" style="white-space:initial">${aktivitas.aktivitas}</div>
                                    <a href="https://www.google.com/maps/?q=${aktivitas.koordinat}" target="blank" class="text-xs my-1 cursor-pointer" style="white-space: initial"><i class="fa-solid fa-location-dot me-1"></i>${aktivitas.koordinat}</a>
                                    <div class="text-xs" style="white-space: initial">${aktivitas.alamat}</div>
                                    <div class="text-end mt-3">
                                        ${btnDelete}
                                        <button class="btn btn-warning"
                                            data-id="${data.absen.id}"
                                            data-aktivitas_id="${aktivitas.id}"
                                            data-nama="${data.nama}"
                                            data-xabsen="${i}"
                                            data-foto="${appUrl}/storage/${aktivitas.foto}"
                                            data-created_at="${aktivitas.time}"
                                            data-aktivitas="${aktivitas.aktivitas}"
                                            data-koordinat="${aktivitas.koordinat}"
                                            data-alamat="${aktivitas.alamat}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#Modal"
                                            onclick="editAktivitas(this)"
                                            >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>`
                }
            });
            return `<div class="d-flex gap-2 justify-content-md-between align-items-center">
                <div class="d-none d-md-block text-uppercase">Detail Absen</div>
                <button class="btn btn-danger flex-grow-1 flex-md-grow-0" onclick="deleteAbsen(${data.absen.id})">Hapus</button>
            </div>
            <div class="d-flex flex-column flex-md-row gap-3 mt-3">
                ${aktivitassEl}
            </div>`
        }

        const detailDailyEmpty = (data) => {
            return `  <div class="d-flex gap-3">
                <div class="card bg-transparent border border-2 card-body p-3 cursor-pointer align-items-center justify-content-center text-center d-flex" style="height:15rem" data-id="${data.id}" data-nama="${data.nama}" onclick="createAbsenBatch(this)" data-bs-toggle="modal" data-bs-target="#Modal">
                    <span><i class="fa-solid fa-check-double fa-xl"></i></span>
                    <div style="white-space:initial">Tambah Absen (Batch)</div>
                </div>
                <div class="card bg-transparent border border-2 card-body p-3 cursor-pointer align-items-center justify-content-center text-center d-flex" style="height:15rem" data-id="${data.id}" data-nama="${data.nama}" onclick="createAbsen(this)" data-bs-toggle="modal" data-bs-target="#Modal">
                    <span><i class="fa-solid fa-check fa-xl"></i></span>
                    <div style="white-space:initial">Tambah Absen (Single)</div>
                </div>
            </div>`
        }

        const detailDailyError = `<div class="cursor-pointer rounded-3 d-flex align-items-center justify-content-center bg-white" style="height:15rem">
            Gagal Dalam Mendapatkan Data, Tekan Untuk Memuat Ulang!
        </div>`


        async function showDetailDaily(id) {
            const el = document.querySelector('#dailyDetail' + id + ' div')

            if (!(id in detailsDaily)) {
                try {
                    const result = await axios.get(`${appUrl}/api/admin/absen/${id}?date=${dailyDate.value}`)
                    detailsDaily[id] = result.data;
                } catch (error) {
                    console.error(error);
                    return;
                }
            }

            if (detailsDaily[id].absen) {
                el.innerHTML = detailDailyEl(detailsDaily[id]);
            } else {
                el.innerHTML = detailDailyEmpty(detailsDaily[id]);
            }
        }



        const absenLabel = ['Pertama', 'Kedua', 'Ketiga', 'Terakhir'];
        const infoWaktuEl = () => {
            let temp = '';
            AbsenSettedTime.forEach((time, i) => {
                if (i != AbsenSettedTime.length - 1) {
                    temp +=
                        `<div class="text-xs">Waktu Absen ${absenLabel[i]}: ${time} s/d ${AbsenSettedTime[i+1]}</div>`
                } else {
                    temp += `<div class="text-xs">Waktu Absen ${absenLabel[i]}: ${time} s/d ${AbsenMax}</div>`
                }
            });
            return temp;
        }

        const modalContentEl = (method, type, id, xabsen = 0, nama = '', data = null) => {
            return `<div class="modal-header">
                <div class="fw-bold m-0">${data ? 'Edit' : ''} Absen ${absenLabel[xabsen]} ${nama}</div>
            </div>
            <div class="modal-body">
                <form novalidate class="mt-3" onsubmit="event.preventDefault();handleSubmit('${method}','${type}',${data ? data.id : ''})">
                    <input type="hidden" id="id" value="${id}">
                    <input type="hidden" id="xabsen" value="${xabsen}">
                    ${imgUploader('foto',data?data.foto:null)}
                    <div class="form-group">
                        <label for="created_at">Waktu</label> <span class="cursor-pointer" data-bs-toggle="collapse" data-bs-target="#infoWaktuCollapse"><i class="fa-solid fa-circle-info"></i></span>
                        <div class="collapse" id="infoWaktuCollapse">
                            <div class="p-3 mb-3 rounded-3 border d-inline-block" style="width:unset">
                                ${infoWaktuEl()}
                            </div>
                        </div>
                        <input type="time" name="" id="created_at" class="form-control" value="${data ? data.created_at : AbsenSettedTime[xabsen]}">
                        <div class="invalid-feedback text-xs" id="created_atFeedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="koordinat">Koordinat</label>
                        <input type="text" id="koordinat" class="form-control" placeholder="cth: -0.0352231,109.2491477" value="${data ? data.koordinat : ''}">
                        <div class="invalid-feedback text-xs" id="koordinatFeedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" rows="3" class="form-control" placeholder="cth: Bangka Belitung Darat, Pontianak Tenggara, Pontianak, Kalimantan Barat, Kalimantan, 78124, Indonesia">${data ? data.alamat : ''}</textarea>
                        <div class="invalid-feedback text-xs" id="alamatFeedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="aktivitas">Aktivitas/Keterangan</label>
                        <input type="text" id="aktivitas" class="form-control" placeholder="cth: Perbaikan user 28 Oktober" value="${data ? data.aktivitas : ''}">
                        <div class="invalid-feedback text-xs" id="aktivitasFeedback"></div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary ms-2">Simpan</button>
                    </div>
                </form>
            </div>`
        }

        const modalContentBatchEl = (id, nama) => {
            return `<div class="modal-header">
                <div class="fw-bold m-0">Absen ${moment(dailyDate.value).format('D MMMM YYYY')} ${nama}</div>
            </div>
            <div class="modal-body">
                <form novalidate onsubmit="event.preventDefault();handleSubmitBatch(${id})">
                    <input type="hidden" id="id" value="${id}">
                    <label for="foto">Foto <span class="fw-normal text-muted">(opsional)</span></label>
                    ${imgUploader('foto')}
                    <div class="form-group">
                        <label for="created_at">Waktu Absen Pertama</label> <span class="cursor-pointer"><i class="fa-solid fa-circle-info"></i></span>
                        <input type="time" id="created_at" class="form-control" value="08:55">
                        <div class="invalid-feedback text-xs" id="created_atFeedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="koordinat">Koordinat</label>
                        <input type="text" id="koordinat" class="form-control" placeholder="cth: -0.0352231,109.2491477">
                        <div class="invalid-feedback text-xs" id="koordinatFeedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" rows="3" class="form-control" placeholder="cth: Bangka Belitung Darat, Pontianak Tenggara, Pontianak, Kalimantan Barat, Kalimantan, 78124, Indonesia"></textarea>
                        <div class="invalid-feedback text-xs" id="alamatFeedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="aktivitas">Aktivitas/Keterangan</label>
                        <input type="text" id="aktivitas" class="form-control" placeholder="cth: Perbaikan user 28 Oktober">
                        <div class="invalid-feedback text-xs" id="aktivitasFeedback"></div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary ms-2">Simpan</button>
                    </div>
                </form>
            </div>`
        }

        function createAbsen(el) {
            modalContent.innerHTML = modalContentEl('post', 'absen', el.dataset.id, 0, el.dataset.nama)
            if (navigator.userAgent.indexOf("Win") != -1 ? true : false) {
                new PerfectScrollbar(document.querySelector('#Modal .modal-body'));
            }
        }

        function createAbsenBatch(el) {
            modalContent.innerHTML = modalContentBatchEl(el.dataset.id, el.dataset.nama)
            if (navigator.userAgent.indexOf("Win") != -1 ? true : false) {
                new PerfectScrollbar(document.querySelector('#Modal .modal-body'));
            }
        }


        function createAktivitas(el) {
            modalContent.innerHTML = modalContentEl('post', 'aktivitas', el.dataset.id, el.dataset.xabsen, el.dataset.nama)
            if (navigator.userAgent.indexOf("Win") != -1 ? true : false) {
                new PerfectScrollbar(document.querySelector('#Modal .modal-body'));
            }
        }

        function editAktivitas(el) {
            const data = {
                id: el.dataset.aktivitas_id,
                nama: el.dataset.nama,
                foto: el.dataset.foto,
                created_at: el.dataset.created_at,
                aktivitas: el.dataset.aktivitas,
                koordinat: el.dataset.koordinat,
                alamat: el.dataset.alamat,
            }
            modalContent.innerHTML = modalContentEl('put', 'aktivitas', el.dataset.id, el.dataset.xabsen, el.dataset.nama,
                data)
            if (navigator.userAgent.indexOf("Win") != -1 ? true : false) {
                new PerfectScrollbar(document.querySelector('#Modal .modal-body'));
            }
        }

        function handleSubmit(method, type, id = null) {
            const btn = document.querySelector('#Modal .btn-primary');
            btn.disabled = true
            btn.innerHTML = '<i class="fa-solid fa-spin fa-spinner me-1"></i>Memproses data'
            const data = {
                _method: method,
                id: document.querySelector('#Modal #id').value,
                foto: document.querySelector('#Modal #foto').files[0],
                date: dailyDate.value,
                created_at: document.querySelector('#Modal #created_at').value,
                koordinat: document.querySelector('#Modal #koordinat').value,
                alamat: document.querySelector('#Modal #alamat').value,
                aktivitas: document.querySelector('#Modal #aktivitas').value,
            }
            if (type === 'aktivitas') {
                data.xabsen = document.querySelector('#Modal #xabsen').value
            }
            axios.post(`${appUrl}/api/admin/${type}${! id ? '' :'/'+id}`, data, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    console.log(response.data);
                    delete detailsDaily[response.data.id]
                    loadDaily();
                    Modal.hide()
                    Success.fire(response.data.message);
                })
                .catch(error => {
                    if (error.response.status == 422) {
                        const errors = error.response.data.errors
                        resetValidationInputs('#Modal')
                        Object.keys(errors).forEach(key => {
                            invalidateInput(key, errors[key])
                        });
                    } else {
                        Failed.fire(error.response.data.message)
                    }
                    console.log(error.response.data);
                })
                .finally(() => {
                    btn.disabled = false
                    btn.innerHTML = 'Simpan'
                })
        }

        function handleSubmitBatch(id) {
            const btn = document.querySelector('#Modal .btn-primary');
            btn.disabled = true
            btn.innerHTML = '<i class="fa-solid fa-spin fa-spinner me-1"></i>Memproses data'
            const data = {
                id: document.querySelector('#Modal #id').value,
                foto: document.querySelector('#Modal #foto').files[0],
                date: dailyDate.value,
                created_at: document.querySelector('#Modal #created_at').value,
                koordinat: document.querySelector('#Modal #koordinat').value,
                alamat: document.querySelector('#Modal #alamat').value,
                aktivitas: document.querySelector('#Modal #aktivitas').value,
            }
            axios.post(`${appUrl}/api/admin/absen/batch`, data, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    delete detailsDaily[response.data.id]
                    loadDaily();
                    Modal.hide()
                    Success.fire(response.data.message);
                })
                .catch(error => {
                    if (error.response.status == 422) {
                        const errors = error.response.data.errors
                        resetValidationInputs('#Modal')
                        Object.keys(errors).forEach(key => {
                            invalidateInput(key, errors[key])
                        });
                    } else {
                        Failed.fire(error.response.data.message)
                    }
                })
                .finally(() => {
                    btn.disabled = false
                    btn.innerHTML = 'Simpan'
                })
        }

        function deleteAbsen(id) {
            Question.fire({
                title: 'Hapus',
                text: 'Apakah anda yakin ingin menghapus data absen karyawan ini? Tindakan ini tidak bisa dibatalkan!',
                preConfirm: () => {
                    return axios.delete(`${appUrl}/api/admin/absen/${id}`)
                        .then(response => {
                            Success.fire(response.data.message);
                            delete detailsDaily[response.data.id]
                            loadDaily()
                        })
                        .catch(error => {
                            Failed.fire(error.response.data.message);
                        })
                }
            });
        }

        function deleteAktivitas(id) {
            Question.fire({
                title: 'Hapus',
                text: 'Apakah anda yakin ingin menghapus data aktivitas karyawan ini? Tindakan ini tidak bisa dibatalkan!',
                preConfirm: () => {
                    return axios.delete(`${appUrl}/api/admin/aktivitas/${id}`)
                        .then(response => {
                            Success.fire(response.data.message);
                            delete detailsDaily[response.data.id]
                            loadDaily()
                        })
                        .catch(error => {
                            console.error(response.data);
                            Failed.fire(error.response.data.message);
                        })
                }
            });
        }

        function getBadgeAbsenR(status) {
            switch (status) {
                case 1:
                    __s = 'H';
                    __tatus = 'adir';
                    __bg = 'success';
                    break;
                case 2:
                    __s = 'T';
                    __tatus = 'erlambat';
                    __bg = 'warning';
                    break;
                case 3:
                    __s = 'A';
                    __tatus = 'lpa';
                    __bg = 'danger';
                    break;
                default:
                    __s = 'A';
                    __tatus = 'lpa';
                    __bg = 'danger';
                    break;
            }
            return `<span class="badge bg-gradient-${__bg} d-inline-flex gap-0">
                <span class="m-0">${__s}</span>
                <span class="d-none d-sm-inline m-0">${__tatus}</span>
            </span>`;
        }

        const tempTable = (datas) => {
            let tbodyEl = '';
            datas.forEach((data, loop) => {
                tbodyEl += `<tr>
                    <td rowspan="3">${loop+1}</td>
                    <td rowspan="3">${data.Nama}</td>
                    <td rowspan="3">${data.Status}</td>
                    <td>${data.Aktivitas[0]['Waktu Absen']}</td>
                    <td>${data.Aktivitas[1]['Waktu Absen']}</td>
                    <td>${data.Aktivitas[2]['Waktu Absen']}</td>
                    <td>${data.Aktivitas[3]['Waktu Absen']}</td>
                </tr>
                <tr>
                    <td>${data.Aktivitas[0]['Koordinat']}</td>
                    <td>${data.Aktivitas[1]['Koordinat']}</td>
                    <td>${data.Aktivitas[2]['Koordinat']}</td>
                    <td>${data.Aktivitas[3]['Koordinat']}</td>
                </tr>
                <tr>
                    <td>${data.Aktivitas[0]['Alamat']}</td>
                    <td>${data.Aktivitas[1]['Alamat']}</td>
                    <td>${data.Aktivitas[2]['Alamat']}</td>
                    <td>${data.Aktivitas[3]['Alamat']}</td>
                </tr>`
            })

            return `<table id="temporaryTable" style="display:none">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Nama</th>
                        <th rowspan="2">Status</th>
                        <th colspan="4">Aktivitas</th>
                    </tr>
                    <tr>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                    </tr>
                </thead>
                <tbody>
                    ${tbodyEl}
                </tbody>
            </table>`
        }

        function exportDaily(el) {
            const temp = el.innerHTML
            el.innerHTML = '<i class="fa-solid fa-spin fa-spinner me-1"></i>Memproses data...'
            el.disabled = true;
            axios.get(`${appUrl}/api/admin/absen/export-daily?tanggal=${dailyDate.value}`)
                .then(response => {
                    document.body.insertAdjacentHTML('beforeend', tempTable(response.data))
                    const table = document.getElementById("temporaryTable");
                    const wb = XLSX.utils.table_to_book(table);
                    XLSX.writeFile(wb, `Rekap Absen Karyawan ${moment(dailyDate.value).format('D MMMM YYYY')}.xlsx`);
                    if (document.getElementById('temporaryTable')) {
                        document.getElementById('temporaryTable').remove()
                    }
                })
                .catch(error => {
                    Failed('Terjadi Kesalahan Dalam Mendapatkan Data!')
                })
                .finally(() => {
                    el.innerHTML = temp
                    el.disabled = false;
                })
        }
    </script>
@endpush
