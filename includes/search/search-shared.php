<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_search_js_config' ) ) {
	function wgcr_search_js_config() {
		return array(
			'restUrl'    => esc_url_raw( rest_url( WGCR_REST_NS . WGCR_REST_ROUTE ) ),
			'homeUrl'    => esc_url_raw( home_url( '/' ) ),
			'minChars'   => 2,
			'maxResults' => WGCR_SEARCH_MAX_RESULTS,
			'i18n'       => array(
				/* translators: %s: search phrase. */
				'noResults' => __( 'نتیجه‌ای برای «%s» پیدا نشد.', 'widgetcore' ),
				/* translators: %d: number of results. */
				'found'     => __( 'تعداد نتایج: %d', 'widgetcore' ),
				'error'     => __( 'خطا در دریافت نتایج. دوباره تلاش کنید.', 'widgetcore' ),
				'prev'      => __( 'قبلی', 'widgetcore' ),
				'next'      => __( 'بعدی', 'widgetcore' ),
				/* translators: %d: page number. */
				'page'      => __( 'صفحه %d', 'widgetcore' ),
				/* translators: 1: current page number, 2: total number of pages. */
				'pageOf'    => __( 'صفحه %1$d از %2$d', 'widgetcore' ),
			),
		);
	}
}

if ( ! function_exists( 'wgcr_search_sanitize_source' ) ) {
	function wgcr_search_sanitize_source( $source ) {
		$source = sanitize_key( (string) $source );
		if ( '' === $source ) {
			return 'post';
		}
		$public = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'names' );
		unset( $public['attachment'], $public['elementor_library'] );
		return isset( $public[ $source ] ) ? $source : 'post';
	}
}

if ( ! function_exists( 'wgcr_search_orderby_options' ) ) {
	function wgcr_search_orderby_options() {
		return array( 'relevance', 'date', 'title', 'author', 'rand', 'menu_order' );
	}
}

if ( ! function_exists( 'wgcr_search_date_options' ) ) {
	function wgcr_search_date_options() {
		return array( 'all', 'week', 'month', 'year' );
	}
}

if ( ! function_exists( 'wgcr_search_pagination_options' ) ) {
	function wgcr_search_pagination_options() {
		return array( 'none', 'numbers', 'prev_next', 'load_on_click', 'load_on_scroll' );
	}
}

if ( ! function_exists( 'wgcr_search_sanitize_terms' ) ) {
	function wgcr_search_sanitize_terms( $terms ) {
		if ( ! is_array( $terms ) ) {
			$terms = explode( ',', (string) $terms );
		}
		$public = get_taxonomies( array( 'public' => true ), 'names' );
		$out    = array();
		foreach ( $terms as $term ) {
			$term = trim( (string) $term );
			if ( preg_match( '/^(?:([a-z0-9_-]+):)?([0-9]+)$/', $term, $m ) ) {
				$tax = '' !== $m[1] ? $m[1] : 'category';
				$id  = absint( $m[2] );
				if ( $id > 0 && isset( $public[ $tax ] ) ) {
					$out[ $tax . ':' . $id ] = array( $tax, $id );
				}
			}
		}
		return array_values( $out );
	}
}

if ( ! function_exists( 'wgcr_search_normalize_args' ) ) {
	function wgcr_search_normalize_args( $args ) {
		$d = array(
			'source'        => 'post',
			'placeholder'   => '',
			'limit'         => 5,
			'columns'       => 1,
			'masonry'       => 'no',
			'equal_height'  => 'no',
			'thumb'         => 'yes',
			'excerpt'       => 'yes',
			'all'           => '',
			'template'      => 0,
			'mode'          => 'modal',
			'orderby'       => 'relevance',
			'order'         => 'DESC',
			'date'          => 'all',
			'terms'         => '',
			'terms_op'      => 'exclude',
			'ignore_sticky' => 'yes',
			'query_id'      => '',
			'pagination'    => 'none',
			'spacer'        => 'no',
			'more_text'     => '',
			'more_icon'     => 'yes',
			'more_id'       => '',
			'no_more'       => '',
		);
		$args = wp_parse_args( is_array( $args ) ? $args : array(), $d );

		$bool = static function ( $v ) {
			$v = strtolower( trim( (string) $v ) );
			return in_array( $v, array( 'yes', '1', 'true', 'on' ), true ) ? 'yes' : 'no';
		};
		$pick = static function ( $v, $allowed, $fallback ) {
			$v = strtolower( trim( (string) $v ) );
			return in_array( $v, $allowed, true ) ? $v : $fallback;
		};

		$source      = wgcr_search_sanitize_source( $args['source'] );
		$placeholder = sanitize_text_field( (string) $args['placeholder'] );
		if ( '' === $placeholder ) {
			$placeholder = ( 'course' === $source ) ? __( 'جستجو در دوره‌ها…', 'widgetcore' ) : __( 'جستجو در مقالات…', 'widgetcore' );
		}
		$all       = sanitize_text_field( (string) $args['all'] );
		$mode      = $pick( $args['mode'], array( 'modal', 'page' ), 'modal' );
		$limit     = absint( $args['limit'] );
		$masonry   = 'page' === $mode ? $bool( $args['masonry'] ) : 'no';
		$equal     = ( 'page' === $mode && 'yes' !== $masonry ) ? $bool( $args['equal_height'] ) : 'no';
		$orderby   = $pick( $args['orderby'], wgcr_search_orderby_options(), 'relevance' );
		$more_text = sanitize_text_field( (string) $args['more_text'] );
		$terms     = array();
		foreach ( wgcr_search_sanitize_terms( $args['terms'] ) as $pair ) {
			$terms[] = $pair[0] . ':' . $pair[1];
		}
		return array(
			'source'        => $source,
			'placeholder'   => $placeholder,
			'limit'         => $limit > 0 ? min( WGCR_SEARCH_MAX_RESULTS, $limit ) : $d['limit'],
			'columns'       => max( 1, min( 6, absint( $args['columns'] ) ) ),
			'masonry'       => $masonry,
			'equal_height'  => $equal,
			'thumb'         => $bool( $args['thumb'] ),
			'excerpt'       => $bool( $args['excerpt'] ),
			'all'           => '' !== $all ? $all : __( 'نمایش همه نتایج', 'widgetcore' ),
			'template'      => absint( $args['template'] ),
			'mode'          => $mode,
			'orderby'       => $orderby,
			'order'         => 'asc' === strtolower( trim( (string) $args['order'] ) ) ? 'ASC' : 'DESC',
			'date'          => $pick( $args['date'], wgcr_search_date_options(), 'all' ),
			'terms'         => implode( ',', $terms ),
			'terms_op'      => $pick( $args['terms_op'], array( 'include', 'exclude' ), 'exclude' ),
			'ignore_sticky' => $bool( $args['ignore_sticky'] ),
			'query_id'      => sanitize_key( (string) $args['query_id'] ),
			'pagination'    => $pick( $args['pagination'], wgcr_search_pagination_options(), 'none' ),
			'spacer'        => $bool( $args['spacer'] ),
			'more_text'     => '' !== $more_text ? $more_text : __( 'مشاهده مقالات بیشتر', 'widgetcore' ),
			'more_icon'     => $bool( $args['more_icon'] ),
			'more_id'       => preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $args['more_id'] ),
			'no_more'       => sanitize_text_field( (string) $args['no_more'] ),
		);
	}
}

if ( ! function_exists( 'wgcr_search_allowed_html' ) ) {
	function wgcr_search_allowed_html() {
		$aria = array(
			'role'                  => true,
			'aria-label'            => true,
			'aria-hidden'           => true,
			'aria-live'             => true,
			'aria-expanded'         => true,
			'aria-controls'         => true,
			'aria-haspopup'         => true,
			'aria-autocomplete'     => true,
			'aria-activedescendant' => true,
		);
		return array_merge(
			wgcr_svg_kses_base(),
			array(
				'div'    => array_merge(
					$aria,
					array(
						'class'             => true,
						'hidden'            => true,
						'data-wgcr-search'  => true,
						'data-source'       => true,
						'data-limit'        => true,
						'data-thumb'        => true,
						'data-excerpt'      => true,
						'data-all'          => true,
						'data-template'     => true,
						'data-display-mode' => true,
						'data-query'        => true,
						'data-pagination'   => true,
					)
				),
				'form'   => array_merge(
					$aria,
					array(
						'class'  => true,
						'method' => true,
						'action' => true,
					)
				),
				'label'  => array(
					'class' => true,
					'for'   => true,
				),
				'span'   => array_merge( $aria, array( 'class' => true ) ),
				'input'  => array_merge(
					$aria,
					array(
						'type'         => true,
						'class'        => true,
						'id'           => true,
						'name'         => true,
						'value'        => true,
						'placeholder'  => true,
						'autocomplete' => true,
					)
				),
				'ul'     => array_merge(
					$aria,
					array(
						'class' => true,
						'id'    => true,
					)
				),
				'nav'    => array_merge(
					$aria,
					array(
						'class'  => true,
						'hidden' => true,
					)
				),
				'button' => array_merge(
					$aria,
					array(
						'type'   => true,
						'class'  => true,
						'id'     => true,
						'hidden' => true,
					)
				),
				'a'      => array(
					'class'  => true,
					'href'   => true,
					'hidden' => true,
				),
			)
		);
	}
}

if ( ! function_exists( 'wgcr_search_root_class' ) ) {
	function wgcr_search_root_class( $a ) {
		$cls = 'wgcr-search wgcr-search--' . $a['mode'];
		if ( 'page' === $a['mode'] ) {
			$cls .= ' wgcr-search--cols-' . $a['columns'];
			if ( 'yes' === $a['masonry'] ) {
				$cls .= ' wgcr-search--masonry';
			}
			if ( 'yes' === $a['equal_height'] ) {
				$cls .= ' wgcr-search--equal';
			}
		}
		if ( $a['template'] > 0 ) {
			$cls .= ' wgcr-search--tpl';
		}
		if ( 'none' !== $a['pagination'] && 'yes' === $a['spacer'] ) {
			$cls .= ' wgcr-search--spacer';
		}
		return $cls;
	}
}

if ( ! function_exists( 'wgcr_search_query_data' ) ) {
	function wgcr_search_query_data( $a ) {
		return array(
			'orderby'       => $a['orderby'],
			'order'         => $a['order'],
			'date'          => $a['date'],
			'terms'         => $a['terms'],
			'terms_op'      => $a['terms_op'],
			'ignore_sticky' => 'yes' === $a['ignore_sticky'] ? 1 : 0,
			'query_id'      => $a['query_id'],
		);
	}
}

if ( ! function_exists( 'wgcr_search_render' ) ) {
	function wgcr_search_render( $args, $uid = '' ) {
		static $counter = 0;
		$counter++;
		$a   = wgcr_search_normalize_args( $args );
		$uid = sanitize_html_class( '' !== (string) $uid ? (string) $uid : 'sc' . $counter );
		$uid = 'wgcr-search-' . $uid;

		if ( function_exists( 'wgcr_register_assets' ) ) {
			wgcr_register_assets();
		}
		wp_enqueue_style( 'wgcr-search' );
		wp_enqueue_script( 'wgcr-search' );

		$action              = home_url( '/' );
		$mode                = $a['mode'];
		$input_aria_expanded = ( 'page' === $mode ) ? 'true' : 'false';
		$no_more             = '' !== $a['no_more'] ? $a['no_more'] : __( 'نوشته‌ی بیشتری موجود نیست.', 'widgetcore' );
		ob_start();
		?>
<div class="<?php echo esc_attr( wgcr_search_root_class( $a ) ); ?>" data-wgcr-search data-display-mode="<?php echo esc_attr( $mode ); ?>" data-source="<?php echo esc_attr( $a['source'] ); ?>" data-limit="<?php echo esc_attr( $a['limit'] ); ?>" data-thumb="<?php echo esc_attr( $a['thumb'] ); ?>" data-excerpt="<?php echo esc_attr( $a['excerpt'] ); ?>" data-all="<?php echo esc_attr( $a['all'] ); ?>" data-template="<?php echo esc_attr( $a['template'] > 0 ? $a['template'] : '' ); ?>" data-query="<?php echo esc_attr( wp_json_encode( wgcr_search_query_data( $a ) ) ); ?>" data-pagination="<?php echo esc_attr( $a['pagination'] ); ?>">
	<form class="wgcr-search-form" role="search" method="get" action="<?php echo esc_url( $action ); ?>">
		<label class="wgcr-search-label" for="<?php echo esc_attr( $uid ); ?>-input"><?php echo esc_html( $a['placeholder'] ); ?></label>
		<div class="wgcr-search-field">
			<span class="wgcr-search-icon"><?php echo wp_kses( wgcr_svg_icon( 'search' ), wgcr_svg_kses_base() ); ?></span>
			<input type="search" class="wgcr-search-input" id="<?php echo esc_attr( $uid ); ?>-input" name="s" placeholder="<?php echo esc_attr( $a['placeholder'] ); ?>" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="<?php echo esc_attr( $input_aria_expanded ); ?>" aria-controls="<?php echo esc_attr( $uid ); ?>-list" aria-haspopup="listbox">
			<input type="hidden" name="post_type" value="<?php echo esc_attr( $a['source'] ); ?>">
			<span class="wgcr-search-spinner" aria-hidden="true"></span>
		</div>
		<div class="wgcr-search-panel"<?php echo 'page' === $mode ? '' : ' hidden'; ?>>
			<ul class="wgcr-search-list" id="<?php echo esc_attr( $uid ); ?>-list" role="listbox" aria-label="<?php esc_attr_e( 'نتایج جستجو', 'widgetcore' ); ?>"></ul>
			<div class="wgcr-search-status" aria-live="polite"></div>
			<?php if ( 'none' !== $a['pagination'] ) : ?>
			<div class="wgcr-search-foot">
				<nav class="wgcr-search-pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی نتایج', 'widgetcore' ); ?>" hidden></nav>
				<button type="button" class="wgcr-search-more"<?php echo '' !== $a['more_id'] ? ' id="' . esc_attr( $a['more_id'] ) . '"' : ''; ?> aria-controls="<?php echo esc_attr( $uid ); ?>-list" hidden><?php if ( 'yes' === $a['more_icon'] ) : ?><span class="wgcr-search-more-icon" aria-hidden="true"><?php echo wp_kses( wgcr_svg_icon( 'chevron' ), wgcr_svg_kses_base() ); ?></span><?php endif; ?><span class="wgcr-search-more-text"><?php echo esc_html( $a['more_text'] ); ?></span></button>
				<div class="wgcr-search-nomore" role="status" hidden><?php echo esc_html( $no_more ); ?></div>
			</div>
			<?php endif; ?>
			<a class="wgcr-search-all" href="#" hidden><?php echo esc_html( $a['all'] ); ?></a>
		</div>
	</form>
</div>
		<?php
		return (string) ob_get_clean();
	}
}

add_shortcode( 'wgcr_search', 'wgcr_search_shortcode' );
if ( ! function_exists( 'wgcr_search_shortcode' ) ) {
	function wgcr_search_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'source'        => 'post',
				'placeholder'   => '',
				'limit'         => 5,
				'columns'       => 1,
				'masonry'       => 'no',
				'equal_height'  => 'no',
				'thumb'         => 'yes',
				'excerpt'       => 'yes',
				'all'           => '',
				'template'      => 0,
				'mode'          => 'modal',
				'orderby'       => 'relevance',
				'order'         => 'DESC',
				'date'          => 'all',
				'terms'         => '',
				'terms_op'      => 'exclude',
				'ignore_sticky' => 'yes',
				'query_id'      => '',
				'pagination'    => 'none',
				'spacer'        => 'no',
				'more_text'     => '',
				'more_icon'     => 'yes',
				'more_id'       => '',
				'no_more'       => '',
			),
			$atts,
			'wgcr_search'
		);
		return wgcr_search_render( $atts );
	}
}
