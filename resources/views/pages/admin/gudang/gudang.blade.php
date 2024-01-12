@extends('layouts.app')
@push('css')
    <script>
        .wrapper {
        margin-top: 5vh;
        }

        .dataTables_filter {
        float: right;
        }

        .table-hover > tbody > tr:hover {
        background-color: #ccffff;
        }

        @media only screen and (min-width: 768px) {
            .table {
                table-layout: fixed;
                max-width: 100% !important;
            }
            }

            thead {
            background: #ddd;
            }

            .table td:nth-child(2) {
            overflow: hidden;
            text-overflow: ellipsis;
            }

            .highlight {
            background: #ffff99;
            }

            @media only screen and (max-width: 767px) {
            /* Force table to not be like tables anymore */
            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            /* Hide table headers (but not display: none;, for accessibility) */
            thead tr,
            tfoot tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            td {
                /* Behave  like a "row" */
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50% !important;
            }

            td:before {
                /* Now like a table header */
                position: absolute;
                /* Top/left values mimic padding */
                top: 6px;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
            }

            .table td:nth-child(1) {
                background: #ccc;
                height: 100%;
                top: 0;
                left: 0;
                font-weight: bold;
            }

            /*
            Label the data
            */
            td:nth-of-type(1):before {
                content: "No";
            }
            td:nth-of-type(2):before {
                content: "Gudang";
            }
            td:nth-of-type(3):before {
                content: "Alamat";
            }
            td:nth-of-type(4):before {
                content: "Opsi";
            }
        }
    </script>
@endpush
@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Gudang'])
@include('sweetalert::alert')
<div class="container-fluid px-0 px-sm-4 py-3">
    <div class="card md-3">
        <div class="card-header d-flex align-items-sm-center justify-content-between gap-1">
            <div class="d-flex align-items-sm-center flex-column flex-sm-row gap-1">
                <h6 class="m-0 lh-1">Daftar Data Gudang</h6>
            </div>
            <button type="button" class="btn bg-gradient-danger btn-md" data-bs-toggle="modal"
            data-bs-target="#modalTambahGudang">
                Tambah Gudang
            </button>
        </div>

        @if ($errors->any())
            <script>
                $(document).ready(function() {
                    $('#modalTambahGudang').modal('show');
                });
            </script>
        @endif

        <!-- Modal Tambah Gudang -->
        <div class="modal fade" id="modalTambahGudang" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Gudang Baru</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('simpanGudang') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="namaGudang" class="form-label">Nama Gudang: </label>
                            <input type="text" class="form-control" id="namaGudang" name="nama">
                            @error('nama')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="alamatGudang" class="form-label">Alamat Gudang: </label>
                            <input type="text" class="form-control" id="alamatGudang" name="alamat">
                            @error('alamat')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <div class="card-body align-items-center">
            <table id="table" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gudang</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $gudang)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td id="gudangNama{{$gudang->id}}">
                                {{ $gudang->nama }}
                                <!-- Tombol untuk toggle collapse -->
                                <button class="btn bg-gradient-primary btn-xs ms-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample{{$gudang->id}}" aria-expanded="false" aria-controls="collapseExample">
                                    +
                                </button>
                                <!-- Isi detail dalam collapse -->
                                <div class="collapse" id="collapseExample{{$gudang->id}}">
                                    <div class="card card-body">
                                        Nama Gudang: {{$gudang->nama}} <br><br>
                                        Alamat Gudang: {{$gudang->alamat}}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <!-- Button Edit Gudang -->
                                <button type="button" class="btn btn-warning" onclick="handleEdit({{ $gudang->id }})"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditGudang">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Button Hapus Gudang -->
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#modalDeleteGudang-{{ $gudang->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal Edit Gudang -->
                                <div class="modal fade" id="modalEditGudang" tabindex="-1" role="dialog"
                                    aria-labelledby="modelTitleId" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Gudang</h5>
                                                <button type="button" class="close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form id="formEditGudang" action="updateGudang/" method="post">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="namaGudang" class="form-label">Nama Gudang: </label>
                                                        <input type="text" class="form-control" id="namaGudang"
                                                            name="nama" value="">
                                                            @error('nama')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="alamatGudang" class="form-label">Alamat Gudang: </label>
                                                        <input type="text" class="form-control" id="alamatGudang"
                                                            name="alamat" value="">
                                                            @error('alamat')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary me-2"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Hapus Gudang -->
                                <div class="modal fade" id="modalDeleteGudang-{{ $gudang->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Peringatan!!!</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Yakin Ingin Menghapus Gudang {{ $gudang->nama }}?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary me-2"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('hapusGudang', $gudang->id) }}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Opsi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
            //disable sorting on last column
            "columnDefs": [
                { "orderable": false, "targets": 2 }
            ],
            language: {
                //customize pagination prev and next buttons: use arrows instead of words
                'paginate': {
                'previous': '<span class="fa fa-chevron-left"></span>',
                'next': '<span class="fa fa-chevron-right"></span>'
                },
                //customize number of elements to be displayed
                "lengthMenu": 'Tampil <select class="form-control input-sm">'+
                '<option value="10">10</option>'+
                '<option value="20">20</option>'+
                '<option value="30">30</option>'+
                '<option value="40">40</option>'+
                '<option value="50">50</option>'+
                '<option value="-1">All</option>'+
                '</select> hasil'
            }
            })
        } );
    </script>
@endpush

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

<!-- jQuery -->
<script src='https://code.jquery.com/jquery-3.7.0.js'></script>
<!-- Data Table JS -->
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
<script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function handleEdit(idGudang) {
        document.getElementById('formEditGudang').action = '/admin/gudang/update-gudang/' + idGudang
        const namaGudang = document.querySelector('#gudangNama'+idGudang).textContent
        const alamatGudang = document.querySelector('#gudangAlamat'+idGudang).textContent
        document.querySelector('#modalEditGudang #namaGudang').value = namaGudang
        document.querySelector('#modalEditGudang #alamatGudang').value = alamatGudang
    }
</script>
