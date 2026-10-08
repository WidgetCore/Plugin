<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_alert_base_css' ) ) {
	function wgcr_alert_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-alert{--wgcr-alert-accent:#2E86DE;--wgcr-alert-bg:#EBF3FC;--wgcr-alert-ink:#1F2937;--wgcr-alert-muted:#4B5563;display:flex;align-items:flex-start;gap:14px;padding:16px 18px;background:var(--wgcr-alert-bg);border:1px solid transparent;border-inline-start:4px solid var(--wgcr-alert-accent);border-radius:10px;color:var(--wgcr-alert-muted)}',
				'.wgcr-alert[hidden]{display:none}',
				'.wgcr-alert-info{--wgcr-alert-accent:#2E86DE;--wgcr-alert-bg:#EBF3FC}',
				'.wgcr-alert-success{--wgcr-alert-accent:#16A34A;--wgcr-alert-bg:#ECFDF3}',
				'.wgcr-alert-warning{--wgcr-alert-accent:#D97706;--wgcr-alert-bg:#FFFBEB}',
				'.wgcr-alert-danger{--wgcr-alert-accent:#DC2626;--wgcr-alert-bg:#FEF2F2}',
				'.wgcr-alert-ic{flex:none;width:28px;height:28px;display:grid;place-items:center;color:var(--wgcr-alert-accent);font-size:22px}',
				'.wgcr-alert-ic svg{display:block;width:1em;height:1em}',
				'.wgcr-alert-body{flex:1 1 auto;min-width:0}',
				'.wgcr-alert-title{margin:0 0 2px;color:var(--wgcr-alert-ink);font-size:16px;font-weight:700;line-height:1.8}',
				'.wgcr-alert-text{font-size:14px;line-height:2}',
				'.wgcr-alert-text>:first-child{margin-top:0}',
				'.wgcr-alert-text>:last-child{margin-bottom:0}',
				'.wgcr-alert-close{flex:none;width:28px;height:28px;display:grid;place-items:center;margin:0;padding:0;background:none;border:0;border-radius:8px;color:var(--wgcr-alert-close-color,var(--wgcr-alert-muted));cursor:pointer;-webkit-appearance:none;appearance:none}',
				'.wgcr-alert-close svg{display:block;width:16px;height:16px}',
				'.wgcr-alert-close:hover{color:var(--wgcr-alert-close-hover-color,var(--wgcr-alert-ink))}',
				'.wgcr-alert-close:focus{outline:none}',
				'.wgcr-alert-close:focus-visible{outline:2px solid var(--wgcr-alert-accent);outline-offset:2px}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_alert_base_js' ) ) {
	function wgcr_alert_base_js() {
		return implode(
			"\n",
			array(
				'(function () {',
				'  if (window.__wgcrAlertReady) { return; }',
				'  window.__wgcrAlertReady = true;',
				'  function init() {',
				'    document.querySelectorAll(\'.wgcr-alert\').forEach(function (root) {',
				'      if (root.getAttribute(\'data-wgcr-alert-init\') === \'1\') { return; }',
				'      root.setAttribute(\'data-wgcr-alert-init\', \'1\');',
				'    });',
				'  }',
				'  document.addEventListener(\'click\', function (e) {',
				'    var btn = e.target && e.target.closest ? e.target.closest(\'.wgcr-alert-close\') : null;',
				'    if (!btn) { return; }',
				'    var box = btn.closest(\'.wgcr-alert\');',
				'    if (!box) { return; }',
				'    box.setAttribute(\'hidden\', \'hidden\');',
				'  });',
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

if ( ! function_exists( 'wgcr_alert_register_assets' ) ) {
	function wgcr_alert_register_assets() {
		if ( wp_style_is( 'wgcr-alert', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-alert', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-alert', wgcr_alert_base_css() );
		wp_register_script( 'wgcr-alert', false, array(), WGCR_VER, true );
		wp_add_inline_script( 'wgcr-alert', wgcr_alert_base_js() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_alert_register_assets', 5 );

if ( ! class_exists( 'WGCR_Alert_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Alert_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-alert'; }
		public function get_title() { return esc_html__( 'پیام - Alert', 'widgetcore' ); }
		public function get_icon() { return 'eicon-alert'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-alert' ); }
		public function get_script_depends() { return array( 'wgcr-alert' ); }
		public function get_keywords() { return array( 'alert', 'notice', 'message', 'callout', __( 'پیام', 'widgetcore' ), __( 'هشدار', 'widgetcore' ), __( 'اعلان', 'widgetcore' ) ); }

		protected function register_controls() {

			$this->wgcr_content_section( 'sec_content', esc_html__( 'محتوای پیام', 'widgetcore' ) );
			$this->wgcr_select_control( 'type',
				esc_html__( 'نوع پیام', 'widgetcore' ),
				array(
					'info'    => esc_html__( 'اطلاع‌رسانی', 'widgetcore' ),
					'success' => esc_html__( 'موفقیت', 'widgetcore' ),
					'warning' => esc_html__( 'هشدار', 'widgetcore' ),
					'danger'  => esc_html__( 'خطر', 'widgetcore' ),
				),
				'info'
			);
			$this->wgcr_text_control( 'title', esc_html__( 'عنوان پیام', 'widgetcore' ), __( 'عنوان پیام', 'widgetcore' ) );
			$this->add_control( 'content', array(
				'label'   => esc_html__( 'متن پیام', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __( 'متن پیام…', 'widgetcore' ),
			) );
			$this->end_controls_section();

			$this->wgcr_content_section( 'sec_behavior', esc_html__( 'تنظیمات', 'widgetcore' ) );
			$this->wgcr_select_control( 'icon',
				esc_html__( 'آیکون', 'widgetcore' ),
				array(
					'auto'   => esc_html__( 'خودکار (متناسب با نوع پیام)', 'widgetcore' ),
					'none'   => esc_html__( 'بدون آیکون', 'widgetcore' ),
					'custom' => esc_html__( 'سفارشی (کتابخانه المنتور)', 'widgetcore' ),
				),
				'auto'
			);
			$this->add_control( 'custom_icon', array(
				'label'     => esc_html__( 'آیکون سفارشی', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-info-circle',
					'library' => 'fa-solid',
				),
				'condition' => array( 'icon' => 'custom' ),
			) );
			$this->wgcr_switch_control( 'dismissible', esc_html__( 'دکمه بستن پیام', 'widgetcore' ), '' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_box', esc_html__( 'کادر پیام', 'widgetcore' ) );
			$this->wgcr_style_box( 'box', '{{WRAPPER}} .wgcr-alert' );
			$this->wgcr_color_control( 'accent', esc_html__( 'رنگ تأکید', 'widgetcore' ), '{{WRAPPER}} .wgcr-alert', '--wgcr-alert-accent' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_texts', esc_html__( 'عنوان و متن', 'widgetcore' ) );
			$this->wgcr_color_control( 'title_color', esc_html__( 'رنگ عنوان', 'widgetcore' ), '{{WRAPPER}} .wgcr-alert-title' );
			$this->wgcr_typography_control( 'title_typo', '{{WRAPPER}} .wgcr-alert-title' );
			$this->wgcr_color_control( 'text_color', esc_html__( 'رنگ متن', 'widgetcore' ), '{{WRAPPER}} .wgcr-alert-text' );
			$this->wgcr_typography_control( 'text_typo', '{{WRAPPER}} .wgcr-alert-text' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_icon', esc_html__( 'آیکون و دکمه بستن', 'widgetcore' ) );
			$this->wgcr_slider_control( 'icon_size', esc_html__( 'اندازه آیکون', 'widgetcore' ), '{{WRAPPER}} .wgcr-alert-ic', 'font-size:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};', 12, 64 );
			$this->wgcr_color_control( 'icon_color', esc_html__( 'رنگ آیکون', 'widgetcore' ), '{{WRAPPER}} .wgcr-alert-ic' );
			$this->start_controls_tabs( 'close_tabs' );
			$this->start_controls_tab( 'close_tab_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'close_color', esc_html__( 'رنگ دکمه بستن', 'widgetcore' ), '{{WRAPPER}} .wgcr-alert-close', '--wgcr-alert-close-color' );
			$this->end_controls_tab();
			$this->start_controls_tab( 'close_tab_hover', array( 'label' => esc_html__( 'هاور', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'close_color_hover', esc_html__( 'رنگ دکمه بستن در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-alert-close', '--wgcr-alert-close-hover-color' );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->end_controls_section();

		}

		protected function render() {
			$s       = $this->get_settings_for_display();
			$type    = ( isset( $s['type'] ) && in_array( $s['type'], array( 'info', 'success', 'warning', 'danger' ), true ) ) ? $s['type'] : 'info';
			$title   = isset( $s['title'] ) ? (string) $s['title'] : '';
			$content = isset( $s['content'] ) ? (string) $s['content'] : '';
			if ( '' === trim( $title ) && '' === trim( wp_strip_all_tags( $content ) ) ) {
				return;
			}
			wgcr_alert_register_assets();
			wp_enqueue_style( 'wgcr-alert' );
			wp_enqueue_script( 'wgcr-alert' );
			$icon = ( isset( $s['icon'] ) && in_array( $s['icon'], array( 'none', 'custom' ), true ) ) ? $s['icon'] : 'auto';
			$role = in_array( $type, array( 'warning', 'danger' ), true ) ? 'alert' : 'status';
			$map  = array(
				'info'    => 'info',
				'success' => 'check',
				'warning' => 'warning',
				'danger'  => 'close',
			);

			echo '<div class="wgcr-alert wgcr-alert-' . esc_attr( $type ) . '" role="' . esc_attr( $role ) . '">';
			if ( 'none' !== $icon ) {
				echo '<span class="wgcr-alert-ic">';
				if ( 'custom' === $icon && ! empty( $s['custom_icon']['value'] ) ) {
					\Elementor\Icons_Manager::render_icon( $s['custom_icon'], array( 'aria-hidden' => 'true' ) );
				} else {
					echo wp_kses( wgcr_svg_icon( $map[ $type ] ), wgcr_svg_kses_base() );
				}
				echo '</span>';
			}
			echo '<div class="wgcr-alert-body">';
			if ( '' !== trim( $title ) ) {
				$this->add_inline_editing_attributes( 'title', 'none' );
				echo '<div class="wgcr-alert-title" ';
				$this->print_render_attribute_string( 'title' );
				echo '>' . esc_html( $title ) . '</div>';
			}
			if ( '' !== trim( $content ) ) {
				$this->add_inline_editing_attributes( 'content', 'advanced' );
				echo '<div class="wgcr-alert-text" ';
				$this->print_render_attribute_string( 'content' );
				echo '>' . wp_kses_post( $content ) . '</div>';
			}
			echo '</div>';
			if ( ! empty( $s['dismissible'] ) && 'yes' === $s['dismissible'] ) {
				echo '<button type="button" class="wgcr-alert-close" aria-label="' . esc_attr__( 'بستن پیام', 'widgetcore' ) . '">' . wp_kses( wgcr_svg_icon( 'close' ), wgcr_svg_kses_base() ) . '</button>';
			}
			echo '</div>';
		}

		protected function content_template() {
			?>
<#
var type = ['info', 'success', 'warning', 'danger'].indexOf(settings.type) !== -1 ? settings.type : 'info';
var icon = ['none', 'custom'].indexOf(settings.icon) !== -1 ? settings.icon : 'auto';
var role = (type === 'warning' || type === 'danger') ? 'alert' : 'status';
var svgs = {
	info: <?php echo wp_json_encode( wgcr_svg_icon( 'info' ), JSON_HEX_TAG ); ?>,
	check: <?php echo wp_json_encode( wgcr_svg_icon( 'check' ), JSON_HEX_TAG ); ?>,
	warning: <?php echo wp_json_encode( wgcr_svg_icon( 'warning' ), JSON_HEX_TAG ); ?>,
	close: <?php echo wp_json_encode( wgcr_svg_icon( 'close' ), JSON_HEX_TAG ); ?>
};
var map = { info: 'info', success: 'check', warning: 'warning', danger: 'close' };
var ic = svgs[map[type]];
if (icon === 'custom') {
	try {
		if (typeof elementor !== 'undefined' && elementor.helpers && elementor.helpers.renderIcon && settings.custom_icon && settings.custom_icon.value) {
			var rendered = elementor.helpers.renderIcon(view, settings.custom_icon, { 'aria-hidden': 'true' }, 'i', 'object');
			if (rendered) { ic = (typeof rendered === 'string') ? rendered : (rendered.value || ic); }
		}
	} catch (e) { ic = svgs.info; }
}
var title = settings.title || '';
var content = settings.content || '';
view.addInlineEditingAttributes('title', 'none');
view.addInlineEditingAttributes('content', 'advanced');
#>
<# if (title.trim() || content.replace(/<[^>]*>/g, '').trim()) { #>
<div class="wgcr-alert wgcr-alert-{{{ type }}}" role="{{{ role }}}">
	<# if (icon !== 'none') { #><span class="wgcr-alert-ic">{{{ ic }}}</span><# } #>
	<div class="wgcr-alert-body">
		<# if (title.trim()) { #><div class="wgcr-alert-title" {{{ view.getRenderAttributeString('title') }}}>{{ title }}</div><# } #>
		<# if (content.trim()) { #><div class="wgcr-alert-text" {{{ view.getRenderAttributeString('content') }}}>{{{ content }}}</div><# } #>
	</div>
	<# if (settings.dismissible === 'yes') { #>
	<button type="button" class="wgcr-alert-close" aria-label="<?php echo esc_attr__( 'بستن پیام', 'widgetcore' ); ?>"><?php echo wp_kses( wgcr_svg_icon( 'close' ), wgcr_svg_kses_base() ); ?></button>
	<# } #>
</div>
<# } #>
			<?php
		}
	}
}
