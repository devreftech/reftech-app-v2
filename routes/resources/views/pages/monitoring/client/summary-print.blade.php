@extends('layouts.sales.app')
@section('reports')
    <div class="invoice-print p-4">
        <div class="contianter">
            <div class="row mb-3">
                <div class="col-6" style="background-color: yellow;">
                    <div class="row">
                        <div class="col-6 text-center">
                            <h4 class="my-1 text-black">
                                MONITORING :
                            </h4>
                        </div>
                        <div class="col-6 text-center">
                            <h4 class="my-1 text-black">
                                {{strtoupper($month)}}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <table class="table table-bordered m-0" style="width: 100%">
                    <thead class="table-light border-top">
                        <tr class="title">
                            <th colspan="5" class="text-center title" style="background-color: pink">Summary Pekerjaan Service</th>
                        </tr>
                        <tr class="subtitle">
                            <th>Date</th>
                            <th>Location Unit</th>
                            <th>Tag</th>
                            <th>Model / type</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summary as $item)
                            <tr>
                                <td>{{ $item->date }}</td>
                                <td>{{ $item->location }}</td>
                                <td>{{ $item->tag }}</td>
                                <td>{{ $item->machine }}</td>
                                <td>{{ $item->main_desc }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('after-style')
        <!-- Page CSS -->
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-reports-print-landscape.css" />
        <link rel="stylesheet" href="style.css">
    @endpush
    @push('after-script')
        <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
    @endpush
    @push('script')
        <script>
            $(document).ready(function() {
                // Ambil tinggi dari elemen <pre>
                var preHeight = $('#notePre').outerHeight();
                // Atur tinggi elemen <p> menjadi sama dengan tinggi elemen <pre>
                $('#noteParagraph').css('height', preHeight + 'px');
            });
        </script>
    @endpush
