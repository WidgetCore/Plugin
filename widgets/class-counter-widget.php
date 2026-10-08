<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_counter_base_css' ) ) {
	function wgcr_counter_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-counter{--wgcr-counter-accent:#2E86DE;--wgcr-counter-ink:#1F2937;--wgcr-counter-muted:#6B7280;display:flex;flex-direction:column;align-items:center;gap:6px;text-align:center}',
				'.wgcr-counter-row{display:flex;align-items:baseline;justify-content:center;gap:6px;flex-wrap:wrap}',
				'.wgcr-counter-num{font-size:44px;font-weight:800;line-height:1.3;color:var(--wgcr-counter-ink);font-variant-numeric:tabular-nums}',
				'.wgcr-counter-affix{font-size:18px;font-weight:700;line-height:1.6;color:var(--wgcr-counter-accent)}',
				'.wgcr-counter-title{margin:0;font-size:17px;font-weight:700;line-height:1.7;color:var(--wgcr-counter-ink)}',
				'.wgcr-counter-desc{margin:0;max-width:52ch;font-size:14px;line-height:2;color:var(--wgcr-counter-muted)}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_counter_base_js' ) ) {
	function wgcr_counter_base_js() {
		return implode(
			"\n",
			array(
				'(function () {',
				'  if (window.__wgcrCounterReady) { return; }',
				'  window.__wgcrCounterReady = true;',
				'  var raf = window.requestAnimationFrame || function (fn) { return window.setTimeout(function () { fn(Date.now()); }, 16); };',
				'  function fmt(n) { return String(Math.round(n)); }',
				'  function run(root) {',
				'    var num = root.querySelector(\'.wgcr-counter-num\');',
				'    if (!num || num.getAttribute(\'data-done\') === \'1\') { return; }',
				'    num.setAttribute(\'data-done\', \'1\');',
				'    var start = parseInt(num.getAttribute(\'data-start\') || \'0\', 10);',
				'    var end = parseInt(num.getAttribute(\'data-end\') || \'0\', 10);',
				'    var dur = parseInt(num.getAttribute(\'data-duration\') || \'1500\', 10);',
				'    if (isNaN(start)) { start = 0; }',
				'    if (isNaN(end)) { end = 0; }',
				'    if (isNaN(dur) || dur < 0) { dur = 0; }',
				'    if (window.matchMedia && window.matchMedia(\'(prefers-reduced-motion: reduce)\').matches) {',
				'      num.textContent = fmt(end);',
				'      return;',
				'    }',
				'    num.textContent = fmt(start);',
				'    var t0 = null;',
				'    function step(ts) {',
				'      if (t0 === null) { t0 = ts; }',
				'      var p = dur > 0 ? Math.min(1, (ts - t0) / dur) : 1;',
				'      num.textContent = fmt(start + (end - start) * (1 - Math.pow(1 - p, 3)));',
				'      if (p < 1) { raf(step); }',
				'    }',
				'    raf(step);',
				'  }',
				'  var observer = null;',
				'  function watch(root) {',
				'    if (observer) { observer.observe(root); } else { run(root); }',
				'  }',
				'  function init() {',
				'    if (observer === null && \'IntersectionObserver\' in window) {',
				'      observer = new IntersectionObserver(function (entries) {',
				'        entries.forEach(function (entry) {',
				'          if (!entry.isIntersecting) { return; }',
				'          run(entry.target);',
				'          observer.unobserve(entry.target);',
				'        });',
				'      }, { threshold: 0.3 });',
				'    }',
				'    document.querySelectorAll(\'.wgcr-counter\').forEach(function (root) {',
				'      if (root.getAttribute(\'data-wgcr-counter-init\') === \'1\') { return; }',
				'      root.setAttribute(\'data-wgcr-counter-init\', \'1\');',
				'      watch(root);',
				'    });',
				'  }',
				'  if (document.readyState === \'loading\') {',
				'    document.addEventListener(\'DOMContentLoaded\', init);',
				'  } else {',
				'    init();',
				'  }',
				'  window.addEventListener(\'load\', init);',
				'})();',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_counter_register_assets' ) ) {
	function wgcr_counter_register_assets() {
		if ( wp_style_is( 'wgcr-counter', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-counter', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-counter', wgcr_counter_base_css() );
		wp_register_script( 'wgcr-counter', false, array(), WGCR_VER, true );
		wp_add_inline_script( 'wgcr-counter', wgcr_counter_base_js() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_counter_register_assets', 5 );

if ( ! class_exists( 'WGCR_Counter_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Counter_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-counter'; }
		public function get_title() { return esc_html__( 'شمارنده - Counter', 'widgetcore' ); }
		public function get_icon() { return 'eicon-counter'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-counter' ); }
		public function get_script_depends() { return array( 'wgcr-counter' ); }
		public function get_keywords() { return array( 'counter', 'number', 'countup', __( 'شمارنده', 'widgetcore' ), __( 'عدد', 'widgetcore' ), __( 'آمار', 'widgetcore' ) ); }

		protected function register_controls() {

			$this->wgcr_content_section( 'sec_counter', esc_html__( 'شمارنده', 'widgetcore' ) );
			$this->wgcr_number_control( 'start_num', esc_html__( 'عدد شروع', 'widgetcore' ), 0, -1000000, 1000000 );
			$this->wgcr_number_control( 'end_num', esc_html__( 'عدد پایان', 'widgetcore' ), 100, -1000000, 1000000 );
			$this->wgcr_number_control( 'duration', esc_html__( 'مدت انیمیشن (میلی‌ثانیه)', 'widgetcore' ), 1500, 100, 10000 );
			$this->end_controls_section();

			$this->wgcr_content_section( 'sec_texts', esc_html__( 'متن‌ها', 'widgetcore' ) );
			$this->wgcr_text_control( 'prefix', esc_html__( 'پیشوند عدد', 'widgetcore' ) );
			$this->wgcr_text_control( 'suffix', esc_html__( 'پسوند عدد', 'widgetcore' ) );
			$this->wgcr_text_control( 'title', esc_html__( 'عنوان', 'widgetcore' ), __( 'دانشجوی فعال', 'widgetcore' ) );
			$this->wgcr_text_control( 'description', esc_html__( 'توضیح', 'widgetcore' ), __( 'توضیح کوتاه درباره این آمار…', 'widgetcore' ), array(), \Elementor\Controls_Manager::TEXTAREA );
			$this->wgcr_select_control( 'title_tag',
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
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_number', esc_html__( 'عدد', 'widgetcore' ) );
			$this->wgcr_color_control( 'num_color', esc_html__( 'رنگ عدد', 'widgetcore' ), '{{WRAPPER}} .wgcr-counter-num' );
			$this->wgcr_typography_control( 'num_typo', '{{WRAPPER}} .wgcr-counter-num', array( 'size' => 44, 'weight' => '800' ) );
			$this->wgcr_color_control( 'affix_color', esc_html__( 'رنگ پیشوند و پسوند', 'widgetcore' ), '{{WRAPPER}} .wgcr-counter-affix' );
			$this->wgcr_slider_control( 'row_gap', esc_html__( 'فاصله اجزای عدد', 'widgetcore' ), '{{WRAPPER}} .wgcr-counter-row', 'gap:{{SIZE}}{{UNIT}};', 0, 40 );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_texts', esc_html__( 'عنوان و توضیح', 'widgetcore' ) );
			$this->wgcr_color_control( 'title_color', esc_html__( 'رنگ عنوان', 'widgetcore' ), '{{WRAPPER}} .wgcr-counter-title' );
			$this->wgcr_typography_control( 'title_typo', '{{WRAPPER}} .wgcr-counter-title' );
			$this->wgcr_color_control( 'desc_color', esc_html__( 'رنگ توضیح', 'widgetcore' ), '{{WRAPPER}} .wgcr-counter-desc' );
			$this->wgcr_typography_control( 'desc_typo', '{{WRAPPER}} .wgcr-counter-desc' );
			$this->wgcr_slider_control( 'texts_gap', esc_html__( 'فاصله بین متن‌ها', 'widgetcore' ), '{{WRAPPER}} .wgcr-counter', 'gap:{{SIZE}}{{UNIT}};', 0, 40 );
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
						'{{WRAPPER}} .wgcr-counter' => 'align-items:{{VALUE}};',
					),
				)
			);
			$this->end_controls_section();

		}

		protected function render() {
			$s        = $this->get_settings_for_display();
			$start    = isset( $s['start_num'] ) && '' !== $s['start_num'] ? (int) $s['start_num'] : 0;
			$end      = isset( $s['end_num'] ) && '' !== $s['end_num'] ? (int) $s['end_num'] : 0;
			$duration = isset( $s['duration'] ) && '' !== $s['duration'] ? (int) $s['duration'] : 1500;
			$duration = max( 0, min( 10000, $duration ) );
			$prefix   = isset( $s['prefix'] ) ? (string) $s['prefix'] : '';
			$suffix   = isset( $s['suffix'] ) ? (string) $s['suffix'] : '';
			$title    = isset( $s['title'] ) ? (string) $s['title'] : '';
			$desc     = isset( $s['description'] ) ? (string) $s['description'] : '';
			$tag      = ( isset( $s['title_tag'] ) && in_array( $s['title_tag'], array( 'h2', 'h3', 'h4', 'h5', 'h6', 'div' ), true ) ) ? $s['title_tag'] : 'h3';

			wgcr_counter_register_assets();
			wp_enqueue_style( 'wgcr-counter' );
			wp_enqueue_script( 'wgcr-counter' );

			echo '<div class="wgcr-counter">';
			echo '<div class="wgcr-counter-row">';
			if ( '' !== trim( $prefix ) ) {
				echo '<span class="wgcr-counter-affix">' . esc_html( $prefix ) . '</span>';
			}
			echo '<span class="wgcr-counter-num" data-start="' . esc_attr( $start ) . '" data-end="' . esc_attr( $end ) . '" data-duration="' . esc_attr( $duration ) . '">' . esc_html( $end ) . '</span>';
			if ( '' !== trim( $suffix ) ) {
				echo '<span class="wgcr-counter-affix">' . esc_html( $suffix ) . '</span>';
			}
			echo '</div>';
			if ( '' !== trim( $title ) ) {
				$this->add_inline_editing_attributes( 'title', 'none' );
				echo '<' . tag_escape( $tag ) . ' class="wgcr-counter-title" ';
				$this->print_render_attribute_string( 'title' );
				echo '>' . esc_html( $title ) . '</' . tag_escape( $tag ) . '>';
			}
			if ( '' !== trim( $desc ) ) {
				$this->add_inline_editing_attributes( 'description', 'none' );
				echo '<p class="wgcr-counter-desc" ';
				$this->print_render_attribute_string( 'description' );
				echo '>' . esc_html( $desc ) . '</p>';
			}
			echo '</div>';
		}

		protected function content_template() {
			?>
<#
var start = settings.start_num === '' || settings.start_num === undefined ? 0 : parseInt(settings.start_num, 10);
var end = settings.end_num === '' || settings.end_num === undefined ? 0 : parseInt(settings.end_num, 10);
var dur = settings.duration === '' || settings.duration === undefined ? 1500 : parseInt(settings.duration, 10);
if (isNaN(start)) { start = 0; }
if (isNaN(end)) { end = 0; }
if (isNaN(dur) || dur < 0) { dur = 0; }
if (dur > 10000) { dur = 10000; }
var tag = ['h2', 'h3', 'h4', 'h5', 'h6', 'div'].indexOf(settings.title_tag) !== -1 ? settings.title_tag : 'h3';
view.addInlineEditingAttributes('title', 'none');
view.addInlineEditingAttributes('description', 'none');
#>
<div class="wgcr-counter">
	<div class="wgcr-counter-row">
		<# if (settings.prefix && settings.prefix.trim()) { #><span class="wgcr-counter-affix">{{ settings.prefix }}</span><# } #>
		<span class="wgcr-counter-num" data-start="{{{ start }}}" data-end="{{{ end }}}" data-duration="{{{ dur }}}">{{ end }}</span>
		<# if (settings.suffix && settings.suffix.trim()) { #><span class="wgcr-counter-affix">{{ settings.suffix }}</span><# } #>
	</div>
	<# if (settings.title && settings.title.trim()) { #>
	<{{{ tag }}} class="wgcr-counter-title" {{{ view.getRenderAttributeString('title') }}}>{{ settings.title }}</{{{ tag }}}>
	<# } #>
	<# if (settings.description && settings.description.trim()) { #>
	<p class="wgcr-counter-desc" {{{ view.getRenderAttributeString('description') }}}>{{ settings.description }}</p>
	<# } #>
</div>
			<?php
		}
	}
}
