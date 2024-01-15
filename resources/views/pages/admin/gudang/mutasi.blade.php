@extends('layouts.app');
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
            content: "Kode";
        }
        td:nth-of-type(3):before {
            content: "Kategori";
        }
        td:nth-of-type(4):before {
            content: "SN";
        }
        td:nth-of-type(5):before {
            content: "Nama";
        }
        td:nth-of-type(6):before {
            content: "Gudang";
        }
        td:nth-of-type(7):before {
            content: "Satuan";
        }
        td:nth-of-type(8):before {
            content: "Jumlah";
        }
        td:nth-of-type(9):before {
            content: "Opsi";
        }
    }
    </script>
@endpush
@section('content');
@include('layouts.navbars.auth.topnav', ['title' => 'Mutasi'])
@include('sweetalert::alert')
<div class="container-fluid px-0 px-sm-4 py-3">
    <div class="card md-3">
        <div class="card-header">
            <h6>Daftar Data Mutasi</h6>
        </div>
        <div class="card-body">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Mutasi Masuk</button>
                  <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Mutasi Keluar</button>
                  <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Mutasi Kembali</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="card-header d-flex align-items-sm-center justify-content-between gap-1">
                        <div class="d-flex align-items-sm-center flex-column flex-sm-row gap-1">
                            <h6 class="m-0 lh-1">Daftar Data Mutasi Masuk</h6>
                        </div>
                        <button type="button" class="btn bg-gradient-danger btn-md" data-bs-toggle="modal"
                        data-bs-target="#modalTambahMutasiMasuk">
                            Tambah Mutasi Masuk
                        </button>
                    </div>
                    <div class="card-body">
                        <table id="table1" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Kategori</th>
                                    <th>SN</th>
                                    <th>Nama</th>
                                    <th>Gudang</th>
                                    <th>Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['barang'] as $barang)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td id="barangKode{{$barang->id}}">{{ $barang->kode }}</td>
                                        <td id="">{{ $barang->kategori->nama }}</td>
                                        <td id="barangSerial{{$barang->id}}">{{ $barang->serial_number }}</td>
                                        <td id="barangNama{{$barang->id}}">{{ $barang->nama }}</td>
                                        <td id="">{{ $barang->gudang->nama }}</td>
                                        <td id="">{{ $barang->satuan->nama }}</td>
                                        <td id="barangJumlah{{$barang->id}}">{{ $barang->jumlah }}</td>
                                        <td>
                                            <!-- Button Edit Mutasi Masuk -->
                                            <button type="button" class="btn btn-warning" onclick="handleEdit({{ $barang->id }})"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditMutasiMasuk">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Button Hapus Mutasi Masuk -->
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#modalDeleteMutasiMasuk{{ $barang->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            <!-- Modal Edit Mutasi Masuk -->
                                            <div class="modal fade" id="modalEditMutasiMasuk" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Mutasi Masuk</h5>
                                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form id="formEditMutasiMasuk" action="updateBarang/" method="post">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="kodeBarang" class="form-label">Kode: </label>
                                                                    <input type="text" class="form-control" id="kodeBarang" name="edit_kode"
                                                                        value="">
                                                                    @error('edit_kode')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="serialBarang" class="form-label">SN: </label>
                                                                    <input type="text" class="form-control" id="serialBarang" name="edit_serial"
                                                                        value="">
                                                                    @error('edit_serial')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="namaBarang" class="form-label">Nama Barang: </label>
                                                                    <input type="text" class="form-control" id="namaBarang" name="edit_nama"
                                                                        value="">
                                                                    @error('edit_nama')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="jumlahBarang" class="form-label">Jumlah: </label>
                                                                    <input type="text" class="form-control" id="jumlahBarang" name="edit_jumlah"
                                                                        value="">
                                                                    @error('edit_jumlah')
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

                                            <!-- Modal Hapus Mutasi Masuk -->
                                            <div class="modal fade" id="modalDeleteMutasiMasuk{{ $barang->id }}" tabindex="-1"
                                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Peringatan!!!</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Yakin Ingin Menghapus Barang {{ $barang->nama }}?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary me-2"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('hapusBarang', $barang->id) }}" method="post">
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
                                    <th>Kode</th>
                                    <th>Kategori</th>
                                    <th>SN</th>
                                    <th>Nama</th>
                                    <th>Gudang</th>
                                    <th>Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Opsi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="card-header d-flex align-items-sm-center justify-content-between gap-1">
                            <div class="d-flex align-items-sm-center flex-column flex-sm-row gap-1">
                                <h6 class="m-0 lh-1">Daftar Data Mutasi Keluar</h6>
                            </div>
                            <button type="button" class="btn bg-gradient-danger btn-md" data-bs-toggle="modal"
                            data-bs-target="#modalTambahKeluar">
                                Tambah Mutasi Keluar
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="table2" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Barang</th>
                                        <th>Sumber</th>
                                        <th>Tujuan</th>
                                        <th>Jumlah</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['mutasiKeluar'] as $keluar)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$keluar->barang->nama}}</td>
                                        <td>{{$keluar->gudang->nama}}</td>
                                        <td>{{$keluar->tujuan}}</td>
                                        <td>{{$keluar->jumlah}}</td>
                                        <td>{{$keluar->updated_at}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Barang</th>
                                        <th>Sumber</th>
                                        <th>Tujuan</th>
                                        <th>Jumlah</th>
                                        <th>Waktu</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="card-header d-flex align-items-sm-center justify-content-between gap-1">
                            <div class="d-flex align-items-sm-center flex-column flex-sm-row gap-1">
                                <h6 class="m-0 lh-1">Daftar Data Mutasi Kembali</h6>
                            </div>
                            <button type="button" class="btn bg-gradient-danger btn-md" data-bs-toggle="modal"
                            data-bs-target="#modalTambahKembali">
                                Tambah Mutasi Kembali
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="table3" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Sumber</th>
                                        <th>Barang</th>
                                        <th>Tujuan</th>
                                        <th>Jumlah</th>
                                        <th>Waktu</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['mutasiKembali'] as $kembali)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$kembali->sumber}}</td>
                                        <td>{{$kembali->barang->nama}}</td>
                                        <td>{{$kembali->gudang->nama}}</td>
                                        <td>{{$kembali->jumlah}}</td>
                                        <td>{{$kembali->updated_at}}</td>
                                        <td>
                                            <!-- Button Hapus Mutasi Kembali -->
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#modalDeleteMutasiKembali{{ $kembali->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            <!-- Modal Hapus Mutasi Kembali -->
                                            <div class="modal fade" id="modalDeleteMutasiKembali{{ $kembali->id }}" tabindex="-1"
                                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Peringatan!!!</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Yakin Ingin Menghapus Mutasi Keluar ke-{{ $loop->iteration }}?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary me-2"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('hapusKembali', $kembali->id) }}" method="post">
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
                                        <th>Sumber</th>
                                        <th>Barang</th>
                                        <th>Tujuan</th>
                                        <th>Jumlah</th>
                                        <th>Waktu</th>
                                        <th>Opsi</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    @if ($errors->has('edit_kode') || $errors->has('edit_serial') || $errors->has('edit_nama') || $errors->has('edit_jumlah'))
        $(document).ready(function () {
            $('#modalEditMutasiMasuk').modal('show');
        });
    @endif

    @if ($errors->has('kode') || $errors->has('kategori_id') || $errors->has('serial_number') || $errors->has('nama') || $errors->has('gudang_id') || $errors->has('satuan_id') || $errors->has('jumlah'))
        $(document).ready(function () {
            $('#modalTambahMutasiMasuk').modal('show');
        });
    @endif
</script>


<!-- Modal Tambah Mutasi Masuk-->
<div class="modal fade" id="modalTambahMutasiMasuk" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Tambah Mutasi Masuk</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="{{ route('simpanBarang') }}" method="post">
            @csrf
            <div class="modal-body">
                <a href="{{ route('indexScan') }}" target="_blank" class="btn bg-gradient-success btn-sm me-2 mb-3">
                    Scan Barcode
                </a>
                <div class="form-group">
                    <label for="kodeBarang" class="form-label">Kode: </label>
                    <input type="text" class="form-control" id="kodeBarang" name="kode">
                    @error('kode')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="kategori">Kategori: </label>
                    <select name="kategori_id" id="kategori" class="custom-select">
                        <option value="">Pilih</option>
                        @foreach ($data['kategori'] as $kategori)
                            <option value="{{ $kategori->id }}" class="form-control">
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="serialBarang" class="form-label">SN: </label>
                    <input type="text" class="form-control" id="serialBarang" name="serial_number">
                    @error('serial_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="namaBarang" class="form-label">Nama Barang: </label>
                    <input type="text" class="form-control" id="namaBarang" name="nama">
                    @error('nama')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="gudang">Gudang: </label>
                    <select name="gudang_id" id="gudang" class="custom-select">
                        <option value="">Pilih</option>
                        @foreach ($data['gudang'] as $gudang)
                            <option value="{{ $gudang->id }}" class="form-control">
                                {{ $gudang->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('gudang_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan: </label>
                    <select name="satuan_id" id="satuan" class="custom-select">
                        <option value="">Pilih</option>
                        @foreach ($data['satuan'] as $satuan)
                            <option value="{{ $satuan->id }}" class="form-control">
                                {{ $satuan->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('satuan_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="jumlahBarang" class="form-label">Jumlah: </label>
                    <input type="text" class="form-control" id="jumlahBarang" name="jumlah">
                    @error('jumlah')
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

<!-- Modal Tambah Mutasi Keluar-->
<div class="modal fade" id="modalTambahKeluar" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Tambah Mutasi Keluar</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="{{ route('simpanKeluar') }}" method="post">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="barang">Barang: </label>
                    <select name="barang" id="barang" class="custom-select">
                        <option value="">Pilih</option>
                        @foreach ($data['barang'] as $barang)
                            <option value="{{ $barang->id }}" class="form-control">
                                {{ $barang->nama }} ({{ $barang->serial_number }}) stok: {{ $barang->jumlah }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="jumlah" class="form-label">Jumlah: </label>
                    <input type="text" class="form-control" id="jumlah" name="jumlah">
                </div>
                <div class="form-group">
                    <label for="keGudang">Pelanggan Tujuan: </label>
                    <input type="text" class="form-control" id="tujuan" name="tujuan">
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

<!-- Modal Tambah Mutasi Kembali-->
<div class="modal fade" id="modalTambahKembali" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Tambah Mutasi Kembali   </h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="{{ route('simpanKembali') }}" method="post">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="mutasiKeluar">Sumber: </label>
                    <select name="mutasiKeluar" id="mutasiKeluar" class="custom-select">
                        <option value="">Pilih</option>
                        @foreach ($data['mutasiKeluar'] as $keluar)
                            <option value="{{ $keluar->id }}" class="form-control">
                                {{ $keluar->tujuan }} ({{ $keluar->barang->nama }}) - ({{ $keluar->jumlah }})
                            </option>
                        @endforeach
                    </select>
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
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('#table1').DataTable({
            //disable sorting on last column
            "columnDefs": [
                { "orderable": false, "targets": 8 }
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
        $(document).ready(function() {
            $('#table2').DataTable({
            //disable sorting on last column
            "columnDefs": [
                { "orderable": false, "targets": 5 }
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
        $(document).ready(function() {
            $('#table3').DataTable({
            //disable sorting on last column
            "columnDefs": [
                { "orderable": false, "targets": 6 }
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

<script>
    function handleEdit(idMutasiMasuk) {
        document.getElementById('formEditMutasiMasuk').action = '/admin/barang/update-barang/' + idMutasiMasuk
        const kodeBarang = document.querySelector('#barangKode'+idMutasiMasuk).textContent
        const serialBarang = document.querySelector('#barangSerial'+idMutasiMasuk).textContent
        const namaBarang = document.querySelector('#barangNama'+idMutasiMasuk).textContent
        const jumlahBarang = document.querySelector('#barangJumlah'+idMutasiMasuk).textContent
        document.querySelector('#modalEditMutasiMasuk #kodeBarang').value = kodeBarang
        document.querySelector('#modalEditMutasiMasuk #serialBarang').value = serialBarang
        document.querySelector('#modalEditMutasiMasuk #namaBarang').value = namaBarang
        document.querySelector('#modalEditMutasiMasuk #jumlahBarang').value = jumlahBarang
    }
</script>
