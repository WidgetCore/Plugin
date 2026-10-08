<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_progress_base_css' ) ) {
	function wgcr_progress_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-progress{--wgcr-progress-track:#EEF2F7;--wgcr-progress-fill:#2E86DE;--wgcr-progress-ink:#1F2937;--wgcr-progress-muted:#6B7280;display:grid;gap:16px}',
				'.wgcr-progress-item{display:grid;gap:8px}',
				'.wgcr-progress-head{display:flex;justify-content:space-between;align-items:baseline;gap:12px}',
				'.wgcr-progress-label{color:var(--wgcr-progress-ink);font-size:15px;font-weight:600;line-height:1.8}',
				'.wgcr-progress-val{color:var(--wgcr-progress-muted);font-size:14px;font-weight:700;line-height:1.8;font-variant-numeric:tabular-nums}',
				'.wgcr-progress-track{height:10px;background:var(--wgcr-progress-track);border-radius:999px;overflow:hidden}',
				'.wgcr-progress-fill{height:100%;background:var(--wgcr-progress-c,var(--wgcr-progress-fill));border-radius:inherit;transform-origin:0 50%;animation:wgcr-progress-in .9s ease-out backwards}',
				'[dir="rtl"] .wgcr-progress-fill{transform-origin:100% 50%}',
				'@keyframes wgcr-progress-in{from{transform:scaleX(0)}}',
				'@media (prefers-reduced-motion:reduce){.wgcr-progress-fill{animation:none}}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_progress_register_assets' ) ) {
	function wgcr_progress_register_assets() {
		if ( wp_style_is( 'wgcr-progress', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-progress', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-progress', wgcr_progress_base_css() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_progress_register_assets', 5 );

if ( ! class_exists( 'WGCR_Progress_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Progress_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-progress'; }
		public function get_title() { return esc_html__( 'نوار پیشرفت - Progress', 'widgetcore' ); }
		public function get_icon() { return 'eicon-skill-bar'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-progress' ); }
		public function get_keywords() { return array( 'progress', 'bar', 'skill', 'percentage', __( 'نوار پیشرفت', 'widgetcore' ), __( 'مهارت', 'widgetcore' ), __( 'درصد', 'widgetcore' ) ); }

		protected function register_controls() {

			$this->wgcr_content_section( 'sec_bars', esc_html__( 'میله‌ها', 'widgetcore' ) );
			$rep = new \Elementor\Repeater();
			$rep->add_control( 'label', array(
				'label'       => esc_html__( 'برچسب', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'برچسب مهارت', 'widgetcore' ),
				'label_block' => true,
			) );
			$rep->add_control( 'percent', array(
				'label'   => esc_html__( 'درصد (۰ تا ۱۰۰)', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'default' => 50,
			) );
			$rep->add_control( 'bar_color', array(
				'label'   => esc_html__( 'رنگ میله', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '',
			) );
			$this->add_control( 'bars', array(
				'label'       => esc_html__( 'میله‌ها', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => array(
					array(
						'label'   => __( 'طراحی رابط کاربری', 'widgetcore' ),
						'percent' => 80,
					),
					array(
						'label'   => __( 'توسعه فرانت‌اند', 'widgetcore' ),
						'percent' => 65,
					),
					array(
						'label'   => __( 'پشتیبانی دانشجویان', 'widgetcore' ),
						'percent' => 90,
					),
				),
				'title_field' => '{{{ label }}}',
			) );
			$this->end_controls_section();

			$this->wgcr_content_section( 'sec_settings', esc_html__( 'تنظیمات', 'widgetcore' ) );
			$this->wgcr_switch_control( 'show_percent', esc_html__( 'نمایش درصد کنار برچسب', 'widgetcore' ), 'yes' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_track', esc_html__( 'میله', 'widgetcore' ) );
			$this->wgcr_color_control( 'track_bg', esc_html__( 'پس‌زمینه ریل', 'widgetcore' ), '{{WRAPPER}} .wgcr-progress-track', 'background-color' );
			$this->wgcr_color_control( 'fill_bg', esc_html__( 'رنگ پیش‌فرض میله‌ها', 'widgetcore' ), '{{WRAPPER}} .wgcr-progress', '--wgcr-progress-fill' );
			$this->wgcr_slider_control( 'bar_height', esc_html__( 'ضخامت میله', 'widgetcore' ), '{{WRAPPER}} .wgcr-progress-track', 'height:{{SIZE}}{{UNIT}};', 2, 60 );
			$this->wgcr_dimensions_control( 'track_radius', esc_html__( 'گردی گوشه‌های ریل', 'widgetcore' ), '{{WRAPPER}} .wgcr-progress-track', 'border-radius' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_labels', esc_html__( 'برچسب و درصد', 'widgetcore' ) );
			$this->wgcr_color_control( 'label_color', esc_html__( 'رنگ برچسب', 'widgetcore' ), '{{WRAPPER}} .wgcr-progress-label' );
			$this->wgcr_typography_control( 'label_typo', '{{WRAPPER}} .wgcr-progress-label' );
			$this->wgcr_color_control( 'val_color', esc_html__( 'رنگ درصد', 'widgetcore' ), '{{WRAPPER}} .wgcr-progress-val' );
			$this->wgcr_typography_control( 'val_typo', '{{WRAPPER}} .wgcr-progress-val' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_layout', esc_html__( 'طرح کلی', 'widgetcore' ) );
			$this->wgcr_slider_control( 'items_gap', esc_html__( 'فاصله بین میله‌ها', 'widgetcore' ), '{{WRAPPER}} .wgcr-progress', 'gap:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->end_controls_section();

		}

		protected function render() {
			$s     = $this->get_settings_for_display();
			$bars  = ( isset( $s['bars'] ) && is_array( $s['bars'] ) ) ? $s['bars'] : array();
			$valid = array();
			foreach ( $bars as $i => $b ) {
				$label = isset( $b['label'] ) ? trim( (string) $b['label'] ) : '';
				if ( '' === $label ) {
					continue;
				}
				$valid[] = array( $i, $b, $label );
			}
			if ( ! $valid ) {
				return;
			}
			wgcr_progress_register_assets();
			wp_enqueue_style( 'wgcr-progress' );
			$show = ( ! isset( $s['show_percent'] ) || 'yes' === $s['show_percent'] );

			echo '<div class="wgcr-progress">';
			foreach ( $valid as $pair ) {
				list( $i, $b, $label ) = $pair;
				$pct = isset( $b['percent'] ) && '' !== $b['percent'] ? (int) $b['percent'] : 0;
				$pct = max( 0, min( 100, $pct ) );
				$lk  = $this->get_repeater_setting_key( 'label', 'bars', $i );
				$this->add_inline_editing_attributes( $lk, 'none' );
				$fill_css = 'width:' . $pct . '%';
				if ( ! empty( $b['bar_color'] ) && preg_match( '/^(#[0-9a-fA-F]{3,8}|rgba?\([^)]+\))$/', (string) $b['bar_color'] ) ) {
					$fill_css .= ';--wgcr-progress-c:' . $b['bar_color'];
				}
				echo '<div class="wgcr-progress-item">';
				echo '<div class="wgcr-progress-head"><span class="wgcr-progress-label" ';
				$this->print_render_attribute_string( $lk );
				echo '>' . esc_html( $label ) . '</span>';
				if ( $show ) {
					echo '<span class="wgcr-progress-val">' . esc_html( sprintf( '%d%%', $pct ) ) . '</span>';
				}
				echo '</div>';
				echo '<div class="wgcr-progress-track"><div class="wgcr-progress-fill" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . esc_attr( $pct ) . '" aria-label="' . esc_attr( $label ) . '" style="' . esc_attr( $fill_css ) . '"></div></div>';
				echo '</div>';
			}
			echo '</div>';
		}

		protected function content_template() {
			?>
<#
var bars = settings.bars || [];
var show = settings.show_percent === undefined || settings.show_percent === 'yes';
var colorRe = /^(#[0-9a-fA-F]{3,8}|rgba?\([^)]+\))$/;
var has = _.some(bars, function(b) { return b.label && b.label.trim(); });
#>
<# if (has) { #>
<div class="wgcr-progress">
	<# _.each(bars, function(b, idx) {
		if (!b.label || !b.label.trim()) { return; }
		var pct = parseInt(b.percent, 10);
		if (isNaN(pct)) { pct = 0; }
		pct = Math.max(0, Math.min(100, pct));
		var lKey = view.getRepeaterSettingKey('label', 'bars', idx);
		view.addInlineEditingAttributes(lKey, 'none');
		var extra = (b.bar_color && colorRe.test(b.bar_color)) ? ';--wgcr-progress-c:' + b.bar_color : '';
	#>
	<div class="wgcr-progress-item">
		<div class="wgcr-progress-head"><span class="wgcr-progress-label" {{{ view.getRenderAttributeString(lKey) }}}>{{ b.label }}</span><# if (show) { #><span class="wgcr-progress-val">{{ pct }}%</span><# } #></div>
		<div class="wgcr-progress-track"><div class="wgcr-progress-fill" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ pct }}" aria-label="{{ b.label }}" style="width:{{ pct }}%{{{ extra }}}"></div></div>
	</div>
	<# }); #>
</div>
<# } #>
			<?php
		}
	}
}
