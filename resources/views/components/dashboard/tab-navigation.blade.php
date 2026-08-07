<div class="row mb-4">
    <div class="col-12">
        <div class="nav-align-top">
            <ul class="nav nav-pills flex-column flex-sm-row mb-0 gap-1" role="tablist">
                <li class="nav-item flex-sm-grow-0">
                    <a class="nav-link {{ request()->routeIs('pending-po.sales-order') ? 'active bg-primary text-white' : 'text-muted' }}" 
                       href="{{ route('pending-po.sales-order') }}">
                        <i class="mdi mdi-cart-outline me-2"></i>Sales Order (Spare Parts)
                    </a>
                </li>
                <li class="nav-item flex-sm-grow-0">
                    <a class="nav-link {{ request()->routeIs('project-monitoring.index') || request()->routeIs('project-monitoring.show') ? 'active bg-primary text-white' : 'text-muted' }}" 
                       href="{{ route('project-monitoring.index') }}">
                        <i class="mdi mdi-briefcase-outline me-2"></i>Project Monitoring
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
