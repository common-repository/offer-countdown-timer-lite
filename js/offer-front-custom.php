<?php 
	$options = get_option('offer_options');
	$date = $options['select-date'];
	$time = $options['select-time'];
	$timezone = $options['timezone'];
	$_top_counter_time = strtotime($date.' '.$time.' '.$timezone);
?>
<script type="text/javascript">
jQuery(document).ready(function(){
'use strict';
	var height = jQuery(".offer-bar").height()+"px";
	jQuery("body").css({"padding-top":height,"transition":"all 480ms ease"});  // checked
	/* Countdown */
	var cutt = jQuery.now();
	jQuery('.offer-timer').ClassyCountdown({
		theme: "flat-colors",
		end: '<?php echo $_top_counter_time; ?>',
    	now: '<?php echo time(); ?>',
	    // whether to display the days/hours/minutes/seconds labels.
		labels: true,

		// object that specifies different language phrases for says/hours/minutes/seconds as well as specific CSS styles.
		labelsOptions: {
		  lang: {
		    days: 'D',
		    hours: 'H',
		    minutes: 'M',
		    seconds: 'S'
		  },
		  style: 'font-size: 15px;'
		},
		// callback that is fired when the countdown reaches 0.
		onEndCallback: function() {
			jQuery('.offer-bar, #slideup, #slidedown').hide();
			jQuery("body").css({"padding-top":"0","transition":"all 480ms ease"});  // unchecked
		}

	});

	jQuery('#slideup').click(function () {
		jQuery('.offer-bar').slideUp('slow');
		jQuery("body").css({"padding-top":'0',"transition":"all 0.4s ease 0s"});
		jQuery(this).fadeOut();
		jQuery('#slidedown').fadeIn();
	});
	jQuery('#slidedown').click(function () {
		var height = jQuery(".offer-bar").height()+"px";
		jQuery('.offer-bar').slideDown('slow');
		jQuery("body").css({"padding-top":height,"transition":"all 0.8s ease 0s"});
		jQuery(this).fadeOut();
		jQuery('#slideup').fadeIn();
	});
});
</script>