@if (($adminView ?? 'sales') === 'salesmanager')
    @include('pages.salesmanager.dashboard._content')
@elseif (($adminView ?? 'sales') === 'accounting')
    @include('pages.accounting.dashboard._content')
@elseif (($adminView ?? 'sales') === 'finance')
    @include('pages.finance.dashboard._content')
@elseif (($adminView ?? 'sales') === 'logistic')
    @include('pages.logistic.dashboard._content')
@elseif (($adminView ?? 'sales') === 'workshop')
    @include('pages.workshop.dashboard._content')
@else
    @include('pages.sales.dashboard_admin_sales')
@endif
