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
            <!-- Слайд 1: Вибір або створення організації -->
            <div class="initial-slide slide active" id="slide-organization">
                <h4>Оберіть або створіть організацію</h4>

                <!-- Виведення повідомлення про успіх -->
                <div id="organization-success" class="alert alert-success" style="display: none;"></div>

                <!-- Поля для вибору або створення організації -->
                @csrf
                <div class="mb-3">
                    <label for="organization_id" class="form-label">Виберіть організацію</label>
                    <select name="organization_id" id="organization_id" class="form-select">
                        <option value="">Створити нову</option>
                        @foreach ($organizations as $organization)
                            <option value="{{ $organization->id }}">
                                {{ $organization->name }} (Тип: {{ $organization->type->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="new-organization-fields">
                    <div class="mb-3">
                        <label for="name" class="form-label">Назва організації</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="organization_type_id" class="form-label">Тип організації</label>
                        <select name="organization_type_id" id="organization_type_id" class="form-select">
                            <option value="">Оберіть тип</option>
                            @foreach ($organizationTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Введіть рік</label>
                    <input type="number" name="year" id="year" class="form-control" min="1901" max="2100" required>
                </div>

                <div id="duplicate-error" class="alert alert-danger" style="display: none;"></div>

                <button type="button" class="btn btn-primary" id="save-organization">Далі</button>
            </div>

            <!-- Слайд 2: Вибір НС -->
            <div class="slide" id="slide-scenarios">
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
