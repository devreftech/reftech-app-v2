@extends('layouts.sales.app')
@section('title', 'Edit Posisi')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">HR / Posisi /</span> Edit Posisi
    </h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('positions.update', $position->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('pages.hr.positions._form')
            </form>
        </div>
    </div>
@endsection
