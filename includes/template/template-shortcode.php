<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wgcr_template_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'wgcr_template' );
	$id   = (int) $atts['id'];
	if ( $id < 1 || ! post_type_exists( 'elementor_library' ) ) {
		return '';
	}
	$post = get_post( $id );
	if ( ! $post || 'elementor_library' !== $post->post_type || 'publish' !== $post->post_status ) {
		return '';
	}
	if ( ! class_exists( '\Elementor\Plugin' ) || ! isset( \Elementor\Plugin::$instance->frontend ) ) {
		return '';
	}
	return (string) \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true );
}
add_shortcode( 'wgcr_template', 'wgcr_template_shortcode' );
