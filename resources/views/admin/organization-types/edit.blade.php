@extends('layouts.app')

@section('title', 'Редагувати тип організації')

@section('content')
    <div class="container mt-5">
        <h1>Редагувати тип організації</h1>

        <form action="{{ route('organization-types.update', $organizationType) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Назва</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $organizationType->name) }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="emergency_scenarios" class="form-label">Сценарії надзвичайних ситуацій</label>
                <select name="emergency_scenarios[]" id="emergency_scenarios" class="form-select" multiple>
                    @foreach ($scenarios as $scenario)
                        <option value="{{ $scenario->id }}"
                                @if($organizationType->emergencyScenarios->contains($scenario->id)) selected @endif>
                            {{ $scenario->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Оновити</button>
            <a href="{{ route('organization-types.index') }}" class="btn btn-secondary">Назад</a>
        </form>
    </div>
@endsection
