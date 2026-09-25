@extends('layouts.sales.app')
@section('title', 'HR Executive Dashboard')

@push('after-style')
<style>
    .hr-stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .hr-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }
    .hr-action-card {
        border-radius: 12px;
        transition: all 0.2s ease;
        text-decoration: none !important;
        display: block;
        height: 100%;
        background: #ffffff;
        border: 1px solid rgba(67, 89, 113, 0.1);
    }
    .hr-action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(105, 108, 255, 0.15);
        border-color: #696cff;
    }
    .hr-action-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .avatar-sm {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @include('pages.hr.dashboard._content')
</div>
@endsection
