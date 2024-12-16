@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Сценарії надзвичайних ситуацій</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('emergency-scenarios.create') }}" class="btn btn-primary mb-3">Створити новий сценарій</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Опис</th>
                <th>Дії</th>
            </tr>
            </thead>
            <tbody>
            @foreach($scenarios as $scenario)
                <tr>
                    <td>{{ $scenario->id }}</td>
                    <td>{{ $scenario->name }}</td>
                    <td>{{ $scenario->description }}</td>
                    <td>
                        <a href="{{ route('emergency-scenarios.edit', $scenario) }}" class="btn btn-warning btn-sm">Редагувати</a>
                        <form action="{{ route('emergency-scenarios.destroy', $scenario) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Видалити</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
