@extends('layouts.app')

@section('title', 'Створити організацію')

@section('content')
    <div class="container mt-5">
        <h1>Створити нову організацію</h1>

        <form action="{{ route('organizations.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Назва</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="organization_type_id" class="form-label">Тип організації</label>
                <select name="organization_type_id" id="organization_type_id" class="form-select @error('organization_type_id') is-invalid @enderror" required>
                    <option value="">Оберіть тип</option>
                    @foreach($organizationTypes as $type)
                        <option value="{{ $type->id }}" @if(old('organization_type_id') == $type->id) selected @endif>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('organization_type_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Зберегти</button>
            <a href="{{ route('organizations.index') }}" class="btn btn-secondary">Назад</a>
        </form>
    </div>
@endsection
