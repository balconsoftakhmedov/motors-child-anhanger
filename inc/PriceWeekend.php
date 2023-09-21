<?php

add_action( 'after_setup_theme', 'child_theme_active' );
function child_theme_active() {
	class PricePerHourWeekend extends PricePerHour {

		public function __construct() {
			parent::__construct();
			add_action( 'save_post', array( get_class(), 'add_price_per_day_post_meta_weekend' ), 10, 2 );
		}

		public static function add_price_per_day_post_meta_weekend( $post_id, $post ) {
			if ( isset( $_POST['price-weekend'] ) && ! empty( $_POST['price-weekend'] ) ) {
				update_post_meta( $post->ID, 'rental_price_weekend_info', filter_var( $_POST['price-weekend'], FILTER_SANITIZE_NUMBER_FLOAT ) );
			} else {
				delete_post_meta( $post->ID, 'rental_price_weekend_info' );
			}
			if ( isset( $_POST['price-week'] ) && ! empty( $_POST['price-week'] ) ) {
				update_post_meta( $post->ID, 'rental_price_week_info', filter_var( $_POST['price-week'], FILTER_SANITIZE_NUMBER_FLOAT ) );
			} else {
				delete_post_meta( $post->ID, 'rental_price_week_info' );
			}
			if ( isset( $_POST['price-per-day'] ) && ! empty( $_POST['price-per-day'] ) ) {
				update_post_meta( $post->ID, 'rental_price_day_info', filter_var( $_POST['price-per-day'], FILTER_SANITIZE_NUMBER_FLOAT ) );
			} else {
				delete_post_meta( $post->ID, 'rental_price_day_info' );
			}
		}

		public static function pricePerHourView() {
			$price         = get_post_meta( stm_get_wpml_product_parent_id( get_the_ID() ), parent::META_KEY_INFO, true );
			$price_day     = get_post_meta( stm_get_wpml_product_parent_id( get_the_ID() ), 'rental_price_day_info', true );
			$price_week    = get_post_meta( stm_get_wpml_product_parent_id( get_the_ID() ), 'rental_price_week_info', true );
			$price_weekend = get_post_meta( stm_get_wpml_product_parent_id( get_the_ID() ), 'rental_price_weekend_info', true );
			$disabled = ( get_the_ID() != stm_get_wpml_product_parent_id( get_the_ID() ) ) ? 'disabled="disabled"' : '';
			?>
			<div class="admin-rent-info-wrap child">
				<ul class="stm-rent-nav-tabs">
					<li>
						<a class="stm-nav-link active"
						   data-id="price-per-hour"><?php echo esc_html__( 'Price Per 4 Hours', 'motors' ); ?></a>
					</li>
					<li>
						<a class="stm-nav-link"
						   data-id="discount-by-days"><?php echo esc_html__( 'Price Per 24 Hours', 'motors' ); ?></a>
					</li>
					<!--          <li>-->
					<!--            <a class="stm-nav-link"-->
					<!--               data-id="price-date-period">-->
					<?php //echo esc_html__( 'Price For Date Peiod', 'motors' );
					?><!--</a>-->
					<!--          </li>-->
					<li>
						<a class="stm-nav-link"
						   data-id="price-date-weekend"><?php echo esc_html__( 'Price For Weekend', 'motors' ); ?></a>
					</li>
					<li>
						<a class="stm-nav-link"
						   data-id="price-date-week"><?php echo esc_html__( 'Price For 7 Day', 'motors' ); ?></a>
					</li>
				</ul>
				<div class="stm-tabs-content">
					<div class="tab-pane show active" id="price-per-hour">
						<div class="price-per-hour-wrap">
							<div class="price-per-hour-input">
								<?php echo esc_html__( 'Price', 'motors' ); ?> <input type="text" name="price-per-hour"
																					  value="<?php echo esc_attr( $price ); ?>" <?php echo esc_attr( $disabled ); ?> />
							</div>
						</div>
					</div>
					<div class="tab-pane" id="discount-by-days">
						<!--            --><?php
						//            if(stm_me_get_wpcfto_mod('enable_fixed_price_for_days', false)) {
						//              do_action( 'stm_fixed_price_for_days' );
						//            } else {
						//              do_action( 'stm_disc_by_days' );
						//            }
						//
						?>
						<div class="price-per-hour-input">
							<?php echo esc_html__( 'Price for 24hrs', 'motors' ); ?> <input type="text"
																							name="price-per-day"
																							value="<?php echo esc_attr( $price_day ); ?>"/>
						</div>
					</div>
					<div class="tab-pane" id="price-date-period">
						<?php do_action( 'stm_date_period' ); ?>
					</div>
					<div class="tab-pane" id="price-date-weekend">
						<div class="price-per-hour-input">
							<?php echo esc_html__( 'Price', 'motors' ); ?> <input type="text" name="price-weekend"
																				  value="<?php echo esc_attr( $price_weekend ); ?>"/>
						</div>
					</div>
					<div class="tab-pane" id="price-date-week">
						<div class="price-per-hour-input">
							<?php echo esc_html__( 'Price for week', 'motors' ); ?> <input type="text"
																						   name="price-week"
																						   value="<?php echo esc_attr( $price_week ); ?>"/>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}

	new PricePerHourWeekend();
}

function stm_me_rental_add_meta_box_weekend() {
	add_action( 'add_meta_boxes', 'stm_me_add_price_per_hour_metabox_weekend' );
}

function stm_me_add_price_per_hour_metabox_weekend() {
	$title = __( 'Car Rent Price Info', 'stm_motors_extends' );
	if ( get_the_ID() !== stm_get_wpml_product_parent_id( get_the_ID() ) ) {
		$title = __( 'Car Rent Price Info (This fields are not editable.)', 'stm_motors_extends' );
	}
	add_meta_box(
			'price_per_hour',
			$title,
			array( 'PricePerHourWeekend', 'pricePerHourView' ),
			'product',
			'advanced',
			'high'
	);
}

add_action( 'stm_rental_meta_box', 'stm_me_rental_add_meta_box_weekend' );
function stm_get_cart_items_new() {
	$total_sum = 0;
	$fields    = stm_get_rental_order_fields_values();
	echo '<pre>';
	print_r( $fields );
	echo '</pre>';
	$cart       = ( ! empty( WC()->cart ) && ! empty( WC()->cart->get_cart() ) ) ? WC()->cart->get_cart() : '';
	$cart_items = array(
			'has_car'      => false,
			'option_total' => 0,
			'options_list' => array(),
			'car_class'    => array(),
			'options'      => array(),
			'total'        => $total_sum,
			'option_ids'   => array(),
			'oldData'      => 0,
	);
	if ( ! empty( $cart ) ) {

		$cart_old_data = ( isset( $_GET['order_old_days'] ) && ! empty( intval( $_GET['order_old_days'] ) ) ) ? intval( $_GET['order_old_days'] ) : 0;
		foreach ( $cart as $cart_item ) {
			$id   = stm_get_wpml_product_parent_id( $cart_item['product_id'] );
			$post = $cart_item['data'];
			$buy_type = ( 'WC_Product_Car_Option' === get_class( $cart_item['data'] ) ) ? 'options' : 'car_class';
			if ( 'options' === $buy_type ) {
				$cart_item_quantity = $cart_item['quantity'];
				if ( $cart_old_data > 0 ) {
					if ( 1 !== $cart_item['quantity'] ) {
						$cart_item_quantity = ( $cart_item['quantity'] / $cart_old_data );
					} else {
						$cart_item_quantity = 1;
					}
				}
				$price_string = $cart_item['data']->get_data();
				$total                      = intval( $cart_item_quantity ) * intval( $price_string['price'] );
				$cart_items['option_total'] += $total;
				$cart_items['option_ids'][] = $id;
				$cart_items[ $buy_type ][] = array(
						'id'       => $id,
						'quantity' => $cart_item_quantity,
						'name'     => $post->get_title(),
						'price'    => $price_string['price'],
						'total'    => $total,
						'opt_days' => $fields['ceil_days'],
						'subname'  => get_post_meta( $id, 'cars_info', true ),
				);
				$cart_items['options_list'][ $id ]    = $post->get_title();
				$cart_items['option_quantity'][ $id ] = $cart_item_quantity;
			} else {
				$variation_id = 0;
				if ( ! empty( $cart_item['variation_id'] ) ) {
					$variation_id = stm_get_wpml_product_parent_id( $cart_item['variation_id'] );
				}
				if ( isset( $_GET['pickup_location'] ) ) {
					$pickup_location_meta = get_post_meta( $id, 'stm_rental_office' );
					if ( ! in_array( $_GET['pickup_location'], explode( ',', $pickup_location_meta[0] ), true ) ) {
						WC()->cart->empty_cart();
					}
				}
				$price_string = $cart_item['data']->get_data();
				$week_days    = 0;
				$weekend_days = 0;
				$fields = stm_get_rental_order_fields_values();
				$startDate = $fields['pickup_date'];
				$endDate   = $fields['return_date'];
				$startDate = new DateTime( $startDate );
				$endDate   = new DateTime( $endDate );
				while ( $startDate <= $endDate ) {
					$timestamp = strtotime( $startDate->format( 'd-m-Y' ) );
					$day = date( 'w', $timestamp );
					if ( $day == 0 || $day == 6 ) {
						$weekend_days ++;
					} else {
						$week_days ++;
					}
					$startDate->modify( '+1 day' );
				}
				$hour4Price   = $price_four = get_post_meta( $id, 'rental_price_per_hour_info', true );
				$dayPrice     = $price_day = get_post_meta( $id, 'rental_price_day_info', true );
				$weekendPrice = $price_weekend = get_post_meta( $id, 'rental_price_weekend_info', true );
				$price_week   = get_post_meta( $id, 'rental_price_week_info', true );
				$datetime1  = new DateTime( $fields['pickup_date'] );
				$datetime2  = new DateTime( $fields['return_date'] );
				$difference = $datetime1->diff( $datetime2 );
				$order_days = $difference->d;
				$originalTime = new DateTimeImmutable( $fields['pickup_date'] );
				$targedTime   = new DateTimeImmutable( $fields['return_date'] );
				$interval     = $originalTime->diff( $targedTime );
				$order_time   = $interval->format( "%h" );
// start of total price calculation
				$total_price = 0;
				$logic       = false;
//$totalCost = calculateRentalCost( $datetime1,  $datetime2, $hour4Price, $dayPrice, $weekendPrice);
//echo "Total cost is $totalCost";
				if ( $weekend_days && $order_days ) {
					$price_string['price'] = intval( $price_weekend ) * $weekend_days;
					if ( $order_days === $weekend_days && ! $order_time ) {
						$total_price = intval( $price_weekend ) * $weekend_days;
						$logic       = true;
					} else if ( $order_days === 7 && $price_week && ! $order_time ) {
						$total_price = intval( $price_week );
						$logic       = true;
					} else if ( $order_days > $weekend_days && $week_days && ! $order_time ) {
						$total_price = ( intval( $price_weekend ) * $weekend_days ) + ( ( $order_days - $weekend_days ) * intval( $price_day ) );
						$logic       = true;
					} else if ( $order_days > $weekend_days && $order_time && $week_days ) {
						if ( $price_day ) {
							$total_price = ( intval( $price_weekend ) * 2 ) + ( intval( $price_day ) * ( $order_days - 1 ) );
							$logic       = true;
						} else {
							$total_price = ( intval( $price_weekend ) * 2 ) + ( ( intval( $price_week / 7 ) * $order_days ) + ( ( intval( $price_week ) / 7 ) / 24 * ( $order_time ) ) );
							$logic       = true;
						}

					} else if ( $order_days === $weekend_days && $order_time ) {
						$total_price = ( intval( $price_weekend ) * $weekend_days ) + intval( $price_day );
						$logic       = true;
					} else if ( $order_days > $weekend_days ) {
						$total_price = ( intval( $price_weekend ) * $weekend_days );
						$logic       = true;
					}
					if ( $order_days > $weekend_days && $week_days && $price_week ) {
						if ( $price_day ) {
							$logic       = true;
							$total_price = ( intval( $price_weekend ) * 2 ) + ( intval( $price_day ) * ( $order_days - 1 ) );
						} else {
							$logic       = true;
							$total_price = ( ( intval( $price_weekend ) * 2 ) + ( ( intval( $price_week / 7 ) * $order_days ) + ( ( intval( $price_week ) / 7 ) / 24 ) * ( $order_time ) ) );
						}
					}
				} else if ( $order_days && $week_days && ! $weekend_days ) {
					$price_string['price'] = intval( $price_day ) * $order_days;
					$total_price           = $price_string['price'];
					$logic                 = true;
					if ( $order_time ) {
						$logic       = true;
						$total_price = intval( $price_day ) * $week_days;
					}
				} else if ( ! $order_days && $order_time <= 4 ) {
					$logic                 = true;
					$price_string['price'] = $price_four;
					$total_price           = $price_four;
				} else if ( $weekend_days == 1 && $order_time > 4 ) {
					$logic                 = true;
					$price_string['price'] = $price_weekend;
					$total_price           = $price_weekend;
				} else if ( $order_time > 4 && $order_days < 1 ) {
					$logic                 = true;
					$price_string['price'] = $price_day;
					$total_price           = $price_day;
				}
				if ( $logic === false ) {
					if ( $weekend_days === 1 && $order_days === 1 && $order_time >= 0 ) {
						$total_price = ( ( intval( $price_weekend ) * 2 ) );
					} else if ( $weekend_days >= 1 && $order_days === 0 && $order_time >= 10 ) {
						$total_price = ( ( intval( $price_weekend ) * 2 ) );
					} else if ( $weekend_days === 2 && $order_days >= 2 && $order_time == 0 ) {
						$total_price = ( ( intval( $price_weekend ) * 2 ) + $price_day );
					} else if ( $weekend_days === 2 && $order_time >= 0 && $order_time < 4 ) {
						$total_price = ( ( intval( $price_weekend ) * 2 ) );
					} else if ( $weekend_days === 2 && $order_time >= 4 ) {
						$total_price = ( ( intval( $price_weekend ) * 2 ) );
					}
				}
// end of total price calculation
				update_post_meta( $id, '_price', $total_price );
				$product = wc_get_product( $id );
				$product->set_price( $total_price );
				$product->set_regular_price( $total_price ); // To be sure
// Save product data (sync data and refresh caches)
				$product->save();
//        var_dump($product->get_price());
				if ( $fields['order_days'] == 0 ) {
					$fields['order_days'] = 1;
				}
				$cart_items[ $buy_type ][] = array(
						'id'             => $id,
						'variation_id'   => $variation_id,
						'quantity'       => $cart_item['quantity'],
						'name'           => $post->get_title(),
						'price'          => $total_price,
						'total'          => $total_price,
						'subname'        => get_post_meta( $id, 'cars_info', true ),
						'payment_method' => get_post_meta( $variation_id, '_stm_payment_method', true ),
						'days'           => $fields['order_days'],
						'ceil_days'      => $fields['ceil_days'],
						'oldData'        => $cart_old_data,
				);
				$cart_items['has_car'] = true;
			}
		}
		$cart_items['total'] = intval( $cart_items['option_total'] ) + intval( $cart_items['car_class'][0]['total'] );
		/*Get only last element*/
		if ( count( $cart_items['car_class'] ) > 1 ) {
			$rent                       = array_pop( $cart_items['car_class'] );
			$cart_items['delete_items'] = $cart_items['car_class'];
			$cart_items['car_class']    = $rent;
		} else {
			if ( ! empty( $cart_items['car_class'] ) ) {
				$cart_items['car_class'] = $cart_items['car_class'][0];
			}
		}
	}
	echo '<pre>';
	print_r( $cart_items );
	echo '</pre>';

	return $cart_items;
}

function diff_time( $pickupDateTime, $returnDateTime ) {
	$duration   = $pickupDateTime->diff( $returnDateTime );
	$totalHours = $duration->days * 24 + $duration->h;

	return $totalHours;
}

function calculateRentalCost( $pickupDate, $returnDate, $hour4Price, $dayPrice, $weekendPrice ) {
	$totalCost = 0;
	$pickupDateTime = new DateTime( $pickupDate );
	$returnDateTime = new DateTime( $returnDate );
	$duration = $pickupDateTime->diff( $returnDateTime );
	$totalHours = diff_time( $pickupDateTime, $returnDateTime );
	if ( $totalHours <= 4 ) {
		return $hour4Price;
	}
	while ( $pickupDateTime < $returnDateTime ) {

		$dayOfWeek = $pickupDateTime->format( 'w' );
		$hourOfDay = (int) $pickupDateTime->format( 'H' );
		$nextWday = new DateTime( $pickupDateTime->format( 'Y-m-d H:i:s' ) );
		$nextWday->modify( '+48 hours' );
		$dayOfW            = $nextWday->format( 'w' );
		$hourOfD           = (int) $nextWday->format( 'H' );
		$currenttotalHours = diff_time( $pickupDateTime, $returnDateTime );
		echo "$dayOfW -  $hourOfD nextWday == " . $nextWday->format( 'Y-m-d H:i:s' ) . " totalHours= $currenttotalHours";
		if ( ( ( $dayOfWeek == 5 && $hourOfDay >= 12 ) ) && ( $currenttotalHours > 24 ) ) {
			$nextInterval = new DateTime( $pickupDateTime->format( 'Y-m-d H:i:s' ) );
			if ( $nextInterval <= $returnDateTime ) {
				$pickupDateTime->modify( '+48 hours' );
				$totalCost += $weekendPrice;
			} else {
				$pickupDateTime->modify( '+48 hours' );
			}
		} else if ( $dayOfWeek != 0 ) {
			echo "<br/> r " . __LINE__ . "  dayOfWeek=" . $dayOfWeek . " <br/>  dur h =" . $duration->h . " <br/>  ";
			if ( $duration->h > 0 && $duration->h <= 4 ) {
				$totalCost += $hour4Price;
				echo "hour4Price == $hour4Price <br/> ";
			} elseif ( $duration->h <= 0 && $totalHours > 4 ) {
				$totalCost += $dayPrice;
				echo "day 1 " . $dayPrice . " <br/>  ";
			} elseif ( $totalHours > 4 ) {
				$totalCost += $dayPrice;
				echo "day 2 " . $dayPrice . " <br/>  ";
			}
			$pickupDateTime->modify( '+24 hours' );

		} else {
			$pickupDateTime->modify( '+24 hours' );
		}

	}

	return $totalCost;
}

// Пример использования:
$pickupDate   = '2023-09-22 18:00';
$returnDate   = '2023-09-24 17:00';
$hour4Price   = 27;
$dayPrice     = 37;
$weekendPrice = 57;
$totalCost = calculateRentalCost( $pickupDate, $returnDate, $hour4Price, $dayPrice, $weekendPrice );
echo "Total cost is $totalCost";
exit;
