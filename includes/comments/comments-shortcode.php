<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'wgcr_comments', 'wgcr_comments_shortcode' );

if ( ! function_exists( 'wgcr_comments_shortcode' ) ) {
	function wgcr_comments_shortcode( $atts ) {
		$atts = shortcode_atts( wgcr_comments_defaults(), $atts, 'wgcr_comments' );
		ob_start();
		wgcr_comments_print( $atts );
		return (string) ob_get_clean();
	}
}
