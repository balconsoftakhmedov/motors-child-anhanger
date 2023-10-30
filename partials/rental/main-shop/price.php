<?php
$stm_id = stm_get_wpml_product_parent_id(get_the_ID());
$product = wc_get_product($stm_id);
$product_type = 'default';

$price_four = get_post_meta($stm_id, 'rental_price_per_hour_info', true);
$price_day = get_post_meta($stm_id, 'rental_price_day_info', true);
$price_weekend = get_post_meta($stm_id, 'rental_price_weekend_info', true);
$price_week = get_post_meta($stm_id, 'rental_price_week_info', true);

$week_days = 0;
$weekend_days = 0;

$fields = stm_get_rental_order_fields_values();

$fields['pickup_date'] = strtotime($fields['pickup_date']) ? $fields['pickup_date'] : date('Y-m-d H:i');
$fields['return_date'] = strtotime($fields['return_date']) ? $fields['return_date'] : date('Y-m-d H:i');

$startDate = $fields['pickup_date'];
$endDate = $fields['return_date'];

$startDate = new DateTime($startDate);
$endDate = new DateTime($endDate);

while ($startDate <= $endDate) {
    $timestamp = strtotime($startDate->format('d-m-Y'));

    $day = date('w', $timestamp);

    if ($day == 0 || $day == 6) {
        $weekend_days++;
    } else {
        $week_days++;
    }
    $startDate->modify('+1 day');
}

$price_four = get_post_meta($stm_id, 'rental_price_per_hour_info', true);
$price_day = get_post_meta($stm_id, 'rental_price_day_info', true);
$price_weekend = get_post_meta($stm_id, 'rental_price_weekend_info', true);

$datetime1 = new DateTime($fields['pickup_date']);
$datetime2 = new DateTime($fields['return_date']);
$difference = $datetime1->diff($datetime2);
$order_days = $difference->d;
$order_hour = $difference->format("%h)");

$originalTime = new DateTimeImmutable($fields['pickup_date']);
$targedTime = new DateTimeImmutable($fields['return_date']);
$interval = $originalTime->diff($targedTime);
$order_time = $interval->format("%h");

$total_price = 0;
if ($price_weekend && $price_day && $price_four) {
    if ($weekend_days && $order_days) {
        $price_string['price'] = $price_weekend * $weekend_days;
        if ($order_days === $weekend_days && !$order_time) {
            $total_price = $price_weekend * $weekend_days;
        } else if ($order_days > $weekend_days && $week_days && !$order_time) {
            $total_price = ($price_weekend * $weekend_days) + (($order_days - $weekend_days) * $price_day);
        } else if ($order_days > $weekend_days && $order_time && $week_days) {
            $total_price = ($price_weekend * 2) + ($price_day * ($order_days - 1));
        } else if ($order_days === $weekend_days && $order_time) {
            $total_price = ($price_weekend * $weekend_days) + $price_day;
        }
    } else if ($order_days && $week_days && !$weekend_days) {
        $price_string['price'] = $price_day * $order_days;
        $total_price = $price_string['price'];
        if ($order_time) {
            $total_price = $price_day * $week_days;
        }
    } else if (!$order_days && $order_time <= 4) {
        $price_string['price'] = $price_four;
        $total_price = $price_four;
    } else if ($order_time > 4 && $order_days < 1) {
        $price_string['price'] = $price_day;
        $total_price = $price_day;
    }
}


update_post_meta($stm_id, '_price', $total_price);
$price = get_post_meta($stm_id, '_price', true);

if (!empty($product)) :
    if ($product->is_type('variable')) :
        $variations = $product->get_available_variations();
        $prices = array();

        $fields = stm_get_rental_order_fields_values();

        if (!empty($variations)) {
            $max_price = 0;
            $i = 0;
            foreach ($variations as $variation) {

                if ((!empty($variation['display_price']) || !empty($variation['display_regular_price'])) && !empty($variation['variation_description'])) {

                    $gets = array(
                        'add-to-cart' => $stm_id,
                        'product_id' => $stm_id,
                        'variation_id' => $variation['variation_id'],
                    );

                    foreach ($variation['attributes'] as $key => $val) {
                        $gets[$key] = $val;
                    }

                    $url = add_query_arg($gets, get_permalink($stm_id));

                    $total_price = false;
                    if (!empty($fields['order_days'])) {
                        $total_price = (!empty($variation['display_price'])) ? $variation['display_price'] : $variation['display_regular_price'];
                    }

                    if (!empty($total_price)) {
                        if ($max_price < $total_price) {
                            $max_price = $total_price;
                        }
                    }

                    $prices[] = array(
                        'price' => stm_get_default_variable_price($stm_id, $i),
                        'text' => $variation['variation_description'],
                        'total' => $total_price,
                        'url' => $url,
                        'var_id' => $variation['variation_id'],
                    );
                }

                $i++;
            }
        } ?>
    <?php
    else :
        $prod = $product->get_data();
        $price = (empty($prod['sale_price'])) ? $prod['price'] : $prod['sale_price'];

        $gets = array(
            'add-to-cart' => $stm_id,
            'product_id' => $stm_id,
        );

        $total_price = false;
        if (!empty($fields['order_days'])) {
            $total_price = $product->get_price();
        }
$ur =  wc_get_checkout_url();
//$ur = 		get_permalink($stm_id);
        $url = add_query_arg($gets, $ur);
        if ((!empty($price_four) || !empty($price_day) || !empty($price_weekend) || !empty($price_week)) && $url) :
            ?>
            <div class="stm_rent_prices">
                <?php if (!empty($price_four)) { ?>
                    <div class="price_item heading-font">
                        <?php echo sprintf(esc_html__('%s / 4 Std.', 'motors'), wp_kses_post(wc_price($price_four))); ?>
                    </div>
                <?php } ?>
                <?php if (!empty($price_day)) { ?>
                    <div class="price_item heading-font">
                        <?php echo sprintf(esc_html__('%s / 24 Std.', 'motors'), wp_kses_post(wc_price($price_day))); ?>
                    </div>
                <?php } ?>

                <?php if (!empty($price_weekend)) { ?>
                    <div class="price_item heading-font">
                        <?php echo sprintf(esc_html__('%s / Wochenende', 'motors'), wp_kses_post(wc_price($price_weekend))); ?>
                    </div>
                <?php } ?>
                <?php if (!empty($price_week)) { ?>

                    <div class="price_item heading-font">
                        <?php echo sprintf(esc_html__('%s / Woche', 'motors'), wp_kses_post(wc_price($price_week))); ?>
                    </div>
                <?php } ?>
                <?php if (!empty($price_four) || !empty($price_day) || !empty($price_weekend) || !empty($price_week)) : ?>
                    <div class="stm_rent_price">
                        <div class="pay">
                            <?php if ($fields['pickup_date'] == $fields['return_date']) { ?>

                                <a class="heading-font" href="/reservation">
                                    <?php esc_html_e('Pay now', 'motors'); ?>
                                </a>
                            <?php } else { ?>
                                <?php
                                if ((($price_four == '-' && $order_hour < 5) || ($price_four > 0 && $order_hour < 5)) && $order_days == 0) { ?>
                                    <a class="heading-font" href="/reservation" style="padding: 0px 10px ">
                                        <?php esc_html_e('Mindestgrenze 5 Stunden', 'motors'); ?>
                                    </a>
                                <?php } elseif (!empty($price_week) && $order_days < 7) { ?>
                                    <a class="heading-font" href="/reservation" style="padding: 0px 10px ">
                                        <?php esc_html_e('Mindestens 7 Tage', 'motors'); ?>
                                    </a>
                                <?php } else { ?>
                                    <a class="heading-font" href="<?php echo esc_url($url); ?>">
                                        <?php esc_html_e('Pay now', 'motors'); ?>
                                    </a>
                                <?php } ?>

                            <?php } ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>