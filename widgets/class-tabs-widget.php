<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_tabs_base_css' ) ) {
	function wgcr_tabs_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-tabs{--wgcr-tabs-accent:#2E86DE;--wgcr-tabs-ink:#1F2937;--wgcr-tabs-muted:#6B7280;--wgcr-tabs-line:#E5E7EB;display:grid;gap:14px}',
				'.wgcr-tabs-nav{display:flex;gap:6px;flex-wrap:wrap;margin:0;padding:0}',
				'.wgcr-tabs[data-wrap="0"] .wgcr-tabs-nav{flex-wrap:nowrap;overflow-x:auto}',
				'.wgcr-tabs[data-mode="vertical"]{grid-template-columns:auto minmax(0,1fr);align-items:start;gap:18px}',
				'.wgcr-tabs[data-mode="vertical"] .wgcr-tabs-nav{flex-direction:column;flex-wrap:nowrap}',
				'.wgcr-tab{flex:none;display:block;margin:0;padding:10px 18px;background:#fff;border:1px solid var(--wgcr-tabs-line);border-radius:10px;box-shadow:none;text-shadow:none;cursor:pointer;text-align:start;font:inherit;font-size:15px;font-weight:600;line-height:1.8;color:var(--wgcr-tabs-ink);letter-spacing:normal;text-transform:none;-webkit-appearance:none;appearance:none;transition:background-color .2s ease,color .2s ease,border-color .2s ease}',
				'.wgcr-tab[aria-selected="true"]{background:var(--wgcr-tabs-accent);border-color:var(--wgcr-tabs-accent);color:#fff}',
				'.wgcr-tab:focus{outline:none}',
				'.wgcr-tab:hover,.wgcr-tab:focus,.wgcr-tab:active,.wgcr-tab:focus-visible,.wgcr-tab:focus-within{box-shadow:none}',
				'.wgcr-tab:focus-visible{outline:2px solid var(--wgcr-tabs-accent);outline-offset:2px}',
				'.wgcr-panel{min-width:0;background:#fff;border:1px solid var(--wgcr-tabs-line);border-radius:10px;padding:18px 22px;color:var(--wgcr-tabs-muted);font-size:15px;line-height:2}',
				'.wgcr-panel[hidden]{display:none}',
				'.wgcr-panel:focus-visible{outline:2px solid var(--wgcr-tabs-accent);outline-offset:2px}',
				'.wgcr-panel>:first-child{margin-top:0}',
				'.wgcr-panel>:last-child{margin-bottom:0}',
				'.wgcr-tpl-note{margin:0;padding:26px 18px;border:2px dashed #c9d2dc;border-radius:10px;text-align:center;color:var(--wgcr-tabs-muted);background:#f7f9fb;font-size:14px}',
				'@media (max-width:767px){.wgcr-tabs[data-mode="vertical"]{grid-template-columns:1fr}}',
				'@media (prefers-reduced-motion:reduce){.wgcr-tab{transition:none}}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_tabs_base_js' ) ) {
	function wgcr_tabs_base_js() {
		return implode(
			"\n",
			array(
				'(function () {',
				'  if (window.__wgcrTabsReady) { return; }',
				'  window.__wgcrTabsReady = true;',
				'  function tabsOf(root) {',
				'    return Array.prototype.slice.call(root.querySelectorAll(\'.wgcr-tab\'));',
				'  }',
				'  function select(root, tab, focus) {',
				'    tabsOf(root).forEach(function (t) {',
				'      var on = t === tab;',
				'      t.setAttribute(\'aria-selected\', on ? \'true\' : \'false\');',
				'      t.tabIndex = on ? 0 : -1;',
				'      var panel = t.ownerDocument.getElementById(t.getAttribute(\'aria-controls\'));',
				'      if (panel) {',
				'        if (on) { panel.removeAttribute(\'hidden\'); } else { panel.setAttribute(\'hidden\', \'hidden\'); }',
				'      }',
				'    });',
				'    if (focus) { tab.focus(); }',
				'  }',
				'  function init() {',
				'    document.querySelectorAll(\'.wgcr-tabs\').forEach(function (root) {',
				'      if (root.getAttribute(\'data-wgcr-tabs-init\') === \'1\') { return; }',
				'      root.setAttribute(\'data-wgcr-tabs-init\', \'1\');',
				'      var tabs = tabsOf(root);',
				'      var has = tabs.some(function (t) { return t.getAttribute(\'aria-selected\') === \'true\'; });',
				'      if (!has && tabs.length) { select(root, tabs[0], false); }',
				'    });',
				'  }',
				'  document.addEventListener(\'click\', function (e) {',
				'    var tab = e.target && e.target.closest ? e.target.closest(\'.wgcr-tab\') : null;',
				'    if (!tab) { return; }',
				'    var root = tab.closest(\'.wgcr-tabs\');',
				'    if (!root) { return; }',
				'    select(root, tab, false);',
				'  });',
				'  var moves = { ArrowRight: 1, Right: 1, ArrowDown: 1, Down: 1, ArrowLeft: -1, Left: -1, ArrowUp: -1, Up: -1, Home: \'first\', End: \'last\' };',
				'  document.addEventListener(\'keydown\', function (e) {',
				'    var tab = e.target && e.target.closest ? e.target.closest(\'.wgcr-tab\') : null;',
				'    if (!tab || !Object.prototype.hasOwnProperty.call(moves, e.key)) { return; }',
				'    var root = tab.closest(\'.wgcr-tabs\');',
				'    if (!root) { return; }',
				'    var all = tabsOf(root);',
				'    var i = all.indexOf(tab);',
				'    if (i === -1) { return; }',
				'    var move = moves[e.key];',
				'    var n = move === \'first\' ? 0 : (move === \'last\' ? all.length - 1 : (i + move + all.length) % all.length);',
				'    e.preventDefault();',
				'    select(root, all[n], true);',
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

if ( ! function_exists( 'wgcr_tabs_register_assets' ) ) {
	function wgcr_tabs_register_assets() {
		if ( wp_style_is( 'wgcr-tabs', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-tabs', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-tabs', wgcr_tabs_base_css() );
		wp_register_script( 'wgcr-tabs', false, array(), WGCR_VER, true );
		wp_add_inline_script( 'wgcr-tabs', wgcr_tabs_base_js() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_tabs_register_assets', 5 );

if ( ! class_exists( 'WGCR_Tabs_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Tabs_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-tabs'; }
		public function get_title() { return esc_html__( 'زبانه‌ها - Tabs', 'widgetcore' ); }
		public function get_icon() { return 'eicon-tabs'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-tabs' ); }
		public function get_script_depends() { return array( 'wgcr-tabs' ); }
		public function get_keywords() { return array( 'tabs', 'tab', 'tabpanel', __( 'زبانه', 'widgetcore' ), __( 'تب', 'widgetcore' ) ); }

		protected function options_needed() {
			if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
				return true;
			}
			$elementor = \Elementor\Plugin::$instance;
			return ( isset( $elementor->editor ) && $elementor->editor->is_edit_mode() ) || ( isset( $elementor->preview ) && $elementor->preview->is_preview_mode() );
		}

		protected function get_template_options() {
			$options = array( '' => esc_html__( 'بدون قالب', 'widgetcore' ) );
			if ( $this->options_needed() && post_type_exists( 'elementor_library' ) ) {
				$templates = get_posts(
					array(
						'post_type'      => 'elementor_library',
						'post_status'    => 'publish',
						'posts_per_page' => 200,
						'orderby'        => 'title',
						'order'          => 'ASC',
					)
				);
				foreach ( $templates as $tpl ) {
					$options[ (int) $tpl->ID ] = get_the_title( $tpl );
				}
			}
			return $options;
		}

		protected function register_controls() {

			$this->wgcr_content_section( 'sec_tabs', esc_html__( 'زبانه‌ها', 'widgetcore' ) );
			$rep = new \Elementor\Repeater();
			$rep->add_control( 'tab_title', array(
				'label'       => esc_html__( 'عنوان زبانه', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'عنوان زبانه', 'widgetcore' ),
				'label_block' => true,
			) );
			$rep->add_control( 'content_type', array(
				'label'   => esc_html__( 'نوع محتوای زبانه', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'text'     => esc_html__( 'متن (ویرایشگر دیداری)', 'widgetcore' ),
					'template' => esc_html__( 'قالب المنتور', 'widgetcore' ),
					'html'     => esc_html__( 'کد سفارشی (HTML)', 'widgetcore' ),
				),
				'default' => 'text',
			) );
			$rep->add_control( 'tab_content', array(
				'label'     => esc_html__( 'محتوای زبانه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::WYSIWYG,
				'default'   => __( 'محتوای زبانه…', 'widgetcore' ),
				'condition' => array( 'content_type' => 'text' ),
			) );
			$rep->add_control( 'tab_template', array(
				'label'       => esc_html__( 'یک قالب انتخاب کنید', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => $this->get_template_options(),
				'default'     => '',
				'label_block' => true,
				'condition'   => array( 'content_type' => 'template' ),
			) );
			$rep->add_control( 'tab_html', array(
				'label'       => esc_html__( 'کد سفارشی', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => '',
				'rows'        => 10,
				'condition'   => array( 'content_type' => 'html' ),
				'description' => esc_html__( 'این کد دقیقاً مانند ویجت «HTML» المنتور بدون فیلتر چاپ می‌شود و اسکریپت‌های آن اجرا می‌شوند؛ مسئولیت محتوا با شماست.', 'widgetcore' ),
			) );
			$this->add_control( 'items', array(
				'label'       => esc_html__( 'آیتم‌ها', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => array(
					array(
						'tab_title'   => __( 'درباره دوره', 'widgetcore' ),
						'tab_content' => __( 'در این زبانه درباره سرفصل‌ها، مدت زمان و پیش‌نیازهای دوره بنویسید. متن هر زبانه را می‌توانید با ویرایشگر به‌راحتی تغییر دهید.', 'widgetcore' ),
					),
					array(
						'tab_title'   => __( 'نظرات دانشجویان', 'widgetcore' ),
						'tab_content' => __( 'نظرات و تجربه‌های دانشجویان قبلی را در این زبانه قرار دهید تا بازدیدکنندگان با دید بازتری تصمیم بگیرند.', 'widgetcore' ),
					),
					array(
						'tab_title'   => __( 'پشتیبانی', 'widgetcore' ),
						'tab_content' => __( 'راه‌های ارتباط با پشتیبانی، ساعت‌های پاسخ‌گویی و سوالات پرتکرار را در این زبانه بنویسید.', 'widgetcore' ),
					),
				),
				'title_field' => '{{{ tab_title }}}',
			) );
			$this->end_controls_section();

			$this->wgcr_content_section( 'sec_behavior', esc_html__( 'تنظیمات رفتار', 'widgetcore' ) );
			$this->wgcr_select_control( 'mode',
				esc_html__( 'چیدمان زبانه‌ها', 'widgetcore' ),
				array(
					'horizontal' => esc_html__( 'افقی (زبانه‌ها بالای محتوا)', 'widgetcore' ),
					'vertical'   => esc_html__( 'عمودی (زبانه‌ها کنار محتوا)', 'widgetcore' ),
				),
				'horizontal'
			);
			$this->wgcr_switch_control( 'wrap', esc_html__( 'شکستن زبانه‌ها در چند ردیف', 'widgetcore' ), 'yes' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_layout', esc_html__( 'طرح کلی', 'widgetcore' ) );
			$this->wgcr_slider_control( 'nav_gap', esc_html__( 'فاصله بین زبانه‌ها', 'widgetcore' ), '{{WRAPPER}} .wgcr-tabs-nav', 'gap:{{SIZE}}{{UNIT}};', 0, 40 );
			$this->wgcr_slider_control( 'panels_gap', esc_html__( 'فاصله فهرست تا محتوا', 'widgetcore' ), '{{WRAPPER}} .wgcr-tabs', 'gap:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_tabs', esc_html__( 'دکمه‌های زبانه', 'widgetcore' ) );
			$this->start_controls_tabs( 'tabcol_tabs' );
			$this->start_controls_tab( 'tabcol_tab_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'tab_bg', esc_html__( 'پس‌زمینه زبانه', 'widgetcore' ), '{{WRAPPER}} .wgcr-tab', 'background-color' );
			$this->wgcr_color_control( 'tab_color', esc_html__( 'رنگ متن زبانه', 'widgetcore' ), '{{WRAPPER}} .wgcr-tab' );
			$this->wgcr_color_control( 'tab_border_color', esc_html__( 'رنگ کادر زبانه', 'widgetcore' ), '{{WRAPPER}} .wgcr-tab', 'border-color' );
			$this->end_controls_tab();
			$this->start_controls_tab( 'tabcol_tab_active', array( 'label' => esc_html__( 'زبانه فعال', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'tab_active_bg', esc_html__( 'پس‌زمینه زبانه فعال', 'widgetcore' ), '{{WRAPPER}} .wgcr-tab[aria-selected="true"]', 'background-color' );
			$this->wgcr_color_control( 'tab_active_color', esc_html__( 'رنگ متن زبانه فعال', 'widgetcore' ), '{{WRAPPER}} .wgcr-tab[aria-selected="true"]' );
			$this->end_controls_tab();
			$this->start_controls_tab( 'tabcol_tab_hover', array( 'label' => esc_html__( 'هاور', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'tab_bg_hover', esc_html__( 'پس‌زمینه در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-tab:hover', 'background-color' );
			$this->wgcr_color_control( 'tab_color_hover', esc_html__( 'رنگ متن در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-tab:hover' );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->wgcr_typography_control( 'tab_typo', '{{WRAPPER}} .wgcr-tab' );
			$this->wgcr_dimensions_control( 'tab_padding', esc_html__( 'فاصلهٔ داخلی زبانه', 'widgetcore' ), '{{WRAPPER}} .wgcr-tab', 'padding' );
			$this->wgcr_dimensions_control( 'tab_radius', esc_html__( 'گردی گوشه‌های زبانه', 'widgetcore' ), '{{WRAPPER}} .wgcr-tab', 'border-radius' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_panels', esc_html__( 'محتوای زبانه', 'widgetcore' ) );
			$this->wgcr_style_box( 'panel', '{{WRAPPER}} .wgcr-panel' );
			$this->wgcr_color_control( 'panel_color', esc_html__( 'رنگ متن', 'widgetcore' ), '{{WRAPPER}} .wgcr-panel' );
			$this->wgcr_typography_control( 'panel_typo', '{{WRAPPER}} .wgcr-panel' );
			$this->end_controls_section();

		}

		protected function render() {
			$s     = $this->get_settings_for_display();
			$items = ( isset( $s['items'] ) && is_array( $s['items'] ) ) ? $s['items'] : array();
			$valid = array();
			foreach ( $items as $i => $it ) {
				$t = isset( $it['tab_title'] ) ? trim( (string) $it['tab_title'] ) : '';
				if ( '' === $t ) {
					continue;
				}
				$valid[] = array( $i, $it );
			}
			if ( ! $valid ) {
				return;
			}
			wgcr_tabs_register_assets();
			wp_enqueue_style( 'wgcr-tabs' );
			wp_enqueue_script( 'wgcr-tabs' );
			$mode = ( isset( $s['mode'] ) && 'vertical' === $s['mode'] ) ? 'vertical' : 'horizontal';
			$wrap = ( isset( $s['wrap'] ) && 'yes' !== $s['wrap'] ) ? '0' : '1';

			echo '<div class="wgcr-tabs" data-mode="' . esc_attr( $mode ) . '" data-wrap="' . esc_attr( $wrap ) . '">';
			echo '<div class="wgcr-tabs-nav" role="tablist" aria-orientation="' . esc_attr( $mode ) . '">';
			foreach ( $valid as $pos => $pair ) {
				list( $i, $it ) = $pair;
				$uid  = $this->get_id() . '-' . $i;
				$tk   = $this->get_repeater_setting_key( 'tab_title', 'items', $i );
				$this->add_inline_editing_attributes( $tk, 'none' );
				$sel  = ( 0 === $pos ) ? 'true' : 'false';
				$tinx = ( 0 === $pos ) ? '0' : '-1';
				$cls  = ( 0 === $pos ) ? ' wgcr-tab-active' : '';
				echo '<button type="button" role="tab" class="wgcr-tab' . esc_attr( $cls ) . '" id="wgcr-tabs-t-' . esc_attr( $uid ) . '" aria-selected="' . esc_attr( $sel ) . '" aria-controls="wgcr-tabs-p-' . esc_attr( $uid ) . '" tabindex="' . esc_attr( $tinx ) . '"><span ';
				$this->print_render_attribute_string( $tk );
				echo '>' . esc_html( $it['tab_title'] ) . '</span></button>';
			}
			echo '</div>';
			echo '<div class="wgcr-panels">';
			foreach ( $valid as $pos => $pair ) {
				list( $i, $it ) = $pair;
				$uid   = $this->get_id() . '-' . $i;
				$hid   = ( 0 === $pos ) ? '' : ' hidden';
				$ctype = isset( $it['content_type'] ) ? (string) $it['content_type'] : 'text';
				echo '<div role="tabpanel" class="wgcr-panel" id="wgcr-tabs-p-' . esc_attr( $uid ) . '" aria-labelledby="wgcr-tabs-t-' . esc_attr( $uid ) . '" tabindex="0"' . esc_attr( $hid ) . '>';
				if ( 'template' === $ctype ) {
					$tid = isset( $it['tab_template'] ) ? (int) $it['tab_template'] : 0;
					echo do_shortcode( '[wgcr_template id="' . $tid . '"]' );
				} elseif ( 'html' === $ctype ) {
					$this->print_unescaped_setting( 'tab_html', 'items', $i );
				} else {
					$pk = $this->get_repeater_setting_key( 'tab_content', 'items', $i );
					$this->add_inline_editing_attributes( $pk, 'advanced' );
					echo '<div ';
					$this->print_render_attribute_string( $pk );
					echo '>' . wp_kses_post( isset( $it['tab_content'] ) ? $it['tab_content'] : '' ) . '</div>';
				}
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
		}

		protected function content_template() {
			?>
<#
var mode = settings.mode === 'vertical' ? 'vertical' : 'horizontal';
var wrap = (settings.wrap === undefined || settings.wrap === 'yes') ? '1' : '0';
var items = settings.items || [];
var firstIdx = -1;
_.each(items, function(item, idx) {
	if (firstIdx === -1 && item.tab_title && item.tab_title.trim()) { firstIdx = idx; }
});
#>
<# if (firstIdx !== -1) { #>
<div class="wgcr-tabs" data-mode="{{{ mode }}}" data-wrap="{{{ wrap }}}">
	<div class="wgcr-tabs-nav" role="tablist" aria-orientation="{{{ mode }}}">
	<# _.each(items, function(item, idx) {
		if (!item.tab_title || !item.tab_title.trim()) { return; }
		var uid = view.cid + '-' + idx;
		var active = idx === firstIdx;
		var tKey = view.getRepeaterSettingKey('tab_title', 'items', idx);
		view.addInlineEditingAttributes(tKey, 'none');
	#>
		<button type="button" role="tab" class="wgcr-tab<# if (active) { #> wgcr-tab-active<# } #>" id="wgcr-tabs-t-{{{ uid }}}" aria-selected="{{{ active ? 'true' : 'false' }}}" aria-controls="wgcr-tabs-p-{{{ uid }}}" tabindex="{{{ active ? '0' : '-1' }}}"><span {{{ view.getRenderAttributeString(tKey) }}}>{{ item.tab_title }}</span></button>
	<# }); #>
	</div>
	<div class="wgcr-panels">
	<# _.each(items, function(item, idx) {
		if (!item.tab_title || !item.tab_title.trim()) { return; }
		var uid = view.cid + '-' + idx;
		var active = idx === firstIdx;
		var ctype = item.content_type || 'text';
		var cKey = view.getRepeaterSettingKey('tab_content', 'items', idx);
		if (ctype === 'text') { view.addInlineEditingAttributes(cKey, 'advanced'); }
	#>
		<div role="tabpanel" class="wgcr-panel" id="wgcr-tabs-p-{{{ uid }}}" aria-labelledby="wgcr-tabs-t-{{{ uid }}}" tabindex="0"<# if (!active) { #> hidden<# } #>>
		<# if (ctype === 'template') { #>
			<p class="wgcr-tpl-note"><?php echo esc_html__( 'محتوای قالب المنتور در نمای واقعی صفحه نمایش داده می‌شود.', 'widgetcore' ); ?></p>
		<# } else if (ctype === 'html') { #>
			{{{ item.tab_html }}}
		<# } else { #>
			<div {{{ view.getRenderAttributeString(cKey) }}}>{{{ item.tab_content }}}</div>
		<# } #>
		</div>
	<# }); #>
	</div>
</div>
<# } #>
			<?php
		}
	}
}
