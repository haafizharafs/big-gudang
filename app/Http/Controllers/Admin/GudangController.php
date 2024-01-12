<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gudang;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Alert;

class GudangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Gudang::all();
        $this->addBreadcrumb('gudang', url('admin/gudang'));
        return view('pages.admin.gudang.gudang', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function simpan(Request $request)
    {
        $validateGudang = $request->validate([
            'nama' => 'required|unique:gudangs,nama',
            'alamat' => 'required',
        ], [
            'nama.required' => 'Nama Gudang wajib diisi!',
            'nama.unique' => 'Gudang telah terdaftar!',
            'alamat.required' => 'Alamat wajib diisi!',
        ]);
        Gudang::create($validateGudang);

        Alert::success('Berhasil',"Gudang $request->nama berhasil ditambahkan");
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|unique:gudangs,nama',
            'alamat' => 'required',
        ], [
            'nama.required' => 'Nama Gudang wajib diisi!',
            'nama.unique' => 'Gudang telah terdaftar!',
            'alamat.required' => 'Alamat wajib diisi!',
        ]);
        $gudang = Gudang::where('id', $id)->first();
        $gudang->nama = $request->nama;
        $gudang->alamat = $request->alamat;
        $gudang->save();

        Alert::success('Berhasil',"Gudang $request->nama berhasil diedit");
        return redirect()->back();
    }

    public function hapus($id)
    {
        $gudang = Gudang::find($id);
        $gudang->delete();

        Alert::success('Berhasil',"$gudang->nama berhasil dihapus");
        return redirect()->back();
    }
}
