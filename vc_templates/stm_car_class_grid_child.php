<?php
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ) );

if ( empty( $posts_per_page ) ) {
    $posts_per_page = 6;
}

$args = array(
    'order' => 'ASC',
    'post_type'      => 'product',
    'posts_per_page' => $posts_per_page,
    'post_status'    => 'publish',
    'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => 'car_option',
            'operator' => 'NOT IN',
        ),
    ),
);





$offices = new WP_Query( $args );

if ( $offices->have_posts() ) : ?>
    <div class="stm_products_grid_class">
        <?php
        while ( $offices->have_posts() ) :
            $offices->the_post();
            $s_title  = get_post_meta( get_the_ID(), 'cars_info', true );
            $car_info = stm_get_car_rent_info( get_the_ID() );
            $price    = stm_get_default_variable_price( get_the_ID(), 0, true );
            $price_four = get_post_meta(get_the_ID(), 'rental_price_per_hour_info', true);
            $price_day = get_post_meta(get_the_ID(), 'rental_price_day_info', true);
            $price_week = get_post_meta(get_the_ID(), 'rental_price_week_info', true);
            $price_weekend = get_post_meta(get_the_ID(), 'rental_price_weekend_info', true);
            $sku = get_post_meta(get_the_ID(), '_sku', true);




            $result2[] = get_the_ID();

            $keyid = (array_search(get_the_ID(),$result2) + 1)/14;
            $whole = floor($keyid);
            $fraction = $keyid - $whole;
            $paginationID = 0;
            if($fraction> 0) {
                $paginationID = $whole + 1;
            }
            else {
                $paginationID = $whole;
            }

            if ($paginationID > 1){

            }
            $paginationWrapper = ($paginationID > 1) ? 'page/' . $paginationID . '/' : '';
            ?>


            <div class="stm_product_grid_single">
                <a href="<?php echo esc_url( stm_woo_shop_page_url() . $paginationWrapper . esc_attr( '#product-' . get_the_ID() ) ) ?> " class="inner">

                    <div class="stm_top clearfix">
                        <div class="stm_left heading-font">

                            <h3 id="stm_sku_title" class="stm_sku_title "><div class="stm_sku_span"><spam class=""><?php echo $sku ?></spam></div><?php the_title(); ?></h3>
                            <?php if ( ! empty( $s_title ) ) : ?>
                                <div class="s_title"><?php echo esc_html( $s_title ); ?></div>
                            <?php endif; ?>

                            <?php if ( ! empty( $car_info ) ) : ?>
                                <div class="stm_right">
                                    <?php
                                    foreach ( $car_info as $slug => $info ) :
                                        $name = $info['value'];
                                        if ( $info['numeric'] ) {
                                            $name = $info['value'] . ' ' . esc_html( $info['name'] );
                                        }
                                        $font = $info['font'];
                                        ?>
                                        <div class="single_info stm_single_info_font_<?php echo esc_attr( $font ); ?>">
                                            <i class="<?php echo esc_attr( $font ); ?>"></i>
                                            <span><?php stm_dynamic_string_translation_e( 'Rental option ' . $name, sanitize_text_field( $name ) ); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php if(!empty( $price_day ) || ! empty( $price_four ) || ! empty( $price_weekend ) || ! empty( $price_week )): ?>
           
                                <div class="price_wr <?php echo !empty( $price_day ) && ! empty( $price_four ) && ! empty( $price_weekend ) && ! empty( $price_week ) ?  'four_price': '' ?> ">
                                    <?php if ( ! empty( $price_four ) ) : ?>
                                        <div class="price">
                                            <?php
                                            echo sprintf(
                                            /* translators: formatted price */
                                                esc_html__( '%s/4 Std.', 'motors' ),
                                                wp_kses_post( wc_price( $price_four ) )
                                            );
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( ! empty( $price_day ) ) : ?>
                                        <div class="price">
                                            <?php
                                            echo sprintf(
                                            /* translators: formatted price */
                                                esc_html__( '%s/24 Std.', 'motors' ),
                                                wp_kses_post( wc_price( $price_day ) )
                                            );
                                            ?>
                                        </div>
                                    <?php endif; ?>


                                    <?php if ( ! empty( $price_weekend ) ) : ?>
                                        <div class="price">
                                            <?php
                                            echo sprintf(
                                            /* translators: formatted price */
                                                esc_html__( '%s/Wochenende', 'motors' ),
                                                wp_kses_post( wc_price( ($price_weekend) ) )
                                            );
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( ! empty( $price_week ) ) : ?>
                                        <div class="price">
                                            <?php
                                            echo sprintf(
                                            /* translators: formatted price */
                                                esc_html__( '%s/Woche', 'motors' ),
                                                wp_kses_post( wc_price( $price_week ) )
                                            );
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="stm_image">
                            <?php the_post_thumbnail( 'stm-img-796-466', array( 'class' => 'img-responsive' ) ); ?>
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
    <?php
    wp_reset_postdata();
endif;
?>