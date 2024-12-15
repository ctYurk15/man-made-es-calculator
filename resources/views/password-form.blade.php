<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Введення пароля</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Введіть пароль для доступу</h1>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('password.validate') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>

            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Увійти</button>
    </form>
</div>
</body>
</html>
