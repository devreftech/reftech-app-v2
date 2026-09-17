@extends('layouts.sales.app')
@section('title', 'Edit Departemen')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">HR / Departemen /</span> Edit Departemen
    </h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('departments.update', $department->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('pages.hr.departments._form')
            </form>
        </div>
    </div>
@endsection
