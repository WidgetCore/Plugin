<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_breadcrumb_base_css' ) ) {
	function wgcr_breadcrumb_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-breadcrumb{--wgcr-bc-accent:#2E86DE;--wgcr-bc-ink:#1F2937;--wgcr-bc-muted:#6B7280;font-size:14px;line-height:2;color:var(--wgcr-bc-text,var(--wgcr-bc-muted))}',
				'.wgcr-breadcrumb-list{display:flex;flex-wrap:wrap;align-items:center;justify-content:var(--wgcr-bc-align,flex-start);gap:var(--wgcr-bc-gap,6px);list-style:none;margin:0;padding:0}',
				'.wgcr-breadcrumb-item{display:flex;align-items:center;gap:var(--wgcr-bc-gap,6px);min-width:0}',
				'.wgcr-breadcrumb a{color:var(--wgcr-bc-link,var(--wgcr-bc-accent));text-decoration:none}',
				'.wgcr-breadcrumb a:hover{color:var(--wgcr-bc-link-hover,#1B5E9E);text-decoration:underline}',
				'.wgcr-breadcrumb a:focus-visible{outline:2px solid var(--wgcr-bc-accent);outline-offset:2px}',
				'.wgcr-breadcrumb-sep{flex:none;color:var(--wgcr-bc-sep,var(--wgcr-bc-muted))}',
				'.wgcr-breadcrumb-item[aria-current="page"]>span{color:var(--wgcr-bc-current,var(--wgcr-bc-ink));font-weight:600;overflow-wrap:anywhere}',
				'@media (max-width:767px){.wgcr-breadcrumb-list{row-gap:2px}}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_breadcrumb_register_assets' ) ) {
	function wgcr_breadcrumb_register_assets() {
		if ( wp_style_is( 'wgcr-breadcrumb', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-breadcrumb', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-breadcrumb', wgcr_breadcrumb_base_css() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_breadcrumb_register_assets', 5 );

if ( ! class_exists( 'WGCR_Breadcrumb_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Breadcrumb_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-breadcrumb'; }
		public function get_title() { return esc_html__( 'مسیر راهنما - Breadcrumb', 'widgetcore' ); }
		public function get_icon() { return 'eicon-post-navigation'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-breadcrumb' ); }
		public function get_keywords() { return array( 'breadcrumb', 'breadcrumbs', 'navigation', 'schema', __( 'مسیر راهنما', 'widgetcore' ), __( 'ناوبری', 'widgetcore' ) ); }

		protected function resolve_provider( $s ) {
			$pref = ( isset( $s['provider'] ) && in_array( $s['provider'], array( 'auto', 'yoast', 'rankmath', 'internal' ), true ) ) ? $s['provider'] : 'auto';
			if ( 'internal' === $pref ) {
				return 'internal';
			}
			$has_yoast    = function_exists( 'yoast_breadcrumb' );
			$has_rankmath = defined( 'RANK_MATH_VERSION' );
			if ( 'yoast' === $pref ) {
				return $has_yoast ? 'yoast' : 'internal';
			}
			if ( 'rankmath' === $pref ) {
				return $has_rankmath ? 'rankmath' : 'internal';
			}
			if ( $has_yoast ) {
				return 'yoast';
			}
			if ( $has_rankmath ) {
				return 'rankmath';
			}
			return 'internal';
		}

		protected function get_separator( $s ) {
			$sep  = ( isset( $s['separator'] ) ) ? (string) $s['separator'] : 'angle';
			$map  = array(
				'angle'   => '‹',
				'chevron' => '›',
				'slash'   => '/',
				'raquo'   => '»',
				'laquo'   => '«',
				'pipe'    => '|',
				'dash'    => '–',
				'bullet'  => '•',
			);
			if ( 'custom' === $sep ) {
				$custom = isset( $s['separator_custom'] ) ? trim( (string) $s['separator_custom'] ) : '';
				return ( '' === $custom ) ? '‹' : $custom;
			}
			return isset( $map[ $sep ] ) ? $map[ $sep ] : '‹';
		}

		protected function build_trail( $s ) {
			$show_home  = ! ( isset( $s['show_home'] ) && 'yes' !== $s['show_home'] );
			$home_label = ( isset( $s['home_label'] ) ) ? trim( (string) $s['home_label'] ) : '';
			if ( '' === $home_label ) {
				$home_label = __( 'خانه', 'widgetcore' );
			}
			$show_current = ! ( isset( $s['show_current'] ) && 'yes' !== $s['show_current'] );
			$with_cat     = ! ( isset( $s['include_category'] ) && 'yes' !== $s['include_category'] );

			$middles       = array();
			$current_label = '';

			if ( is_front_page() ) {
				$current_label = $home_label;
			} elseif ( is_singular() ) {
				if ( is_page() ) {
					$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
					foreach ( $ancestors as $anc ) {
						$middles[] = array(
							'label' => get_the_title( $anc ),
							'url'   => (string) get_permalink( $anc ),
						);
					}
				}
				if ( is_single() && $with_cat ) {
					$cats = get_the_category();
					if ( ! empty( $cats ) ) {
						$middles[] = array(
							'label' => $cats[0]->name,
							'url'   => (string) get_category_link( $cats[0] ),
						);
					}
				}
				$current_label = (string) get_the_title();
			} elseif ( is_home() ) {
				$posts_page    = (int) get_option( 'page_for_posts' );
				$current_label = $posts_page ? (string) get_the_title( $posts_page ) : __( 'وبلاگ', 'widgetcore' );
			} elseif ( is_category() || is_tag() || is_tax() ) {
				$term          = get_queried_object();
				$current_label = ( $term && isset( $term->name ) ) ? (string) $term->name : '';
			} elseif ( is_author() ) {
				$current_label = (string) get_the_author_meta( 'display_name', get_queried_object_id() );
			} elseif ( is_search() ) {
				/* translators: %s: search phrase. */
				$current_label = sprintf( __( 'جستجو: %s', 'widgetcore' ), get_search_query() );
			} elseif ( is_404() ) {
				$current_label = __( 'یافت نشد', 'widgetcore' );
			} elseif ( is_post_type_archive() ) {
				$current_label = (string) post_type_archive_title( '', false );
			} elseif ( is_archive() ) {
				$current_label = (string) wp_strip_all_tags( get_the_archive_title() );
			}

			$trail = array();
			if ( $show_home ) {
				$trail[] = array(
					'label' => $home_label,
					'url'   => (string) home_url( '/' ),
				);
			}
			foreach ( $middles as $m ) {
				$trail[] = $m;
			}
			if ( $show_current && '' !== $current_label && ! ( is_front_page() && $show_home ) ) {
				$trail[] = array(
					'label' => $current_label,
					'url'   => '',
				);
			}
			return $trail;
		}

		protected function register_controls() {

			$this->wgcr_content_section( 'sec_source', esc_html__( 'منبع مسیر', 'widgetcore' ) );
			$this->add_control( 'provider', array(
				'label'       => esc_html__( 'تولیدکننده مسیر', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'auto'     => esc_html__( 'خودکار (ترجیح با افزونه سئو)', 'widgetcore' ),
					'yoast'    => esc_html__( 'یواست سئو (Yoast)', 'widgetcore' ),
					'rankmath' => esc_html__( 'رنک مث (Rank Math)', 'widgetcore' ),
					'internal' => esc_html__( 'داخلی (ساخته‌شده توسط ویجت)', 'widgetcore' ),
				),
				'default'     => 'auto',
				'label_block' => true,
				'description' => esc_html__( 'در حالت «خودکار» اگر Yoast یا Rank Math فعال باشد مسیر راهنمای همان افزونه چاپ می‌شود؛ در غیر این صورت ویجت مسیر را از ساختار وردپرس می‌سازد.', 'widgetcore' ),
			) );
			$this->wgcr_switch_control( 'schema', esc_html__( 'اسکیمای BreadcrumbList (JSON-LD)', 'widgetcore' ), 'yes' );
			$this->add_control( 'schema_note', array(
				'type'      => \Elementor\Controls_Manager::RAW_HTML,
				'raw'       => esc_html__( 'اسکیمای ساختارمند فقط در حالت داخلی توسط این ویجت چاپ می‌شود؛ افزونه‌های سئو اسکیمای خود را دارند.', 'widgetcore' ),
				'condition' => array( 'schema' => 'yes' ),
			) );
			$this->end_controls_section();

			$this->wgcr_content_section( 'sec_labels', esc_html__( 'برچسب‌ها و جداکننده', 'widgetcore' ) );
			$this->wgcr_text_control( 'home_label', esc_html__( 'برچسب خانه', 'widgetcore' ), __( 'خانه', 'widgetcore' ) );
			$this->wgcr_switch_control( 'show_home', esc_html__( 'نمایش پیوند خانه', 'widgetcore' ), 'yes' );
			$this->wgcr_switch_control( 'show_current', esc_html__( 'نمایش بخش جاری (آخرین بخش بدون پیوند)', 'widgetcore' ), 'yes' );
			$this->wgcr_switch_control( 'include_category', esc_html__( 'نمایش دسته اصلی در نوشته‌ها', 'widgetcore' ), 'yes' );
			$this->add_control( 'separator', array(
				'label'   => esc_html__( 'جداکننده', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'angle'   => esc_html__( 'فلش چپ ‹', 'widgetcore' ),
					'chevron' => esc_html__( 'فلش راست ›', 'widgetcore' ),
					'slash'   => esc_html__( 'اسلش /', 'widgetcore' ),
					'raquo'   => esc_html__( 'گیومه »', 'widgetcore' ),
					'laquo'   => esc_html__( 'گیومه «', 'widgetcore' ),
					'pipe'    => esc_html__( 'خط عمودی |', 'widgetcore' ),
					'dash'    => esc_html__( 'خط تیره –', 'widgetcore' ),
					'bullet'  => esc_html__( 'نقطه •', 'widgetcore' ),
					'custom'  => esc_html__( 'سفارشی', 'widgetcore' ),
				),
				'default' => 'angle',
			) );
			$this->wgcr_text_control( 'separator_custom', esc_html__( 'جداکننده سفارشی', 'widgetcore' ), '', array( 'separator' => 'custom' ) );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_box', esc_html__( 'جعبه', 'widgetcore' ) );
			$this->wgcr_style_box( 'box', '{{WRAPPER}} .wgcr-breadcrumb' );
			$this->wgcr_typography_control( 'bc_typo', '{{WRAPPER}} .wgcr-breadcrumb' );
			$this->wgcr_color_control( 'text_color', esc_html__( 'رنگ متن', 'widgetcore' ), '{{WRAPPER}} .wgcr-breadcrumb', '--wgcr-bc-text' );
			$this->wgcr_color_control( 'current_color', esc_html__( 'رنگ بخش جاری', 'widgetcore' ), '{{WRAPPER}} .wgcr-breadcrumb', '--wgcr-bc-current' );
			$this->add_responsive_control( 'align', array(
				'label'     => esc_html__( 'چیدمان', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'flex-start' => esc_html__( 'ابتدا', 'widgetcore' ),
					'center'     => esc_html__( 'وسط', 'widgetcore' ),
					'flex-end'   => esc_html__( 'انتها', 'widgetcore' ),
				),
				'selectors' => array( '{{WRAPPER}} .wgcr-breadcrumb' => '--wgcr-bc-align:{{VALUE}};' ),
			) );
			$this->wgcr_slider_control( 'items_gap', esc_html__( 'فاصله بین بخش‌ها', 'widgetcore' ), '{{WRAPPER}} .wgcr-breadcrumb', '--wgcr-bc-gap:{{SIZE}}{{UNIT}};', 0, 40 );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_links', esc_html__( 'پیوندها و جداکننده', 'widgetcore' ) );
			$this->start_controls_tabs( 'bc_link_tabs' );
			$this->start_controls_tab( 'bc_link_tab_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'link_color', esc_html__( 'رنگ پیوند', 'widgetcore' ), '{{WRAPPER}} .wgcr-breadcrumb', '--wgcr-bc-link' );
			$this->wgcr_color_control( 'sep_color', esc_html__( 'رنگ جداکننده', 'widgetcore' ), '{{WRAPPER}} .wgcr-breadcrumb', '--wgcr-bc-sep' );
			$this->end_controls_tab();
			$this->start_controls_tab( 'bc_link_tab_hover', array( 'label' => esc_html__( 'هاور', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'link_color_hover', esc_html__( 'رنگ پیوند در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-breadcrumb', '--wgcr-bc-link-hover' );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->end_controls_section();

		}

		protected function render() {
			$s        = $this->get_settings_for_display();
			$provider = $this->resolve_provider( $s );

			if ( 'yoast' === $provider || 'rankmath' === $provider ) {
				echo '<div class="wgcr-breadcrumb wgcr-breadcrumb--bridge">';
				echo do_shortcode( 'yoast' === $provider ? '[wpseo_breadcrumb]' : '[rank_math_breadcrumb]' );
				echo '</div>';
				return;
			}

			$trail = $this->build_trail( $s );
			if ( empty( $trail ) ) {
				return;
			}
			wgcr_breadcrumb_register_assets();
			wp_enqueue_style( 'wgcr-breadcrumb' );
			$sep   = $this->get_separator( $s );
			$last  = count( $trail ) - 1;

			echo '<nav class="wgcr-breadcrumb" aria-label="' . esc_attr__( 'مسیر راهنما', 'widgetcore' ) . '">';
			echo '<ol class="wgcr-breadcrumb-list">';
			foreach ( $trail as $pos => $crumb ) {
				$label = (string) $crumb['label'];
				$url   = isset( $crumb['url'] ) ? (string) $crumb['url'] : '';
				if ( $pos === $last ) {
					echo '<li class="wgcr-breadcrumb-item" aria-current="page"><span>' . esc_html( $label ) . '</span></li>';
					continue;
				}
				echo '<li class="wgcr-breadcrumb-item">';
				if ( '' !== $url ) {
					echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
				} else {
					echo '<span>' . esc_html( $label ) . '</span>';
				}
				echo '<span class="wgcr-breadcrumb-sep" aria-hidden="true">' . esc_html( $sep ) . '</span>';
				echo '</li>';
			}
			echo '</ol>';
			echo '</nav>';

			if ( isset( $s['schema'] ) && 'yes' === $s['schema'] ) {
				$entities = array();
				foreach ( $trail as $pos => $crumb ) {
					$entity = array(
						'@type'    => 'ListItem',
						'position' => $pos + 1,
						'name'     => (string) $crumb['label'],
					);
					$url    = isset( $crumb['url'] ) ? (string) $crumb['url'] : '';
					if ( $pos !== $last && '' !== $url ) {
						$entity['item'] = $url;
					}
					$entities[] = $entity;
				}
				if ( ! empty( $entities ) ) {
					wp_print_inline_script_tag(
						wp_json_encode(
							array(
								'@context'        => 'https://schema.org',
								'@type'           => 'BreadcrumbList',
								'itemListElement' => $entities,
							),
							JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
						),
						array( 'type' => 'application/ld+json' )
					);
				}
			}
		}
	}
}
