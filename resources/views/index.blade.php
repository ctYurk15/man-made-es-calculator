<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Оцінка ймовірності НС</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css')}}">
    <link rel="stylesheet" href="{{ asset('css/index.css')}}">
</head>
<body>
    <div class="container mt-5">
        <h1>Оцінка ймовірності надзвичайних ситуацій</h1>
        <form id="risk-form">
            <!-- Слайд 1: Вибір НС -->
            <div class="initial-slide slide active" id="slide-1">
                <h4>Виберіть можливі НС на вашому підприємстві</h4>
                <div id="scenarios-list">
                    @foreach ($scenarios as $scenario)
                        <div class="form-check mb-2">
                            <input class="form-check-input scenario-checkbox" type="checkbox" value="{{ $scenario->name }}"
                                   id="scenario-{{ $scenario->id }}">
                            <label class="form-check-label fw-bold" for="scenario-{{ $scenario->id }}">{{ $scenario->name }}</label>
                            <small class="text-muted d-block">{{ $scenario->description }}</small>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-primary next-slide">Далі</button>
            </div>

            <!-- Динамічно створені слайди -->
            <div id="dynamic-slides"></div>

            <!-- Фінальний слайд -->
            <div class="slide" id="final-slide">
                <h4>Розрахунок</h4>
                <p>Натисніть "Розрахувати", щоб переглянути результати.</p>
                <button type="button" class="btn btn-secondary prev-slide">Назад</button>
                <button type="submit" class="btn btn-success">Розрахувати</button>
            </div>
        </form>

        <div id="results" class="mt-5" style="display: none;">
            <h4>Результати</h4>
            <ul id="results-list"></ul>
        </div>
    </div>
</body>

<script src="{{ asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/index.js')}}"></script>

</html>
