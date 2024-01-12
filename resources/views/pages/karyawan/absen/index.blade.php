@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@push('css')
    <style>
        th,
        td {
            padding-left: 0 !important;
            padding-right: 0 !important;
            width: calc(100% / 7);
        }
    </style>
@endpush
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Absen'])
    <div class="container-fluid px-0 px-sm-4 py-3">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 d-flex flex-column flex-md-row lh-sm">
                    <span>Absen Hari ini</span>
                    <span class="d-none d-md-inline mx-1">-</span>
                    <span>{{ now()->translatedFormat('l, j F Y') }}</span>
                </h6>
                <div id="todayStatus">

                </div>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex gap-3 flex-column flex-md-row" id="todayContainer">

                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex align-items-center gap-1">
                <h6 class="m-0 lh-1">Rekap Absen Bulan</h6>
                <select id="monthlyMonth" class="form-control" style="width: unset;" onchange="loadMonthly()">
                    @for ($i = 1; $i <= 12; $i++)
                        <option {{ $i == date('n') ? 'selected' : '' }} value="{{ $i }}">
                            {{ Carbon\Carbon::parse('2000-' . $i . '-01')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select id="monthlyYear" class="form-control" style="width: unset;" onchange="loadMonthly()">
                    @for ($i = 2017; $i <= date('Y'); $i++)
                        <option {{ $i == date('Y') ? 'selected' : '' }} value="{{ $i }}">{{ $i }}
                        </option>
                    @endfor
                </select>

            </div>
            <div class="card-body pt-0">
                <table class="table table-bordered m-0" id="table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">S
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">S
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">R
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">K
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">J
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">S
                            </th>
                        </tr>
                    </thead>
                    <tbody id="monthlyContainer">

                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mt-3 align-items-center d-flex gap-1">
                        <span class="bg-gradient-success badge" id="hMonthly">0</span><span>Hadir</span>
                    </div>
                    <div class="mt-3 align-items-center d-flex gap-1">
                        <span class="bg-gradient-warning badge" id="tMonthly">0</span><span>Terlambat</span>
                    </div>
                    <div class="mt-3 align-items-center d-flex gap-1">
                        <span class="bg-gradient-danger badge" id="aMonthly">0</span><span>Tidak hadir</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('modal')
    <div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="ModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex gap-3 flex-column flex-md-row">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endpush
@push('js')
    <script>
        const monthEl = document.getElementById('monthlyMonth')
        const yearEl = document.getElementById('monthlyYear')
        const modalEl = document.getElementById('Modal')
        let Modal;
        document.addEventListener('DOMContentLoaded', () => {
            Modal = new bootstrap.Modal(modalEl);
            loadToday();
            loadMonthly();
        });


        const todayLoader = `<div class="w-100">
            <div class="overflow-hidden rounded-3 placeholder-glow" style="height: 15rem;">
                <div class="placeholder h-100 w-100"></div>
            </div>
            <div class="placeholder-glow">
                <div class="placeholder h-100 w-100"></div>
                <div class="placeholder h-100 w-100"></div>
                <div class="placeholder h-100 w-100"></div>
                <div class="placeholder h-100 w-100"></div>
            </div>
        </div>`;

        const todayMsg = (msg, style = '') => {
            return `<div class="w-100">
                <div class="overflow-hidden rounded-3 bg-light d-flex align-items-center justify-content-center" style="height: 15rem; ">
                    <span class="${style}">${msg}</span>
                </div>
            </div>`;
        }

        const todayContainer = document.getElementById('todayContainer');
        const todayStatus = document.getElementById('todayStatus');

        let tryingToday = 0;

        function loadToday() {
            todayContainer.innerHTML = todayLoader;
            todayStatus.innerHTML = `<span class="badge bg-gradient-secondary" id="statusToday">
                <i class="fa-solid fa-spin fa-spinner me-1"></i>
                Loading
            </span>`;

            axios.get(`${appUrl}/api/karyawan/absen/today`)
                .then(response => {
                    const data = response.data;
                    if (data) {
                        todayStatus.innerHTML = getBadgeAbsen(data.status);
                        let todayElements = '';
                        data.aktivitass.forEach(aktivitas => {
                            todayElements += aktivitassEl(aktivitas);
                        });
                        todayContainer.innerHTML = todayElements;
                    } else {
                        todayStatus.innerHTML = '';
                        todayContainer.innerHTML = todayMsg('Tidak ada absen hari ini!');
                    }
                })
                .catch(error => {
                    console.error(error);
                    if (error.response.status === 500 && tryingToday < 10) {
                        loadToday()
                        tryingToday++;
                    } else {
                        tryingToday = 0;
                        todayContainer.innerHTML = todayMsg('Server error', 'text-danger');
                    }
                })
        }
        const monthlyErr = `<tr>
            <td colspan="7" class="text-center border p-0 bg-light" style="height:25rem">
                <div class=" text-danger d-flex align-items-center justify-content-center w-100 h-100">
                    Server error
                </div>
            </td>
        </tr>`;
        const monthlyLoader = `<tr>
            <td colspan="7" class="text-center border p-0 placeholder-glow" style="height:25rem">
                <div class="w-100 h-100 placeholder bg-light"></div>
            </td>
        </tr>`;

        const monthlyContainer = document.getElementById('monthlyContainer');
        const hMonthly = document.getElementById('hMonthly');
        const tMonthly = document.getElementById('tMonthly');
        const aMonthly = document.getElementById('aMonthly');

        let tryingMonthly = 0;

        function loadMonthly() {
            const year = yearEl.value
            const month = monthEl.value
            monthlyContainer.innerHTML = monthlyLoader;
            axios.get(`${appUrl}/api/karyawan/absen/monthly?m=${parseInt(monthEl.value)}&y=${yearEl.value}`)
                .then(response => {
                    hMonthly.textContent = response.data.h
                    tMonthly.textContent = response.data.t
                    aMonthly.textContent = response.data.a

                    const result = response.data.absens

                    // kosongkan container
                    monthlyContainer.innerHTML = ''
                    const sMoment = moment(`${year}-${month}`, 'YYYY-M')
                    const week = Math.ceil((sMoment.daysInMonth() + parseInt(sMoment.format('d'))) / 7)
                    let date = 1;
                    for (let i = 0; i < week; i++) {

                        // buat tr
                        const row = document.createElement("tr");

                        // looping tanggal
                        for (let j = 0; j < 7; j++) {

                            // buat Date() pertanggal setiap looping
                            const lMoment = moment(`${year}-${month}-${date}`, 'YYYY-M-D')

                            // buat cell
                            const cell = document.createElement("td");
                            cell.classList.add('text-center', 'border')
                            cell.style.height = '5rem'

                            // jika bukan tanggal di bulan terpilih
                            if ((i === 0 && j < sMoment.format('d')) || (date > sMoment.daysInMonth())) {
                                // beri warna cell bg-light
                                cell.classList.add('bg-light')
                            } else {

                                // buat elemen span untuk label tanggal
                                const isi = document.createElement("span");
                                // default text dark
                                isi.classList.add('badge', 'text-dark')


                                // cek apakah tanggal date terdapat data absen
                                const itemDate = result.filter(k => k.date === date)[0]

                                // jika tanggal lebih kecil dari sekarang dan bukan hari minggu set tidak hadir
                                if (lMoment.isBefore(moment(), 'day') && lMoment.format('d') != 0) {
                                    cell.classList.add('bg-gradient-danger')
                                    isi.classList.replace('text-dark', 'text-white')
                                }


                                // jika hari minggu set bg jadi light
                                if (lMoment.format('d') == 0) {
                                    cell.classList.add('bg-gradient-light')
                                }


                                if (itemDate) {
                                    // jika ada data absen, ganti teks jadi putih
                                    isi.classList.replace('text-dark', 'text-white')

                                    // pengecekan status absen
                                    let bg;
                                    switch (itemDate.status) {
                                        case 1:
                                            bg = 'success'
                                            break;
                                        case 2:
                                            bg = 'warning'
                                            break;
                                        case 3:
                                            bg = 'danger'
                                            break;
                                        default:
                                            bg = 'danger'
                                            break;
                                    }


                                    if (cell.classList.contains('bg-gradient-danger')) {
                                        cell.classList.replace('bg-gradient-danger', 'bg-gradient-' + bg)
                                    } else {
                                        cell.classList.replace('bg-gradient-light', 'bg-gradient-' + bg)
                                    }
                                    cell.classList.add('cursor-pointer')
                                    cell.onclick = e => {
                                        showDetail(e, itemDate.id)
                                    };
                                }

                                if (authUserId == 72) {
                                    if (lMoment.format('d') == 6) {
                                        if (cell.classList.contains('bg-gradient-danger')) {
                                            cell.classList.replace('bg-gradient-danger', 'bg-gradient-success')
                                        } else {
                                            cell.classList.replace('bg-gradient-light', 'bg-gradient-success')
                                        }
                                    }
                                }

                                // jika date sama dengan hari ini, maka ubah bg badgeg jadi primary
                                if (lMoment.isSame(moment(), 'day')) {
                                    isi.classList.add('bg-gradient-primary')
                                    isi.classList.remove('text-dark')
                                }
                                isi.textContent = date
                                cell.appendChild(isi);
                                date++;
                            }
                            row.appendChild(cell);
                        }
                        monthlyContainer.appendChild(row);
                    }
                })
                .catch(error => {
                    if (error.response.status === 500 && tryingMonthly < 10) {
                        loadMonthly()
                        tryingMonthly++;
                    } else {
                        tryingMonthly = 0;
                        console.error(error);
                        monthlyContainer.innerHTML = monthlyErr
                        hMonthly.textContent = 0
                        tMonthly.textContent = 0
                        aMonthly.textContent = 0
                    }
                })
        }


        let isFetchingShow = false;

        const modalHeaderEl = modalEl.querySelector('.modal-header');
        const modalBodyEl = modalEl.querySelector('.modal-body');

        function showDetail(e, id) {
            modalHeaderEl.innerHTML = ''
            modalBodyEl.innerHTML = ''
            const isi = e.currentTarget.querySelector('span');
            const valueIsi = isi.textContent;
            if (isFetchingShow) return;
            isFetchingShow = true;
            isi.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'
            axios.get(`${appUrl}/api/karyawan/absen/${id}`)
                .then(response => {
                    modalHeaderEl.innerHTML = `<h6 class="m-0 d-flex flex-column flex-md-row lh-sm">
                            <span>Detail Absen</span>
                            <span class="d-none d-md-inline mx-1">-</span>
                            <span>${response.data.created_atFormat}</span>
                        </h6>
                        <div id="todayStatus">
                            ${getBadgeAbsen(response.data.status)}
                        </div>`
                    let aktivitasEl = '';
                    response.data.aktivitass.forEach(akt => {
                        aktivitasEl += aktivitassEl(akt);
                    });
                    modalBodyEl.innerHTML = aktivitasEl
                    if (navigator.userAgent.indexOf("Win") != -1 ? true : false) {
                        new PerfectScrollbar(modalBodyEl);
                    }
                    Modal.show()
                })
                .catch(error => {
                    console.log(error);
                    Failed.fire(error.response.message)
                })
                .finally(() => {
                    isi.textContent = valueIsi
                    isFetchingShow = false;
                })
        }
    </script>
@endpush
