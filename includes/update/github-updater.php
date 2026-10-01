<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WGCR_UPDATE_URI', 'https://github.com/WidgetCore/Plugin' );
define( 'WGCR_UPDATE_API', 'https://api.github.com/repos/WidgetCore/Plugin/releases/latest' );
define( 'WGCR_UPDATE_ASSET', 'widgetcore.zip' );

function wgcr_update_release() {
	$args = array(
		'timeout' => 8,
		'headers' => array( 'Accept' => 'application/vnd.github+json' ),
	);
	if ( defined( 'WGCR_GITHUB_TOKEN' ) && WGCR_GITHUB_TOKEN ) {
		$args['headers']['Authorization'] = 'Bearer ' . WGCR_GITHUB_TOKEN;
	}
	$response = wp_remote_get( WGCR_UPDATE_API, $args );
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	$tag  = isset( $data['tag_name'] ) ? ltrim( (string) $data['tag_name'], 'vV' ) : '';
	if ( ! preg_match( '/^\d+\.\d+\.\d+$/', $tag ) ) {
		return false;
	}
	$package = '';
	$assets  = isset( $data['assets'] ) && is_array( $data['assets'] ) ? $data['assets'] : array();
	foreach ( $assets as $asset ) {
		if ( isset( $asset['name'], $asset['browser_download_url'] ) && WGCR_UPDATE_ASSET === $asset['name'] ) {
			$package = (string) $asset['browser_download_url'];
		}
	}
	if ( '' === $package && ! empty( $data['zipball_url'] ) ) {
		$package = (string) $data['zipball_url'];
	}
	if ( ! in_array( wp_parse_url( $package, PHP_URL_HOST ), array( 'github.com', 'api.github.com' ), true ) ) {
		return false;
	}
	return array(
		'tag'       => (string) $data['tag_name'],
		'version'   => $tag,
		'package'   => esc_url_raw( $package ),
		'url'       => esc_url_raw( ! empty( $data['html_url'] ) ? (string) $data['html_url'] : WGCR_UPDATE_URI . '/releases' ),
		'notes'     => isset( $data['body'] ) ? (string) $data['body'] : '',
		'published' => isset( $data['published_at'] ) ? (string) $data['published_at'] : '',
	);
}

function wgcr_update_notes_html( $notes ) {
	$html = '';
	$list = false;
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $notes ) as $line ) {
		$line = trim( $line );
		if ( preg_match( '/^(?:#{1,6}\s*(.+?)\s*#*|=+\s*(.+?)\s*=+)$/', $line, $m ) ) {
			$html .= ( $list ? '</ul>' : '' ) . '<h4>' . esc_html( '' !== $m[1] ? $m[1] : $m[2] ) . '</h4>';
			$list  = false;
			continue;
		}
		if ( preg_match( '/^[*-]\s+(.+)$/', $line, $m ) ) {
			$html .= ( $list ? '' : '<ul>' ) . '<li>' . preg_replace( '/`([^`]+)`/', '<code>$1</code>', esc_html( $m[1] ) ) . '</li>';
			$list  = true;
			continue;
		}
		if ( $list ) {
			$html .= '</ul>';
			$list  = false;
		}
		if ( '' !== $line ) {
			$html .= '<p>' . preg_replace( '/`([^`]+)`/', '<code>$1</code>', esc_html( $line ) ) . '</p>';
		}
	}
	return $html . ( $list ? '</ul>' : '' );
}

function wgcr_update_tested_wp( $readme = '' ) {
	static $tested = null;
	$custom       = '' !== $readme;
	$path         = $custom ? $readme : dirname( __DIR__, 2 ) . '/readme.txt';
	$value        = '';
	if ( is_readable( $path ) ) {
		$raw = file_get_contents( $path, false, null, 0, 2048 );
		if ( is_string( $raw ) && preg_match( '/^Tested up to:[ \t]*([0-9][0-9.]*)[ \t]*$/mi', $raw, $m ) && preg_match( '/^\d+\.\d+(\.\d+)?$/', $m[1] ) ) {
			$value = $m[1];
		}
	}
	if ( ! $custom ) {
		if ( null === $tested ) {
			$tested = $value;
		}
		$value = $tested;
	}
	return $value;
}

function wgcr_update_tested_release( $tag ) {
	static $cache = array();
	if ( '' === $tag ) {
		return '';
	}
	if ( isset( $cache[ $tag ] ) ) {
		return $cache[ $tag ];
	}
	$args = array(
		'timeout' => 8,
		'headers' => array( 'Accept' => 'application/vnd.github.raw+json' ),
	);
	if ( defined( 'WGCR_GITHUB_TOKEN' ) && WGCR_GITHUB_TOKEN ) {
		$args['headers']['Authorization'] = 'Bearer ' . WGCR_GITHUB_TOKEN;
	}
	$url      = str_replace( '/releases/latest', '/contents/readme.txt', WGCR_UPDATE_API ) . '?ref=' . rawurlencode( $tag );
	$response = wp_remote_get( $url, $args );
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return '';
	}
	$raw   = (string) wp_remote_retrieve_body( $response );
	$value = '';
	if ( '' !== $raw && preg_match( '/^Tested up to:[ \t]*([0-9][0-9.]*)[ \t]*$/mi', substr( $raw, 0, 2048 ), $m ) && preg_match( '/^\d+\.\d+(\.\d+)?$/', $m[1] ) ) {
		$value = $m[1];
	}
	if ( '' !== $value ) {
		$cache[ $tag ] = $value;
	}
	return $value;
}

function wgcr_update_tested_for( $release ) {
	$tested = wgcr_update_tested_release( isset( $release['tag'] ) ? (string) $release['tag'] : '' );
	if ( '' === $tested ) {
		$tested = wgcr_update_tested_wp();
	}
	return $tested;
}

add_filter( 'update_plugins_github.com', 'wgcr_update_check', 10, 3 );
function wgcr_update_check( $update, $plugin_data, $plugin_file ) {
	if ( plugin_basename( WGCR_FILE ) !== $plugin_file ) {
		return $update;
	}
	$release = wgcr_update_release();
	if ( ! $release ) {
		return $update;
	}
	$response = array(
		'slug'         => 'widgetcore',
		'version'      => $release['version'],
		'url'          => $release['url'],
		'package'      => $release['package'],
		'requires'     => isset( $plugin_data['RequiresWP'] ) ? $plugin_data['RequiresWP'] : '',
		'requires_php' => isset( $plugin_data['RequiresPHP'] ) ? $plugin_data['RequiresPHP'] : '',
	);
	if ( version_compare( $release['version'], isset( $plugin_data['Version'] ) ? (string) $plugin_data['Version'] : '0', '>' ) ) {
		$tested = wgcr_update_tested_for( $release );
		if ( '' !== $tested ) {
			$response['tested'] = $tested;
		}
	}
	return $response;
}

add_filter( 'plugins_api', 'wgcr_update_info', 10, 3 );
function wgcr_update_info( $result, $action, $args ) {
	if ( 'plugin_information' !== $action || empty( $args->slug ) || 'widgetcore' !== $args->slug ) {
		return $result;
	}
	$release = wgcr_update_release();
	if ( ! $release ) {
		return $result;
	}
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugin = get_plugin_data( WGCR_FILE, false, false );
	$info = (object) array(
		'name'           => $plugin['Name'],
		'slug'           => 'widgetcore',
		'version'        => $release['version'],
		'author'         => $plugin['Author'],
		'author_profile' => $plugin['AuthorURI'],
		'homepage'       => $plugin['PluginURI'],
		'requires'       => $plugin['RequiresWP'],
		'requires_php'   => $plugin['RequiresPHP'],
		'last_updated'   => $release['published'],
		'download_link'  => $release['package'],
		'sections'       => array(
			'description' => wp_kses_post( wpautop( $plugin['Description'] ) ),
			'changelog'   => wp_kses_post( wgcr_update_notes_html( $release['notes'] ) ),
		),
	);
	$tested = wgcr_update_tested_for( $release );
	if ( '' !== $tested ) {
		$info->tested = $tested;
	}
	return $info;
}

add_filter( 'upgrader_source_selection', 'wgcr_update_source', 10, 4 );
function wgcr_update_source( $source, $remote_source, $upgrader, $hook_extra ) {
	global $wp_filesystem;
	if ( empty( $hook_extra['plugin'] ) || plugin_basename( WGCR_FILE ) !== $hook_extra['plugin'] || ! $wp_filesystem ) {
		return $source;
	}
	$target = trailingslashit( $remote_source ) . 'widgetcore/';
	if ( untrailingslashit( $source ) === untrailingslashit( $target ) ) {
		return $source;
	}
	return $wp_filesystem->move( untrailingslashit( $source ), untrailingslashit( $target ), true ) ? $target : $source;
}

add_filter( 'plugin_action_links_' . plugin_basename( WGCR_FILE ), 'wgcr_update_action_link' );
function wgcr_update_action_link( $links ) {
	if ( current_user_can( 'update_plugins' ) ) {
		$url     = wp_nonce_url( add_query_arg( 'wgcr-check-update', '1', self_admin_url( 'plugins.php' ) ), 'wgcr-check-update' );
		$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'بررسی به‌روزرسانی', 'widgetcore' ) . '</a>';
	}
	return $links;
}

add_action( 'load-plugins.php', 'wgcr_update_manual_check' );
function wgcr_update_manual_check() {
	if ( ! isset( $_GET['wgcr-check-update'] ) || ! current_user_can( 'update_plugins' ) ) {
		return;
	}
	check_admin_referer( 'wgcr-check-update' );
	wp_clean_plugins_cache( true );
	wp_update_plugins();
	$updates = get_site_transient( 'update_plugins' );
	$state   = isset( $updates->response[ plugin_basename( WGCR_FILE ) ] ) ? 'available' : 'latest';
	$url     = add_query_arg( array( 'wgcr-checked' => $state, '_wgcrnonce' => wp_create_nonce( 'wgcr-checked' ) ), self_admin_url( 'plugins.php' ) );
	if ( wp_safe_redirect( $url ) ) {
		exit;
	}
}

add_action( 'admin_notices', 'wgcr_update_notice' );
function wgcr_update_notice() {
	if ( ! isset( $_GET['wgcr-checked'], $_GET['_wgcrnonce'] ) || ! current_user_can( 'update_plugins' ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wgcrnonce'] ) ), 'wgcr-checked' ) ) {
		return;
	}
	$state = sanitize_key( wp_unslash( $_GET['wgcr-checked'] ) );
	if ( 'available' === $state ) {
		$message = __( 'نسخه‌ی جدیدی از WidgetCore موجود است؛ می‌توانید همین‌جا به‌روزرسانی کنید.', 'widgetcore' );
	} else {
		$message = __( 'WidgetCore به‌روز است؛ نسخه‌ی نصب‌شده آخرین نسخه‌ی منتشرشده است.', 'widgetcore' );
	}
	echo '<div class="notice notice-info is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
}
