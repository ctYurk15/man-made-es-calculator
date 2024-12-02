$(document).ready(function () {
    let scenarios = [];

    let csrf_token = $('meta[name=csrf-token]').attr('content');

    const initial_slide = $('.initial-slide');

    // Перехід між слайдами
    function switchSlide(current, next) {
        $(current).removeClass('active');
        $(next).addClass('active');
    }

    /// Обробка "Далі" на першому слайді
    $('.next-slide').click(function () {
        const scenarios = [];
        $('.scenario-checkbox:checked').each(function () {
            scenarios.push($(this).val());
        });

        if (scenarios.length === 0) {
            alert('Оберіть хоча б одну НС!');
            return;
        }

        // Генерація динамічних слайдів
        const slideContainer = $('#dynamic-slides');
        slideContainer.empty(); // Очищаємо контейнер перед додаванням нових слайдів

        scenarios.forEach((scenario, index) => {
            const isActive = index === 0 ? 'active' : ''; // Клас active лише для першого слайда
            const slide = `
                        <div class="slide ${isActive}" id="slide-${index + 2}">
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
            slideContainer.append(slide);
        });

        // Приховуємо початковий слайд
        initial_slide.removeClass('active');

        // Активуємо перший динамічний слайд
        $('#dynamic-slides .slide').first().addClass('active');
    });

    // Обробка переходу між динамічними слайдами
    $(document).on('click', '.next-slide', function () {
        const currentSlide = $(this).closest('.slide');
        const allSlides = $('.slide'); // Збираємо всі слайди
        const currentIndex = allSlides.index(currentSlide); // Індекс поточного слайда
        const nextSlide = allSlides.eq(currentIndex + 1); // Наступний слайд

        // Валідація перед переходом
        const formData = currentSlide.find(':input').serializeArray();
        const structuredData = {};

        formData.forEach((field) => {
            const normalizedFieldName = field.name.replace(/\[\]/g, '');
            if (!structuredData[normalizedFieldName]) {
                structuredData[normalizedFieldName] = [];
            }
            structuredData[normalizedFieldName].push(field.value.trim());
        });

        // Відправка даних на сервер
        $.ajax({
            url: '/validate-slide',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf_token,
            },
            data: structuredData,
            success: function () {
                switchSlide(currentSlide, nextSlide);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    currentSlide.find('.error-message').remove();

                    for (const [field, messages] of Object.entries(errors)) {
                        const fieldName = field.split('.')[0];
                        console.log(field, fieldName)
                        const input = currentSlide.find(`[name="${fieldName}[]"], [name="${fieldName}"]`);
                        if (input.length) {
                            input.after(`<div class="text-danger error-message">${messages.join('<br>')}</div>`);
                        }
                    }
                }
            },
        });
    });

    $(document).on('click', '.prev-slide', function () {
        const currentSlide = $(this).closest('.slide');
        const allSlides = $('.slide'); // Збираємо всі слайди
        const currentIndex = allSlides.index(currentSlide); // Індекс поточного слайда
        let prevSlide = allSlides.eq(currentIndex - 1); // Попередній слайд

        if (prevSlide.length == 0) {
            prevSlide = initial_slide;
        }

        switchSlide(currentSlide, prevSlide);
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
                'X-CSRF-TOKEN': csrf_token
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
