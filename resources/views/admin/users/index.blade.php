@extends('layouts.app')
@section('title', 'Kelola Admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Kelola Admin</h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-dark btn-sm">
        <i class="bi bi-person-plus me-1"></i>Tambah Admin
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 45px;">No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th class="text-center">Transaksi Dicatat</th>
                        <th class="text-center" data-orderable="false">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                        <td class="fw-semibold">
                            {{ $user->nama }}
                            @if(auth()->id() === $user->id)
                                <span class="badge bg-light text-dark border ms-1">Anda</span>
                            @endif
                        </td>
                        <td><code>{{ $user->username }}</code></td>
                        <td class="text-center"><span class="badge bg-light text-dark border rounded-pill">{{ $user->transactions_count }}x</span></td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border py-1 px-2" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('admin.users.edit', $user) }}">
                                            <i class="bi bi-pencil text-secondary me-2"></i>Edit
                                        </a>
                                    </li>
                                    @if(auth()->id() !== $user->id)
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus admin ini?')" class="m-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 text-danger">
                                                    <i class="bi bi-trash text-danger me-2"></i>Hapus
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
