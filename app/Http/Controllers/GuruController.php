<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;



class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mapel = Mapel::orderBy('nama_mapel', 'asc')->get();
        $guru = Guru::orderBy('nama', 'asc')->get();
        return view('pages.admin.guru.index', compact('guru', 'mapel'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mapels = Mapel::all();
        return view('pages.guru.create', compact('mapels'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {

    //     $this->validate($request, [
    //         'nama' => 'required',
    //         'nip' => 'required|unique:gurus',
    //         'no_telp' => 'required',
    //         'alamat' => 'required',
    //         'mapel_id' => 'required',
    //         'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    //     ], [
    //         'nip.unique' => 'NIP sudah terdaftar',
    //     ]);

    //     if(isset($request->foto)){
    //         $file = $request->file('foto');
    //         $namaFoto = time() . '.' . $file->getClientOriginalExtension();
    //         $foto = $file->storeAs('images/guru', $namaFoto, 'public');
    //     }

    //     $guru = new Guru;
    //     $guru->nama = $request->nama;
    //     $guru->nip = $request->nip;
    //     $guru->no_telp = $request->no_telp;
    //     $guru->alamat = $request->alamat;
    //     $guru->mapel_id = $request->mapel_id;
    //     $guru->foto = $foto;
    //     $guru->save();

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nip' => 'required|unique:gurus',
            'no_telp' => 'required',
            'alamat' => 'required',
            'mapel_id' => 'required|exists:mapels,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nip.unique' => 'NIP sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih.',
        ]);

        // Simpan ke tabel users
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'nip' => $request->nip,
            'roles' => 'guru',
            'password' => Hash::make($request->password),
        ]);

        // Upload foto jika ada
        $foto = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFoto = time() . '.' . $file->getClientOriginalExtension();
            $foto = $file->storeAs('images/guru', $namaFoto, 'public');
        }

        // Simpan ke tabel gurus
        Guru::create([
            'user_id' => $user->id,
            'nama' => $request->nama,
            'nip' => $request->nip,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'mapel_id' => $request->mapel_id,
            'foto' => $foto,
        ]);

        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }


    //     return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan');
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $guru = Guru::findOrFail($id);

        return view('pages.admin.guru.profile', compact('guru'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $mapel = Mapel::all();
        $guru = Guru::findOrFail($id);

        return view('pages.admin.guru.edit', compact('guru', 'mapel'));
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
        $this->validate($request, [
            'nip' => 'required|unique:gurus'
        ], [
            'nip.unique' => 'NIP sudah terdaftar',
        ]);

        $guru = Guru::find($id);
        $guru->nama = $request->input('nama');
        $guru->nip = $request->input('nip');
        $guru->no_telp = $request->input('no_telp');
        $guru->alamat = $request->input('alamat');
        $guru->mapel_id = $request->input('mapel_id');

        if ($request->hasFile('foto')) {
            $lokasi = 'images/guru/' . $guru->foto;
            if (File::exists($lokasi)) {
                File::delete($lokasi);
            }
            $foto = $request->file('foto');
            $namaFoto = time() . '.' . $foto->getClientOriginalExtension();
            $tujuanFoto = public_path('/images/guru');
            $foto->move($tujuanFoto, $namaFoto);
            $guru->foto = $namaFoto;
        }

        $guru->update();

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $guru = Guru::find($id);
        $guru->delete();

        // Hapus data user
        if ($user = User::where('id', $guru->user_id)->first()) {
            $user->delete();
        }

        return back()->with('success', 'Data mapel berhasil dihapus!');
    }
}
