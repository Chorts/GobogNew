@extends('layouts.app')
@section('title', 'Kelola Tenan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-shop me-2"></i>Kelola Tenan</h4>
    <a href="{{ route('admin.tenan.create') }}" class="btn btn-dark btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Tambah Tenan
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($tenans->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shop fs-1 d-block mb-2"></i>
                <p class="mb-2">Belum ada data tenan.</p>
                <a href="{{ route('admin.tenan.create') }}" class="btn btn-sm btn-outline-dark">Tambah Tenan Pertama</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Nama Tenan</th>
                            <th>Keterangan</th>
                            <th class="text-center">Transaksi</th>
                            <th class="text-center" data-orderable="false">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenans as $tenan)
                        <tr>
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $tenan->nama }}</td>
                            <td class="small text-muted">{{ $tenan->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill">{{ $tenan->transactions_count }}x</span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border py-1 px-2" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('admin.tenan.edit', $tenan) }}">
                                                <i class="bi bi-pencil text-secondary me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('admin.laporan.index', ['tenan_id' => $tenan->id]) }}">
                                                <i class="bi bi-file-earmark-bar-graph text-secondary me-2"></i>Lihat Laporan
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.tenan.destroy', $tenan) }}" onsubmit="return confirm('Hapus tenan ini?')" class="m-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 text-danger">
                                                    <i class="bi bi-trash text-danger me-2"></i>Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
