<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_share_endpoints' ) ) {
	function wgcr_share_endpoints() {
		return array(
			'x'        => 'https://twitter.com/intent/tweet?url={url}&text={title}',
			'facebook' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
			'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
			'telegram' => 'https://t.me/share/url?url={url}&text={title}',
			'whatsapp' => 'https://api.whatsapp.com/send?text={title}%20{url}',
			'eitaa'    => 'https://eitaa.com/share/url?url={url}&text={title}',
			'email'    => 'mailto:?subject={title}&body={url}',
		);
	}
}

if ( ! function_exists( 'wgcr_share_network_labels' ) ) {
	function wgcr_share_network_labels() {
		return array(
			'x'        => __( 'ایکس', 'widgetcore' ),
			'facebook' => __( 'فیس‌بوک', 'widgetcore' ),
			'linkedin' => __( 'لینکدین', 'widgetcore' ),
			'telegram' => __( 'تلگرام', 'widgetcore' ),
			'whatsapp' => __( 'واتس‌اپ', 'widgetcore' ),
			'eitaa'    => __( 'ایتا', 'widgetcore' ),
			'email'    => __( 'ایمیل', 'widgetcore' ),
			'copy'     => __( 'کپی لینک', 'widgetcore' ),
		);
	}
}

if ( ! function_exists( 'wgcr_share_network_icons' ) ) {
	function wgcr_share_network_icons() {
		return array(
			'x'        => 'x',
			'facebook' => 'facebook',
			'linkedin' => 'linkedin',
			'telegram' => 'telegram',
			'whatsapp' => 'whatsapp',
			'eitaa'    => 'eitaa',
			'email'    => 'mail',
			'copy'     => 'copy',
		);
	}
}

if ( ! function_exists( 'wgcr_share_custom_icons' ) ) {
	function wgcr_share_custom_icons() {
		return array( 'share', 'link', 'globe', 'mail', 'phone' );
	}
}

if ( ! function_exists( 'wgcr_share_base_css' ) ) {
	function wgcr_share_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-share{--wgcr-share-accent:#2E86DE;--wgcr-share-ink:#1F2937;--wgcr-share-bg:#F4F6F8;display:grid;gap:10px}',
				'.wgcr-share-heading{margin:0;font-size:15px;font-weight:700;line-height:1.8;color:var(--wgcr-share-heading-color,var(--wgcr-share-ink))}',
				'.wgcr-share-list{display:flex;flex-wrap:wrap;align-items:center;justify-content:var(--wgcr-share-align,flex-start);gap:var(--wgcr-share-gap,8px);list-style:none;margin:0;padding:0}',
				'.wgcr-share-link{display:inline-flex;align-items:center;justify-content:center;gap:7px;margin:0;padding:9px;min-width:40px;border:0;border-radius:var(--wgcr-share-radius,12px);background-color:var(--wgcr-share-item-bg,var(--wgcr-share-bg));color:var(--wgcr-share-item-color,var(--wgcr-share-ink));font:inherit;text-decoration:none;cursor:pointer;transition:background-color .2s ease,color .2s ease}',
				'.wgcr-share-link:hover{background-color:var(--wgcr-share-item-bg-hover,var(--wgcr-share-accent));color:var(--wgcr-share-item-color-hover,#fff)}',
				'.wgcr-share-link:focus{outline:none}',
				'.wgcr-share-link:focus-visible{outline:2px solid var(--wgcr-share-accent);outline-offset:2px}',
				'.wgcr-share-ic{flex:none;display:grid;place-items:center;font-size:var(--wgcr-share-icon-size,20px);line-height:1}',
				'.wgcr-share-ic svg{display:block;width:1em;height:1em}',
				'.wgcr-share-text{font-size:14px;font-weight:600;line-height:1.8;color:var(--wgcr-share-text-color,inherit)}',
				'.wgcr-share-link:hover .wgcr-share-text{color:var(--wgcr-share-text-color-hover,var(--wgcr-share-item-color-hover,#fff))}',
				'.wgcr-share[data-shape="circle"]{--wgcr-share-radius:999px}',
				'.wgcr-share[data-shape="square"]{--wgcr-share-radius:4px}',
				'@media (prefers-reduced-motion:reduce){.wgcr-share-link{transition:none}}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_share_base_js' ) ) {
	function wgcr_share_base_js() {
		return implode(
			"\n",
			array(
				'(function () {',
				'  if (window.__wgcrShareBound) { return; }',
				'  window.__wgcrShareBound = true;',
				'  function flash(btn, msg) {',
				'    var t = btn.querySelector(".wgcr-share-text");',
				'    if (!t) { return; }',
				'    if (!t.hasAttribute("data-label")) { t.setAttribute("data-label", t.textContent); }',
				'    var wasHidden = t.hasAttribute("hidden");',
				'    if (wasHidden) { t.removeAttribute("hidden"); }',
				'    t.textContent = msg;',
				'    setTimeout(function () {',
				'      t.textContent = t.getAttribute("data-label") || t.textContent;',
				'      if (wasHidden) { t.setAttribute("hidden", ""); }',
				'    }, 1800);',
				'  }',
				'  function legacyCopy(text) {',
				'    var ta = document.createElement("textarea");',
				'    ta.value = text;',
				'    ta.setAttribute("readonly", "");',
				'    ta.style.position = "fixed";',
				'    ta.style.opacity = "0";',
				'    document.body.appendChild(ta);',
				'    ta.select();',
				'    var ok = false;',
				'    try { ok = document.execCommand("copy"); } catch (err) { ok = false; }',
				'    document.body.removeChild(ta);',
				'    return ok;',
				'  }',
				'  document.addEventListener("click", function (e) {',
				'    var btn = e.target && e.target.closest ? e.target.closest(".wgcr-share-copy") : null;',
				'    if (!btn) { return; }',
				'    e.preventDefault();',
				'    var url = btn.getAttribute("data-copy") || "";',
				'    var done = btn.getAttribute("data-copied") || url;',
				'    if (navigator.clipboard && navigator.clipboard.writeText) {',
				'      navigator.clipboard.writeText(url).then(function () { flash(btn, done); }, function () { flash(btn, legacyCopy(url) ? done : url); });',
				'    } else {',
				'      flash(btn, legacyCopy(url) ? done : url);',
				'    }',
				'  });',
				'})();',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_share_register_assets' ) ) {
	function wgcr_share_register_assets() {
		if ( wp_style_is( 'wgcr-share', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-share', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-share', wgcr_share_base_css() );
		wp_register_script( 'wgcr-share', false, array(), WGCR_VER, true );
		wp_add_inline_script( 'wgcr-share', wgcr_share_base_js() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_share_register_assets', 5 );

if ( ! class_exists( 'WGCR_Share_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Share_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-share'; }
		public function get_title() { return esc_html__( 'اشتراک‌گذاری - Share', 'widgetcore' ); }
		public function get_icon() { return 'eicon-share-arrow'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-share' ); }
		public function get_script_depends() { return array( 'wgcr-share' ); }
		public function get_keywords() { return array( 'share', 'social', 'telegram', 'whatsapp', 'eitaa', __( 'اشتراک‌گذاری', 'widgetcore' ), __( 'شبکه اجتماعی', 'widgetcore' ) ); }

		protected function register_controls() {

			$this->wgcr_content_section( 'sec_networks', esc_html__( 'شبکه‌ها', 'widgetcore' ) );
			$rep = new \Elementor\Repeater();
			$rep->add_control( 'net', array(
				'label'   => esc_html__( 'شبکه', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'telegram' => esc_html__( 'تلگرام', 'widgetcore' ),
					'whatsapp' => esc_html__( 'واتس‌اپ', 'widgetcore' ),
					'eitaa'    => esc_html__( 'ایتا', 'widgetcore' ),
					'x'        => esc_html__( 'ایکس (توییتر)', 'widgetcore' ),
					'facebook' => esc_html__( 'فیس‌بوک', 'widgetcore' ),
					'linkedin' => esc_html__( 'لینکدین', 'widgetcore' ),
					'email'    => esc_html__( 'ایمیل', 'widgetcore' ),
					'copy'     => esc_html__( 'کپی لینک', 'widgetcore' ),
					'custom'   => esc_html__( 'سفارشی (بله، روبیکا، سروش و …)', 'widgetcore' ),
				),
				'default' => 'telegram',
			) );
			$rep->add_control( 'custom_label', array(
				'label'       => esc_html__( 'برچسب شبکه سفارشی', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'مثلاً بله', 'widgetcore' ),
				'label_block' => true,
				'condition'   => array( 'net' => 'custom' ),
			) );
			$rep->add_control( 'custom_url', array(
				'label'       => esc_html__( 'الگوی نشانی اشتراک‌گذاری', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '',
				'placeholder' => 'https://example.test/share?url={url}&text={title}',
				'label_block' => true,
				'condition'   => array( 'net' => 'custom' ),
				'description' => esc_html__( 'نشانی اشتراک‌گذاری شبکه موردنظر را وارد کنید؛ {url} با نشانی صفحه و {title} با عنوان آن جایگزین می‌شوند.', 'widgetcore' ),
			) );
			$rep->add_control( 'custom_icon', array(
				'label'     => esc_html__( 'آیکون شبکه سفارشی', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'share' => esc_html__( 'اشتراک', 'widgetcore' ),
					'link'  => esc_html__( 'پیوند', 'widgetcore' ),
					'globe' => esc_html__( 'کره زمین', 'widgetcore' ),
					'mail'  => esc_html__( 'پاکت نامه', 'widgetcore' ),
					'phone' => esc_html__( 'تلفن', 'widgetcore' ),
				),
				'default'   => 'share',
				'condition' => array( 'net' => 'custom' ),
			) );
			$this->add_control( 'networks', array(
				'label'       => esc_html__( 'شبکه‌های اشتراک‌گذاری', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => array(
					array( 'net' => 'telegram' ),
					array( 'net' => 'whatsapp' ),
					array( 'net' => 'eitaa' ),
					array( 'net' => 'x' ),
					array( 'net' => 'copy' ),
				),
				'title_field' => '{{{ net }}}',
			) );
			$this->wgcr_switch_control( 'show_label', esc_html__( 'نمایش نام شبکه کنار آیکون', 'widgetcore' ), '' );
			$this->add_control( 'heading', array(
				'label'       => esc_html__( 'عنوان بالای دکمه‌ها', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'این مطلب را به اشتراک بگذارید:', 'widgetcore' ),
				'label_block' => true,
			) );
			$this->wgcr_text_control( 'copy_label', esc_html__( 'برچسب دکمه کپی', 'widgetcore' ), __( 'کپی لینک', 'widgetcore' ) );
			$this->wgcr_text_control( 'copied_label', esc_html__( 'برچسب پس از کپی', 'widgetcore' ), __( 'کپی شد!', 'widgetcore' ) );
			$this->add_control( 'target', array(
				'label'   => esc_html__( 'باز شدن پیوندها', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'blank' => esc_html__( 'پنجره جدید', 'widgetcore' ),
					'self'  => esc_html__( 'همان پنجره', 'widgetcore' ),
				),
				'default' => 'blank',
			) );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_buttons', esc_html__( 'دکمه‌ها', 'widgetcore' ) );
			$this->add_control( 'shape', array(
				'label'   => esc_html__( 'شکل دکمه‌ها', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'rounded' => esc_html__( 'گرد (پیش‌فرض)', 'widgetcore' ),
					'circle'  => esc_html__( 'دایره', 'widgetcore' ),
					'square'  => esc_html__( 'مربع', 'widgetcore' ),
				),
				'default' => 'rounded',
			) );
			$this->add_responsive_control( 'align', array(
				'label'     => esc_html__( 'چیدمان', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'flex-start' => esc_html__( 'ابتدا', 'widgetcore' ),
					'center'     => esc_html__( 'وسط', 'widgetcore' ),
					'flex-end'   => esc_html__( 'انتها', 'widgetcore' ),
				),
				'selectors' => array( '{{WRAPPER}} .wgcr-share' => '--wgcr-share-align:{{VALUE}};' ),
			) );
			$this->wgcr_slider_control( 'items_gap', esc_html__( 'فاصله بین دکمه‌ها', 'widgetcore' ), '{{WRAPPER}} .wgcr-share', '--wgcr-share-gap:{{SIZE}}{{UNIT}};', 0, 40 );
			$this->wgcr_slider_control( 'icon_size', esc_html__( 'اندازه آیکون', 'widgetcore' ), '{{WRAPPER}} .wgcr-share', '--wgcr-share-icon-size:{{SIZE}}{{UNIT}};', 12, 64 );
			$this->wgcr_dimensions_control( 'link_padding', esc_html__( 'فاصلهٔ داخلی دکمه', 'widgetcore' ), '{{WRAPPER}} .wgcr-share-link', 'padding' );
			$this->start_controls_tabs( 'share_item_tabs' );
			$this->start_controls_tab( 'share_item_tab_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'item_bg', esc_html__( 'پس‌زمینه دکمه', 'widgetcore' ), '{{WRAPPER}} .wgcr-share', '--wgcr-share-item-bg' );
			$this->wgcr_color_control( 'item_color', esc_html__( 'رنگ آیکون و متن', 'widgetcore' ), '{{WRAPPER}} .wgcr-share', '--wgcr-share-item-color' );
			$this->end_controls_tab();
			$this->start_controls_tab( 'share_item_tab_hover', array( 'label' => esc_html__( 'هاور', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'item_bg_hover', esc_html__( 'پس‌زمینه در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-share', '--wgcr-share-item-bg-hover' );
			$this->wgcr_color_control( 'item_color_hover', esc_html__( 'رنگ در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-share', '--wgcr-share-item-color-hover' );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_text', esc_html__( 'عنوان و متن', 'widgetcore' ) );
			$this->wgcr_color_control( 'heading_color', esc_html__( 'رنگ عنوان', 'widgetcore' ), '{{WRAPPER}} .wgcr-share', '--wgcr-share-heading-color' );
			$this->wgcr_typography_control( 'heading_typo', '{{WRAPPER}} .wgcr-share-heading' );
			$this->wgcr_color_control( 'text_color', esc_html__( 'رنگ نام شبکه‌ها', 'widgetcore' ), '{{WRAPPER}} .wgcr-share', '--wgcr-share-text-color' );
			$this->wgcr_color_control( 'text_color_hover', esc_html__( 'رنگ نام شبکه‌ها در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-share', '--wgcr-share-text-color-hover' );
			$this->end_controls_section();

		}

		protected function render() {
			$s        = $this->get_settings_for_display();
			$networks = ( isset( $s['networks'] ) && is_array( $s['networks'] ) ) ? $s['networks'] : array();

			$page_url   = rawurlencode( (string) get_permalink() );
			$page_title = rawurlencode( wp_strip_all_tags( (string) get_the_title() ) );
			$endpoints  = wgcr_share_endpoints();
			$labels     = wgcr_share_network_labels();
			$icons      = wgcr_share_network_icons();
			$custom_set = wgcr_share_custom_icons();

			$valid = array();
			foreach ( $networks as $row ) {
				$net = isset( $row['net'] ) ? (string) $row['net'] : '';
				if ( 'copy' === $net ) {
					$valid[] = array( 'type' => 'copy' );
					continue;
				}
				if ( 'custom' === $net ) {
					$pattern = isset( $row['custom_url'] ) ? trim( (string) $row['custom_url'] ) : '';
					if ( '' === $pattern ) {
						continue;
					}
					$url   = esc_url_raw( str_replace( array( '{url}', '{title}' ), array( $page_url, $page_title ), $pattern ) );
					$label = ( isset( $row['custom_label'] ) && '' !== trim( (string) $row['custom_label'] ) ) ? trim( (string) $row['custom_label'] ) : __( 'شبکه سفارشی', 'widgetcore' );
					$icon  = ( isset( $row['custom_icon'] ) && in_array( (string) $row['custom_icon'], $custom_set, true ) ) ? (string) $row['custom_icon'] : 'share';
				} elseif ( isset( $endpoints[ $net ] ) ) {
					$url   = esc_url_raw( str_replace( array( '{url}', '{title}' ), array( $page_url, $page_title ), $endpoints[ $net ] ) );
					$label = $labels[ $net ];
					$icon  = $icons[ $net ];
				} else {
					continue;
				}
				if ( '' === $url ) {
					continue;
				}
				$valid[] = array(
					'type'  => 'link',
					'url'   => $url,
					'label' => $label,
					'icon'  => $icon,
				);
			}
			if ( ! $valid ) {
				return;
			}
			wgcr_share_register_assets();
			wp_enqueue_style( 'wgcr-share' );
			wp_enqueue_script( 'wgcr-share' );

			$show_label = ( isset( $s['show_label'] ) && 'yes' === $s['show_label'] );
			$target     = ( isset( $s['target'] ) && 'self' === $s['target'] ) ? '_self' : '_blank';
			$shape      = ( isset( $s['shape'] ) && in_array( $s['shape'], array( 'rounded', 'circle', 'square' ), true ) ) ? $s['shape'] : 'rounded';
			$heading    = isset( $s['heading'] ) ? trim( (string) $s['heading'] ) : '';
			$copy_label = ( isset( $s['copy_label'] ) && '' !== trim( (string) $s['copy_label'] ) ) ? trim( (string) $s['copy_label'] ) : $labels['copy'];
			$copied     = ( isset( $s['copied_label'] ) && '' !== trim( (string) $s['copied_label'] ) ) ? trim( (string) $s['copied_label'] ) : __( 'کپی شد!', 'widgetcore' );

			echo '<div class="wgcr-share" data-shape="' . esc_attr( $shape ) . '">';
			if ( '' !== $heading ) {
				echo '<p class="wgcr-share-heading">' . esc_html( $heading ) . '</p>';
			}
			echo '<ul class="wgcr-share-list">';
			foreach ( $valid as $item ) {
				if ( 'copy' === $item['type'] ) {
					echo '<li><button type="button" class="wgcr-share-link wgcr-share-copy" data-copy="' . esc_attr( (string) get_permalink() ) . '" data-copied="' . esc_attr( $copied ) . '">';
					echo '<span class="wgcr-share-ic">' . wp_kses( wgcr_svg_icon( 'copy' ), wgcr_svg_kses_base() ) . '</span>';
					echo '<span class="wgcr-share-text" aria-live="polite"' . ( $show_label ? '' : ' hidden' ) . '>' . esc_html( $copy_label ) . '</span>';
					echo '</button></li>';
					continue;
				}
				$url      = (string) $item['url'];
				$is_mail  = ( 0 === strpos( $url, 'mailto:' ) );
				$link_tgt = $is_mail ? '_self' : $target;
				echo '<li><a class="wgcr-share-link" href="' . esc_url( $url ) . '" target="' . esc_attr( $link_tgt ) . '"';
				echo ( '_blank' === $link_tgt ) ? ' rel="noopener noreferrer"' : '';
				echo '>';
				echo '<span class="wgcr-share-ic">' . wp_kses( wgcr_svg_icon( (string) $item['icon'] ), wgcr_svg_kses_base() ) . '</span>';
				echo '<span class="wgcr-share-text"' . ( $show_label ? '' : ' hidden' ) . '>' . esc_html( (string) $item['label'] ) . '</span>';
				echo '</a></li>';
			}
			echo '</ul>';
			echo '</div>';
		}
	}
}
