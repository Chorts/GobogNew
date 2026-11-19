<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Gobog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #181c24;
            min-height: 100vh;
            color: #212529;
        }
        .card-login {
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
    <div class="card card-login shadow-sm border-0">
        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4">
                <i class="bi bi-coin fs-1 text-dark"></i>
                <h3 class="fw-bold mt-2 mb-1">Sistem Gobog</h3>
                <p class="text-muted small mb-0">Pasar Preng Sewu &mdash; Login Admin</p>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger py-2 small border-0 mb-3">
                    @foreach ($errors->all() as $error)
                        <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                               value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-dark w-100 fw-semibold py-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
