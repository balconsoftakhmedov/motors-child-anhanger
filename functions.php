<?php


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
    }

}




add_action('init','stm_rental__new_template', 99);