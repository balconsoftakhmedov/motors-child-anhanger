<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

add_action( 'wp_enqueue_scripts', 'stm_enqueue_parent_styles' );

function stm_enqueue_parent_styles() {
    $style_ver = filemtime( get_stylesheet_directory() . '/style.css' );
    $script_ver = filemtime( get_stylesheet_directory() . '/assets/js/custom.js' );

	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('stm-theme-style'), $style_ver );
	wp_enqueue_style( 'custom-fonts', get_stylesheet_directory_uri() . '/assets/fonts/fonts/open-sans/style.css', $style_ver );
	wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/assets/js/custom.js', '', $script_ver );

}

function getWeekendDays($startDate, $endDate): int
{
  $weekendDays = array(6, 7);

  $period = new DatePeriod(
    new DateTime($startDate),
    new DateInterval('P1D'),
    new DateTime($endDate)
  );

  $weekendDaysCount = 0;
  foreach ($period as $day) {
    if (in_array($day->format('N'), $weekendDays)) {
      $weekendDaysCount++;
    }
  }

  return $weekendDaysCount;
}

function isWeekend($date) {
  $weekDay = date('w', strtotime($date));
  return ($weekDay == 0 || $weekDay == 6);
}

remove_action( 'template_redirect', 'stm_rental_add_quantity_to_cart' );

include_once 'inc/PriceWeekend.php';


function stm_rental__new_template() {
    if(stm_is_rental()) {

        vc_map(
            array(
                'name'     => __( 'STM Products Grid', 'motors-wpbakery-widgets' ),
                'base'     => 'stm_car_class_grid',
                'category' => __( 'STM', 'motors-wpbakery-widgets' ),
                'html_template' => get_template_directory() . '-child/vc_templates/stm_car_class_grid_child.php',
                'params'   => array(
                    array(
                        'type'       => 'textfield',
                        'heading'    => __( 'Number of items to show', 'motors-wpbakery-widgets' ),
                        'param_name' => 'posts_per_page',
                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => __( 'CSS', 'motors-wpbakery-widgets' ),
                        'param_name' => 'css',
                        'group'      => __( 'Design options', 'motors-wpbakery-widgets' ),
                    ),
                ),
            )
        );

				vc_map(
				array(
					'name'     => __( 'STM Rent Car Form', 'motors-wpbakery-widgets' ),
					'base'     => 'stm_rent_car_form',
					'category' => __( 'STM', 'motors-wpbakery-widgets' ),
					'html_template' => get_template_directory() . '-child/vc_templates/stm_rent_car_form_child.php',
					'params'   => array(
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Set working hours. example: 9-18', 'motors-wpbakery-widgets' ),
							'param_name' => 'office_working_hours',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Style', 'motors-wpbakery-widgets' ),
							'param_name' => 'style',
							'value'      => array(
								__( 'Style 1', 'motors-wpbakery-widgets' ) => 'style_1',
								__( 'Style 2', 'motors-wpbakery-widgets' ) => 'style_2',
							),
							'std'        => 'style_1',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Align', 'motors-wpbakery-widgets' ),
							'param_name' => 'align',
							'value'      => array(
								__( 'Left', 'motors-wpbakery-widgets' )   => 'text-left',
								__( 'Center', 'motors-wpbakery-widgets' ) => 'text-center',
								__( 'Right', 'motors-wpbakery-widgets' )  => 'text-right',
							),
							'std'        => 'text-right',
						),
						array(
							'type'       => 'css_editor',
							'heading'    => __( 'CSS', 'motors-wpbakery-widgets' ),
							'param_name' => 'css',
							'group'      => __( 'Design options', 'motors-wpbakery-widgets' ),
						),
					),
				)
			);
    }

}




add_action('init','stm_rental__new_template', 99);

add_action( 'woocommerce_new_order', 'stm_order_fields_child', 99 );
function stm_order_fields_child( $order_id ) {
    if ( is_admin() ) {
        return false;
    }
    $cart_items = stm_get_cart_items();
	$cart_items_data = stm_get_cart_items_new();
    $date       = stm_get_rental_order_fields_values();
    update_post_meta( $order_id, 'order_car', $cart_items );
	update_post_meta( $order_id, 'order_car_data_full', $cart_items_data );
    update_post_meta( $order_id, 'order_car_date', $date );
    update_post_meta( $order_id, 'order_pickup_date', $date['pickup_date'] );
    update_post_meta( $order_id, 'order_pickup_location', $date['pickup_location'] );
    update_post_meta( $order_id, 'order_drop_date', $date['return_date'] );
    update_post_meta( $order_id, 'order_drop_location', $date['return_location'] );
}

function sendEmail() {

	$recipientEmail = "tutyou1972@gmail.com"; // Replace with the recipient's email address
	$subject        = "Your Subject Here";
	$emailMessage   = "Hello, this is a test email!";
	$senderEmail    = "sender@example.com"; // Replace with your email address
	$headers        = "From: $senderEmail";
	// Send the email
	$clientIP = $_SERVER['REMOTE_ADDR'];
	$targetIP = '213.230.80.28'; // Replace with the target IP address
	if ( $clientIP === $targetIP ) {
		$mailSent = mail( $recipientEmail, $subject, $emailMessage, $headers );
		if ( $mailSent ) {
			echo "Email sent successfully.";
		} else {
			echo "Email sending failed.";
		}
	}
}

function get_formatted_dates($date) {
	$date_time_string = $date;
	if ( preg_match( '/^\d{4}-\d{2}-\d{2}\d{4}$/', $date_time_string ) ) {
		$date = DateTime::createFromFormat( 'Y-m-dHi', $date_time_string )->format( 'Y-m-d H:i' );
	}
	return $date;
}

function stm_custom_modify_order_info( $order_info ) {
	if ( ! empty( $order_info['pickup_date'] ) ) {
		$order_info['pickup_date'] = get_formatted_dates( $order_info['pickup_date'] );
	}
	if ( ! empty( $order_info['return_date'] ) ) {
		$order_info['return_date'] = get_formatted_dates( $order_info['return_date'] );
	}

	return $order_info;
}

function stm_init_hook_function() {
	add_filter( 'stm_rental_date_values', 'stm_custom_modify_order_info', 99, 1 );
}

add_action( 'init', 'stm_init_hook_function' );

function flance_write_log( $message, $file = 'logs/logfile.log' ) {

	ob_start();
	print_r( $message );
	$message         = ob_get_clean();
	$theme_directory = get_stylesheet_directory();
	$log_file_path = $theme_directory . '/' . $file;
	$log_directory = dirname( $log_file_path );
	if ( ! file_exists( $log_directory ) ) {
		mkdir( $log_directory, 0755, true );
	}
	file_put_contents( $log_file_path, date( 'Y-m-d H:i:s' ) . ' ' . $message . "\n",  LOCK_EX );
}
