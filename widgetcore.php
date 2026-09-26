<?php
/**
 * Plugin Name:       WidgetCore
 * Plugin URI:        https://github.com/WidgetCore/Plugin
 * Description:       WidgetCore — custom widgets for Elementor: an accessible FAQ accordion with FAQPage schema, and a live AJAX search with a [wgcr_search] shortcode.
 * Version:           0.0.7
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * Author:            WidgetCore
 * Author URI:        https://github.com/WidgetCore
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       widgetcore
 * Domain Path:       /languages
 * Update URI:        https://github.com/WidgetCore/Plugin
 * Elementor tested up to: 4.3.2
 *
 * @package WidgetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WGCR_VER', '0.0.7' );
define( 'WGCR_FILE', __FILE__ );
define( 'WGCR_DIR', plugin_dir_path( __FILE__ ) );
define( 'WGCR_URL', plugin_dir_url( __FILE__ ) );

define( 'WGCR_REST_NS', 'wgcr/v1' );
define( 'WGCR_REST_ROUTE', '/search' );
define( 'WGCR_SEARCH_MAX_RESULTS', 50 );

function wgcr_svg_kses_base() {
	return array(
		'svg'    => array(
			'viewbox'         => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'aria-hidden'     => true,
			'focusable'       => true,
		),
		'path'   => array( 'd' => true ),
		'circle' => array(
			'cx' => true,
			'cy' => true,
			'r'  => true,
		),
	);
}

function wgcr_svg_icon( $name ) {
	$open = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">';
	$body = array(
		'search'  => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'plus'    => '<path d="M12 5v14M5 12h14"/>',
		'chevron' => '<path d="m6 9 6 6 6-6"/>',
	);
	return isset( $body[ $name ] ) ? $open . $body[ $name ] . '</svg>' : '';
}

require_once WGCR_DIR . 'includes/search/search-shared.php';
require_once WGCR_DIR . 'includes/search/rest-search.php';
require_once WGCR_DIR . 'includes/update/github-updater.php';

add_action( 'init', 'wgcr_load_translations', 1 );

function wgcr_load_translations() {
	$domain = 'widgetcore';
	$locale = determine_locale();
	$global = WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo';
	if ( is_readable( $global ) ) {
		load_textdomain( $domain, $global, $locale );
	}
	load_textdomain( $domain, WGCR_DIR . 'languages/' . $domain . '-' . $locale . '.mo', $locale );
}

add_action( 'elementor/elements/categories_registered', 'wgcr_add_category' );

function wgcr_add_category( $elements_manager ) {
	$elements_manager->add_category(
		'wgcr',
		array(
			'title' => esc_html__( 'ویجت‌های WidgetCore', 'widgetcore' ),
			'icon'  => 'fa fa-plug',
		)
	);
}

function wgcr_widget_map() {
	return array(
		'class-faq-widget.php'    => 'WGCR_FAQ_Widget',
		'class-search-widget.php' => 'WGCR_Search_Widget',
	);
}

function wgcr_require_widgets() {
	if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
		return;
	}
	foreach ( array_keys( wgcr_widget_map() ) as $file ) {
		require_once WGCR_DIR . 'widgets/' . $file;
	}
}

add_action( 'elementor/widgets/register', 'wgcr_register_widgets' );

function wgcr_register_widgets( $widgets_manager ) {
	wgcr_require_widgets();
	foreach ( wgcr_widget_map() as $class ) {
		if ( class_exists( $class ) ) {
			$widgets_manager->register( new $class() );
		}
	}
}

function wgcr_register_assets() {
	if ( wp_script_is( 'wgcr-search', 'registered' ) ) {
		return;
	}
	wp_register_style( 'wgcr-search', WGCR_URL . 'assets/search/search.css', array(), WGCR_VER );
	wp_register_script( 'wgcr-search', WGCR_URL . 'assets/search/search.js', array(), WGCR_VER, true );
	$config = wgcr_search_js_config();
	wp_add_inline_script( 'wgcr-search', 'window.WGCRSearch = ' . wp_json_encode( $config ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'wgcr_register_assets', 5 );
add_action( 'elementor/frontend/after_register_scripts', 'wgcr_register_assets' );
add_action( 'elementor/frontend/after_register_styles', 'wgcr_register_assets' );

add_action( 'elementor/preview/enqueue_styles', 'wgcr_preview_assets' );
add_action( 'elementor/preview/enqueue_scripts', 'wgcr_preview_assets' );

function wgcr_preview_assets() {
	wgcr_register_assets();
	wp_enqueue_style( 'wgcr-search' );
	wp_enqueue_script( 'wgcr-search' );
	wgcr_require_widgets();
	if ( function_exists( 'wgcr_faq_register_assets' ) ) {
		wgcr_faq_register_assets();
		wp_enqueue_style( 'wgcr-faq' );
		wp_enqueue_script( 'wgcr-faq' );
	}
}
