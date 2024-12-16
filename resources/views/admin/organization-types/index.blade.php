@extends('layouts.app')

@section('title', 'Типи організацій')

@section('content')
    <div class="container mt-5">
        <h1>Типи організацій</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('organization-types.create') }}" class="btn btn-primary mb-3">Створити новий тип</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Дії</th>
                <th>НС</th>
            </tr>
            </thead>
            <tbody>
            @foreach($organizationTypes as $type)
                <tr>
                    <td>{{ $type->id }}</td>
                    <td>{{ $type->name }}</td>
                    <td>
                        <a href="{{ route('organization-types.edit', $type) }}" class="btn btn-warning btn-sm">Редагувати</a>
                        <form action="{{ route('organization-types.destroy', $type) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Видалити</button>
                        </form>
                    </td>
                    <td>
                        @foreach($type->emergencyScenarios as $scenario)
                            <span class="badge bg-primary">{{ $scenario->name }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
