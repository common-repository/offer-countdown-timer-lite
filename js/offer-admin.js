jQuery(document).ready(function() {
    jQuery('.date-select').datepicker({
        dateFormat : 'dd-mm-yy'
    });
    jQuery('.time-select').timepicker({'timeFormat': 'G:i' });
});