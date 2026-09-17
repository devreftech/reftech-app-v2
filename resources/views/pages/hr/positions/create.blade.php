@extends('layouts.sales.app')
@section('title', 'Tambah Posisi')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">HR / Posisi /</span> Tambah Posisi
    </h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('positions.store') }}" method="POST">
                @csrf
                @include('pages.hr.positions._form')
            </form>
        </div>
    </div>
@endsection
