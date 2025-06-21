@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h2>Tambah Data Guru</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops!</strong> Ada kesalahan pada input:<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('guru.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mt-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label>No Telepon</label>
            <input type="text" name="no_telp" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="2" required></textarea>
        </div>

        <div class="form-group mt-3">
            <label>Mata Pelajaran</label>
            <select name="mapel_id" class="form-control" required>
                <option value="">-- Pilih Mata Pelajaran --</option>
                @foreach($mapels as $mapel)
                    <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label>Foto</label>
            <input type="file" name="foto" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary mt-4">Simpan</button>
    </form>
</div>
@endsection
