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
                @foreach ($scenarios as $scenario)
                    <div class="form-check">
                        <input class="form-check-input scenario-checkbox" type="checkbox" value="{{ $scenario['name'] }}" id="scenario-{{ $scenario['id'] }}">
                        <label class="form-check-label" for="scenario-{{ $scenario['id'] }}">
                            {{ $scenario['name'] }}
                        </label>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-primary next-slide">Далі</button>
        </div>

        <!-- Слайди для кожної НС -->
        <div id="dynamic-slides"></div>

        <!-- Останній слайд -->
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

        // Зберегти вибрані НС і створити слайди для кожної
        $('.next-slide').click(function () {
            scenarios = [];
            $('.scenario-checkbox:checked').each(function () {
                scenarios.push($(this).val());
            });

            if (scenarios.length === 0) {
                alert('Оберіть хоча б одну НС!');
                return;
            }

            // Динамічно створити слайди
            $('#dynamic-slides').empty();
            scenarios.forEach((scenario, index) => {
                const slide = `
                        <div class="slide" id="slide-${index + 2}">
                            <h4>${scenario}</h4>
                            <div class="mb-3">
                                <label class="form-label">Тип підприємства</label>
                                <select name="enterpriseType[]" class="form-select" required>
                                    <option value="Промисловість">Промисловість</option>
                                    <option value="Хімічне виробництво">Хімічне виробництво</option>
                                    <option value="Енергетика">Енергетика</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Рівень зносу обладнання (%)</label>
                                <input type="number" name="equipmentWear[]" class="form-control" min="0" max="100" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Частота обслуговування</label>
                                <select name="maintenanceFrequency[]" class="form-select" required>
                                    <option value="Регулярно">Регулярно</option>
                                    <option value="Зрідка">Зрідка</option>
                                    <option value="Ніколи">Ніколи</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Середня кількість годин навчання</label>
                                <input type="number" name="trainingHours[]" class="form-control" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Відсоток атестованих працівників (%)</label>
                                <input type="number" name="certifiedEmployees[]" class="form-control" min="0" max="100" required>
                            </div>
                            <button type="button" class="btn btn-secondary prev-slide">Назад</button>
                            <button type="button" class="btn btn-primary next-slide">Далі</button>
                        </div>`;
                $('#dynamic-slides').append(slide);
            });

            switchSlide('#slide-1', '#slide-2');
        });

        // Перемикання "Назад"
        $(document).on('click', '.prev-slide', function () {
            const currentSlide = $(this).closest('.slide');
            const allSlides = $('.slide'); // Всі слайди в документі
            const currentIndex = allSlides.index(currentSlide); // Індекс поточного слайда
            const prevSlide = allSlides.eq(currentIndex - 1); // Попередній слайд за індексом

            if (prevSlide.length > 0) {
                currentSlide.removeClass('active');
                prevSlide.addClass('active');
            } else {
                console.error('Попередній слайд не знайдено');
                alert('Попередній слайд відсутній!');
            }
        });

        // Перемикання "Далі"
        $(document).on('click', '.next-slide', function () {
            const currentSlide = $(this).closest('.slide');
            const allSlides = $('.slide'); // Всі слайди в документі
            const currentIndex = allSlides.index(currentSlide); // Індекс поточного слайда
            const nextSlide = allSlides.eq(currentIndex + 1); // Наступний слайд за індексом

            if (nextSlide.length > 0) {
                currentSlide.removeClass('active');
                nextSlide.addClass('active');
            } else {
                console.error('Наступний слайд не знайдено');
                alert('Наступний слайд відсутній!');
            }
        });


        // Відправка форми
        $('#risk-form').on('submit', function (e) {
            e.preventDefault();

            let formData = {
                _token: '{{ csrf_token() }}',
                scenarios: scenarios,
                enterpriseTypes: $('select[name="enterpriseType[]"]').map(function () { return $(this).val(); }).get(),
                equipmentWears: $('input[name="equipmentWear[]"]').map(function () { return $(this).val(); }).get(),
                maintenanceFrequencies: $('select[name="maintenanceFrequency[]"]').map(function () { return $(this).val(); }).get(),
                trainingHours: $('input[name="trainingHours[]"]').map(function () { return $(this).val(); }).get(),
                certifiedEmployees: $('input[name="certifiedEmployees[]"]').map(function () { return $(this).val(); }).get(),
            };

            $.ajax({
                url: '/calculate',
                method: 'POST',
                data: formData,
                success: function (response) {
                    $('#results').show();
                    $('#results-list').empty();
                    response.forEach(function (result) {
                        $('#results-list').append(`<li>${result.scenario}: ${result.probability}%</li>`);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Помилка AJAX:', error);
                    alert('Сталася помилка під час обробки запиту. Перевірте введені дані та спробуйте ще раз.');
                }
            });
        });
    });
</script>
</body>
</html>
