<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_faq_base_css' ) ) {
	function wgcr_faq_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-faq{--wgcr-faq-speed:.3s;max-width:760px;margin-inline:auto}',
				'.wgcr-faq-list{display:grid;gap:14px;margin:0;padding:0}',
				'.wgcr-faq-item{overflow:hidden;background:rgba(255,255,255,.72);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.7);border-radius:12px;box-shadow:0 1px 2px rgba(18,71,127,.06)}',
				'.wgcr-faq-qwrap{margin:0;padding:0;border:0;background:none;box-shadow:none;text-shadow:none;font:inherit}',
				'.wgcr-faq-q{width:100%;display:flex;align-items:center;justify-content:space-between;gap:16px;margin:0;padding:20px 24px;background:none;border:0;box-shadow:none;text-shadow:none;letter-spacing:normal;text-transform:none;cursor:pointer;text-align:start;font:inherit;color:#1F2937;font-size:16px;font-weight:700;line-height:1.8;-webkit-appearance:none;appearance:none}',
				'.wgcr-faq-q:focus{outline:none}',
				'.wgcr-faq-q:hover,.wgcr-faq-q:focus,.wgcr-faq-q:active,.wgcr-faq-q:focus-visible,.wgcr-faq-q:focus-within{box-shadow:none}',
				'.wgcr-faq-q:focus-visible{outline:2px solid #2E86DE;outline-offset:-2px}',
				'.wgcr-faq-qtext{flex:1 1 auto;min-width:0;text-shadow:none}',
				'.wgcr-faq-ic{flex:none;width:30px;height:30px;display:grid;place-items:center;border-radius:8px;background:#EBF3FC;color:#2E86DE;transition:transform .25s ease,background-color .25s ease,color .25s ease}',
				'.wgcr-faq-ic svg{display:block;width:16px;height:16px}',
				'.wgcr-faq-item.open .wgcr-faq-ic{background:#2E86DE;color:#fff}',
				'.wgcr-faq-item[data-icon="plus"].open .wgcr-faq-ic{transform:rotate(45deg)}',
				'.wgcr-faq-item[data-icon="chevron"].open .wgcr-faq-ic{transform:rotate(180deg)}',
				'.wgcr-faq-item[data-icon="custom"].open .wgcr-faq-ic{transform:rotate(180deg)}',
				'.wgcr-faq-a{max-height:0;overflow:hidden;visibility:hidden;transition:max-height var(--wgcr-faq-speed,.3s) ease,visibility 0s linear var(--wgcr-faq-speed,.3s)}',
				'.wgcr-faq-item.open>.wgcr-faq-a{max-height:640px;visibility:visible;transition-delay:0s}',
				'.wgcr-faq-atext{padding:0 24px 22px;color:#6B7280;font-size:15px;line-height:2}',
				'.wgcr-faq-atext>:first-child{margin-top:0}',
				'.wgcr-faq-atext>:last-child{margin-bottom:0}',
				'@media (prefers-reduced-motion:reduce){.wgcr-faq-a,.wgcr-faq-ic{transition:none}}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_faq_base_js' ) ) {
	function wgcr_faq_base_js() {
		return implode(
			"\n",
			array(
				'(function () {',
				'  if (window.__wgcrFaqReady) { return; }',
				'  window.__wgcrFaqReady = true;',
				'  function paint(item) {',
				'    var a = item.querySelector(\'.wgcr-faq-a\');',
				'    if (!a) { return; }',
				'    var open = item.classList.contains(\'open\');',
				'    a.style.maxHeight = open ? (a.scrollHeight + \'px\') : \'0px\';',
				'    var q = item.querySelector(\'.wgcr-faq-q\');',
				'    if (q) { q.setAttribute(\'aria-expanded\', open ? \'true\' : \'false\'); }',
				'  }',
				'  function init() {',
				'    document.querySelectorAll(\'.wgcr-faq\').forEach(function (root) {',
				'      if (root.getAttribute(\'data-wgcr-faq-init\') === \'1\') { return; }',
				'      root.setAttribute(\'data-wgcr-faq-init\', \'1\');',
				'      var sp = parseInt(root.getAttribute(\'data-speed\') || \'300\', 10);',
				'      root.style.setProperty(\'--wgcr-faq-speed\', (sp / 1000) + \'s\');',
				'      root.querySelectorAll(\'.wgcr-faq-item.open\').forEach(paint);',
				'    });',
				'  }',
				'  document.addEventListener(\'click\', function (e) {',
				'    var q = e.target && e.target.closest ? e.target.closest(\'.wgcr-faq-q\') : null;',
				'    if (!q) { return; }',
				'    var root = q.closest(\'.wgcr-faq\');',
				'    var item = q.closest(\'.wgcr-faq-item\');',
				'    if (!root || !item) { return; }',
				'    var willOpen = !item.classList.contains(\'open\');',
				'    if (root.getAttribute(\'data-mode\') !== \'toggle\') {',
				'      root.querySelectorAll(\'.wgcr-faq-item.open\').forEach(function (i) {',
				'        i.classList.remove(\'open\');',
				'        paint(i);',
				'      });',
				'    }',
				'    if (willOpen) { item.classList.add(\'open\'); } else { item.classList.remove(\'open\'); }',
				'    paint(item);',
				'  });',
				'  var moves = { ArrowDown: 1, Down: 1, ArrowUp: -1, Up: -1, Home: \'first\', End: \'last\' };',
				'  document.addEventListener(\'keydown\', function (e) {',
				'    var q = e.target && e.target.closest ? e.target.closest(\'.wgcr-faq-q\') : null;',
				'    if (!q || !Object.prototype.hasOwnProperty.call(moves, e.key)) { return; }',
				'    var root = q.closest(\'.wgcr-faq\');',
				'    if (!root) { return; }',
				'    var all = Array.prototype.slice.call(root.querySelectorAll(\'.wgcr-faq-q\'));',
				'    var i = all.indexOf(q);',
				'    if (i === -1) { return; }',
				'    var move = moves[e.key];',
				'    var n = move === \'first\' ? 0 : (move === \'last\' ? all.length - 1 : (i + move + all.length) % all.length);',
				'    e.preventDefault();',
				'    all[n].focus();',
				'  });',
				'  if (document.readyState === \'loading\') {',
				'    document.addEventListener(\'DOMContentLoaded\', init);',
				'  } else {',
				'    init();',
				'  }',
				'  function refit() {',
				'    document.querySelectorAll(\'.wgcr-faq-item.open .wgcr-faq-a\').forEach(function (a) {',
				'      a.style.maxHeight = a.scrollHeight + \'px\';',
				'    });',
				'  }',
				'  window.addEventListener(\'load\', function () {',
				'    init();',
				'    refit();',
				'  });',
				'  window.addEventListener(\'resize\', refit);',
				'})();',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_faq_register_assets' ) ) {
	function wgcr_faq_register_assets() {
		if ( wp_style_is( 'wgcr-faq', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-faq', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-faq', wgcr_faq_base_css() );
		wp_register_script( 'wgcr-faq', false, array(), WGCR_VER, true );
		wp_add_inline_script( 'wgcr-faq', wgcr_faq_base_js() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_faq_register_assets', 5 );

if ( ! class_exists( 'WGCR_FAQ_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_FAQ_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-faq'; }
		public function get_title() { return esc_html__( 'سوالات متداول - FAQ', 'widgetcore' ); }
		public function get_icon() { return 'eicon-accordion'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-faq' ); }
		public function get_script_depends() { return array( 'wgcr-faq' ); }
		public function get_keywords() { return array( 'faq', 'accordion', 'toggle', __( 'سوالات متداول', 'widgetcore' ), __( 'سوالات', 'widgetcore' ), __( 'آکاردئون', 'widgetcore' ) ); }

		protected function register_controls() {

			$this->start_controls_section( 'sec_items', array( 'label' => esc_html__( 'سوال‌ها', 'widgetcore' ) ) );
			$rep = new \Elementor\Repeater();
			$rep->add_control( 'question', array(
				'label'       => esc_html__( 'عنوان سوال', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'عنوان سوال', 'widgetcore' ),
				'label_block' => true,
			) );
			$rep->add_control( 'answer', array(
				'label'   => esc_html__( 'پاسخ', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __( 'متن پاسخ…', 'widgetcore' ),
			) );
			$rep->add_control( 'open_default', array(
				'label'        => esc_html__( 'باز بودن پیش‌فرض', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'widgetcore' ),
				'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
				'return_value' => 'yes',
				'default'      => '',
			) );
			$this->add_control( 'items', array(
				'label'       => esc_html__( 'آیتم‌ها', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => array(
					array(
						'question'     => __( 'دوره‌ها به چه صورت برگزار می‌شوند؟', 'widgetcore' ),
						'answer'       => __( 'همه دوره‌ها ویدیویی و آنلاین هستند. پس از خرید، از پنل کاربری خود به‌صورت مادام‌العمر به جلسه‌ها دسترسی دارید و می‌توانید با گوشی، تبلت یا کامپیوتر در هر ساعت از شبانه‌روز یاد بگیرید.', 'widgetcore' ),
						'open_default' => 'yes',
					),
					array(
						'question' => __( 'آیا پس از پایان دوره گواهی دریافت می‌کنم؟', 'widgetcore' ),
						'answer'   => __( 'بله. پس از اتمام جلسه‌ها و تحویل پروژه پایانی، گواهی پایان دوره با کد رهگیری اختصاصی صادر می‌شود که از بخش «دوره‌های من» در پنل کاربری قابل دریافت و چاپ است.', 'widgetcore' ),
					),
					array(
						'question' => __( 'اگر سوالی در طول دوره داشته باشم چه کنم؟', 'widgetcore' ),
						'answer'   => __( 'از طریق سیستم تیکت پشتیبانی در پنل کاربری سوال خود را مطرح کنید. کارشناسان ما معمولاً در کمتر از ۲۴ ساعت پاسخ می‌دهند و سوالات پرتکرار هر دوره هم در همان صفحه دوره پاسخ داده شده است.', 'widgetcore' ),
					),
					array(
						'question' => __( 'دسترسی به دوره‌ها چه مدت فعال است؟', 'widgetcore' ),
						'answer'   => __( 'دسترسی شما به محتوای خریداری‌شده مادام‌العمر است و هر زمان که دوره‌ای به‌روزرسانی شود، محتوای جدید به‌صورت رایگان برای شما فعال می‌شود.', 'widgetcore' ),
					),
					array(
						'question' => __( 'آیا امکان پرداخت قسطی وجود دارد؟', 'widgetcore' ),
						'answer'   => __( 'برای برخی دوره‌ها پرداخت قسطی (معمولاً در دو قسط) فراهم شده است. برای اطلاع از شرایط، قبل از خرید با پشتیبانی در ارتباط باشید یا از طریق صفحه تماس پیام بدهید.', 'widgetcore' ),
					),
				),
				'title_field' => '{{{ question }}}',
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sec_behavior', array( 'label' => esc_html__( 'تنظیمات رفتار', 'widgetcore' ) ) );
			$this->add_control( 'mode', array(
				'label'   => esc_html__( 'حالت باز شدن', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'accordion' => esc_html__( 'آکاردئون (فقط یک آیتم باز)', 'widgetcore' ),
					'toggle'    => esc_html__( 'آزاد (چند آیتم همزمان)', 'widgetcore' ),
				),
				'default' => 'accordion',
			) );
			$this->add_control( 'icon_type', array(
				'label'   => esc_html__( 'آیکون', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'plus'    => esc_html__( 'پلاس', 'widgetcore' ),
					'chevron' => esc_html__( 'فلش (شورون)', 'widgetcore' ),
					'custom'  => esc_html__( 'سفارشی (کتابخانه المنتور)', 'widgetcore' ),
				),
				'default' => 'plus',
			) );
			$this->add_control( 'custom_icon', array(
				'label'     => esc_html__( 'آیکون سفارشی', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				),
				'condition' => array( 'icon_type' => 'custom' ),
			) );
			$this->add_control( 'speed', array(
				'label'   => esc_html__( 'سرعت انیمیشن (میلی‌ثانیه)', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array(
						'min'  => 100,
						'max'  => 1000,
						'step' => 50,
					),
				),
				'default' => array(
					'size' => 300,
					'unit' => 'px',
				),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sec_seo', array( 'label' => esc_html__( 'سئو و ساختار', 'widgetcore' ) ) );
			$this->add_control( 'q_tag', array(
				'label'   => esc_html__( 'تگ عنوان سوال', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'h2'  => 'H2',
					'h3'  => esc_html__( 'H3 (پیشنهادی)', 'widgetcore' ),
					'h4'  => 'H4',
					'div' => esc_html__( 'DIV (بدون تگ هدینگ)', 'widgetcore' ),
				),
				'default' => 'h3',
			) );
			$this->add_control( 'schema', array(
				'label'        => esc_html__( 'اسکیمای FAQ (JSON-LD)', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'widgetcore' ),
				'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'خروجی ساخت‌یافته FAQPage برای موتورهای جستجو. اگر چند ویجت در صفحه باشد، فقط اولی اسکیما می‌دهد.', 'widgetcore' ),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sty_layout', array( 'label' => esc_html__( 'طرح کلی', 'widgetcore' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
			$this->wgcr_size_control( 'max_width', array(
				'label'      => esc_html__( 'حداکثر عرض', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'range'      => array(
					'px'  => array(
						'min'  => 280,
						'max'  => 1400,
						'step' => 10,
					),
					'%'   => array(
						'min' => 20,
						'max' => 100,
					),
					'em'  => array(
						'min' => 20,
						'max' => 90,
					),
					'rem' => array(
						'min' => 20,
						'max' => 90,
					),
				),
				'default'    => array(
					'size' => 760,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq' => 'max-width:{{SIZE}}{{UNIT}};' ),
			) );
			$this->add_control( 'faq_align', array(
				'label'                => esc_html__( 'تراز افقی ویجت', 'widgetcore' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => array(
					'right'  => array(
						'title' => esc_html__( 'راست', 'widgetcore' ),
						'icon'  => 'eicon-text-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'وسط', 'widgetcore' ),
						'icon'  => 'eicon-text-align-center',
					),
					'left'   => array(
						'title' => esc_html__( 'چپ', 'widgetcore' ),
						'icon'  => 'eicon-text-align-left',
					),
				),
				'default'              => 'center',
				'selectors_dictionary' => array(
					'right'  => 'margin-left:auto;margin-right:0;',
					'center' => 'margin-left:auto;margin-right:auto;',
					'left'   => 'margin-left:0;margin-right:auto;',
				),
				'selectors'            => array( '{{WRAPPER}} .wgcr-faq' => '{{VALUE}}' ),
			) );
			$this->wgcr_size_control( 'items_gap', array(
				'label'      => esc_html__( 'فاصله بین کارت‌ها', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 14,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-list' => 'gap:{{SIZE}}{{UNIT}};' ),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sty_body', array( 'label' => esc_html__( 'بدنه کارت', 'widgetcore' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
			$this->start_controls_tabs( 'body_tabs' );
			$this->start_controls_tab( 'body_closed', array( 'label' => esc_html__( 'بسته', 'widgetcore' ) ) );
			$this->add_control( 'box_bg', array(
				'label'     => esc_html__( 'پس‌زمینه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.72)',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-item' => 'background:{{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
				'name'           => 'box_border',
				'label'          => esc_html__( 'کادر', 'widgetcore' ),
				'selector'       => '{{WRAPPER}} .wgcr-faq-item',
				'fields_options' => array(
					'border' => array( 'default' => 'solid' ),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'unit'     => 'px',
							'isLinked' => true,
						),
					),
					'color'  => array( 'default' => 'rgba(255, 255, 255, 0.7)' ),
				),
			) );
			$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
				'name'     => 'box_shadow',
				'label'    => esc_html__( 'سایه', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-faq-item',
			) );
			$this->end_controls_tab();
			$this->start_controls_tab( 'body_open', array( 'label' => esc_html__( 'باز', 'widgetcore' ) ) );
			$this->add_control( 'open_bg', array(
				'label'     => esc_html__( 'پس‌زمینه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-item.open' => 'background:{{VALUE}};' ),
			) );
			$this->add_control( 'open_border', array(
				'label'     => esc_html__( 'رنگ کادر', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-item.open' => 'border-color:{{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
				'name'     => 'open_shadow',
				'label'    => esc_html__( 'سایه', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-faq-item.open',
			) );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->wgcr_size_control( 'glass_blur', array(
				'label'      => esc_html__( 'میزان تاری شیشه‌ای', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 24,
					),
				),
				'default'    => array(
					'size' => 12,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-item' => 'backdrop-filter:blur({{SIZE}}{{UNIT}});-webkit-backdrop-filter:blur({{SIZE}}{{UNIT}});' ),
			) );
			$this->wgcr_size_control( 'box_radius', array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'      => '12',
					'right'    => '12',
					'bottom'   => '12',
					'left'     => '12',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-item' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sty_title', array( 'label' => esc_html__( 'عنوان', 'widgetcore' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
			$this->start_controls_tabs( 'title_tabs' );
			$this->start_controls_tab( 'title_closed', array( 'label' => esc_html__( 'بسته', 'widgetcore' ) ) );
			$this->add_control( 'q_row_bg', array(
				'label'     => esc_html__( 'پس‌زمینه ردیف عنوان', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-q' => 'background:{{VALUE}};' ),
			) );
			$this->add_control( 'q_color', array(
				'label'     => esc_html__( 'رنگ متن', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1F2937',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-qtext' => 'color:{{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
				'name'     => 'q_row_shadow',
				'label'    => esc_html__( 'سایه', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-faq-q',
			) );
			$this->end_controls_tab();
			$this->start_controls_tab( 'title_open', array( 'label' => esc_html__( 'باز', 'widgetcore' ) ) );
			$this->add_control( 'q_row_bg_open', array(
				'label'     => esc_html__( 'پس‌زمینه ردیف عنوان', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-item.open .wgcr-faq-q' => 'background:{{VALUE}};' ),
			) );
			$this->add_control( 'q_color_open', array(
				'label'     => esc_html__( 'رنگ متن', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-item.open .wgcr-faq-qtext' => 'color:{{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
				'name'     => 'q_row_shadow_open',
				'label'    => esc_html__( 'سایه', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-faq-item.open .wgcr-faq-q',
			) );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->add_control( 'q_color_hover', array(
				'label'     => esc_html__( 'رنگ متن (هاور)', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-q:hover .wgcr-faq-qtext' => 'color:{{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
				'name'           => 'q_typo',
				'label'          => esc_html__( 'تایپوگرافی', 'widgetcore' ),
				'selector'       => '{{WRAPPER}} .wgcr-faq-qtext',
				'fields_options' => array(
					'font_weight' => array( 'default' => '700' ),
					'font_size'   => array(
						'default' => array(
							'size' => 16,
							'unit' => 'px',
						),
					),
				),
			) );
			$this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
				'name'     => 'q_row_border',
				'label'    => esc_html__( 'کادر ردیف عنوان', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-faq-q',
			) );
			$this->wgcr_size_control( 'q_row_radius', array(
				'label'      => esc_html__( 'گردی گوشه‌های ردیف عنوان', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 28,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-q' => 'border-radius:{{SIZE}}{{UNIT}};' ),
			) );
			$this->wgcr_size_control( 'q_padding', array(
				'label'      => esc_html__( 'پدینگ', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'      => '20',
					'right'    => '24',
					'bottom'   => '20',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-q' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sty_answer', array( 'label' => esc_html__( 'کارت توضیحات', 'widgetcore' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
			$this->start_controls_tabs( 'ans_tabs' );
			$this->start_controls_tab( 'ans_closed', array( 'label' => esc_html__( 'بسته', 'widgetcore' ) ) );
			$this->add_control( 'a_bg', array(
				'label'     => esc_html__( 'پس‌زمینه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-a' => 'background:{{VALUE}};' ),
			) );
			$this->add_control( 'a_color', array(
				'label'     => esc_html__( 'رنگ متن', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#6B7280',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-atext, {{WRAPPER}} .wgcr-faq-atext p' => 'color:{{VALUE}};' ),
			) );
			$this->end_controls_tab();
			$this->start_controls_tab( 'ans_open', array( 'label' => esc_html__( 'باز', 'widgetcore' ) ) );
			$this->add_control( 'a_bg_open', array(
				'label'     => esc_html__( 'پس‌زمینه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-item.open .wgcr-faq-a' => 'background:{{VALUE}};' ),
			) );
			$this->add_control( 'a_color_open', array(
				'label'     => esc_html__( 'رنگ متن', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-item.open .wgcr-faq-atext, {{WRAPPER}} .wgcr-faq-item.open .wgcr-faq-atext p' => 'color:{{VALUE}};' ),
			) );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
				'name'           => 'a_typo',
				'label'          => esc_html__( 'تایپوگرافی', 'widgetcore' ),
				'selector'       => '{{WRAPPER}} .wgcr-faq-atext, {{WRAPPER}} .wgcr-faq-atext p',
				'fields_options' => array(
					'font_size' => array(
						'default' => array(
							'size' => 15,
							'unit' => 'px',
						),
					),
				),
			) );
			$this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
				'name'     => 'a_border',
				'label'    => esc_html__( 'کادر', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-faq-a',
			) );
			$this->wgcr_size_control( 'a_radius', array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 24,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-a' => 'border-radius:{{SIZE}}{{UNIT}};' ),
			) );
			$this->wgcr_size_control( 'a_padding', array(
				'label'      => esc_html__( 'پدینگ', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'      => '0',
					'right'    => '24',
					'bottom'   => '22',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-atext' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sty_icon', array( 'label' => esc_html__( 'آیکون', 'widgetcore' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
			$this->wgcr_size_control( 'ic_gap', array(
				'label'      => esc_html__( 'فاصله از متن', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-q' => 'gap:{{SIZE}}{{UNIT}};' ),
			) );
			$this->wgcr_size_control( 'ic_box', array(
				'label'      => esc_html__( 'اندازه باکس', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 56,
					),
				),
				'default'    => array(
					'size' => 30,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-ic' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};' ),
			) );
			$this->wgcr_size_control( 'ic_size', array(
				'label'      => esc_html__( 'اندازه آیکون', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 32,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wgcr-faq-ic svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wgcr-faq-ic i'   => 'font-size:{{SIZE}}{{UNIT}};',
				),
			) );
			$this->wgcr_size_control( 'ic_radius', array(
				'label'      => esc_html__( 'گردی باکس', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 28,
					),
				),
				'default'    => array(
					'size' => 8,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-ic' => 'border-radius:{{SIZE}}{{UNIT}};' ),
			) );
			$this->add_control( 'custom_rotate', array(
				'label'      => esc_html__( 'چرخش آیکون سفارشی در حالت باز (درجه)', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 5,
					),
				),
				'default'    => array(
					'size' => 180,
					'unit' => 'deg',
				),
				'selectors'  => array( '{{WRAPPER}} .wgcr-faq-item[data-icon="custom"].open .wgcr-faq-ic' => 'transform:rotate({{SIZE}}{{UNIT}});' ),
				'condition'  => array( 'icon_type' => 'custom' ),
			) );
			$this->start_controls_tabs( 'ic_tabs' );
			$this->start_controls_tab( 'ic_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->add_control( 'ic_bg', array(
				'label'     => esc_html__( 'پس‌زمینه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#EBF3FC',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-ic' => 'background:{{VALUE}};' ),
			) );
			$this->add_control( 'ic_color', array(
				'label'     => esc_html__( 'رنگ آیکون', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#2E86DE',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-ic' => 'color:{{VALUE}};' ),
			) );
			$this->end_controls_tab();
			$this->start_controls_tab( 'ic_open', array( 'label' => esc_html__( 'حالت باز', 'widgetcore' ) ) );
			$this->add_control( 'ic_bg_open', array(
				'label'     => esc_html__( 'پس‌زمینه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#2E86DE',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-item.open .wgcr-faq-ic' => 'background:{{VALUE}};' ),
			) );
			$this->add_control( 'ic_color_open', array(
				'label'     => esc_html__( 'رنگ آیکون', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .wgcr-faq-item.open .wgcr-faq-ic' => 'color:{{VALUE}};' ),
			) );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->end_controls_section();

		}

		protected function render() {
			$s = $this->get_settings_for_display();
			wgcr_faq_register_assets();
			wp_enqueue_style( 'wgcr-faq' );
			wp_enqueue_script( 'wgcr-faq' );
			$mode  = ( isset( $s['mode'] ) && 'toggle' === $s['mode'] ) ? 'toggle' : 'accordion';
			$speed = isset( $s['speed']['size'] ) ? (int) $s['speed']['size'] : 300;
			$icon  = ( isset( $s['icon_type'] ) && in_array( $s['icon_type'], array( 'chevron', 'custom' ), true ) ) ? $s['icon_type'] : 'plus';
			$qtag  = ( isset( $s['q_tag'] ) && in_array( $s['q_tag'], array( 'h2', 'h3', 'h4', 'div' ), true ) ) ? $s['q_tag'] : 'h3';
			$items = ( isset( $s['items'] ) && is_array( $s['items'] ) ) ? $s['items'] : array();

			echo '<div class="wgcr-faq" data-mode="' . esc_attr( $mode ) . '" data-speed="' . esc_attr( $speed ) . '">';
			echo '<div class="wgcr-faq-list">';
			foreach ( $items as $i => $it ) {
				$open = ( ! empty( $it['open_default'] ) && 'yes' === $it['open_default'] ) ? ' open' : '';
				$qk   = $this->get_repeater_setting_key( 'question', 'items', $i );
				$this->add_inline_editing_attributes( $qk, 'none' );
				$ak = $this->get_repeater_setting_key( 'answer', 'items', $i );
				$this->add_inline_editing_attributes( $ak, 'advanced' );
				$q   = isset( $it['question'] ) ? $it['question'] : '';
				$a   = isset( $it['answer'] ) ? $it['answer'] : '';
				$uid = $this->get_id() . '-' . $i;
				$exp = ( '' !== $open ) ? 'true' : 'false';
				echo '<div class="wgcr-faq-item' . esc_attr( $open ) . '" data-icon="' . esc_attr( $icon ) . '">';
				echo '<' . tag_escape( $qtag ) . ' class="wgcr-faq-qwrap"><button type="button" class="wgcr-faq-q" id="wgcr-faq-q-' . esc_attr( $uid ) . '" aria-expanded="' . esc_attr( $exp ) . '" aria-controls="wgcr-faq-a-' . esc_attr( $uid ) . '">';
				echo '<span class="wgcr-faq-qtext" ';
				$this->print_render_attribute_string( $qk );
				echo '>' . esc_html( $q ) . '</span>';
				echo '<span class="wgcr-faq-ic">';
				if ( 'custom' === $icon && ! empty( $s['custom_icon']['value'] ) ) {
					\Elementor\Icons_Manager::render_icon( $s['custom_icon'], array( 'aria-hidden' => 'true' ) );
				} else {
					echo wp_kses( wgcr_svg_icon( $icon ), wgcr_svg_kses_base() );
				}
				echo '</span>';
				echo '</button></' . tag_escape( $qtag ) . '>';
				echo '<div class="wgcr-faq-a" role="region" id="wgcr-faq-a-' . esc_attr( $uid ) . '" aria-labelledby="wgcr-faq-q-' . esc_attr( $uid ) . '"><div class="wgcr-faq-atext" ';
				$this->print_render_attribute_string( $ak );
				echo '>' . wp_kses_post( $a ) . '</div></div>';
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';

			if ( ! empty( $s['schema'] ) && 'yes' === $s['schema'] ) {
				static $schema_done = false;
				if ( ! $schema_done ) {
					$schema_done = true;
					$entities    = array();
					foreach ( $items as $sit ) {
						$sq = trim( wp_strip_all_tags( isset( $sit['question'] ) ? $sit['question'] : '' ) );
						$sa = trim( wp_strip_all_tags( isset( $sit['answer'] ) ? $sit['answer'] : '' ) );
						if ( '' === $sq || '' === $sa ) {
							continue;
						}
						$entities[] = array(
							'@type'          => 'Question',
							'name'           => $sq,
							'acceptedAnswer' => array(
								'@type' => 'Answer',
								'text'  => $sa,
							),
						);
					}
					if ( ! empty( $entities ) ) {
						wp_print_inline_script_tag(
							wp_json_encode(
								array(
									'@context'   => 'https://schema.org',
									'@type'      => 'FAQPage',
									'mainEntity' => $entities,
								),
								JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
							),
							array( 'type' => 'application/ld+json' )
						);
					}
				}
			}
		}

		protected function content_template() {
			?>
<#
var mode  = settings.mode === 'toggle' ? 'toggle' : 'accordion';
var speed = settings.speed && settings.speed.size ? settings.speed.size : 300;
var icon  = 'plus';
if (settings.icon_type === 'chevron') { icon = 'chevron'; }
if (settings.icon_type === 'custom') { icon = 'custom'; }
var plusSvg = <?php echo wp_json_encode( wgcr_svg_icon( 'plus' ), JSON_HEX_TAG ); ?>;
var chevSvg = <?php echo wp_json_encode( wgcr_svg_icon( 'chevron' ), JSON_HEX_TAG ); ?>;
var ic = icon === 'chevron' ? chevSvg : plusSvg;
var qtag = (settings.q_tag === 'h2' || settings.q_tag === 'h4' || settings.q_tag === 'div') ? settings.q_tag : 'h3';
if (icon === 'custom') {
	try {
		if (typeof elementor !== 'undefined' && elementor.helpers && elementor.helpers.renderIcon && settings.custom_icon && settings.custom_icon.value) {
			var rendered = elementor.helpers.renderIcon(view, settings.custom_icon, { 'aria-hidden': 'true' }, 'i', 'object');
			if (rendered) { ic = (typeof rendered === 'string') ? rendered : (rendered.value || ic); }
		}
	} catch (e) { ic = plusSvg; }
}
#>
<div class="wgcr-faq" data-mode="{{{ mode }}}" data-speed="{{{ speed }}}">
	<div class="wgcr-faq-list">
	<# _.each(settings.items, function(item, idx) {
		var oc = item.open_default === 'yes' ? ' open' : '';
		var uid = view.cid + '-' + idx;
		var exp = item.open_default === 'yes' ? 'true' : 'false';
		var qKey = view.getRepeaterSettingKey('question', 'items', idx);
		var aKey = view.getRepeaterSettingKey('answer', 'items', idx);
		view.addInlineEditingAttributes(qKey, 'none');
		view.addInlineEditingAttributes(aKey, 'advanced');
	#>
		<div class="wgcr-faq-item{{{ oc }}}" data-icon="{{{ icon }}}">
			<{{{ qtag }}} class="wgcr-faq-qwrap"><button type="button" class="wgcr-faq-q" id="wgcr-faq-q-{{{ uid }}}" aria-expanded="{{{ exp }}}" aria-controls="wgcr-faq-a-{{{ uid }}}">
				<span class="wgcr-faq-qtext" {{{ view.getRenderAttributeString(qKey) }}}>{{ item.question }}</span>
				<span class="wgcr-faq-ic">{{{ ic }}}</span>
			</button></{{{ qtag }}}>
			<div class="wgcr-faq-a" role="region" id="wgcr-faq-a-{{{ uid }}}" aria-labelledby="wgcr-faq-q-{{{ uid }}}" <# if (item.open_default === 'yes') { #> style="max-height:none"<# } #>><div class="wgcr-faq-atext" {{{ view.getRenderAttributeString(aKey) }}}>{{{ item.answer }}}</div></div>
		</div>
	<# }); #>
	</div>
</div>
			<?php
		}
	}
}
