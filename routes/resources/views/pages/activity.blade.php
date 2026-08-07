@extends('layouts.sales.app')
@section('title', 'activity')
@section('content')
    <h4> Notification & Activity</h4>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Notification</h5>
                    <div class="form-floating form-floating-outline">
                        <input class="form-control date-{{ Auth::user()->role == 'Admin' ? 'notif-admin' : 'notif' }}"
                            type="date" value="{{ carbon\Carbon::now() }}">
                        <label for="html5-date-input">Date Notif</label>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-2">Baru</h6>
                    <ul class="list-group list-group-flush">
                        @if (Auth::user()->role == 'Admin')
                            @if (@$notifAdmin)
                                @forelse ($notifAdmin as $item)
                                    <a href="{{ route('quotation.show', $item->idQ) }}#viewComment"
                                        class="view-quote {{ $item->level == 1 ? 'bg-label-secondary' : '' }}"
                                        data-id="{{ $item->idC }}" data-quotation="{{ $item->idQ }}">
                                        <li
                                            class="list-group-item list-group-item-action dropdown-notifications-item view-quote">
                                            <div class="d-flex gap-2">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar me-1">
                                                        <img src="{{ url('') . '/' . $item->image }}" alt
                                                            class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                                    <h6 class="mb-1 text-truncate">{{ $item->name }}</h6>
                                                    <small class="text-truncate text-body">New Replies on your
                                                        Comments! {{ $item->no_quote }} </small>
                                                </div>
                                                @php
                                                    $date = \Carbon\Carbon::parse($item->date);
                                                @endphp
                                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                                    <small
                                                        class="text-muted">{{ $date->diffInHours(Carbon\Carbon::now()) > 24 ? $date->format('d M y h:i:s') : $date->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </li>
                                    </a>
                                @empty
                                    <li class="list-group-item">No Notification found for this date.</li>
                                @endforelse
                            @endif
                        @else
                            @if (@$comment)
                                @forelse ($comment as $item)
                                    @php
                                        if ($item->type == 'prospect') {
                                            $route = 'prospect.show';
                                        } else {
                                            $route = 'quotation.show';
                                        }
                                    @endphp
                                    <a href="{{ route($route, $item->idQ) }}#viewComment"
                                        class="view-{{ $item->type == 'prospect' ? 'prospect' : 'quote' }} {{ $item->level == 1 ? 'bg-label-secondary' : '' }}"
                                        data-id="{{ $item->idC }}" data-quotation="{{ $item->idQ }}">
                                        <li
                                            class="list-group-item list-group-item-action dropdown-notifications-item view-quote">
                                            <div class="d-flex gap-2">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar me-1">
                                                        <img src="{{ url('') . '/' . $item->image }}" alt
                                                            class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                                    <h6 class="mb-1 text-truncate">
                                                        {{ $item->type == 'prospect' ? 'Prospect ' : '' }}{{ $item->no_quote }}
                                                    </h6>
                                                    <small class="text-truncate text-body">New Comment on your
                                                        {{ $item->type == 'prospect' ? 'Prospect!' : 'Quotations' }}!
                                                    </small>
                                                </div>
                                                @php
                                                    $date = \Carbon\Carbon::parse($item->date);
                                                @endphp
                                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                                    <small
                                                        class="text-muted">{{ $date->diffInHours(Carbon\Carbon::now()) > 24 ? $date->format('d M y h:i:s') : $date->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </li>
                                    </a>
                                @empty
                                    <li class="list-group-item">No Notification found for this date.</li>
                                @endforelse
                            @endif
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Activity Timeline</h5>
                    <div class="form-floating form-floating-outline">
                        <input class="form-control date-activity" type="date" value="{{ carbon\Carbon::now() }}">
                        <label for="html5-date-input">Date Activity</label>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="timeline mb-0 pb-5">
                        @forelse ($activities as $item)
                            @php
                                if ($item->type == 'comment') {
                                    $tpColor = 'primary';
                                } elseif ($item->type == 'quotation') {
                                    $tpColor = 'success';
                                } elseif ($item->type == 'activities') {
                                    $tpColor = 'info';
                                }

                                $date = \Carbon\Carbon::parse($item->created_at);
                            @endphp
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-{{ $tpColor }}"></span>
                                @if ($item->type == 'quotation')
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-3">
                                            <h6 class="mb-0">You Has Been Created a
                                                {{ $item->vers != '0' ? 'Revision' : '' }} Quotation</h6>
                                            <small
                                                class="text-muted">{{ $date->diffInHours(Carbon\Carbon::now()) > 24 ? $date->format('d M y h:i:s') : $date->diffForHumans() }}</small>
                                        </div>
                                        <a href="{{ route('quotation.show', $item->id) }}" class="mb-2 text-black">
                                            {{ $item->detail }}{{ $item->vers != '0' ? '-REV-' . $item->vers : '' }}
                                        </a>
                                    </div>
                                @elseif ($item->type == 'comment')
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-3">
                                            <h6 class="mb-0">You Has Been Comment a Quotation ({{ $item->status }})</h6>
                                            <small
                                                class="text-muted">{{ $date->diffInHours(Carbon\Carbon::now()) > 24 ? $date->format('d M y h:i:s') : $date->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-2">
                                            commented "{{ $item->detail }}" on
                                            <a href="{{ route('quotation.show', $item->id) }}#viewComment"
                                                class="text-black">
                                                <span class="badge bg-label-success">{{ $item->vers }}</span>
                                            </a>
                                        </p>
                                    </div>
                                @elseif ($item->type == 'activities')
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-3">
                                            <h6 class="mb-0">You Has Been Do Activities</h6>
                                            <small
                                                class="text-muted">{{ $date->diffInHours(Carbon\Carbon::now()) > 24 ? $date->format('d M y h:i:s') : $date->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-2">
                                            {{ $item->status }} {{ $item->detail }} ({{ $item->vers }})
                                        </p>
                                    </div>
                                @endif
                            </li>
                        @empty
                            <li class="timeline-item">No Activity found for this date.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/jquery-timepicker/jquery-timepicker.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/pickr/pickr-themes.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/jquery-timepicker/jquery-timepicker.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/pickr/pickr.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-pickers.js"></script>
@endpush

@push('script')
    <script>
        var baseUrlProspect = "{{ route('prospect.show', ':id') }}";
        var baseUrlQuotation = "{{ route('quotation.show', ':id') }}";

        $(document).on('change', '.date-notif', function() {
            var date = $('.date-notif').val();
            console.log(date);

            $.ajax({
                url: '/notifactivity/notif/' + date,
                type: 'GET',
                success: function(response) {
                    console.log(response);

                    // Kosongkan list yang ada
                    var listGroup = $('.list-group-flush');
                    listGroup.empty();

                    // Loop melalui respons dan tambahkan item baru ke dalam daftar
                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            // Tentukan route berdasarkan type item
                            var url = item.type === 'prospect' ?
                                baseUrlProspect.replace(':id', item.idQ) + '#viewComment' :
                                baseUrlQuotation.replace(':id', item.idQ) + '#viewComment';

                            // Format tanggal
                            var dateFormatted = formatDate(item
                                .date); // Fungsi untuk format tanggal jika diperlukan

                            // Tentukan class level
                            var levelClass = item.level == 1 ? 'bg-label-secondary' : '';
                            var typeLabel = item.type === 'prospect' ? 'Prospect ' : '';

                            // Buat HTML item daftar
                            var listItem = `
                        <a href="${url}" class="view-${item.type} ${levelClass}" data-id="${item.idC}" data-quotation="${item.idQ}">
                            <li class="list-group-item list-group-item-action dropdown-notifications-item view-quote">
                                <div class="d-flex gap-2">
                                    <div class="flex-shrink-0">
                                        <div class="avatar me-1">
                                            <img src="${item.image}" alt class="w-px-40 h-auto rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                        <h6 class="mb-1 text-truncate">${typeLabel}${item.no_quote}</h6>
                                        <small class="text-truncate text-body">New Comment on your ${typeLabel.trim()}!</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <small class="text-muted">${dateFormatted}</small>
                                    </div>
                                </div>
                            </li>
                        </a>
                    `;

                            // Tambahkan item baru ke dalam daftar
                            listGroup.append(listItem);
                        });
                    } else {
                        listGroup.append(
                            '<li class="list-group-item">No Notification found for this date.</li>');
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });

        $(document).on('change', '.date-notif-admin', function() {
            var date = $('.date-notif-admin').val();
            console.log(date);

            $.ajax({
                url: '/notifactivity/notifAdmin/' + date,
                type: 'GET',
                success: function(response) {
                    console.log(response);

                    // Clear the current list
                    var listGroup = $('.list-group-flush');
                    listGroup.empty();

                    // Loop through the response and add new items to the list
                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            var dateFormatted = formatDate(item
                                .date); // Function to format the date if needed
                            var levelClass = item.level == 1 ? 'bg-label-secondary' : '';

                            // Create list item HTML
                            var listItem = `
                        <a href="/quotation/${item.idQ}#viewComment" class="view-quote ${levelClass}" data-id="${item.idC}" data-quotation="${item.idQ}">
                            <li class="list-group-item list-group-item-action dropdown-notifications-item view-quote">
                                <div class="d-flex gap-2">
                                    <div class="flex-shrink-0">
                                        <div class="avatar me-1">
                                            <img src="${item.image}" alt class="w-px-40 h-auto rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                        <h6 class="mb-1 text-truncate">${item.name}</h6>
                                        <small class="text-truncate text-body">New Replies on your Comment! 
                                            <span class="badge bg-label-success">
                                                ${item.no_quote}
                                            </span>
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <small class="text-muted">${dateFormatted}</small>
                                    </div>
                                </div>
                            </li>
                        </a>
                    `;

                            // Append the new list item
                            listGroup.append(listItem);
                        });
                    } else {
                        listGroup.append(
                            '<li class="list-group-item">No comments found for this date.</li>');
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });
        $(document).on('change', '.date-activity', function() {
            var date = $('.date-activity').val(); // Mengambil nilai tanggal yang dipilih
            console.log(date);

            $.ajax({
                url: '/notifactivity/activity/' +
                    date, // URL endpoint untuk mengambil data berdasarkan tanggal
                type: 'GET',
                success: function(response) {
                    console.log(response);

                    // Clear the current activity list
                    var timelineList = $('.timeline');
                    timelineList.empty();

                    // Loop through the response and add new items to the timeline
                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            var dateFormatted = formatDate(item
                                .created_at); // Fungsi untuk memformat tanggal
                            var tpColor = '';

                            // Determine the color based on the activity type
                            if (item.type === 'comment') {
                                tpColor = 'primary';
                            } else if (item.type === 'quotation') {
                                tpColor = 'success';
                            } else if (item.type === 'activities') {
                                tpColor = 'info';
                            }

                            // Construct the timeline item based on the type
                            var timelineItem = `
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-${tpColor}"></span>
                            <div class="timeline-event">
                    `;

                            // Add specific content based on the type of activity
                            if (item.type === 'comment') {
                                timelineItem += `
                            <div class="timeline-header mb-3">
                                <h6 class="mb-0">You Have Commented on a Quotation (${item.status})</h6>
                                <small class="text-muted">${dateFormatted}</small>
                            </div>
                            <p class="mb-2">
                                commented "${item.detail}" on 
                                <a href="/quotation/${item.id}#viewComment" class="text-black">
                                    <span class="badge bg-label-success">
                                        ${item.vers}
                                    </span>
                                </a>
                            </p>
                        `;
                            } else if (item.type === 'quotation') {
                                timelineItem += `
                            <div class="timeline-header mb-3">
                                <h6 class="mb-0">You Have Created a ${item.vers != '0' ? 'Revision' : ''} Quotation</h6>
                                <small class="text-muted">${dateFormatted}</small>
                            </div>
                            <a href="/quotation/${item.id}" class="mb-2 text-black">
                                    ${item.detail}${item.vers != '0' ? '-REV-' + item.vers : ''}
                            </a>
                        `;
                            } else if (item.type === 'activities') {
                                timelineItem += `
                            <div class="timeline-header mb-3">
                                <h6 class="mb-0">You Have Completed an Activity</h6>
                                <small class="text-muted">${dateFormatted}</small>
                            </div>
                            <p class="mb-2">
                                ${item.status} ${item.detail} (${item.vers})
                            </p>
                        `;
                            }

                            // Close the timeline event
                            timelineItem += `
                            </div>
                        </li>
                    `;

                            // Append the new timeline item
                            timelineList.append(timelineItem);
                        });
                    } else {
                        timelineList.append(
                            '<li class="timeline-item">No activities found for this date.</li>');
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });

        function formatDate(date) {
            var currentDate = new Date();
            var commentDate = new Date(date);
            var diffHours = Math.abs(currentDate - commentDate) / 36e5;

            // If comment is more than 24 hours old, return formatted date
            if (diffHours > 24) {
                return commentDate.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
            // Else, return 'X hours ago' style format
            return timeAgo(commentDate);
        }

        function timeAgo(date) {
            var seconds = Math.floor((new Date() - new Date(date)) / 1000);
            var intervals = {
                'year': 31536000,
                'month': 2592000,
                'day': 86400,
                'hour': 3600,
                'minute': 60,
                'second': 1
            };
            for (var interval in intervals) {
                var count = Math.floor(seconds / intervals[interval]);
                if (count > 0) {
                    return `${count} ${interval}${count !== 1 ? 's' : ''} ago`;
                }
            }
            return 'just now';
        }
    </script>
@endpush
