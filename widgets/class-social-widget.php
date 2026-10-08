<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_social_base_css' ) ) {
	function wgcr_social_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-social{--wgcr-social-accent:#2E86DE;--wgcr-social-ink:#1F2937;--wgcr-social-bg:#F4F6F8}',
				'.wgcr-social-list{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;list-style:none;margin:0;padding:0}',
				'.wgcr-social-link{display:inline-flex;align-items:center;gap:8px;padding:8px;border-radius:12px;background-color:var(--wgcr-social-link-bg,var(--wgcr-social-bg));color:var(--wgcr-social-link-color,var(--wgcr-social-ink));text-decoration:none;transition:background-color .2s ease,color .2s ease}',
				'.wgcr-social-link:hover{background-color:var(--wgcr-social-link-bg-hover,var(--wgcr-social-accent));color:var(--wgcr-social-link-color-hover,#fff)}',
				'.wgcr-social-link:focus{outline:none}',
				'.wgcr-social-link:focus-visible{outline:2px solid var(--wgcr-social-accent);outline-offset:2px}',
				'.wgcr-social-ic{flex:none;display:grid;place-items:center;width:1.4em;height:1.4em;font-size:20px;line-height:1}',
				'.wgcr-social-ic svg{display:block;width:1em;height:1em}',
				'.wgcr-social-text{font-size:14px;font-weight:600;line-height:1.8;color:var(--wgcr-social-text-color,inherit)}',
				'.wgcr-social-link:hover .wgcr-social-text{color:var(--wgcr-social-text-color-hover,var(--wgcr-social-link-color-hover,#fff))}',
				'@media (prefers-reduced-motion:reduce){.wgcr-social-link{transition:none}}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_social_register_assets' ) ) {
	function wgcr_social_register_assets() {
		if ( wp_style_is( 'wgcr-social', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-social', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-social', wgcr_social_base_css() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_social_register_assets', 5 );

if ( ! class_exists( 'WGCR_Social_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Social_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-social'; }
		public function get_title() { return esc_html__( 'آیکون‌های اجتماعی - Social', 'widgetcore' ); }
		public function get_icon() { return 'eicon-social-icons'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-social' ); }
		public function get_keywords() { return array( 'social', 'icons', 'links', 'share', __( 'شبکه‌های اجتماعی', 'widgetcore' ), __( 'آیکون اجتماعی', 'widgetcore' ), __( 'پیوند', 'widgetcore' ) ); }

		protected function register_controls() {

			$this->wgcr_content_section( 'sec_links', esc_html__( 'پیوندها', 'widgetcore' ) );
			$rep = new \Elementor\Repeater();
			$rep->add_control( 'label', array(
				'label'       => esc_html__( 'عنوان پیوند', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'نام شبکه یا پیوند', 'widgetcore' ),
				'label_block' => true,
			) );
			$rep->add_control( 'url', array(
				'label'       => esc_html__( 'نشانی پیوند', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array( 'url' => '#' ),
				'label_block' => true,
			) );
			$rep->add_control( 'icon', array(
				'label'   => esc_html__( 'آیکون', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'mail'   => esc_html__( 'ایمیل', 'widgetcore' ),
					'phone'  => esc_html__( 'تلفن', 'widgetcore' ),
					'link'   => esc_html__( 'پیوند', 'widgetcore' ),
					'globe'  => esc_html__( 'وب‌سایت (کره)', 'widgetcore' ),
					'custom' => esc_html__( 'سفارشی (کتابخانه المنتور)', 'widgetcore' ),
				),
				'default' => 'link',
			) );
			$rep->add_control( 'custom_icon', array(
				'label'     => esc_html__( 'آیکون سفارشی', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-globe',
					'library' => 'fa-solid',
				),
				'condition' => array( 'icon' => 'custom' ),
			) );
			$this->add_control( 'links', array(
				'label'       => esc_html__( 'پیوندها', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => array(
					array(
						'label' => __( 'ایمیل', 'widgetcore' ),
						'url'   => array( 'url' => '#' ),
						'icon'  => 'mail',
					),
					array(
						'label' => __( 'تلفن', 'widgetcore' ),
						'url'   => array( 'url' => '#' ),
						'icon'  => 'phone',
					),
					array(
						'label' => __( 'وب‌سایت', 'widgetcore' ),
						'url'   => array( 'url' => '#' ),
						'icon'  => 'globe',
					),
				),
				'title_field' => '{{{ label }}}',
			) );
			$this->end_controls_section();

			$this->wgcr_content_section( 'sec_settings', esc_html__( 'تنظیمات', 'widgetcore' ) );
			$this->wgcr_switch_control( 'show_labels', esc_html__( 'نمایش متن کنار آیکون', 'widgetcore' ), '' );
			$this->wgcr_switch_control( 'new_tab', esc_html__( 'باز شدن در زبانه جدید', 'widgetcore' ), 'yes' );
			$this->wgcr_switch_control( 'nofollow', esc_html__( 'افزودن nofollow به پیوندها', 'widgetcore' ), 'yes' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_layout', esc_html__( 'طرح کلی', 'widgetcore' ) );
			$this->add_responsive_control(
				'align',
				array(
					'label'     => esc_html__( 'تراز افقی', 'widgetcore' ),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'start'  => array(
							'title' => esc_html__( 'ابتدا', 'widgetcore' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'مرکز', 'widgetcore' ),
							'icon'  => 'eicon-text-align-center',
						),
						'end'    => array(
							'title' => esc_html__( 'انتها', 'widgetcore' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .wgcr-social-list' => 'justify-content:{{VALUE}};',
					),
				)
			);
			$this->wgcr_slider_control( 'links_gap', esc_html__( 'فاصله بین پیوندها', 'widgetcore' ), '{{WRAPPER}} .wgcr-social-list', 'gap:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_link', esc_html__( 'پیوند', 'widgetcore' ) );
			$this->start_controls_tabs( 'link_tabs' );
			$this->start_controls_tab( 'link_tab_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'link_bg', esc_html__( 'پس‌زمینه پیوند', 'widgetcore' ), '{{WRAPPER}} .wgcr-social-link', '--wgcr-social-link-bg' );
			$this->wgcr_color_control( 'link_color', esc_html__( 'رنگ آیکون و متن', 'widgetcore' ), '{{WRAPPER}} .wgcr-social-link', '--wgcr-social-link-color' );
			$this->end_controls_tab();
			$this->start_controls_tab( 'link_tab_hover', array( 'label' => esc_html__( 'هاور', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'link_bg_hover', esc_html__( 'پس‌زمینه در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-social-link', '--wgcr-social-link-bg-hover' );
			$this->wgcr_color_control( 'link_color_hover', esc_html__( 'رنگ در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-social-link', '--wgcr-social-link-color-hover' );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->wgcr_slider_control( 'icon_size', esc_html__( 'اندازه آیکون', 'widgetcore' ), '{{WRAPPER}} .wgcr-social-ic', 'font-size:{{SIZE}}{{UNIT}};', 12, 64 );
			$this->wgcr_dimensions_control( 'link_padding', esc_html__( 'فاصلهٔ داخلی پیوند', 'widgetcore' ), '{{WRAPPER}} .wgcr-social-link', 'padding' );
			$this->wgcr_dimensions_control( 'link_radius', esc_html__( 'گردی گوشه‌های پیوند', 'widgetcore' ), '{{WRAPPER}} .wgcr-social-link', 'border-radius' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_text', esc_html__( 'متن پیوندها', 'widgetcore' ) );
			$this->wgcr_color_control( 'text_color', esc_html__( 'رنگ متن', 'widgetcore' ), '{{WRAPPER}} .wgcr-social-text', '--wgcr-social-text-color' );
			$this->wgcr_typography_control( 'text_typo', '{{WRAPPER}} .wgcr-social-text' );
			$this->end_controls_section();

		}

		protected function render() {
			$s     = $this->get_settings_for_display();
			$links = ( isset( $s['links'] ) && is_array( $s['links'] ) ) ? $s['links'] : array();
			$valid = array();
			foreach ( $links as $i => $l ) {
				$url = ( isset( $l['url'] ) && is_array( $l['url'] ) && ! empty( $l['url']['url'] ) ) ? (string) $l['url']['url'] : '';
				if ( '' === trim( $url ) || '' === esc_url( $url ) ) {
					continue;
				}
				$valid[] = array( $i, $l );
			}
			if ( ! $valid ) {
				return;
			}
			wgcr_social_register_assets();
			wp_enqueue_style( 'wgcr-social' );
			$show    = ( ! empty( $s['show_labels'] ) && 'yes' === $s['show_labels'] );
			$new_tab = ( ! isset( $s['new_tab'] ) || 'yes' === $s['new_tab'] );
			$nofol   = ( ! isset( $s['nofollow'] ) || 'yes' === $s['nofollow'] );
			$icons   = array( 'mail', 'phone', 'link', 'globe' );

			echo '<nav class="wgcr-social" aria-label="' . esc_attr__( 'پیوندهای اجتماعی', 'widgetcore' ) . '">';
			echo '<ul class="wgcr-social-list">';
			foreach ( $valid as $pair ) {
				list( $i, $l ) = $pair;
				$url   = (string) $l['url']['url'];
				$label = isset( $l['label'] ) ? trim( (string) $l['label'] ) : '';
				$name  = ( '' !== $label ) ? $label : __( 'پیوند اجتماعی', 'widgetcore' );
				$icon  = ( isset( $l['icon'] ) && in_array( $l['icon'], array( 'mail', 'phone', 'link', 'globe', 'custom' ), true ) ) ? $l['icon'] : 'link';
				$lk    = $this->get_repeater_setting_key( 'label', 'links', $i );
				$this->add_inline_editing_attributes( $lk, 'none' );
				$rel = array();
				echo '<li><a class="wgcr-social-link" href="' . esc_url( $url ) . '"';
				if ( $new_tab ) {
					echo ' target="_blank"';
					$rel[] = 'noopener';
					$rel[] = 'noreferrer';
				}
				if ( $nofol ) {
					$rel[] = 'nofollow';
				}
				if ( $rel ) {
					echo ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"';
				}
				echo ' aria-label="' . esc_attr( $name ) . '" title="' . esc_attr( $name ) . '">';
				echo '<span class="wgcr-social-ic">';
				if ( 'custom' === $icon && ! empty( $l['custom_icon']['value'] ) ) {
					\Elementor\Icons_Manager::render_icon( $l['custom_icon'], array( 'aria-hidden' => 'true' ) );
				} else {
					echo wp_kses( wgcr_svg_icon( in_array( $icon, $icons, true ) ? $icon : 'link' ), wgcr_svg_kses_base() );
				}
				echo '</span>';
				if ( $show ) {
					echo '<span class="wgcr-social-text" ';
					$this->print_render_attribute_string( $lk );
					echo '>' . esc_html( $label ) . '</span>';
				}
				echo '</a></li>';
			}
			echo '</ul>';
			echo '</nav>';
		}

		protected function content_template() {
			?>
<#
var links = settings.links || [];
var show = settings.show_labels === 'yes';
var newTab = settings.new_tab === undefined || settings.new_tab === 'yes';
var nofol = settings.nofollow === undefined || settings.nofollow === 'yes';
var svgs = {
	mail: <?php echo wp_json_encode( wgcr_svg_icon( 'mail' ), JSON_HEX_TAG ); ?>,
	phone: <?php echo wp_json_encode( wgcr_svg_icon( 'phone' ), JSON_HEX_TAG ); ?>,
	link: <?php echo wp_json_encode( wgcr_svg_icon( 'link' ), JSON_HEX_TAG ); ?>,
	globe: <?php echo wp_json_encode( wgcr_svg_icon( 'globe' ), JSON_HEX_TAG ); ?>
};
var has = _.some(links, function(l) { return l.url && l.url.url && l.url.url.trim(); });
var label = <?php echo wp_json_encode( __( 'پیوندهای اجتماعی', 'widgetcore' ), JSON_HEX_TAG ); ?>;
var fallback = <?php echo wp_json_encode( __( 'پیوند اجتماعی', 'widgetcore' ), JSON_HEX_TAG ); ?>;
#>
<# if (has) { #>
<nav class="wgcr-social" aria-label="{{ label }}">
	<ul class="wgcr-social-list">
	<# _.each(links, function(l, idx) {
		if (!l.url || !l.url.url || !l.url.url.trim()) { return; }
		var name = (l.label && l.label.trim()) ? l.label.trim() : fallback;
		var icon = ['mail', 'phone', 'link', 'globe', 'custom'].indexOf(l.icon) !== -1 ? l.icon : 'link';
		var ic = svgs[icon] || svgs.link;
		if (icon === 'custom' && l.custom_icon && l.custom_icon.value) {
			try {
				if (typeof elementor !== 'undefined' && elementor.helpers && elementor.helpers.renderIcon) {
					var rendered = elementor.helpers.renderIcon(view, l.custom_icon, { 'aria-hidden': 'true' }, 'i', 'object');
					if (rendered) { ic = (typeof rendered === 'string') ? rendered : (rendered.value || ic); }
				}
			} catch (e) { ic = svgs.link; }
		}
		var rel = [];
		var attrs = ' href="' + l.url.url + '"';
		if (newTab) { attrs += ' target="_blank"'; rel.push('noopener'); rel.push('noreferrer'); }
		if (nofol) { rel.push('nofollow'); }
		if (rel.length) { attrs += ' rel="' + rel.join(' ') + '"'; }
		var lKey = view.getRepeaterSettingKey('label', 'links', idx);
		view.addInlineEditingAttributes(lKey, 'none');
	#>
		<li><a class="wgcr-social-link"{{{ attrs }}} aria-label="{{ name }}" title="{{ name }}"><span class="wgcr-social-ic">{{{ ic }}}</span><# if (show) { #><span class="wgcr-social-text" {{{ view.getRenderAttributeString(lKey) }}}>{{ l.label }}</span><# } #></a></li>
	<# }); #>
	</ul>
</nav>
<# } #>
			<?php
		}
	}
}
