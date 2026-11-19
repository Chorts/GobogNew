@extends('layouts.app')
@section('title', 'Edit Tenan')
@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.tenan.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0">Edit Tenan</h4>
</div>

<div class="card border-0 shadow-sm" style="max-width:560px">
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin.tenan.update', $tenan) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Tenan</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $tenan->nama) }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Keterangan <span class="text-muted fw-normal">(opsional)</span></label>
                <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $tenan->keterangan) }}">
            </div>
            <button type="submit" class="btn btn-dark fw-semibold">
                <i class="bi bi-save me-1"></i>Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
