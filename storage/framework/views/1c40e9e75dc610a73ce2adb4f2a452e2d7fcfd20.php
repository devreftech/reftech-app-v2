<?php if(($adminView ?? 'sales') === 'salesmanager'): ?>
    <?php echo $__env->make('pages.salesmanager.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif(($adminView ?? 'sales') === 'accounting'): ?>
    <?php echo $__env->make('pages.accounting.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif(($adminView ?? 'sales') === 'finance'): ?>
    <?php echo $__env->make('pages.finance.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif(($adminView ?? 'sales') === 'logistic'): ?>
    <?php echo $__env->make('pages.logistic.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif(($adminView ?? 'sales') === 'workshop'): ?>
    <?php echo $__env->make('pages.workshop.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
    <?php echo $__env->make('pages.sales.dashboard_admin_sales', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/dashboard_view_content.blade.php ENDPATH**/ ?>