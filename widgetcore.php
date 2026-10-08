<?php
/**
 * Plugin Name:       WidgetCore
 * Plugin URI:        https://github.com/WidgetCore/Plugin
 * Description:       WidgetCore — custom widgets for Elementor: an accessible FAQ accordion with FAQPage schema, a live AJAX search with a [wgcr_search] shortcode, a customizable comments list and form with a [wgcr_comments] shortcode, accessible tabs with text, Elementor template or custom HTML content per tab (plus a [wgcr_template] shortcode), animated counter, progress bars, alert box, icon box, price table, social icons, a breadcrumb trail with a Yoast/Rank Math bridge and BreadcrumbList schema, and social share buttons with Iranian networks and custom share URLs.
 * Version:           0.1.2
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * Author:            WidgetCore
 * Author URI:        https://docs.widgetcore.ir
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       widgetcore
 * Domain Path:       /languages
 * Update URI:        https://github.com/WidgetCore/Plugin
 * Elementor tested up to: 4.3.4
 *
 * @package WidgetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WGCR_VER', '0.1.2' );
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
		'check'   => '<path d="m5 12.5 4.5 4.5L19 7"/>',
		'close'   => '<path d="M6.5 6.5 17.5 17.5M17.5 6.5 6.5 17.5"/>',
		'info'    => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5.5M12 7.6h.01"/>',
		'warning' => '<path d="M12 4.5 21 19.5H3L12 4.5z"/><path d="M12 10v4M12 17h.01"/>',
		'mail'    => '<path d="M3.5 6h17v12h-17z"/><path d="m4 7 8 6 8-6"/>',
		'phone'   => '<path d="M6.5 3.5h3l1.5 4-2 1.5a12 12 0 0 0 5.5 5.5l1.5-2 4 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.5 5.7a2 2 0 0 1 2-2.2z"/>',
		'link'    => '<path d="m9.5 14.5 5-5"/><path d="m11.7 6.9 1.8-1.8a4.6 4.6 0 0 1 6.5 6.5l-1.8 1.8"/><path d="m12.3 17.1-1.8 1.8a4.6 4.6 0 0 1-6.5-6.5l1.8-1.8"/>',
		'globe'   => '<circle cx="12" cy="12" r="9"/><path d="M3.2 12h17.6"/><path d="M12 3.2c2.6 2.6 2.6 15 0 17.6M12 3.2c-2.6 2.6-2.6 15 0 17.6"/>',
		'x'        => '<path d="M4.8 4.8 19.2 19.2M19.2 4.8 4.8 19.2"/>',
		'facebook' => '<path d="M12 20.8v-8.6"/><path d="M9.4 12.2h5.2"/><path d="M12 12.2V9.1a3.1 3.1 0 0 1 3.1-3.1h1.6"/>',
		'linkedin' => '<circle cx="6.2" cy="6.4" r="1.5"/><path d="M6.2 10.4v9"/><path d="M10.8 19.4v-9"/><path d="M10.8 13.8a3.4 3.4 0 0 1 6.8 0v5.6"/>',
		'telegram' => '<path d="M21 4 3 11l6.5 2.5L13 21l2.5-6L21 4z"/><path d="m9.5 13.5 8-6.5"/>',
		'whatsapp' => '<path d="M20.4 11.6a8.4 8.4 0 0 1-12.3 7.4L3.9 20.2l1.3-4.1A8.4 8.4 0 1 1 20.4 11.6z"/><path d="M9 8.9c-.3.7-.2 1.6.3 2.5a8.7 8.7 0 0 0 3.3 3.2c.9.5 1.8.6 2.5.3l.6-.4-1.6-1.5-.8.7c-.7-.4-1.6-1.3-2-2l.7-.8L10.5 9l-.5.4z"/>',
		'eitaa'    => '<path d="M12 4.2c4.6 0 8.2 2.9 8.2 6.6s-3.6 6.6-8.2 6.6c-1 0-2-.2-2.9-.5l-4.4 1.5 1.2-3.6c-1.3-1.2-2.1-2.6-2.1-4 0-3.7 3.6-6.6 8.2-6.6z"/>',
		'copy'     => '<path d="M14.5 5.5h-9v9"/><path d="M9.5 9.5h9v9h-9z"/>',
		'share'    => '<circle cx="17.5" cy="6" r="2.3"/><circle cx="6.5" cy="12" r="2.3"/><circle cx="17.5" cy="18" r="2.3"/><path d="m8.6 10.8 6.8-3.6M8.6 13.2l6.8 3.6"/>',
	);
	return isset( $body[ $name ] ) ? $open . $body[ $name ] . '</svg>' : '';
}

require_once WGCR_DIR . 'includes/style/style-controls.php';
require_once WGCR_DIR . 'includes/search/search-shared.php';
require_once WGCR_DIR . 'includes/search/rest-search.php';
require_once WGCR_DIR . 'includes/comments/comments-shared.php';
require_once WGCR_DIR . 'includes/comments/comments-policy.php';
require_once WGCR_DIR . 'includes/comments/comments-shortcode.php';
require_once WGCR_DIR . 'includes/template/template-shortcode.php';
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
		'class-faq-widget.php'        => 'WGCR_FAQ_Widget',
		'class-search-widget.php'     => 'WGCR_Search_Widget',
		'class-comments-widget.php'   => 'WGCR_Comments_Widget',
		'class-tabs-widget.php'       => 'WGCR_Tabs_Widget',
		'class-counter-widget.php'    => 'WGCR_Counter_Widget',
		'class-progress-widget.php'   => 'WGCR_Progress_Widget',
		'class-alert-widget.php'      => 'WGCR_Alert_Widget',
		'class-iconbox-widget.php'    => 'WGCR_Icon_Box_Widget',
		'class-pricetable-widget.php' => 'WGCR_Price_Table_Widget',
		'class-social-widget.php'     => 'WGCR_Social_Widget',
		'class-breadcrumb-widget.php' => 'WGCR_Breadcrumb_Widget',
		'class-share-widget.php'      => 'WGCR_Share_Widget',
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
	foreach ( array( 'faq', 'tabs', 'counter', 'progress', 'alert', 'iconbox', 'pricetable', 'social', 'breadcrumb', 'share' ) as $wgcr_w ) {
		$wgcr_fn = 'wgcr_' . $wgcr_w . '_register_assets';
		if ( ! function_exists( $wgcr_fn ) ) {
			continue;
		}
		$wgcr_fn();
		wp_enqueue_style( 'wgcr-' . $wgcr_w );
		if ( wp_script_is( 'wgcr-' . $wgcr_w, 'registered' ) ) {
			wp_enqueue_script( 'wgcr-' . $wgcr_w );
		}
	}
}
