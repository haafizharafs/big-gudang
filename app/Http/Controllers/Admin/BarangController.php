<?php

namespace App\Http\Controllers\Admin;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Kategori;
use App\Models\Satuan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Alert;

class BarangController extends Controller
{
    //
    public function index()
    {
        $data['gudang'] = Gudang::all();
        $data['barang'] = Barang::all();
        $data['kategori'] = Kategori::all();
        $data['satuan'] = Satuan::all();
        $this->addBreadcrumb('barang', url('admin/barang'));
        return view('pages.admin.gudang.barang', compact('data'));
    }

    public function simpan(Request $request)
    {
        // $barang = Barang::create([
        //     'kode' => $request->kode,
        //     'serial_number' => $request->serial,
        //     'nama' => $request->nama,
        //     'gudang_id' => $request->gudang,
        //     'kategori_id' => $request->kategori,
        //     'satuan_id' => $request->satuan,
        //     'jumlah' => $request->jumlah,
        // ]);

        $validateBarang = $request->validate([
            'kode' => 'required',
            'kategori_id' => 'required',
            'serial_number' => 'nullable|unique:barangs,serial_number',
            'nama' => 'required',
            'gudang_id' => 'required',
            'satuan_id' => 'required',
            'jumlah' => 'required|numeric'
        ], [
            'kode.required' => 'Kode wajib diisi!',
            'kategori_id.required' => 'Kategori wajib diisi!',
            'serial_number.unique' => 'SN telah terdaftar!',
            'nama.required' => 'Nama Barang wajib diisi!',
            'gudang_id.required' => 'Gudang wajib diisi!',
            'satuan_id.required' => 'Satuan wajib diisi!',
            'jumlah.required' => 'Jumlah wajib diisi!',
            'jumlah.numeric' => 'Jumlah wajib berupa angka!',
        ]);

        Barang::create($validateBarang);

        Alert::success('Berhasil',"Barang $request->nama berhasil ditambahkan");
        return redirect()->back()->with('success', 'Data added successfully');
        // return response()->json(['message' => 'added successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required',
            'serial_number' => 'nullable',
            'nama' => 'required',
            'jumlah' => 'required|numeric',
        ], [
            'kode.required' => 'Kode wajib diisi!',
            'nama.required' => 'Nama Barang wajib diisi!',
            'jumlah.required' => 'Jumlah wajib diisi!',
            'jumlah.numeric' => 'Jumlah wajib berupa angka!',
        ]);
        $barang = Barang::where('id', $id)->first();
        $barang->kode = $request->kode;
        $barang->serial_number = $request->serial;
        $barang->nama = $request->nama;
        $barang->jumlah = $request->jumlah;
        $barang->save();

        Alert::success('Berhasil',"Barang $request->nama berhasil diedit");
        return redirect()->back();
    }

    public function hapus($id)
    {
        $barang = Barang::find($id);
        $barang->delete();

        Alert::success('Berhasil',"$barang->nama berhasil dihapus");
        return redirect()->back();
    }
}
