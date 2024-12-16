<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Захищена сторінка</title>
</head>
<body>
<div class="container mt-5">
    <h1>Ласкаво просимо на захищену сторінку!</h1>
    <p>Ви успішно ввели пароль і отримали доступ.</p>

    <ul class="list-group mt-4">
        <li class="list-group-item">
            <a href="{{ route('emergency-scenarios.index') }}" class="btn btn-link">Керування сценаріями надзвичайних ситуацій</a>
        </li>
    </ul>

    <a href="{{ route('filter.calculations') }}" class="btn btn-info">Фільтрувати архівні записи</a>
</div>
</body>
</html>
