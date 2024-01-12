<?php

namespace App\Http\Controllers\Apis;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WilayahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Wilayah::latest()->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_wilayah' => 'required',
            'ket' => 'nullable',
        ]);
        Wilayah::create($validatedData);
        return response()->json([
            'status' => 200,
            'message' => 'Wilayah baru telah ditambahkan',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Wilayah::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_wilayah' => 'required',
            'ket' => 'nullable',
        ]);
        Wilayah::find($id)->update($validatedData);
        return response()->json([
            'status' => 200,
            'message' => 'Wilayah berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $wilayah = Wilayah::findOrFail($id);
            $wilayah->delete();

            return response()->json([
                'message' => 'Data wilayah has been deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Wilayah ini tidak dapat dihapus',
            ], 500);
        }
    }

}
