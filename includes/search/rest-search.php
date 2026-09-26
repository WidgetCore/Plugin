<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'wgcr_search_register_route' );
if ( ! function_exists( 'wgcr_search_register_route' ) ) {
	function wgcr_search_register_route() {
		register_rest_route(
			WGCR_REST_NS,
			WGCR_REST_ROUTE,
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'wgcr_search_rest_callback',
				'permission_callback' => '__return_true',
				'args'                => array(
					'q'             => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => static function ( $v ) {
							return is_string( $v ) && mb_strlen( trim( $v ) ) >= 2;
						},
					),
					'type'          => array(
						'type'              => 'string',
						'default'           => 'post',
						'sanitize_callback' => 'sanitize_key',
					),
					'limit'         => array(
						'type'              => 'integer',
						'default'           => 5,
						'sanitize_callback' => 'absint',
					),
					'template'      => array(
						'type'              => 'integer',
						'default'           => 0,
						'sanitize_callback' => 'absint',
					),
					'page'          => array(
						'type'              => 'integer',
						'default'           => 0,
						'sanitize_callback' => 'absint',
					),
					'orderby'       => array(
						'type'              => 'string',
						'default'           => 'relevance',
						'sanitize_callback' => 'sanitize_key',
					),
					'order'         => array(
						'type'              => 'string',
						'default'           => 'DESC',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'date'          => array(
						'type'              => 'string',
						'default'           => 'all',
						'sanitize_callback' => 'sanitize_key',
					),
					'terms'         => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'terms_op'      => array(
						'type'              => 'string',
						'default'           => 'exclude',
						'sanitize_callback' => 'sanitize_key',
					),
					'ignore_sticky' => array(
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					),
					'query_id'      => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);
	}
}

if ( ! function_exists( 'wgcr_search_query_args' ) ) {
	function wgcr_search_query_args( WP_REST_Request $request ) {
		$q     = trim( (string) $request->get_param( 'q' ) );
		$type  = wgcr_search_sanitize_source( $request->get_param( 'type' ) );
		$limit = max( 1, min( WGCR_SEARCH_MAX_RESULTS, absint( $request->get_param( 'limit' ) ) ) );
		$page  = absint( $request->get_param( 'page' ) );
		$args  = array(
			's'                   => $q,
			'post_type'           => $type,
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'paged'               => max( 1, $page ),
			'no_found_rows'       => $page < 1,
			'ignore_sticky_posts' => true,
			'has_password'        => false,
		);

		$orderby = sanitize_key( (string) $request->get_param( 'orderby' ) );
		if ( in_array( $orderby, wgcr_search_orderby_options(), true ) && 'relevance' !== $orderby ) {
			$args['orderby'] = 'menu_order' === $orderby ? 'menu_order title' : $orderby;
			$args['order']   = 'asc' === strtolower( (string) $request->get_param( 'order' ) ) ? 'ASC' : 'DESC';
		}

		$after = array(
			'week'  => '1 week ago',
			'month' => '1 month ago',
			'year'  => '1 year ago',
		);
		$date  = sanitize_key( (string) $request->get_param( 'date' ) );
		if ( isset( $after[ $date ] ) ) {
			$args['date_query'] = array(
				array(
					'after'     => $after[ $date ],
					'inclusive' => true,
				),
			);
		}

		$terms = wgcr_search_sanitize_terms( $request->get_param( 'terms' ) );
		if ( $terms ) {
			$include = 'include' === sanitize_key( (string) $request->get_param( 'terms_op' ) );
			$by_tax  = array();
			foreach ( $terms as $pair ) {
				$by_tax[ $pair[0] ][] = $pair[1];
			}
			$tax_query = array( 'relation' => $include ? 'OR' : 'AND' );
			foreach ( $by_tax as $tax => $ids ) {
				$tax_query[] = array(
					'taxonomy' => $tax,
					'field'    => 'term_id',
					'terms'    => $ids,
					'operator' => $include ? 'IN' : 'NOT IN',
				);
			}
			$args['tax_query'] = $tax_query;
		}

		$query_id = sanitize_key( (string) $request->get_param( 'query_id' ) );
		$args     = apply_filters( 'wgcr_search_query_args', $args, $query_id, $request );
		if ( '' !== $query_id ) {
			$args = apply_filters( 'wgcr_search_query_args/' . $query_id, $args, $request );
		}
		return $args;
	}
}

if ( ! function_exists( 'wgcr_search_rest_callback' ) ) {
	function wgcr_search_rest_callback( WP_REST_Request $request ) {
		$q = trim( (string) $request->get_param( 'q' ) );
		if ( mb_strlen( $q ) < 2 ) {
			return new WP_Error( 'wgcr_search_short', __( 'حداقل ۲ کاراکتر وارد کنید.', 'widgetcore' ), array( 'status' => 400 ) );
		}

		$query = new WP_Query( wgcr_search_query_args( $request ) );

		if ( 1 !== absint( $request->get_param( 'ignore_sticky' ) ) ) {
			$sticky = array_map( 'absint', (array) get_option( 'sticky_posts' ) );
			$first  = array();
			$rest   = array();
			foreach ( $query->posts as $post ) {
				if ( in_array( (int) $post->ID, $sticky, true ) ) {
					$first[] = $post;
				} else {
					$rest[] = $post;
				}
			}
			$query->posts = array_merge( $first, $rest );
		}

		$template = absint( $request->get_param( 'template' ) );
		$items    = array();
		foreach ( $query->posts as $post ) {
			$thumb   = get_the_post_thumbnail_url( $post, 'thumbnail' );
			$excerpt = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;
			$excerpt = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $excerpt ) ), 18, '…' );
			$items[] = array(
				'id'      => (int) $post->ID,
				'title'   => html_entity_decode( wp_strip_all_tags( get_the_title( $post ) ), ENT_QUOTES, 'UTF-8' ),
				'link'    => esc_url_raw( get_permalink( $post ) ),
				'thumb'   => $thumb ? esc_url_raw( $thumb ) : '',
				'excerpt' => html_entity_decode( $excerpt, ENT_QUOTES, 'UTF-8' ),
				'date'    => get_the_date( '', $post ),
			);
		}

		if ( $template > 0 && class_exists( '\Elementor\Plugin', false ) && ! empty( \Elementor\Plugin::$instance->frontend ) ) {
			$tpl_post = get_post( $template );
			if ( $tpl_post && 'elementor_library' === $tpl_post->post_type && 'publish' === $tpl_post->post_status ) {
				$count = count( $items );
				$query->rewind_posts();
				for ( $i = 0; $i < $count; $i++ ) {
					if ( ! $query->have_posts() ) {
						break;
					}
					$query->the_post();
					try {
						$rendered = (string) \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template, true );
					} catch ( \Throwable $e ) {
						$rendered = '';
					}
					$visible = trim( wp_strip_all_tags( preg_replace( '#<(script|style)\b[^>]*>.*?</\1>#is', '', $rendered ) ) );
					$media   = preg_match( '#<(img|svg|picture|video|iframe|canvas)\b#i', $rendered );
					if ( '' !== $visible || $media ) {
						$items[ $i ]['html'] = $rendered;
					}
				}
				wp_reset_postdata();
			}
		}

		$response = rest_ensure_response( $items );
		if ( absint( $request->get_param( 'page' ) ) > 0 ) {
			$response->header( 'X-WP-Total', (string) (int) $query->found_posts );
			$response->header( 'X-WP-TotalPages', (string) (int) $query->max_num_pages );
		}
		return $response;
	}
}
