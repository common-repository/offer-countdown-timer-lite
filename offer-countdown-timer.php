<?php
/*
	Plugin Name: Offer CountDown Timer Lite
	Plugin URI: http://wpfruits.com
	Description: Countdown Timer is one of the most effective plugins for showcasing offers, discounts and special occasion bonanza by any woo commerce, construction, corporate or business site. Countdown Timer for Offers provides a smarter way to manage all kinds of offers online. Countdown Timer also comes ready with useful features which allow the users to set Time Zone,  date, set time for validity of the offer period and also the much required offer content. Specially designed for offers, this plugin is an integral too for the sites which frequently run offers to attract their customers, clients and visitors. Just go for it!
	Version: 1.0.0
	Author: wpfruits, tikendramaitry, rahulbrilliant2004, sparkleptic
	Author URI: http://wpfruits.com
*/
class OfferTimer
{
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'offer_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'offer_page_init' ) );
		add_action( 'admin_enqueue_scripts', 'offer_admin_style' );
	}

	/**
	 * Add plugin page
	 */
	public function offer_plugin_page()
	{
		// This page will be in "Dashboard Menu"
		add_menu_page(
			__('Settings Admin', 'offer-timer'), 
			__('Offer Timer', 'offer-timer'), 
			'manage_options',
			'offer-setting-admin', 
			array( $this, 'offer_admin_page' ),
			plugins_url( '/images/icon.png',__FILE__)
		);
	}

	/**
	 * Plugin page callback
	 */
	public function offer_admin_page()
	{
		// Set class property
		$this->options = get_option( 'offer_options' );
?>
		<div class="wrap">
			<h2><?php _e('Offer CountDown Timer Settings', 'offer-timer'); ?></h2>
			<div id="offer_setting">
			<form method="post" action="options.php">
			<?php
				// This printts out all hidden setting fields          
				settings_fields( 'offer_option_group' );
				do_settings_sections( 'offer-setting-admin' );
				submit_button();
			?>
			</form>
			</div>
		</div>
<?php
	}

	/**
	 * Register and add settings
	 */
	public function offer_page_init()
	{
		register_setting(
			'offer_option_group', // Option group
			'offer_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'offer_setting', // ID
			'', // Title
			array( $this, 'offer_section_info' ), // Callback
			'offer-setting-admin' // Page
		);

		add_settings_field(
			'timezone', // ID
			__('Select Timezone','offer-timer'), // Title 
			array( $this, 'offer_timezone_callback' ), // Callback
			'offer-setting-admin', // Page
			'offer_setting' // Section
		);

		add_settings_field(
			'select_date', // ID
			__('Select Date','offer-timer'), // Title 
			array( $this, 'offer_date_callback' ), // Callback
			'offer-setting-admin', // Page
			'offer_setting' // Section
		);

		add_settings_field(
			'select_time', // ID
			__('Select Time','offer-timer'), // Title 
			array( $this, 'offer_time_callback' ), // Callback
			'offer-setting-admin', // Page
			'offer_setting' // Section
		);

		add_settings_field(
			'coupon_code', // ID
			__('Enter Coupon Code','offer-timer'), // Title
			array( $this, 'offer_couponcode_callback' ), // Callback
			'offer-setting-admin', // Page
			'offer_setting' // Section
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input )
	{
		$new_input = array();

		if( isset( $input['timezone'] ) )
			$new_input['timezone'] = sanitize_text_field( $input['timezone'] );

		if( isset( $input['select-date'] ) )
			$new_input['select-date'] = sanitize_text_field( $input['select-date'] );

		if( isset( $input['select-time'] ) )
			$new_input['select-time'] = sanitize_text_field( $input['select-time'] );

		if( isset( $input['coupon-code'] ) )
			$new_input['coupon-code'] = force_balance_tags($input['coupon-code'] );

		return $new_input;
	}

	/** 
	 * SELECT DATE CALLBACK
	 */
	public function offer_section_info()
	{
		echo '';
	}

	/** 
	 * SELECT TIMEZONE CALLBACK
	 */
	public function offer_timezone_callback()
	{
		$timezone = $this->options["timezone"];
		if(empty($timezone))
			$timezone = 'UTC';
		$date = new DateTime('now', new DateTimeZone($timezone));
		$localtime = $date->format('h:i:s a');
		echo '<select id="timezone" name="offer_options[timezone]">'.wp_timezone_choice($timezone).'</select><br>';
		echo "Local time is $localtime.";
	}

	/** 
	 * SELECT DATE CALLBACK
	 */
	public function offer_date_callback()
	{
		printf(
			'<input type="text" id="select-date" class="date-select" name="offer_options[select-date]" value="%s" />',
			isset( $this->options['select-date'] ) ? esc_attr( $this->options['select-date']) : ''
		);
	}

	/** 
	 * SELECT TIME CALLBACK
	 */
	public function offer_time_callback()
	{
		printf(
			'<input type="text" id="select-time" class="time-select" name="offer_options[select-time]" value="%s" />',
			isset( $this->options['select-time'] ) ? esc_attr( $this->options['select-time']) : ''
		);
	}

	/** 
	 * COPUN CODE CALLBACK
	 */
	public function offer_couponcode_callback()
	{
        wp_editor( $this->options['coupon-code'], 'coupon-code', $settings = array('textarea_name' => 'offer_options[coupon-code]', 'textarea_rows' => 15) );
	}

}

/**** Instantiate Class ****/
if( is_admin() )
	$offer_timer = new OfferTimer();

/**** Include Front Style ****/
function offer_front_styles() {
	$options = get_option('offer_options');
	$_cupon = $options['coupon-code'];
	
	?>
	<div class="offer-bar">
	<div class="offer-wrapper">
        <div class="offer-code"><span><?php echo $_cupon; ?></span></div>
		<div class="offer-timer"></div>
	</div>
	</div>
		<span id="slideup">Hide Offer</span>
		<span id="slidedown">Show Offer</span>
    <?php

	wp_enqueue_script('jquery');
	// Counter CSS
    wp_enqueue_style('offer-jquery.classycountdown', plugins_url('/css/jquery.classycountdown.css',__FILE__), false, '1.0.0' );
    wp_enqueue_style('offer-css', plugins_url('/css/offer-front.css',__FILE__), false, '1.0.0' );
    // Counter JS
    wp_enqueue_script('offer-classycountdown-js', plugins_url('/js/jquery.classycountdown.js',__FILE__), array('jquery') );
    wp_enqueue_script('offer-knob-js', plugins_url('/js/jquery.knob.js',__FILE__), array('jquery') );
    wp_enqueue_script('offer-throttle-js', plugins_url('/js/jquery.throttle.js',__FILE__), array('jquery') );
    include_once(plugin_dir_path( __FILE__ ) . "/js/offer-front-custom.php");
}
add_action( 'wp_footer', 'offer_front_styles' );

/**** Include Admin Style ****/
function offer_admin_style() {
	if(isset($_REQUEST['page']) && $_REQUEST['page']=="offer-setting-admin"){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
	    wp_enqueue_style('offer-jquery-style', plugins_url('/css/jquery-ui.css',__FILE__), false, '1.0.0' );
	    // Timepicker JS
	    wp_enqueue_script('offer-timepicker-js', plugins_url('/js/jquery.timepicker.min.js',__FILE__), true );
	    wp_enqueue_script('offer-admin-js', plugins_url('/js/offer-admin.js',__FILE__), true );
	    include_once(plugin_dir_path( __FILE__ ) . "/js/offer-custom.php");
	}
}