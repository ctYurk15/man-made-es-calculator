@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Створити новий сценарій</h1>

        <form action="{{ route('emergency-scenarios.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Назва</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Опис</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Зберегти</button>
            <a href="{{ route('emergency-scenarios.index') }}" class="btn btn-secondary">Назад</a>
        </form>
    </div>
@endsection
