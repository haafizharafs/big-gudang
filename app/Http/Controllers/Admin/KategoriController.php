<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kategori;
use Alert;

class KategoriController extends Controller
{
    //
    public function index()
    {
        $data = Kategori::all();
        $this->addBreadcrumb('kategori', url('admin/kategori'));
        return view('pages.admin.gudang.kategori')->with('data',$data);
    }

    public function simpan(Request $request)
    {
        //
        $validateKategori = $request->validate([
            'nama' => 'required|unique:kategoris,nama',
        ], [
            'nama.required' => 'Nama Kategori wajib diisi!',
            'nama.unique' => 'Kategori telah terdaftar!',
        ]);
        $kategori = Kategori::create($validateKategori);

        Alert::success('Berhasil',"Kategori $request->nama berhasil ditambahkan");
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'edit_nama'=>'required|unique:kategoris,nama',
        ], [
            'edit_nama.required' => 'Nama Kategori wajib diisi',
            'edit_nama.unique' => 'Kategori telah terdaftar!',
        ]);
        $kategori = Kategori::where('id', $id)->first();
        $kategori->nama = $request->edit_nama;
        $kategori->save();

        Alert::success('Berhasil',"Kategori $request->nama berhasil diedit");
        return redirect()->back();
    }

    public function delete($id)
    {
        $kategori = Kategori::find($id);
        $kategori->delete();

        Alert::success('Berhasil',"$kategori->nama berhasil dihapus");
        return redirect()->back();
    }
}
