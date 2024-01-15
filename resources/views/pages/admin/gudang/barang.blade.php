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
@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Barang'])
@include('sweetalert::alert')
<div class="container-fluid px-0 px-sm-4 py-3">
    <div class="card md-3">
        <div class="card-header d-flex align-items-sm-center justify-content-between gap-1">
            <div class="d-flex align-items-sm-center flex-column flex-sm-row gap-1">
                <h6 class="m-0 lh-1">Daftar Data Barang</h6>
            </div>
            {{-- <button type="button" class="btn bg-gradient-danger btn-md" onclick="bukaModalTambahBarang()">
                Tambah Barang
            </button> --}}
        </div>
        <div class="card-body">
            <div class="card-text float-end">
                <a href="{{route('indexKategori')}}" class="btn btn-success btn-xs me-2">
                Kategori
                </a>
                <a href="{{route('indexSatuan')}}" class="btn btn-success btn-xs">
                Satuan
                </a>
            </div>
        </div>

        <script>
            @if ($errors->has('edit_kode') || $errors->has('edit_serial') || $errors->has('edit_nama') || $errors->has('edit_jumlah'))
                $(document).ready(function () {
                    $('#modalEditBarang').modal('show');
                });
            @endif
        </script>

        <!-- Modal Tambah Barang-->
        <div class="modal fade" id="modalTambahBarang" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Barang</h5>
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
                        </div>
                        <div class="form-group">
                            <label for="kategori">Kategori: </label>
                            <select name="kategori" id="kategori" class="custom-select">
                                <option value="">Pilih</option>
                                @foreach ($data['kategori'] as $kategori)
                                    <option value="{{ $kategori->id }}" class="form-control">
                                        {{ $kategori->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="serialBarang" class="form-label">SN: </label>
                            <input type="text" class="form-control" id="serialBarang" name="serial">
                        </div>
                        <div class="form-group">
                            <label for="namaBarang" class="form-label">Nama Barang: </label>
                            <input type="text" class="form-control" id="namaBarang" name="nama">
                        </div>
                        <div class="form-group">
                            <label for="gudang">Gudang: </label>
                            <select name="gudang" id="gudang" class="custom-select">
                                <option value="">Pilih</option>
                                @foreach ($data['gudang'] as $gudang)
                                    <option value="{{ $gudang->id }}" class="form-control">
                                        {{ $gudang->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="satuan">Satuan: </label>
                            <select name="satuan" id="satuan" class="custom-select">
                                <option value="">Pilih</option>
                                @foreach ($data['satuan'] as $satuan)
                                    <option value="{{ $satuan->id }}" class="form-control">
                                        {{ $satuan->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jumlahBarang" class="form-label">Jumlah: </label>
                            <input type="text" class="form-control" id="jumlahBarang" name="jumlah">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Tambah Kategori-->
        <div class="modal fade" id="modalTambahKategori" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('simpanKategori') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="namaKategori" class="form-label">Nama Kategori: </label>
                            <input type="text" class="form-control" id="namaKategori" name="nama">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Tambah Satuan-->
        <div class="modal fade" id="modalTambahSatuan" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Satuan</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('simpanSatuan') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="namaSatuan" class="form-label">Nama Satuan: </label>
                            <input type="text" class="form-control" id="namaSatuan" name="nama">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                                <!-- Button Edit Barang -->
                                <button type="button" class="btn btn-warning" onclick="handleEdit({{ $barang->id }})"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditBarang">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Button Hapus Barang -->
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#modalDeleteBarang{{ $barang->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal Edit Barang -->
                                <div class="modal fade" id="modalEditBarang" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Barang</h5>
                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form id="formEditBarang" action="updateBarang/" method="post">
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

                                <!-- Modal Hapus Barang -->
                                <div class="modal fade" id="modalDeleteBarang{{ $barang->id }}" tabindex="-1"
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
                                                <button type="button" class="btn btn-secondary"
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
</div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
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

<!-- include the library -->
<script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>

<!-- CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/html5-qrcode@2.0.1/dist/html5-qrcode.min.css">

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.0.1/dist/html5-qrcode.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function handleEdit(idBarang) {
        document.getElementById('formEditBarang').action = '/admin/barang/update-barang/' + idBarang
        const kodeBarang = document.querySelector('#barangKode'+idBarang).textContent
        const serialBarang = document.querySelector('#barangSerial'+idBarang).textContent
        const namaBarang = document.querySelector('#barangNama'+idBarang).textContent
        const jumlahBarang = document.querySelector('#barangJumlah'+idBarang).textContent
        document.querySelector('#modalEditBarang #kodeBarang').value = kodeBarang
        document.querySelector('#modalEditBarang #serialBarang').value = serialBarang
        document.querySelector('#modalEditBarang #namaBarang').value = namaBarang
        document.querySelector('#modalEditBarang #jumlahBarang').value = jumlahBarang
    }

    function bukaModalTambahBarang() {
    // Mengambil nilai dari sessionStorage
    var dataFromScan = sessionStorage.getItem('dataFromScan');

    // Mengisi field pada modal dengan nilai dari sessionStorage
    document.getElementById('serialBarang').value = dataFromScan;

    // Menampilkan modal dengan ID modalTambahBarang
    $('#modalTambahBarang').modal('show');
    $('#modalTambahMutasiMasuk').modal('show');
    }

    //scanner
    function onScanSuccess(decodedText, decodedResult) {
    console.log(`Code scanned = ${decodedText}`, decodedResult);
    }
    var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 24, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
</script>
