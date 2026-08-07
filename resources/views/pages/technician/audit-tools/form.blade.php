@extends('layouts.sales.app')
@section('title', 'Form Audit Tools')
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card mb-4">
        <form action="{{ route('audit-tools.update', $audit->id) }}" id="" method="post"
            enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="card-header">
                <h4>Form Audit : {{ $audit->technician->name }}</h4>
                <h5>Month {{ now()->format('F') }}</h5>
            </div>
            <div class="card-body pt-2 mt-1">
                <div class="row mt-2 gy-4">
                    <div class="col-8">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="noAudit" aria-describedby="noAuditHelp"
                                name="no_audit"
                                value="{{ $audit->technician->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year }}">
                            <label for="noAudit">Number Audit</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-floating form-floating-outline mb-4">
                            <input class="form-control" type="date" id="date" name="date"
                                value="{{ now()->format('Y-m-d') }}">
                            <label for="date">Date</label>
                        </div>
                    </div>
                </div>
                <h5>Tools</h5>
                @php
                    $no = 0;
                    $asment = 0;
                @endphp
                @foreach ($tools as $tool)
                    @php
                        $no++;
                    @endphp
                    <hr>
                    <div class="row gy-4">
                        <div class="col-1">
                            <p class="text-nowrap">
                                {{ $no }}
                            </p>
                        </div>
                        <div class="col-8 col-lg-3">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" id="tools" name="tools[]"
                                    value="{{ $tool->tools }}" placeholder="Type Your Tools Here..." />
                                <label for="tools">Tools</label>
                            </div>
                        </div>
                        <div class="col-3 col-lg-1">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="number" id="qty" name="qty[]"
                                    value="{{ $tool->qty }}" />
                                <label for="qty">Quantity</label>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" id="desc" name="desc[]"
                                    value="{{ $tool->desc }}" placeholder="Type Your Description Here..." />
                                <label for="desc">Description</label>
                            </div>
                        </div>
                        <div class="col-6 col-lg-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assesment[{{ $asment }}]"
                                    id="assesment1" value="Ada" {{ $tool->assesment == 'Ada' ? 'checked' : '' }}>
                                <label class="form-check-label" for="assesment1">Ada</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assesment[{{ $asment }}]"
                                    id="assesment2" value="Tidak Lengkap">
                                <label class="form-check-label" for="assesment2">Tidak Lengkap</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assesment[{{ $asment }}]"
                                    id="assesment3" value="Hilang" {{ $tool->assesment == 'Hilang' ? 'checked' : '' }}>
                                <label class="form-check-label" for="assesment3">Hilang</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" id="note" name="note[]"
                                    value="{{ $tool->note }}" placeholder="Barang A Hilang" />
                                <label for="note">Note</label>
                            </div>
                        </div>
                    </div>
                    @php
                        $asment++;
                    @endphp
                @endforeach
                <div class="row mt-2 gy-4">
                    <div class="col-md-4"></div>
                    <div class="col-6 col-md-4">
                        <div class="form-floating form-floating-outline mb-4">
                            <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example" name="status">
                                <option value="OK" {{ $audit->status == 'OK' ? 'selected' : '' }}>OK</option>
                                <option value="Not OK" {{ $audit->status == 'Not OK' ? 'selected' : '' }}>Not OK</option>
                            </select>
                            <label for="exampleFormControlSelect1">Status</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" id="note" name="noteD"
                                value="{{ $audit->note }}" placeholder="Barang A Hilang" />
                            <label for="noteD">Detail Note</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Save changes</button>
                    <a href="{{route('audit-tools.index')}}" class="btn btn-outline-secondary">Cancel</a>
                </div>
        </form>
    </div>
@endsection
