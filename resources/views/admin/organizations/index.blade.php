@extends('layouts.app')

@section('title', 'Організації')

@section('content')
    <div class="container mt-5">
        <h1>Організації</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('organizations.create') }}" class="btn btn-primary mb-3">Створити нову організацію</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Тип</th>
                <th>Дії</th>
            </tr>
            </thead>
            <tbody>
            @foreach($organizations as $organization)
                <tr>
                    <td>{{ $organization->id }}</td>
                    <td>{{ $organization->name }}</td>
                    <td>{{ $organization->type->name }}</td>
                    <td>
                        <a href="{{ route('organizations.edit', $organization) }}" class="btn btn-warning btn-sm">Редагувати</a>
                        <form action="{{ route('organizations.destroy', $organization) }}" method="POST" style="display: inline-block;">
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
