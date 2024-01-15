<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Satuan;
use Alert;

class SatuanController extends Controller
{
    //
    public function index()
    {
        $data = Satuan::all();
        $this->addBreadcrumb('satuan', url('admin/satuan'));
        return view('pages.admin.gudang.satuan')->with('data',$data);
    }

    public function simpan(Request $request)
    {
        //
        $validateSatuan = $request->validate([
            'nama' => 'required|unique:satuans,nama',
        ], [
            'nama.required' => 'Nama Satuan wajib diisi!',
            'nama.unique' => 'Satuan telah terdaftar!',
        ]);
        $satuan = Satuan::create($validateSatuan);

        Alert::success('Berhasil',"Satuan $request->nama berhasil ditambahkan");
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'edit_nama' => 'required|unique:satuans,nama',
        ], [
            'edit_nama.required' => 'Nama Satuan wajib diisi!',
            'edit_nama.unique' => 'Satuan telah terdaftar!',
        ]);
        $satuan = Satuan::where('id', $id)->first();
        $satuan->nama = $request->edit_nama;
        $satuan->save();

        Alert::success('Berhasil',"Satuan $request->nama berhasil diedit");
        return redirect()->back();
    }

    public function delete($id)
    {
        $satuan = Satuan::find($id);
        $satuan->delete();

        Alert::success('Berhasil',"$satuan->nama berhasil dihapus");
        return redirect()->back();
    }
}
