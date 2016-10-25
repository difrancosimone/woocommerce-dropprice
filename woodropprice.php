<?php

/*
Plugin Name: Woocommerce Drop Price
Plugin URI: http://
Description: Simply add in the dropdown menù itemized price for every option
Version: 1.0
Author: Simone Di Franco
Author URI: 
License: GPL2
*/

// add filter for specific hook
add_filter( 'woocommerce_variation_option_name', 'display_price_in_variation_option_name' );

function display_price_in_variation_option_name( $term ) {
    global $wpdb, $product;
	
    $result = $wpdb->get_col( "SELECT slug FROM {$wpdb->prefix}terms WHERE name = '$term'" );

    $term_slug = ( !empty( $result ) ) ? $result[0] : $term;

    //build query to get vars
    $query = "SELECT postmeta.post_id AS product_id
                FROM {$wpdb->prefix}postmeta AS postmeta
                    LEFT JOIN {$wpdb->prefix}posts AS products ON ( products.ID = postmeta.post_id )
                WHERE postmeta.meta_key LIKE 'attribute_%'
                    AND postmeta.meta_value = '$term_slug'
                    AND products.post_parent = $product->id";

    $variation_id = $wpdb->get_col( $query );

    $parent = wp_get_post_parent_id( $variation_id[0] );

    if ( $parent > 0 ) {
	    $_product = new WC_Product_Variation( $variation_id[0] );
		if (is_numeric($term)) {
			// calculating itemized price
			$totsomma = ($_product->get_price())/$term;
			return $term . ' --- ' . number_format($totsomma, 2, ',', ' ').'€ per 1';
		} 
    }
    return $term;

}
?>
