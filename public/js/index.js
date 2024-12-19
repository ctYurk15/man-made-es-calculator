$(document).ready(function () {

    $('#risk-form')[0].reset();

    $('.scenario-checkbox').prop('checked', false);

    let scenarios = [];

    let csrf_token = $('meta[name=csrf-token]').attr('content');

    const initial_slide = $('.initial-slide');

    function switchSlide(current, next) {
        $(current).removeClass('active');
        $(next).addClass('active');
    }

    $('.next-slide').click(function () {
        const scenarios = [];
        $('.scenario-checkbox:checked').each(function () {
            scenarios.push($(this).val());
        });

        if (scenarios.length === 0) {
            alert('Оберіть хоча б одну НС!');
            return;
        }

        const slideContainer = $('#dynamic-slides');
        slideContainer.empty();

        scenarios.forEach((scenario, index) => {
            const isActive = index === 0 ? 'active' : '';
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


                            <button type="button" class="btn btn-secondary prev-slide">Назад</button>
                            <button type="button" class="btn btn-primary next-slide">Далі</button>
                        </div>`;
            slideContainer.append(slide);
        });

        initial_slide.removeClass('active');

        $('#dynamic-slides .slide').first().addClass('active');
    });

    $(document).on('click', '.next-slide', function () {
        const currentSlide = $(this).closest('.slide');
        const allSlides = $('.slide');
        const currentIndex = allSlides.index(currentSlide);
        const nextSlide = allSlides.eq(currentIndex + 1);

        const formData = currentSlide.find(':input').serializeArray();
        const structuredData = {};

        formData.forEach((field) => {
            const normalizedFieldName = field.name.replace(/\[\]/g, '');
            if (!structuredData[normalizedFieldName]) {
                structuredData[normalizedFieldName] = [];
            }
            structuredData[normalizedFieldName].push(field.value.trim());
        });

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
        const allSlides = $('.slide');
        const currentIndex = allSlides.index(currentSlide);
        let prevSlide = allSlides.eq(currentIndex - 1);

        if (prevSlide.length == 0) {
            prevSlide = initial_slide;
        }

        switchSlide(currentSlide, prevSlide);
    });

    $('#risk-form').on('submit', function (e) {
        e.preventDefault();

        const scenarios = [];
        $('.scenario-checkbox:checked').each(function () {
            scenarios.push($(this).val());
        });

        if (scenarios.length === 0) {
            alert('Оберіть хоча б один сценарій!');
            return;
        }

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

        structuredData.scenarios = scenarios;

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

                $('#results-list').append(`
            <li><strong>Загальна оцінка:</strong> ${response.calculation.numeric_assessment} - ${response.calculation.text_assessment}</li>
        `);

                response.scenarios.forEach(function (scenario) {
                    $('#results-list').append(`
                <li>
                    <strong>Сценарій:</strong> `+scenario.name+`<br>
                    <!--<strong>Ймовірність:</strong> `+scenario.numeric_assessment+` %<br>-->
                    <strong>Оцінка імовірності:</strong> `+scenario.numeric_assessment+` - `+scenario.text_assessment+` <br>
                    <strong>Оцінка індивідуальних вимірів:</strong><br>
                    <ol>
                        <li>Рівень зношеності: `+scenario.single_dimensions.equipment_wear+`</li>
                        <li>Частота обслуговування: `+scenario.single_dimensions.maintenance_frequency+`</li>
                        <li>Перевірка обладнання: `+scenario.single_dimensions.last_check+`</li>
                        <li>Кількість навчань: `+scenario.single_dimensions.training_count+`</li>
                        <li>Відсоток атестації працівників: `+scenario.single_dimensions.certified_employees+`</li>
                        <li>Оцінка знань: `+scenario.single_dimensions.knowledge_score+`</li>
                    </ol><br>
                    `+(
                        scenario.improve_advise != ''
                        ? `<strong>Порада:</strong> необхідно покращити наступні показники - `+scenario.improve_advise+`<br>`
                        : (
                            scenario.numeric_assessment > 10
                                ? `<strong>Порада:</strong>  трохи покращити усі показники<br>`
                                : `<strong>Покращення не потрібні</strong><br>`
                            )
                    )+`
                </li>
            `);
                });

                $("#final-slide button").prop('disabled', 'disabled');
            },
            error: function (xhr, status, error) {
                console.error('Помилка:', error);
                alert('Сталася помилка при обробці запиту.');

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    console.error(xhr.responseJSON.message);
                }
            }
        });
    });


    $('#organization_id').on('change', function () {
        const fields = $('#new-organization-fields');
        if ($(this).val()) {
            fields.hide();
        } else {
            fields.show();
        }
    });

    $('#organization_id').on('change', function () {
        const organizationId = $(this).val();

        if (organizationId) {
            $.ajax({
                url: `/organizations/${organizationId}/scenarios`,
                method: 'GET',
                success: function (response) {
                    $('.scenario-checkbox').each(function () {
                        const scenarioName = $(this).val();
                        if (response.some(scenario => scenario.name === scenarioName)) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });
                },
                error: function () {
                    alert('Не вдалося завантажити надзвичайні ситуації для обраної організації.');
                },
            });
        } else {
            $('.scenario-checkbox').prop('checked', false);
        }
    });

    $('#save-organization').on('click', function (e) {
        e.preventDefault();

        const organizationId = $('#organization_id').val();
        const year = $('#year').val();

        $('.error-message').remove();
        $('#organization_id').removeClass('is-invalid');
        $('#year').removeClass('is-invalid');
        $('#duplicate-error').hide().text('');

        if (!organizationId && (!$('#name').val() || !$('#organization_type_id').val())) {
            $('#organization_id').addClass('is-invalid').after('<div class="text-danger error-message">Будь ласка, виберіть організацію або створіть нову.</div>');
            return;
        }

        if (!year || year < 1900 || year > 2100) {
            $('#year').addClass('is-invalid').after('<div class="text-danger error-message">Будь ласка, введіть коректний рік (від 1900 до 2100).</div>');
            return;
        }

        $.ajax({
            url: '/validate-organization-year',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf_token,
            },
            data: {
                organization_id: organizationId,
                year: year,
            },
            success: function (response) {
                if (response.success) {
                    const formData = $('#risk-form').serialize();

                    $.ajax({
                        url: '/organizations',
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        success: function (response) {
                            $('#organization-success').text('Організація успішно збережена!').show();

                            $('#slide-organization').removeClass('active').hide();
                            $('#slide-scenarios').addClass('active').show();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;

                                for (const [field, messages] of Object.entries(errors)) {
                                    const input = $(`[name="${field}"]`);
                                    input.addClass('is-invalid').after(`<div class="text-danger error-message">${messages.join(', ')}</div>`);
                                }
                            } else {
                                alert('Сталася помилка при збереженні організації.');
                            }
                        },
                    });
                } else {
                    $('#duplicate-error').text(response.message).show();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.organization_id) {
                        $('#organization_id').addClass('is-invalid').after(`<div class="text-danger error-message">${errors.organization_id.join(', ')}</div>`);
                    }
                    if (errors.year) {
                        $('#year').addClass('is-invalid').after(`<div class="text-danger error-message">${errors.year.join(', ')}</div>`);
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    $('#duplicate-error').text(xhr.responseJSON.message).show();
                } else {
                    alert('Сталася помилка при перевірці організації.');
                }
            },
        });
    });


    $('#organization_id, #name, #organization_type_id, #year').on('input change', function () {
        $(this).removeClass('is-invalid').next('.error-message').remove();
    });

    $('#name').on('input', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });

    $(document).on('click', '#slide-scenarios .next-slide', function () {
        const scenarios = [];
        $('.scenario-checkbox:checked').each(function () {
            scenarios.push($(this).val());
        });

        if (scenarios.length === 0) {
            alert('Оберіть хоча б одну НС`!');
            return;
        }

        $('#slide-scenarios').removeClass('active').hide();
        $('#dynamic-slides .slide').first().addClass('active').show();
    });
});
