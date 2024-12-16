<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Мій проект')</title>

    <!-- Підключення CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Ваш кастомний CSS, якщо потрібно -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Мій Проект</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.page') }}">Головна</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('emergency-scenarios.index') }}">Сценарії НС</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('organization-types.index') }}">Типи організацій</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('organizations.index') }}">Організації</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</nav>

<div class="container mt-5">
    @yield('content')
</div>

<!-- Підключення JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script> <!-- Ваш кастомний JS, якщо потрібно -->
</body>
</html>
