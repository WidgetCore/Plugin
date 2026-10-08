<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_pricetable_base_css' ) ) {
	function wgcr_pricetable_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-pricetable{--wgcr-pt-accent:#2E86DE;--wgcr-pt-ink:#1F2937;--wgcr-pt-muted:#6B7280;--wgcr-pt-line:#E5E7EB;position:relative;display:grid;gap:18px;padding:28px;background:#fff;border:1px solid var(--wgcr-pt-line);border-radius:14px;text-align:center}',
				'.wgcr-pricetable-featured{border-color:var(--wgcr-pt-accent);box-shadow:0 8px 24px rgba(46,134,222,.14)}',
				'.wgcr-pt-badge{position:absolute;top:14px;inset-inline-end:14px;background:var(--wgcr-pt-accent);color:#fff;font-size:12px;font-weight:700;line-height:1.8;padding:3px 12px;border-radius:999px}',
				'.wgcr-pt-name{margin:0;font-size:18px;font-weight:700;line-height:1.7;color:var(--wgcr-pt-ink)}',
				'.wgcr-pt-price{display:flex;align-items:baseline;justify-content:center;gap:6px;flex-wrap:wrap;color:var(--wgcr-pt-ink)}',
				'.wgcr-pt-amount{font-size:38px;font-weight:800;line-height:1.3;font-variant-numeric:tabular-nums}',
				'.wgcr-pt-cur,.wgcr-pt-period{font-size:14px;font-weight:600;line-height:1.8;color:var(--wgcr-pt-muted)}',
				'.wgcr-pt-features{list-style:none;margin:0;padding:0;display:grid;gap:10px;text-align:start}',
				'.wgcr-pt-feat{display:flex;align-items:center;gap:10px;font-size:14px;line-height:1.9;color:var(--wgcr-pt-ink)}',
				'.wgcr-pt-ic{flex:none;width:20px;height:20px;display:grid;place-items:center;color:#16A34A}',
				'.wgcr-pt-ic svg{display:block;width:16px;height:16px}',
				'.wgcr-pt-no{color:var(--wgcr-pt-muted)}',
				'.wgcr-pt-no .wgcr-pt-ic{color:#9CA3AF}',
				'.wgcr-pt-btn{display:inline-block;justify-self:center;margin:0;padding:12px 28px;background-color:var(--wgcr-pt-btn-bg,var(--wgcr-pt-accent));color:var(--wgcr-pt-btn-color,#fff);border-radius:10px;font-size:15px;font-weight:700;line-height:1.8;text-decoration:none;transition:background-color .2s ease,color .2s ease,filter .2s ease}',
				'.wgcr-pt-btn:hover{background-color:var(--wgcr-pt-btn-bg-hover,var(--wgcr-pt-btn-bg,var(--wgcr-pt-accent)));color:var(--wgcr-pt-btn-color-hover,var(--wgcr-pt-btn-color,#fff));filter:var(--wgcr-pt-btn-hover-filter,brightness(1.08))}',
				'.wgcr-pt-btn:focus{outline:none}',
				'.wgcr-pt-btn:focus-visible{outline:2px solid var(--wgcr-pt-accent);outline-offset:3px}',
				'@media (prefers-reduced-motion:reduce){.wgcr-pt-btn{transition:none}}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_pricetable_register_assets' ) ) {
	function wgcr_pricetable_register_assets() {
		if ( wp_style_is( 'wgcr-pricetable', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-pricetable', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-pricetable', wgcr_pricetable_base_css() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_pricetable_register_assets', 5 );

if ( ! class_exists( 'WGCR_Price_Table_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Price_Table_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-pricetable'; }
		public function get_title() { return esc_html__( 'جدول قیمت - Price Table', 'widgetcore' ); }
		public function get_icon() { return 'eicon-price-table'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-pricetable' ); }
		public function get_keywords() { return array( 'price', 'pricing', 'table', 'plan', __( 'جدول قیمت', 'widgetcore' ), __( 'قیمت‌گذاری', 'widgetcore' ), __( 'پلن', 'widgetcore' ) ); }

		protected function register_controls() {

			$this->wgcr_content_section( 'sec_header', esc_html__( 'سربرگ', 'widgetcore' ) );
			$this->wgcr_text_control( 'heading', esc_html__( 'نام پلن', 'widgetcore' ), __( 'پلن حرفه‌ای', 'widgetcore' ) );
			$this->wgcr_select_control( 'heading_tag',
				esc_html__( 'تگ عنوان', 'widgetcore' ),
				array(
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'DIV',
				),
				'h3'
			);
			$this->wgcr_switch_control( 'featured', esc_html__( 'پلن ویژه', 'widgetcore' ), '' );
			$this->wgcr_text_control( 'badge_text', esc_html__( 'متن نشان', 'widgetcore' ), __( 'پیشنهاد ویژه', 'widgetcore' ), array( 'featured' => 'yes' ) );
			$this->end_controls_section();

			$this->wgcr_content_section( 'sec_price', esc_html__( 'قیمت', 'widgetcore' ) );
			$this->wgcr_text_control( 'currency', esc_html__( 'نماد یا واحد پول', 'widgetcore' ), __( 'تومان', 'widgetcore' ) );
			$this->wgcr_text_control( 'price', esc_html__( 'مبلغ', 'widgetcore' ), '490000' );
			$this->wgcr_text_control( 'period', esc_html__( 'بازه زمانی', 'widgetcore' ), __( 'ماهانه', 'widgetcore' ) );
			$this->end_controls_section();

			$this->wgcr_content_section( 'sec_features', esc_html__( 'امکانات', 'widgetcore' ) );
			$rep = new \Elementor\Repeater();
			$rep->add_control( 'feature_text', array(
				'label'       => esc_html__( 'متن امکان', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'امکان پلن', 'widgetcore' ),
				'label_block' => true,
			) );
			$rep->add_control( 'included', array(
				'label'        => esc_html__( 'شامل این پلن می‌شود', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'widgetcore' ),
				'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			) );
			$this->add_control( 'features', array(
				'label'       => esc_html__( 'فهرست امکانات', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => array(
					array(
						'feature_text' => __( 'دسترسی به همه جلسه‌های دوره', 'widgetcore' ),
						'included'     => 'yes',
					),
					array(
						'feature_text' => __( 'پشتیبانی اختصاصی', 'widgetcore' ),
						'included'     => 'yes',
					),
					array(
						'feature_text' => __( 'گواهی پایان دوره', 'widgetcore' ),
						'included'     => '',
					),
				),
				'title_field' => '{{{ feature_text }}}',
			) );
			$this->end_controls_section();

			$this->wgcr_content_section( 'sec_cta', esc_html__( 'دکمه', 'widgetcore' ) );
			$this->wgcr_text_control( 'button_text', esc_html__( 'متن دکمه', 'widgetcore' ), __( 'ثبت‌نام در دوره', 'widgetcore' ) );
			$this->add_control( 'button_link', array(
				'label'       => esc_html__( 'پیوند دکمه', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array( 'url' => '#' ),
				'label_block' => true,
			) );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_card', esc_html__( 'کارت', 'widgetcore' ) );
			$this->wgcr_style_box( 'card', '{{WRAPPER}} .wgcr-pricetable' );
			$this->wgcr_color_control( 'accent', esc_html__( 'رنگ تأکید', 'widgetcore' ), '{{WRAPPER}} .wgcr-pricetable', '--wgcr-pt-accent' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_head', esc_html__( 'سربرگ و قیمت', 'widgetcore' ) );
			$this->wgcr_color_control( 'name_color', esc_html__( 'رنگ نام پلن', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-name' );
			$this->wgcr_typography_control( 'name_typo', '{{WRAPPER}} .wgcr-pt-name' );
			$this->wgcr_color_control( 'amount_color', esc_html__( 'رنگ مبلغ', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-amount' );
			$this->wgcr_typography_control( 'amount_typo', '{{WRAPPER}} .wgcr-pt-amount', array( 'size' => 38, 'weight' => '800' ) );
			$this->wgcr_color_control( 'meta_color', esc_html__( 'رنگ واحد پول و بازه', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-cur,{{WRAPPER}} .wgcr-pt-period' );
			$this->wgcr_color_control( 'badge_bg', esc_html__( 'پس‌زمینه نشان', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-badge', 'background-color' );
			$this->wgcr_color_control( 'badge_color', esc_html__( 'رنگ متن نشان', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-badge' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_features', esc_html__( 'فهرست امکانات', 'widgetcore' ) );
			$this->wgcr_color_control( 'feat_color', esc_html__( 'رنگ متن امکانات', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-feat' );
			$this->wgcr_typography_control( 'feat_typo', '{{WRAPPER}} .wgcr-pt-feat' );
			$this->wgcr_color_control( 'feat_yes_color', esc_html__( 'رنگ آیکون شامل', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-feat:not(.wgcr-pt-no) .wgcr-pt-ic' );
			$this->wgcr_color_control( 'feat_no_color', esc_html__( 'رنگ آیکون و متن بدون شامل', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-no' );
			$this->wgcr_slider_control( 'feat_gap', esc_html__( 'فاصله بین امکانات', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-features', 'gap:{{SIZE}}{{UNIT}};', 0, 40 );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_button', esc_html__( 'دکمه', 'widgetcore' ) );
			$this->start_controls_tabs( 'btn_tabs' );
			$this->start_controls_tab( 'btn_tab_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'btn_bg', esc_html__( 'پس‌زمینه دکمه', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-btn', '--wgcr-pt-btn-bg' );
			$this->wgcr_color_control( 'btn_color', esc_html__( 'رنگ متن دکمه', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-btn', '--wgcr-pt-btn-color' );
			$this->end_controls_tab();
			$this->start_controls_tab( 'btn_tab_hover', array( 'label' => esc_html__( 'هاور', 'widgetcore' ) ) );
			$this->add_control(
				'btn_bg_hover',
				array(
					'label'     => esc_html__( 'پس‌زمینه دکمه در هاور', 'widgetcore' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array( '{{WRAPPER}} .wgcr-pt-btn' => '--wgcr-pt-btn-bg-hover:{{VALUE}};--wgcr-pt-btn-hover-filter:none;' ),
				)
			);
			$this->wgcr_color_control( 'btn_color_hover', esc_html__( 'رنگ متن دکمه در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-btn', '--wgcr-pt-btn-color-hover' );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->wgcr_typography_control( 'btn_typo', '{{WRAPPER}} .wgcr-pt-btn' );
			$this->wgcr_dimensions_control( 'btn_padding', esc_html__( 'فاصلهٔ داخلی دکمه', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-btn', 'padding' );
			$this->wgcr_dimensions_control( 'btn_radius', esc_html__( 'گردی گوشه‌های دکمه', 'widgetcore' ), '{{WRAPPER}} .wgcr-pt-btn', 'border-radius' );
			$this->end_controls_section();

		}

		protected function render() {
			$s        = $this->get_settings_for_display();
			$heading  = isset( $s['heading'] ) ? (string) $s['heading'] : '';
			$currency = isset( $s['currency'] ) ? (string) $s['currency'] : '';
			$price    = isset( $s['price'] ) ? (string) $s['price'] : '';
			$period   = isset( $s['period'] ) ? (string) $s['period'] : '';
			$features = ( isset( $s['features'] ) && is_array( $s['features'] ) ) ? $s['features'] : array();
			$btn_text = isset( $s['button_text'] ) ? (string) $s['button_text'] : '';
			$btn_link = ( isset( $s['button_link'] ) && is_array( $s['button_link'] ) && ! empty( $s['button_link']['url'] ) ) ? $s['button_link'] : array();
			if ( $btn_link && '' === esc_url( (string) $btn_link['url'] ) ) {
				$btn_link = array();
			}
			$has_feat = false;
			foreach ( $features as $f ) {
				if ( '' !== trim( (string) ( isset( $f['feature_text'] ) ? $f['feature_text'] : '' ) ) ) {
					$has_feat = true;
					break;
				}
			}
			if ( '' === trim( $heading ) && '' === trim( $price ) && ! $has_feat && '' === trim( $btn_text ) ) {
				return;
			}
			wgcr_pricetable_register_assets();
			wp_enqueue_style( 'wgcr-pricetable' );
			$tag      = ( isset( $s['heading_tag'] ) && in_array( $s['heading_tag'], array( 'h2', 'h3', 'h4', 'h5', 'h6', 'div' ), true ) ) ? $s['heading_tag'] : 'h3';
			$featured = ( ! empty( $s['featured'] ) && 'yes' === $s['featured'] );
			$badge    = isset( $s['badge_text'] ) ? trim( (string) $s['badge_text'] ) : '';

			echo '<div class="wgcr-pricetable' . ( $featured ? ' wgcr-pricetable-featured' : '' ) . '">';
			if ( $featured && '' !== $badge ) {
				echo '<span class="wgcr-pt-badge">' . esc_html( $badge ) . '</span>';
			}
			if ( '' !== trim( $heading ) ) {
				$this->add_inline_editing_attributes( 'heading', 'none' );
				echo '<' . tag_escape( $tag ) . ' class="wgcr-pt-name" ';
				$this->print_render_attribute_string( 'heading' );
				echo '>' . esc_html( $heading ) . '</' . tag_escape( $tag ) . '>';
			}
			if ( '' !== trim( $price ) || '' !== trim( $currency ) ) {
				echo '<div class="wgcr-pt-price">';
				if ( '' !== trim( $currency ) ) {
					echo '<span class="wgcr-pt-cur">' . esc_html( $currency ) . '</span>';
				}
				if ( '' !== trim( $price ) ) {
					echo '<span class="wgcr-pt-amount">' . esc_html( $price ) . '</span>';
				}
				if ( '' !== trim( $period ) ) {
					echo '<span class="wgcr-pt-period">/ ' . esc_html( $period ) . '</span>';
				}
				echo '</div>';
			}
			if ( $has_feat ) {
				echo '<ul class="wgcr-pt-features">';
				foreach ( $features as $i => $f ) {
					$text = isset( $f['feature_text'] ) ? trim( (string) $f['feature_text'] ) : '';
					if ( '' === $text ) {
						continue;
					}
					$yes  = ( ! isset( $f['included'] ) || 'yes' === $f['included'] );
					$fk   = $this->get_repeater_setting_key( 'feature_text', 'features', $i );
					$this->add_inline_editing_attributes( $fk, 'none' );
					echo '<li class="wgcr-pt-feat' . ( $yes ? '' : ' wgcr-pt-no' ) . '"><span class="wgcr-pt-ic">' . wp_kses( wgcr_svg_icon( $yes ? 'check' : 'close' ), wgcr_svg_kses_base() ) . '</span><span ';
					$this->print_render_attribute_string( $fk );
					echo '>' . esc_html( $text ) . '</span></li>';
				}
				echo '</ul>';
			}
			if ( '' !== trim( $btn_text ) && $btn_link ) {
				$rel = array();
				echo '<a class="wgcr-pt-btn" href="' . esc_url( $btn_link['url'] ) . '"';
				if ( ! empty( $btn_link['is_external'] ) ) {
					echo ' target="_blank"';
					$rel[] = 'noopener';
					$rel[] = 'noreferrer';
				}
				if ( ! empty( $btn_link['nofollow'] ) ) {
					$rel[] = 'nofollow';
				}
				if ( $rel ) {
					echo ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"';
				}
				echo '>' . esc_html( $btn_text ) . '</a>';
			}
			echo '</div>';
		}

		protected function content_template() {
			?>
<#
var tag = ['h2', 'h3', 'h4', 'h5', 'h6', 'div'].indexOf(settings.heading_tag) !== -1 ? settings.heading_tag : 'h3';
var featured = settings.featured === 'yes';
var badge = (settings.badge_text || '').trim();
var heading = settings.heading || '';
var currency = settings.currency || '';
var price = settings.price || '';
var period = settings.period || '';
var features = settings.features || [];
var btnText = (settings.button_text || '').trim();
var btnLink = settings.button_link && settings.button_link.url ? settings.button_link : null;
var btnAttrs = '';
if (btnLink) {
	var rel = [];
	btnAttrs = ' href="' + btnLink.url + '"';
	if (btnLink.is_external) { btnAttrs += ' target="_blank"'; rel.push('noopener'); rel.push('noreferrer'); }
	if (btnLink.nofollow) { rel.push('nofollow'); }
	if (rel.length) { btnAttrs += ' rel="' + rel.join(' ') + '"'; }
}
var checkSvg = <?php echo wp_json_encode( wgcr_svg_icon( 'check' ), JSON_HEX_TAG ); ?>;
var closeSvg = <?php echo wp_json_encode( wgcr_svg_icon( 'close' ), JSON_HEX_TAG ); ?>;
var hasFeat = _.some(features, function(f) { return f.feature_text && f.feature_text.trim(); });
view.addInlineEditingAttributes('heading', 'none');
#>
<# if (heading.trim() || price.trim() || hasFeat || btnText) { #>
<div class="wgcr-pricetable<# if (featured) { #> wgcr-pricetable-featured<# } #>">
	<# if (featured && badge) { #><span class="wgcr-pt-badge">{{ badge }}</span><# } #>
	<# if (heading.trim()) { #><{{{ tag }}} class="wgcr-pt-name" {{{ view.getRenderAttributeString('heading') }}}>{{ heading }}</{{{ tag }}}><# } #>
	<# if (price.trim() || currency.trim()) { #>
	<div class="wgcr-pt-price">
		<# if (currency.trim()) { #><span class="wgcr-pt-cur">{{ currency }}</span><# } #>
		<# if (price.trim()) { #><span class="wgcr-pt-amount">{{ price }}</span><# } #>
		<# if (period.trim()) { #><span class="wgcr-pt-period">/ {{ period }}</span><# } #>
	</div>
	<# } #>
	<# if (hasFeat) { #>
	<ul class="wgcr-pt-features">
		<# _.each(features, function(f, idx) {
			if (!f.feature_text || !f.feature_text.trim()) { return; }
			var yes = f.included === undefined || f.included === 'yes';
			var fKey = view.getRepeaterSettingKey('feature_text', 'features', idx);
			view.addInlineEditingAttributes(fKey, 'none');
		#>
		<li class="wgcr-pt-feat<# if (!yes) { #> wgcr-pt-no<# } #>"><span class="wgcr-pt-ic">{{{ yes ? checkSvg : closeSvg }}}</span><span {{{ view.getRenderAttributeString(fKey) }}}>{{ f.feature_text }}</span></li>
		<# }); #>
	</ul>
	<# } #>
	<# if (btnText && btnLink) { #><a class="wgcr-pt-btn"{{{ btnAttrs }}}>{{ btnText }}</a><# } #>
</div>
<# } #>
			<?php
		}
	}
}
