@extends('layouts.app')

@section('title', 'Фільтр обрахунків')

@section('content')
    <div class="container mt-5">
        <h1>Фільтр архівних записів</h1>

        <form method="GET" action="{{ route('filter.calculations') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="year" class="form-label">Рік</label>
                    <input type="number" name="year" id="year" class="form-control" value="{{ request('year') }}">
                </div>
                <div class="col-md-4">
                    <label for="organization_id" class="form-label">Організація</label>
                    <select name="organization_id" id="organization_id" class="form-select">
                        <option value="">Всі</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}"
                                {{ request('organization_id') == $organization->id ? 'selected' : '' }}>
                                {{ $organization->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Фільтрувати</button>
                </div>
            </div>
        </form>

        @if($calculations->isEmpty())
            <p class="text-muted">Записи не знайдені.</p>
        @else
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Рік</th>
                    <th>Організація</th>
                    <th>Числова оцінка</th>
                    <th>Текстова оцінка</th>
                    <th>Дії</th>
                </tr>
                </thead>
                <tbody>
                @foreach($calculations as $calculation)
                    <tr>
                        <td>{{ $calculation->id }}</td>
                        <td>{{ $calculation->year }}</td>
                        <td>{{ $calculation->organization->name ?? 'Невідомо' }}</td>
                        <td>{{ $calculation->numeric_assessment }}</td>
                        <td>{{ $calculation->text_assessment }}</td>
                        <td>
                            <a href="{{ route('export.calculations', ['calculation_id' => $calculation->id]) }}" class="btn btn-success btn-sm">Експортувати</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
