jQuery(document).ready(function($) {
    var stepsData = enaSinappsusSteps || {};

    $(document).on('change', '[data-setting="sales_funnel"]', function() {
        console.log('Dropdown changed');
        var selectedFunnel = $(this).val();
        var $stepSelect = $(this).closest('.elementor-control').siblings('.elementor-control-step').find('select');

        $stepSelect.empty();

        if (stepsData[selectedFunnel]) {
            $.each(stepsData[selectedFunnel], function(stepId, stepName) {
                $stepSelect.append(new Option(stepName, stepId));
            });
        } else {
            $stepSelect.append(new Option('No steps available', ''));
        }
    });

    // Trigger change event to populate the steps when editing an existing widget
    $('[data-setting="sales_funnel"]').trigger('change');
});
