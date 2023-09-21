<?php
$cart_items = stm_get_cart_items_new();
$car_rent   = $cart_items['car_class'];
$id         = $car_rent['id'];

$priceDate    = PriceForDatePeriod::getDescribeTotalByDays( $car_rent['price'], $id );
$pricePerHour = get_post_meta( $id, 'rental_price_per_hour_info', true );
$discount     = ( class_exists( 'DiscountByDays' ) ) ? DiscountByDays::get_days_post_meta( $id ) : null;
$fixedPrice   = ( class_exists( 'PriceForQuantityDays' ) ) ? PriceForQuantityDays::get_sorted_fixed_price( $id ) : null;

$price_four = get_post_meta($id, 'rental_price_per_hour_info', true);
$price_day = get_post_meta($id, 'rental_price_day_info', true);
$price_weekend = get_post_meta($id, 'rental_price_weekend_info', true);
$price_week = get_post_meta($id, 'rental_price_week_info', true);

$fields = stm_get_rental_order_fields_values();

$datetime1 = new DateTime($fields['pickup_date']);
$datetime2 = new DateTime($fields['return_date']);
$difference = $datetime1->diff($datetime2);
$order_days = $difference->d;

$originalTime = new DateTimeImmutable($fields['pickup_date']);
$targedTime = new DateTimeImmutable($fields['return_date']);
$interval = $originalTime->diff($targedTime);
$order_time = $interval->format("%h");

$total = get_post_meta($id, '_price', true);
$product = wc_get_product( $id );


?>

<div class="title">
    <h4><?php echo sanitize_text_field( $car_rent['name'] ); ?></h4>
    <div class="subtitle heading-font"><?php echo sanitize_text_field( $car_rent['subname'] ); ?></div>
</div>
<?php
if ( has_post_thumbnail( $id ) ) :
    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'stm-img-350-181' );
    if ( ! empty( $image[0] ) ) :
        ?>
        <div class="image">
            <img src="<?php echo esc_url( $image[0] ); ?>" />
        </div>
    <?php endif; ?>
<?php endif; ?>

<!--Car rent-->
<div class="stm_rent_table">

    <!--  <div class="heading heading-font"><h4>--><?php //esc_html_e( 'Rate', 'motors' ); ?><!--</h4></div>-->
    <table>
        <thead class="heading-font">
        <tr>
            <td><?php esc_html_e( 'QTY', 'motors' ); ?></td>
            <td><?php esc_html_e( 'Rate', 'motors' ); ?></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="3" class="divider"></td>
        </tr>
        <!--FIXED PRICE-->
        <?php
        if ( ! empty( $price_day ) ) : ?>
            <tr>
                <td><?php echo sprintf( esc_html__( '%s Tag', 'motors' ), $car_rent['days'] ); ?></td>
                <td>
                    <?php echo wc_price( $price_day ); ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php if ( ! empty( $price_four  && $price_four != '-') ) : ?>
            <tr>

                <td><?php echo esc_html__( '4 Stunden', 'motors' ); ?></td>
                <td>
                    <?php echo wc_price( $price_four ); ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ( ! empty( $price_weekend ) ) : ?>
            <tr>
                <td><?php echo esc_html__( 'Wochenende', 'motors' ); ?></td>
                <td>
                    <?php echo wc_price( $price_weekend ); ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php if ( ! empty( $price_week ) ) : ?>
            <tr>
                <td><?php echo esc_html__( 'week', 'motors' ); ?></td>
                <td>
                    <?php echo wc_price( $price_week ); ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td colspan="3" class="divider"></td>
        </tr>
        </tbody>
        <tfoot class="heading-font">
        <tr>
            <td colspan="2"><?php esc_html_e( 'Rental Charges Rate', 'motors' ); ?></td>
            <td><?php echo wc_price( intval($total) ); ?></td>
        </tr>
        </tfoot>
    </table>
</div>

<!--Add-ons-->
<?php if ( ! empty( $cart_items['options'] ) ) : ?>
    <div class="stm_rent_table">
        <div class="heading heading-font"><h4><?php esc_html_e( 'Add-ons', 'motors' ); ?></h4></div>
        <table>
            <thead class="heading-font">
            <tr>
                <td><?php esc_html_e( 'QTY', 'motors' ); ?></td>
                <td><?php esc_html_e( 'Rate', 'motors' ); ?></td>
                <td><?php esc_html_e( 'Subtotal', 'motors' ); ?></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="3" class="divider"></td>
            </tr>
            <?php foreach ( $cart_items['options'] as $car_option ) : ?>
                <tr>
                    <td>
                        <?php
                        $opt_days = ( ! empty( $car_option['opt_days'] ) ) ? $car_option['opt_days'] : 1;
                        $quant    = ( ! empty( get_post_meta( $car_option['id'], '_car_option', true ) ) ) ? $car_option['quantity'] : $car_option['quantity'] / $opt_days;
                        echo sprintf( esc_html__( '%1$s x %2$1s %3$s %4$s day(s)', 'motors' ), $quant, $car_option['name'], esc_html__( 'for', 'motors' ), $car_option['opt_days'] );
                        ?>
                    </td>
                    <td><?php echo wc_price( $car_option['price'] ); ?></td>
                    <td><?php echo wc_price( $car_option['total'] ); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="divider"></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="heading-font">
            <tr>
                <td colspan="2"><?php esc_html_e( 'Add-ons Charges Rate', 'motors' ); ?></td>
                <td><?php echo wc_price( $cart_items['option_total'] ); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php endif; ?>

<?php get_template_part( 'partials/rental/common/tax' ); ?>

<?php get_template_part( 'partials/rental/common/coupon' ); ?>

<div class="stm-rent-total heading-font">
    <table>
        <tr>
            <td><?php esc_html_e( 'Estimated total', 'motors' ); ?></td>
            <td><?php echo wc_price( $cart_items['total'] ); ?></td>
        </tr>
    </table>
</div>
