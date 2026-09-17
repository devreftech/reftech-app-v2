@extends('layouts.sales.app')
@section('title', 'Tambah Departemen')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">HR / Departemen /</span> Tambah Departemen
    </h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                @include('pages.hr.departments._form')
            </form>
        </div>
    </div>
@endsection
