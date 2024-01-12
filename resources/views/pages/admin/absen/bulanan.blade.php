@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@push('css')
    <style>
        th,
        td {
            width: unset !important;
        }

        td {
            vertical-align: middle;
            border: none;
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
        <div class="card overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between gap-1 pb-2 pb-sm-4">
                <div class="d-flex align-items-sm-center flex-column flex-sm-row gap-1">
                    <h6 class="m-0 lh-1">Rekap Absen Bulan</h6>
                    <div class="d-flex gap-1">
                        <select id="monthEl" class="form-select flex-grow-0 pe-5" style="width: unset">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::parse('2023-' . $i . '-20')->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select id="yearEl" class="form-select flex-grow-0 pe-5" style="width: unset">
                            @for ($i = 2020; $i <= date('Y'); $i++)
                                <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                </div>
                <button class="btn btn-light" onclick="exportTable(this)"><i class="fa-solid fa-file me-1"></i><span
                        class="d-none d-sm-inline-block">Generate Report</span></button>
            </div>
            <div class="card-body p-0 ">
                <div class="px-4 d-flex gap-3 ">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="search" class="form-control" id="searchEl" placeholder="Cari karyawan...">
                    </div>
                    <select id="wilayahEl" class="form-select flex-grow-sm-0 flex-grow-1 pe-5" style="width: unset"
                        onchange="loadTable()">
                        <option value="">Wilayah</option>
                        @foreach (\App\Models\Wilayah::all() as $wilayah)
                            <option value="{{ $wilayah->id }}">
                                {{ $wilayah->nama_wilayah }}
                            </option>
                        @endforeach
                    </select>

                </div>
                <div class="table-responsive ">
                    <table class="table table-hover m-0" id="table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th
                                    class="d-none d-md-table-cell text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pe-2">
                                    No.
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Teknisi
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    <div class="d-inline-flex gap-0">
                                        <span>
                                            H
                                        </span>
                                        <span class="d-none d-sm-inline">
                                            adir
                                        </span>
                                    </div>
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    <div class="d-inline-flex gap-0">

                                        <span>
                                            T
                                        </span>
                                        <span class="d-none d-sm-inline">
                                            erlambat
                                        </span>
                                    </div>
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    <div class="d-inline-flex gap-0">

                                        <span>
                                            A
                                        </span>
                                        <span class="d-none d-sm-inline">
                                            lpa
                                        </span>
                                    </div>
                                </th>
                            </tr>

                        </thead>
                        <tbody id="tbodyEl">

                        </tbody>
                    </table>
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
        let detailsTable = {};
        const tbodyEl = document.getElementById('tbodyEl')
        const monthEl = document.getElementById('monthEl')
        const yearEl = document.getElementById('yearEl')
        const searchEl = document.getElementById('searchEl')
        const wilayahEl = document.getElementById('wilayahEl')
        const modalEl = document.getElementById('Modal')
        let Modal;

        searchEl.addEventListener('input', debounce(loadTable, 300));
        monthEl.addEventListener('change', () => {
            loadTable();
            detailsTable = {}

        });

        yearEl.addEventListener('change', () => {
            loadTable();
            detailsTable = {}
        });

        wilayahEl.addEventListener('change', () => {
            loadTable();
        });



        document.addEventListener('DOMContentLoaded', () => {
            Modal = new bootstrap.Modal(modalEl);
            loadTable();
        });

        const tableLoader = `<tr>
            <td colspan="5" class="text-center">
                <i class="fa-solid fa-spin fa-spinner me-1"></i>
                Mendapatkan data...
            </td>
        </tr>`

        const tableEmpty = `<tr>
            <td colspan="5" class="text-center">
                Tidak ada data
            </td>
        </tr>`


        const tableError = `<tr>
            <td colspan="5" class="text-center" onclick="loadTable()">
                Tidak Dapat Memperoleh Data, Tekan Untuk Memuat Ulang!
            </td>
        </tr>`

        const tableRow = (data, loop) => {
            return `<tr style="cursor:pointer" onclick="showDetailTable(${data.id})" data-bs-toggle="collapse" data-bs-target="#tableDetail${data.id}">
            <td class="ps-4 d-none d-md-table-cell w-0 text-uppercase text-secondary text-xxs font-weight-bolder">
                ${loop +1}.
            </td>
            <td class="ps-4">
                <div class="d-inline-flex  align-items-center flex-row gap-2">
                    <img class="rounded-3" src="${appUrl}/storage/${data.foto_profil}" alt="foto profil" height="35">
                    <div style="white-space: initial" class="lh-sm text-sm">
                        ${data.nama}
                    </div>
                </div>
            </td>

            <td class="text-center">
                <span class="badge bg-gradient-success">
                    ${data.hadir}
                </span>
            </td>
            <td class="text-center">
                <span class="badge bg-gradient-warning">
                    ${data.terlambat}
                </span>
            </td>
            <td class="text-center">
                <span class="badge bg-gradient-danger">
                    ${data.alpa}
                </span>
            </td>

        </tr>
        <tr class="no-hover">
            <td colspan="5" class="p-0 border-bottom">
                <div class="collapse" id="tableDetail${data.id}" data-bs-parent="#tbodyEl">
                    <div>
                        <table class="table m-0" id="table" width="100%" cellspacing="0">
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
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center border p-0 placeholder-glow" style="height:25rem">
                                        <div class="w-100 h-100 placeholder bg-white"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
        </tr>`
        }

        let isTableFetching = false;

        function loadTable() {
            if (isTableFetching) return;
            isTableFetching = true;
            tbodyEl.innerHTML = tableLoader;
            axios.get(
                    `${appUrl}/api/admin/absen/bulanan?month=${monthEl.value}&year=${yearEl.value}&nama=${searchEl.value}&wilayah=${wilayahEl.value}`
                )
                .then(response => {
                    const data = response.data;
                    if (data.length > 0) {
                        let tableRows = '';
                        data.forEach((data, loop) => {
                            tableRows += tableRow(data, loop);
                        });
                        tbodyEl.innerHTML = tableRows;
                    } else {
                        tbodyEl.innerHTML = tableEmpty;
                    }
                })
                .catch(error => {
                    console.error(error);
                    if (error.response.status === 500 && tryingTable < 10) {
                        loadTable()
                        tryingTable++;
                    } else {
                        tryingTable = 0;
                    }
                    tbodyEl.innerHTML = tableError;
                })
                .finally(() => {
                    isTableFetching = false;
                })
        }

        const detailTbodyLoader = `<tr>
            <td colspan="7" class="text-center border p-0 placeholder-glow" style="height:25rem">
                <div class="w-100 h-100 placeholder bg-light"></div>
            </td>
        </tr>`


        async function showDetailTable(id) {
            const year = yearEl.value
            const month = monthEl.value
            const el = document.querySelector('#tableDetail' + id + ' div')
            const elTbody = el.querySelector('tr tbody');
            console.log(elTbody);

            if (!(id in detailsTable)) {
                try {
                    const result = await axios.get(
                        `${appUrl}/api/admin/absen/bulanan/${id}?m=${monthEl.value}&y=${yearEl.value}`)
                    detailsTable[id] = result.data;
                } catch (error) {
                    console.error(error);
                    return;
                }
            }

            elTbody.innerHTML = ''
            const sMoment = moment(`${year}-${month}`, 'YYYY-M')
            const week = Math.ceil(sMoment.daysInMonth() / 7)

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
                        const itemDate = detailsTable[id].filter(k => k.date === date)[0]

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
                            cell.style.cursor = 'pointer'
                            cell.onclick = e => {
                                showDetail(e, itemDate.id)
                            };
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
                elTbody.appendChild(row);
            }
        }

        const modalHeaderEl = modalEl.querySelector('.modal-header');
        const modalBodyEl = modalEl.querySelector('.modal-body');

        function showDetail(e, id) {
            modalHeaderEl.innerHTML = ''
            modalBodyEl.innerHTML = ''
            const isi = e.currentTarget.querySelector('span');
            const valueIsi = isi.textContent;
            isFetchingShow = true;
            isi.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'
            axios.get(`${appUrl}/api/karyawan/absen/${id}`)
                .then(response => {
                    console.log(response.data);
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
                    console.error(error);
                })
                .finally(() => {
                    isi.textContent = valueIsi
                    isFetchingShow = false;
                })
        }
        const tempTable = (datas) => {
            let tempTbody = '';
            datas.forEach((data, loop) => {
                tempTbody += `<tr>
                    <td>${loop+1}</td>
                    <td>${data.nama}</td>
                    <td>${data.hadir}</td>
                    <td>${data.terlambat}</td>
                    <td>${data.alpa}</td>
                </tr>`
            })

            return `<table id="temporaryTable" style="display:none">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Hadir</th>
                        <th>Terlambat</th>
                        <th>Alpa</th>
                    </tr>
                </thead>
                <tbody>
                    ${tempTbody}
                </tbody>
            </table>`
        }

        function exportTable(el) {
            const temp = el.innerHTML
            el.innerHTML = '<i class="fa-solid fa-spin fa-spinner me-1"></i>Memproses data...'
            el.disabled = true;
            axios.get(`${appUrl}/api/admin/absen/bulanan?month=${monthEl.value}&year=${yearEl.value}`)
                .then(response => {
                    document.body.insertAdjacentHTML('beforeend', tempTable(response.data))
                    const table = document.getElementById("temporaryTable");
                    const wb = XLSX.utils.table_to_book(table);
                    XLSX.writeFile(wb, `Rekap Absen Karyawan Bulan ${moment(monthEl.value, 'M').format('MMMM')} Tahun ${yearEl.value}.xlsx`);
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
