@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@push('css')
    <style>
        th,
        td {
            width: unset !important;
        }

        th {
            cursor: pointer !important;
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
    @include('layouts.navbars.auth.topnav', ['title' => 'Karyawan'])
    <div class="container-fluid px-2 px-sm-4 py-3">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-1">
                <div class="d-flex align-items-sm-center flex-column flex-sm-row gap-1">
                    <h6 class="m-0 lh-1">Daftar Data Karyawan</h6>
                </div>
                <button class="btn bg-gradient-danger" onclick="createKaryawan()">
                    <i class="fa-solid fa-plus me-1"></i>
                    <span class="d-none d-sm-inline-block">Tambah Karyawan</span>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="px-4 d-flex gap-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="search" class="form-control" id="searchEl" placeholder="Cari karyawan...">
                    </div>
                    <select id="wilayahEl" class="form-select flex-grow-sm-0 flex-grow-1 pe-5" style="width: unset"
                        onchange="loadTable()">
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
                                <th data-order="id"
                                    class="d-none d-md-table-cell text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pe-2">
                                    No.
                                </th>
                                <th data-order="nama"
                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4 ps-sm-2 pe-2">
                                    Karyawan
                                    <i id="orderByEl" class="fa-solid fa-arrow-down-a-z ms-2"></i>
                                </th>
                                <th data-order="role"
                                    class="d-none d-md-table-cell  text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Hak Akses
                                </th>
                                <th data-order="email"
                                    class="d-none d-lg-table-cell text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Email
                                </th>
                                <th data-order="no_telp"
                                    class="d-none d-lg-table-cell text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    No Telp
                                </th>
                                <th data-order="wilayah_id"
                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Wilayah
                                </th>
                            </tr>

                        </thead>
                        <tbody id="tbodyEl">
                            <tr>
                                <td colspan="7" class="text-center">
                                    <i class="fa-solid fa-spin fa-spinner me-1"></i>
                                    Mendapatkan data...
                                </td>
                            </tr>
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
        const searchEl = document.getElementById('searchEl');
        searchEl.addEventListener('input', debounce(loadTable, 500));
        const wilayahEl = document.getElementById('wilayahEl');
        const tbodyEl = document.getElementById('tbodyEl');
        const ModalEL = document.getElementById('Modal');
        const modalContent = document.querySelector('#Modal .modal-content');

        let Modal, karyawans, selectedKaryawanId;
        let orderBy = ['nama', 'asc'];
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('th').forEach(tableHead => {
                tableHead.addEventListener('click', () => {
                    defineOrder(tableHead)
                })
            });
            Modal = new bootstrap.Modal(ModalEL);
            loadTable();
        });

        const tableLoader = `<tr>
            <td colspan="7" class="text-center">
                <i class="fa-solid fa-spin fa-spinner me-1"></i>
                Mendapatkan data...
            </td>
        </tr>`

        const tableEmpty = `<tr>
            <td colspan="7" class="text-center">
                Tidak ada data
            </td>
        </tr>`

        const tableRowEl = (data, loop) => {
            return `<tr data-bs-toggle="collapse" data-bs-target="#tableRow${data.id}">
                <td class="ps-4 d-none d-md-table-cell w-0 text-uppercase text-secondary text-xxs font-weight-bolder">
                    ${loop +1}.
                </td>
                <td class="ps-4 ps-sm-2">
                    <div class="d-inline-flex gap-2">
                        <img class="rounded-3" src="${appUrl}/storage/${data.foto_profil}" alt="foto profil" height="35">
                        <div class="">
                            <div class="lh-sm" style="white-space:initial !important">
                                ${data.nama}
                            </div>
                            <div class="lh-sm text-xs" style="white-space:initial !important">
                                ${data.speciality}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="d-none d-md-table-cell text-sm">
                    ${getRoleName(data.role)}
                </td>
                <td class="d-none d-lg-table-cell text-sm">
                    ${data.email}
                </td>
                <td class="d-none d-lg-table-cell text-sm">
                    +${data.no_telp}
                </td>
                <td class="text-sm pe-4">
                    ${data.wilayah.nama_wilayah}
                </td>
            </tr>
            <tr class="no-hover bg-light">
                <td colspan="7" class="p-0 border-bottom">
                    <div class="collapse" id="tableRow${data.id}" data-bs-parent="#tbodyEl">
                        <div class="p-4">
                            <div class="d-lg-none mb-3">
                                <div class="text-sm text-uppercase mb-3">Detail Karyawan</div>
                                <div class="d-flex flex-wrap gap-3 flex-column flex-sm-row">
                                    <div class="">
                                        <div class="text-xs text-uppercase">ID Karyawan</div>
                                        <div class="">${data.id}</div>
                                    </div>
                                    <div class="">
                                        <div class="text-xs text-uppercase">Hak Akses</div>
                                        <div class="">${getRoleName(data.role)}</div>
                                    </div>
                                    <div class="">
                                        <div class="text-xs text-uppercase">Email</div>
                                        <a href="mailto:${data.email}" class="text-primary">${data.email}<a href="javascript:;" class="ms-2 text-primary" onclick="navigator.clipboard.writeText('${data.email}');Success.fire('Email berhasil disalin')"><i class="fa-solid fa-copy"></i></a></a>
                                    </div>
                                    <div class="">
                                        <div class="text-xs text-uppercase">Nomor Telepon/Whatsapp</div>
                                        <a href="https://wa.me/${data.no_telp}" target="blank" class="text-primary">+${data.no_telp}<a href="javascript:;" class="ms-2 text-primary" onclick="navigator.clipboard.writeText('${data.no_telp}');Success.fire('Nomor berhasil disalin')"><i class="fa-solid fa-copy"></i></a></a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn bg-gradient-danger" onclick="deleteKaryawan(${data.id})">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <button class="btn bg-gradient-warning" onclick="editKaryawan(${data.id})">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>`
        }

        const tableError = `<tr>
            <td colspan="7" class="text-center" onclick="loadTable()">
                Tidak Dapat Memperoleh Data, Tekan Untuk Memuat Ulang!
            </td>
        </tr>`


        let tryingTable = 0;
        let isTableFetching = false;

        function loadTable() {
            if (isTableFetching) return;
            isTableFetching = true;
            tbodyEl.innerHTML = tableLoader;
            axios.get(`${appUrl}/api/admin/karyawan?nama=${searchEl.value}&order_by=${orderBy}`)
                .then(response => {
                    const data = response.data;
                    karyawans = data;
                    if (data.length > 0) {
                        tbodyEl.innerHTML = ''
                        data.forEach((data, loop) => {
                            tbodyEl.insertAdjacentHTML('beforeend', tableRowEl(data, loop))
                        });
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

        function defineOrder(el) {
            document.getElementById('orderByEl').remove()
            if (orderBy[0] === el.dataset.order) {
                orderBy[1] === 'asc' ? orderBy[1] = 'desc' : orderBy[1] = 'asc';
            } else {
                orderBy[0] = el.dataset.order
            }
            el.insertAdjacentHTML('beforeend',
                `<i id="orderByEl" class="fa-solid fa-arrow-${orderBy[1]==='asc' ? 'down':'up'}-a-z ms-2"></i>`)
            // console.log(orderBy);
            loadTable()

        }

        function createKaryawan() {
            modalContent.innerHTML = modalContentEl()
            if (navigator.userAgent.indexOf("Win") != -1 ? true : false) {
                new PerfectScrollbar(document.querySelector('#Modal .modal-body'));
            }
            Modal.show()
        }

        function editKaryawan(id) {
            const karyawan = karyawans.filter(karyawan => karyawan.id === id)[0];
            selectedKaryawanId = id;
            modalContent.innerHTML = modalContentEl(karyawan)
            if (navigator.userAgent.indexOf("Win") != -1 ? true : false) {
                new PerfectScrollbar(document.querySelector('#Modal .modal-body'));
            }
            Modal.show()
        }

        const modalContentEl = (data = null) => {
            return `<div class="modal-header">
                <div class="fw-bold m-0">${data ? ('Edit data' + data.nama) : 'Tambah Karyawan'}</div>
            </div>
            <div class="modal-body">
                <form onsubmit="event.preventDefault();handleSubmit('${data ? 'patch' : 'post'}')" novalidate>

                    <div class="form-group">
                        <label for="nama">Nama Lengkap Tanpa Gelar</label>
                        <input type="text" class="form-control" id="nama" placeholder="cth: John Doe" value="${data ? data.nama : ''}">
                        <div class="invalid-feedback text-xs ps-2" id="namaFeedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="speciality">Jabatan</label>
                        <input type="text" class="form-control" id="speciality" placeholder="cth: Teknisi Lapangan" value="${data ? data.speciality : ''}">
                        <div class="invalid-feedback text-xs ps-2" id="specialityFeedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="role">Hak Akses</label>
                        <select class="form-select" id="role" value="${data ? data.role : ''}">
                            <option value="">--Pilih Hak Akses--</option>
                            <option ${data ? (data.role == 0 ? 'selected' : '') : ''} value="0">Supervisor</option>
                            <option ${data ? (data.role == 1 ? 'selected' : '') : ''} value="1">Admin</option>
                            <option ${data ? (data.role == 2 ? 'selected' : '') : ''} value="2">Karyawan</option>
                        </select>
                        <div class="invalid-feedback text-xs ps-2" id="roleFeedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="wilayah_id">Wilayah</label>
                        <select class="form-select" id="wilayah_id">
                            <option value="">--Pilih Wilayah--</option>
                            @foreach (\App\Models\Wilayah::all() as $wilayah)
                                <option ${data ? (data.wilayah.id == {{ $wilayah->id }} ? 'selected' : '') : ''}  value="{{ $wilayah->id }}">{{ $wilayah->nama_wilayah }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback text-xs ps-2" id="wilayah_idFeedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" placeholder="cth: johndoe@big.com" value="${data ? data.email : ''}">
                        <div class="invalid-feedback text-xs ps-2" id="emailFeedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="no_telp">Nomor Telepon/Whatsapp</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">+</span>
                            <input type="number" class="form-control" id="no_telp" placeholder="cth: 6281234567890" autocomplete="tel" value="${data ? data.no_telp : ''}">
                            <div class="invalid-feedback text-xs ps-2" id="no_telpFeedback"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password Akun</label>
                        <div class="input-group has-validation">
                            <input type="password" class="form-control" id="password">
                            <button type="button" onclick="event.preventDefault();showPassword(this)" class="btn btn-white input-group-append">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button onclick="document.getElementById('password').value = getRandomPassword(8)" type="button" class="btn btn-warning input-group-append">
                                <i class="fa-solid fa-rotate fa-flip-horizontal"></i>
                            </button>
                            <button onclick="document.getElementById('password').select(); document.execCommand('copy');Success.fire('Password berhasil dicopy')" type="button" class="btn btn-success input-group-append">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                            <div class="invalid-feedback text-xs ps-2" id="passwordFeedback"></div>
                        </div>
                    </div>
                    <input type="submit" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary ms-2" id="btnSimpan" onclick="handleSubmit('${data ? 'patch' : 'post'}')">Simpan</button>
            </div>`
        }



        function handleSubmit(method) {
            const btn = document.getElementById('btnSimpan');
            btn.disabled = true
            btn.innerHTML = '<i class="fa-solid fa-spin fa-spinner me-1"></i>Memproses data...'
            const data = {
                _method: method,
                nama: document.getElementById('nama').value,
                speciality: document.getElementById('speciality').value,
                role: document.getElementById('role').value,
                wilayah_id: document.getElementById('wilayah_id').value,
                email: document.getElementById('email').value,
                no_telp: document.getElementById('no_telp').value,
                password: document.getElementById('password').value,
            }
            console.log(data['_method']);
            axios.post(`${appUrl}/api/admin/karyawan${method==='patch'? ('/'+selectedKaryawanId):''}`, data)
                .then(response => {
                    console.log(response.data);
                    loadTable();
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


        function deleteKaryawan(id) {
            Question.fire({
                title: 'Hapus',
                text: 'Apakah anda yakin ingin menghapus data karyawan ini?',
                preConfirm: () => {
                    return axios.delete(`${appUrl}/api/admin/karyawan/${id}`)
                        .then(response => {
                            Success.fire(response.data.message);
                            loadTable()
                        })
                        .catch(error => {
                            Failed.fire(error.response.data.message);
                        })
                }
            });
        }

        function getRoleName(id) {
            switch (id) {
                case 0:
                    return 'Supervisor'
                case 1:
                    return 'Admin'
                case 2:
                    return 'Karyawan'
            }
        }
    </script>
@endpush
