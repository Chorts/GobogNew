@extends('layouts.app')
@section('title', 'Edit Admin')
@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0">Edit Admin</h4>
</div>

<div class="card border-0 shadow-sm" style="max-width:560px">
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Password Baru <span class="text-muted fw-normal">(kosongkan jika tidak diganti)</span></label>
                <input type="password" name="password" class="form-control" minlength="6">
            </div>
            <button type="submit" class="btn btn-dark fw-semibold">
                <i class="bi bi-save me-1"></i>Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
