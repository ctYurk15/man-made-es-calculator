<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оцінка ймовірності НС</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <style>
        .slide { display: none; }
        .slide.active { display: block; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Оцінка ймовірності надзвичайних ситуацій</h1>
    <form id="risk-form">
        <!-- Слайд 1: Вибір НС -->
        <div class="slide active" id="slide-1">
            <h4>Можливі НС</h4>
            <div id="scenarios-list">
                @foreach (['Пожежа', 'Вибух', 'Розлив хімічних речовин', 'Збої в роботі обладнання'] as $scenario)
                    <div class="form-check">
                        <input class="form-check-input scenario-checkbox" type="checkbox" value="{{ $scenario }}" id="scenario-{{ $loop->index }}">
                        <label class="form-check-label" for="scenario-{{ $loop->index }}">{{ $scenario }}</label>
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

<script>
    $(document).ready(function () {
        let scenarios = [];

        // Перехід між слайдами
        function switchSlide(current, next) {
            $(current).removeClass('active');
            $(next).addClass('active');
        }

        // Обробка "Далі" на першому слайді
        $('.next-slide').click(function () {
            scenarios = [];
            $('.scenario-checkbox:checked').each(function () {
                scenarios.push($(this).val());
            });

            if (scenarios.length === 0) {
                alert('Оберіть хоча б одну НС!');
                return;
            }

            // Створення динамічних слайдів
            $('#dynamic-slides').empty();
            scenarios.forEach((scenario, index) => {
                const slide = `
                        <div class="slide" id="slide-${index + 2}">
                            <h4>${scenario}</h4>
                            <h5>Дані про технічний стан обладнання</h5>
                            <div class="mb-3">
                                <label class="form-label">Рівень зношеності (%)</label>
                                <input type="number" name="equipmentWear[]" class="form-control" min="0" max="100" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Частота обслуговування (за рік)</label>
                                <input type="number" name="maintenanceFrequency[]" class="form-control" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Тип обладнання</label>
                                <input type="text" name="equipmentType[]" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Дата останньої перевірки</label>
                                <input type="date" name="lastCheck[]" class="form-control" required>
                            </div>

                            <h5>Дані про навчання персоналу</h5>
                            <div class="mb-3">
                                <label class="form-label">Кількість навчань</label>
                                <input type="number" name="trainingCount[]" class="form-control" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Відсоток атестації</label>
                                <input type="number" name="certifiedEmployees[]" class="form-control" min="0" max="100" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Оцінка знань (%)</label>
                                <input type="number" name="knowledgeScore[]" class="form-control" min="0" max="100" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Категорії навчань</label>
                                <input type="text" name="trainingCategories[]" class="form-control" required>
                            </div>

                            <h5>Зовнішні фактори</h5>
                            <div class="mb-3">
                                <label class="form-label">Погодні умови</label>
                                <input type="text" name="weatherConditions[]" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Географічні особливості</label>
                                <input type="text" name="geographicalFeatures[]" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Природні загрози</label>
                                <input type="text" name="naturalThreats[]" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Гранично допустимі норми</label>
                                <input type="text" name="normative.limits[]" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Нормативні документи</label>
                                <input type="text" name="normative.standards[]" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Контрольні значення</label>
                                <input type="text" name="normative.controls[]" class="form-control" required>
                            </div>

                            <button type="button" class="btn btn-secondary prev-slide">Назад</button>
                            <button type="button" class="btn btn-primary next-slide">Далі</button>
                        </div>`;
                $('#dynamic-slides').append(slide);
            });

            // Перехід на перший слайд динамічного блоку
            switchSlide('#slide-1', '#slide-2');
        });

        // Перемикання між слайдами
        $(document).on('click', '.next-slide', function () {
            const currentSlide = $(this).closest('.slide');
            const allSlides = $('.slide'); // Збираємо всі слайди
            const currentIndex = allSlides.index(currentSlide); // Індекс поточного слайда
            const nextSlide = allSlides.eq(currentIndex + 1); // Наступний слайд

            if (nextSlide.length > 0) {
                switchSlide(currentSlide, nextSlide);
            }
        });

        $(document).on('click', '.prev-slide', function () {
            const currentSlide = $(this).closest('.slide');
            const allSlides = $('.slide'); // Збираємо всі слайди
            const currentIndex = allSlides.index(currentSlide); // Індекс поточного слайда
            const prevSlide = allSlides.eq(currentIndex - 1); // Попередній слайд

            if (prevSlide.length > 0) {
                switchSlide(currentSlide, prevSlide);
            }
        });

        // Відправка форми
        $('#risk-form').on('submit', function (e) {
            e.preventDefault();

            // Збираємо вибрані сценарії
            const scenarios = [];
            $('.scenario-checkbox:checked').each(function () {
                scenarios.push($(this).val());
            });

            // Перевірка, чи вибрано хоча б один сценарій
            if (scenarios.length === 0) {
                alert('Оберіть хоча б один сценарій!');
                return;
            }

            // Збираємо дані з форми у структурований об'єкт
            const formData = $(this).serializeArray();
            const structuredData = {
                normative: {
                    limits: [],
                    standards: [],
                    controls: []
                }
            };

            formData.forEach((field) => {
                if (field.name.startsWith('normative.limits')) {
                    structuredData.normative.limits.push(field.value);
                } else if (field.name.startsWith('normative.standards')) {
                    structuredData.normative.standards.push(field.value);
                } else if (field.name.startsWith('normative.controls')) {
                    structuredData.normative.controls.push(field.value);
                } else if (structuredData[field.name]) {
                    if (Array.isArray(structuredData[field.name])) {
                        structuredData[field.name].push(field.value);
                    } else {
                        structuredData[field.name] = [structuredData[field.name], field.value];
                    }
                } else {
                    structuredData[field.name] = field.value;
                }
            });

            // Додаємо сценарії
            structuredData.scenarios = scenarios;

            // Відправка AJAX-запиту
            $.ajax({
                url: '/calculate',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: structuredData,
                success: function (response) {
                    $('#results').show();
                    $('#results-list').empty();
                    response.forEach(function (result) {
                        $('#results-list').append(`<li>${result.scenario}: ${result.probability}%</li>`);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Помилка:', error);
                    alert('Сталася помилка при обробці запиту.');
                }
            });
        });
    });
</script>
</body>
</html>
