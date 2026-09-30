<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WGCR_Search_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Search_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-search'; }
		public function get_title() { return esc_html__( 'جستجوی زنده', 'widgetcore' ); }
		public function get_icon() { return 'eicon-search'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_keywords() { return array( 'search', 'live', 'ajax', __( 'جستجو', 'widgetcore' ), __( 'سرچ', 'widgetcore' ), __( 'دوره', 'widgetcore' ), __( 'مقاله', 'widgetcore' ) ); }
		public function get_style_depends() { return array( 'wgcr-search' ); }
		public function get_script_depends() { return array( 'wgcr-search' ); }

		protected function options_needed() {
			if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
				return true;
			}
			$elementor = \Elementor\Plugin::$instance;
			return ( isset( $elementor->editor ) && $elementor->editor->is_edit_mode() ) || ( isset( $elementor->preview ) && $elementor->preview->is_preview_mode() );
		}

		protected function get_post_type_options() {
			$options = array();
			$types   = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'objects' );
			unset( $types['attachment'], $types['elementor_library'] );
			foreach ( $types as $pt ) {
				$options[ $pt->name ] = $pt->labels->name;
			}
			if ( ! isset( $options['post'] ) ) {
				$options['post'] = __( 'نوشته‌ها', 'widgetcore' );
			}
			ksort( $options );
			return $options;
		}

		protected function get_template_options() {
			$options = array( '' => esc_html__( 'قالب پیش‌فرض', 'widgetcore' ) );
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

		protected function get_term_options() {
			$options = array();
			if ( ! $this->options_needed() ) {
				return $options;
			}
			$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
			unset( $taxonomies['post_format'] );
			foreach ( $taxonomies as $tax ) {
				$terms = get_terms(
					array(
						'taxonomy'   => $tax->name,
						'hide_empty' => false,
						'number'     => 200,
					)
				);
				if ( is_wp_error( $terms ) ) {
					continue;
				}
				foreach ( $terms as $term ) {
					$options[ $tax->name . ':' . (int) $term->term_id ] = $tax->labels->singular_name . ': ' . $term->name;
				}
			}
			return $options;
		}

		protected function register_controls() {

			$this->start_controls_section( 'sec_general', array( 'label' => esc_html__( 'عمومی', 'widgetcore' ) ) );
			$this->add_control( 'display_mode', array(
				'label'       => esc_html__( 'حالت نمایش نتایج', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'modal' => esc_html__( 'حالت لیست', 'widgetcore' ),
					'page'  => esc_html__( 'حالت نمایش در صفحه', 'widgetcore' ),
				),
				'default'     => 'modal',
				'label_block' => true,
				'description' => esc_html__( '«حالت لیست»: نتایج در یک پنل بازشو زیر فیلد جستجو. «حالت نمایش در صفحه»: نتایج در یک ناحیه‌ی همیشه‌باز داخل همین ویجت.', 'widgetcore' ),
			) );
			$this->add_control( 'placeholder', array(
				'label'       => esc_html__( 'متن راهنما', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'جستجو در مقالات…', 'widgetcore' ),
				'label_block' => true,
			) );
			$this->add_control( 'thumb', array(
				'label'        => esc_html__( 'نمایش تصویر', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'widgetcore' ),
				'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			) );
			$this->add_control( 'excerpt', array(
				'label'        => esc_html__( 'نمایش خلاصه', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'widgetcore' ),
				'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			) );
			$this->add_control( 'all_text', array(
				'label'       => esc_html__( 'متن «نمایش همه»', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'نمایش همه نتایج', 'widgetcore' ),
				'label_block' => true,
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sec_layout', array( 'label' => esc_html__( 'طرح‌بندی', 'widgetcore' ) ) );
			$this->add_control( 'template_mode', array(
				'label'       => esc_html__( 'انتخاب قالب', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__( 'قالب پیش‌فرض', 'widgetcore' ),
					'custom'  => esc_html__( 'سفارشی', 'widgetcore' ),
				),
				'default'     => 'default',
				'label_block' => true,
				'description' => esc_html__( '«قالب پیش‌فرض»: طراحی داخلی افزونه. «سفارشی»: یک قالب ذخیره‌شده در کتابخانه المنتور.', 'widgetcore' ),
			) );
			$this->add_control( 'template', array(
				'label'       => esc_html__( 'یک قالب انتخاب کنید', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => $this->get_template_options(),
				'default'     => '',
				'label_block' => true,
				'condition'   => array( 'template_mode' => 'custom' ),
			) );
			$this->add_control( 'edit_template_link', array(
				'type'      => \Elementor\Controls_Manager::RAW_HTML,
				'raw'       => '<a class="elementor-button elementor-button-default" style="display:block;text-align:center;" target="_blank" href="' . esc_url( admin_url( 'edit.php?post_type=elementor_library' ) ) . '">' . esc_html__( 'ویرایش قالب', 'widgetcore' ) . '</a>',
				'condition' => array( 'template_mode' => 'custom' ),
			) );
			$this->add_responsive_control( 'columns', array(
				'label'          => esc_html__( 'ستون‌ها', 'widgetcore' ),
				'type'           => \Elementor\Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'step'           => 1,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'description'    => esc_html__( 'تعداد ستون‌های شبکه‌ی نتایج در «حالت نمایش در صفحه».', 'widgetcore' ),
				'render_type'    => 'template',
				'condition'      => array( 'display_mode' => 'page' ),
				'selectors'      => array( '{{WRAPPER}} .wgcr-search--page' => '--wgcr-search-cols:{{VALUE}};' ),
			) );
			$this->add_control( 'posts_per_page', array(
				'label'        => esc_html__( 'آیتم در هر برگه', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::NUMBER,
				'min'          => 1,
				'max'          => WGCR_SEARCH_MAX_RESULTS,
				'step'         => 1,
				'default'      => 12,
				'save_default' => true,
				'description'  => esc_html__( 'تعداد نتایجی که برای هر جستجو دریافت و نمایش داده می‌شود.', 'widgetcore' ),
			) );
			$this->add_control( 'masonry', array(
				'label'        => esc_html__( 'کاشی', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'widgetcore' ),
				'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'چیدمان آجری (Masonry): کارت‌ها با ارتفاع متفاوت، بدون فضای خالی، زیر هم چیده می‌شوند.', 'widgetcore' ),
				'condition'    => array( 'display_mode' => 'page' ),
			) );
			$this->add_control( 'equal_height', array(
				'label'        => esc_html__( 'ارتفاع برابر', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'widgetcore' ),
				'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'همه‌ی کارت‌های یک ردیف هم‌ارتفاع می‌شوند.', 'widgetcore' ),
				'condition'    => array(
					'display_mode' => 'page',
					'masonry!'     => 'yes',
				),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sec_query', array( 'label' => esc_html__( 'کوئری', 'widgetcore' ) ) );
			$this->add_control( 'source', array(
				'label'       => esc_html__( 'منبع', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $this->get_post_type_options() + array( 'custom' => esc_html__( 'پست‌تایپ سفارشی', 'widgetcore' ) ),
				'default'     => 'post',
				'label_block' => true,
				'description' => esc_html__( 'پست‌تایپ اصلی برای جستجو. با «پست‌تایپ سفارشی» می‌توانید اسلاگ دلخواه وارد کنید.', 'widgetcore' ),
			) );
			$this->add_control( 'custom_type', array(
				'label'       => esc_html__( 'پست‌تایپ سفارشی', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'مثلاً sfwd-courses', 'widgetcore' ),
				'description' => esc_html__( 'اسلاگ پست‌تایپ دلخواه. فقط پست‌تایپ‌های عمومیِ قابل جستجو مجازند (در غیر این صورت: نوشته‌ها).', 'widgetcore' ),
				'label_block' => true,
				'condition'   => array( 'source' => 'custom' ),
			) );
			$this->add_control( 'include_by', array(
				'label'       => esc_html__( 'شامل توسط', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::CHOOSE,
				'options'     => array(
					'exclude' => array(
						'title' => esc_html__( 'استثنا', 'widgetcore' ),
						'icon'  => 'eicon-ban',
					),
					'include' => array(
						'title' => esc_html__( 'شامل', 'widgetcore' ),
						'icon'  => 'eicon-check',
					),
				),
				'default'     => 'exclude',
				'toggle'      => false,
				'description' => esc_html__( '«استثنا»: نتایج فقط شامل آیتم‌هایی که در لیست نیستند. «شامل»: فقط آیتم‌های داخل لیست.', 'widgetcore' ),
			) );
			$this->add_control( 'include_terms', array(
				'label'       => esc_html__( 'دسته‌ها و برچسب‌ها', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => $this->get_term_options(),
				'default'     => array(),
				'multiple'    => true,
				'label_block' => true,
				'description' => esc_html__( 'ترم‌های موردنظر از همه‌ی طبقه‌بندی‌های عمومی (دسته، برچسب، …) را جستجو و انتخاب کنید. خالی = بدون فیلتر.', 'widgetcore' ),
			) );
			$this->add_control( 'date_filter', array(
				'label'       => esc_html__( 'تاریخ', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'all'   => esc_html__( 'همه', 'widgetcore' ),
					'week'  => esc_html__( 'هفته', 'widgetcore' ),
					'month' => esc_html__( 'ماه', 'widgetcore' ),
					'year'  => esc_html__( 'سال', 'widgetcore' ),
				),
				'default'     => 'all',
				'label_block' => true,
				'description' => esc_html__( 'محدوده‌ی زمانی نتایج. پیش‌فرض: همه.', 'widgetcore' ),
			) );
			$this->add_control( 'orderby', array(
				'label'       => esc_html__( 'ترتیب بر اساس', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'relevance'  => esc_html__( 'ارتباط با عبارت جستجو', 'widgetcore' ),
					'date'       => esc_html__( 'تاریخ', 'widgetcore' ),
					'title'      => esc_html__( 'عنوان', 'widgetcore' ),
					'author'     => esc_html__( 'نویسنده', 'widgetcore' ),
					'rand'       => esc_html__( 'تصادفی', 'widgetcore' ),
					'menu_order' => esc_html__( 'سفارش منو', 'widgetcore' ),
				),
				'default'     => 'relevance',
				'label_block' => true,
			) );
			$this->add_control( 'order', array(
				'label'       => esc_html__( 'سفارش', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'DESC' => esc_html__( 'نزولی', 'widgetcore' ),
					'ASC'  => esc_html__( 'صعودی', 'widgetcore' ),
				),
				'default'     => 'DESC',
				'label_block' => true,
				'condition'   => array( 'orderby!' => array( 'relevance', 'rand' ) ),
			) );
			$this->add_control( 'ignore_sticky', array(
				'label'        => esc_html__( 'نادیده گرفتن نوشته‌های چسبنده', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'widgetcore' ),
				'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'اگر خاموش باشد، نوشته‌های چسبنده (Sticky) در ابتدای نتایج قرار می‌گیرند.', 'widgetcore' ),
				'condition'    => array( 'source' => 'post' ),
			) );
			$this->add_control( 'query_id', array(
				'label'       => esc_html__( 'کوئری ID', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'my_search_query', 'widgetcore' ),
				'description' => esc_html__( 'شناسه‌ی یکتا برای فیلتر کردن آرگومان‌های کوئری در سمت سرور با هوک wgcr_search_query_args/{query_id}.', 'widgetcore' ),
				'label_block' => true,
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sec_pagination', array( 'label' => esc_html__( 'صفحه‌بندی', 'widgetcore' ) ) );
			$this->add_control( 'pagination_type', array(
				'label'       => esc_html__( 'صفحه‌بندی', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'none'           => esc_html__( 'هیچکدام', 'widgetcore' ),
					'numbers'        => esc_html__( 'شماره‌ها', 'widgetcore' ),
					'prev_next'      => esc_html__( 'قبلی / بعدی', 'widgetcore' ),
					'load_on_click'  => esc_html__( 'بارگذاری با کلیک', 'widgetcore' ),
					'load_on_scroll' => esc_html__( 'بارگذاری با اسکرول', 'widgetcore' ),
				),
				'default'     => 'none',
				'label_block' => true,
				'description' => esc_html__( 'نتایج بیشتر از «آیتم در هر برگه» به صفحه‌های بعدی می‌روند و داخل همان کادر نتایج بارگذاری می‌شوند.', 'widgetcore' ),
			) );
			$this->add_responsive_control( 'pagination_align', array(
				'label'                => esc_html__( 'چیدمان', 'widgetcore' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => array(
					'right'   => array(
						'title' => esc_html__( 'راست', 'widgetcore' ),
						'icon'  => 'eicon-h-align-right',
					),
					'center'  => array(
						'title' => esc_html__( 'وسط', 'widgetcore' ),
						'icon'  => 'eicon-h-align-center',
					),
					'left'    => array(
						'title' => esc_html__( 'چپ', 'widgetcore' ),
						'icon'  => 'eicon-h-align-left',
					),
					'justify' => array(
						'title' => esc_html__( 'کشیده', 'widgetcore' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default'              => 'center',
				'toggle'               => false,
				'selectors_dictionary' => array(
					'right'   => 'right',
					'center'  => 'center',
					'left'    => 'left',
					'justify' => 'stretch',
				),
				'selectors'            => array( '{{WRAPPER}} .wgcr-search-foot' => 'justify-items:{{VALUE}};' ),
				'condition'            => array( 'pagination_type!' => 'none' ),
			) );
			$this->add_control( 'pagination_spacer', array(
				'label'     => esc_html__( 'اسپیسر', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'با', 'widgetcore' ),
						'icon'  => 'eicon-spacer',
					),
					'no'  => array(
						'title' => esc_html__( 'بدون', 'widgetcore' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'no',
				'toggle'    => false,
				'condition' => array( 'pagination_type!' => 'none' ),
			) );
			$this->add_control( 'button_heading', array(
				'label'     => esc_html__( 'دکمه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'pagination_type' => 'load_on_click' ),
			) );
			$this->add_control( 'button_text', array(
				'label'       => esc_html__( 'متن دکمه', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'مشاهده مقالات بیشتر', 'widgetcore' ),
				'label_block' => true,
				'condition'   => array( 'pagination_type' => 'load_on_click' ),
			) );
			$this->add_control( 'button_icon', array(
				'label'     => esc_html__( 'آیکن', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'نمایش', 'widgetcore' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'مخفی', 'widgetcore' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'yes',
				'toggle'    => false,
				'condition' => array( 'pagination_type' => 'load_on_click' ),
			) );
			$this->add_control( 'button_id', array(
				'label'       => esc_html__( 'شناسه دکمه', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'اطمینان حاصل کنید که شناسه (ID) یکتا بوده و در جای دیگری از برگه استفاده نشده است. فقط حروف انگلیسی، عدد و زیرخط بدون فاصله مجاز است.', 'widgetcore' ),
				'label_block' => true,
				'condition'   => array( 'pagination_type' => 'load_on_click' ),
			) );
			$this->add_control( 'no_posts_heading', array(
				'label'     => esc_html__( 'پیام نوشته‌های بیشتر موجود نیست', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'pagination_type' => array( 'load_on_click', 'load_on_scroll' ) ),
			) );
			$this->add_responsive_control( 'no_posts_align', array(
				'label'                => esc_html__( 'چیدمان', 'widgetcore' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => array(
					'right'   => array(
						'title' => esc_html__( 'راست', 'widgetcore' ),
						'icon'  => 'eicon-h-align-right',
					),
					'center'  => array(
						'title' => esc_html__( 'وسط', 'widgetcore' ),
						'icon'  => 'eicon-h-align-center',
					),
					'left'    => array(
						'title' => esc_html__( 'چپ', 'widgetcore' ),
						'icon'  => 'eicon-h-align-left',
					),
					'justify' => array(
						'title' => esc_html__( 'کشیده', 'widgetcore' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default'              => 'center',
				'toggle'               => false,
				'selectors_dictionary' => array(
					'right'   => 'right',
					'center'  => 'center',
					'left'    => 'left',
					'justify' => 'justify',
				),
				'selectors'            => array( '{{WRAPPER}} .wgcr-search-nomore' => 'text-align:{{VALUE}};' ),
				'condition'            => array( 'pagination_type' => array( 'load_on_click', 'load_on_scroll' ) ),
			) );
			$this->add_control( 'custom_message', array(
				'label'        => esc_html__( 'پیام سفارشی', 'widgetcore' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'widgetcore' ),
				'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'pagination_type' => array( 'load_on_click', 'load_on_scroll' ) ),
			) );
			$this->add_control( 'custom_message_text', array(
				'label'     => esc_html__( 'متن پیام سفارشی', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => '',
				'condition' => array(
					'pagination_type' => array( 'load_on_click', 'load_on_scroll' ),
					'custom_message'  => 'yes',
				),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sty_field', array( 'label' => esc_html__( 'فیلد جستجو', 'widgetcore' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
				'name'     => 'field_typo',
				'label'    => esc_html__( 'تایپوگرافی', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-search .wgcr-search-input',
			) );
			$this->add_control( 'field_color', array(
				'label'     => esc_html__( 'رنگ متن', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-input' => 'color:{{VALUE}};' ),
			) );
			$this->add_control( 'field_ph_color', array(
				'label'     => esc_html__( 'رنگ متن راهنما', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-input::placeholder' => 'color:{{VALUE}};' ),
			) );
			$this->add_control( 'field_bg', array(
				'label'     => esc_html__( 'پس‌زمینه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-input' => 'background-color:{{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
				'name'     => 'field_border',
				'label'    => esc_html__( 'کادر', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-search .wgcr-search-input',
			) );
			$this->add_control( 'field_focus_border', array(
				'label'     => esc_html__( 'رنگ کادر (فوکوس)', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-input:focus' => 'border-color:{{VALUE}};' ),
			) );
			$this->wgcr_size_control( 'field_radius', array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array( '{{WRAPPER}} .wgcr-search .wgcr-search-input' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			) );
			$this->wgcr_size_control( 'field_padding', array(
				'label'      => esc_html__( 'پدینگ', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .wgcr-search .wgcr-search-input' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			) );
			$this->add_control( 'icon_heading', array(
				'label'     => esc_html__( 'آیکون', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			) );
			$this->add_control( 'icon_color', array(
				'label'     => esc_html__( 'رنگ آیکون', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-icon' => 'color:{{VALUE}};' ),
			) );
			$this->wgcr_size_control( 'icon_size', array(
				'label'      => esc_html__( 'اندازه آیکون', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .wgcr-search .wgcr-search-icon svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};' ),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sty_panel', array( 'label' => esc_html__( 'کادر نتایج', 'widgetcore' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
			$this->add_control( 'panel_bg', array(
				'label'     => esc_html__( 'پس‌زمینه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-panel' => 'background-color:{{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
				'name'     => 'panel_border',
				'label'    => esc_html__( 'کادر', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-search .wgcr-search-panel',
			) );
			$this->wgcr_size_control( 'panel_radius', array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .wgcr-search .wgcr-search-panel' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
				'name'     => 'panel_shadow',
				'label'    => esc_html__( 'سایه', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-search .wgcr-search-panel',
			) );
			$this->add_control( 'page_container_heading', array(
				'label'     => esc_html__( 'کانتینر نتایج (حالت نمایش در صفحه)', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->wgcr_size_control( 'page_container_width', array(
				'label'      => esc_html__( 'عرض', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 100, 'max' => 1600, 'step' => 1 ),
					'%'  => array( 'min' => 10, 'max' => 100, 'step' => 1 ),
				),
				'default'    => array( 'unit' => '%', 'size' => 100 ),
				'selectors'  => array(
					'{{WRAPPER}} .wgcr-search--page .wgcr-search-panel' => 'width:{{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->wgcr_size_control( 'page_container_height', array(
				'label'      => esc_html__( 'حداقل ارتفاع', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 100, 'max' => 1200, 'step' => 10 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 300 ),
				'selectors'  => array(
					'{{WRAPPER}} .wgcr-search--page .wgcr-search-panel' => 'min-height:{{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->wgcr_size_control( 'page_container_max_height', array(
				'label'      => esc_html__( 'حداکثر ارتفاع', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 100, 'max' => 1600, 'step' => 10 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wgcr-search--page .wgcr-search-panel' => 'max-height:{{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->wgcr_size_control( 'page_container_padding', array(
				'label'      => esc_html__( 'پدینگ', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .wgcr-search--page .wgcr-search-panel' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->wgcr_size_control( 'page_container_margin', array(
				'label'      => esc_html__( 'مارجین', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .wgcr-search--page .wgcr-search-panel' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->add_control( 'page_container_bg', array(
				'label'     => esc_html__( 'پس‌زمینه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search--page .wgcr-search-panel' => 'background-color:{{VALUE}};' ),
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
				'name'      => 'page_container_border',
				'label'     => esc_html__( 'کادر', 'widgetcore' ),
				'selector'  => '{{WRAPPER}} .wgcr-search--page .wgcr-search-panel',
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->wgcr_size_control( 'page_container_radius', array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'widgetcore' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array( '{{WRAPPER}} .wgcr-search--page .wgcr-search-panel' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->add_control( 'page_container_align', array(
				'label'       => esc_html__( 'تراز افقی', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::CHOOSE,
				'options'     => array(
					'right'   => array(
						'title' => esc_html__( 'راست', 'widgetcore' ),
						'icon'  => 'eicon-h-align-right',
					),
					'center'  => array(
						'title' => esc_html__( 'وسط', 'widgetcore' ),
						'icon'  => 'eicon-h-align-center',
					),
					'left'    => array(
						'title' => esc_html__( 'چپ', 'widgetcore' ),
						'icon'  => 'eicon-h-align-left',
					),
					'justify' => array(
						'title' => esc_html__( 'کشیده', 'widgetcore' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default'     => 'justify',
				'toggle'      => false,
				'selectors_dictionary' => array(
					'right'   => 'margin-left:auto;margin-right:0;',
					'center'  => 'margin-left:auto;margin-right:auto;',
					'left'    => 'margin-right:auto;margin-left:0;',
					'justify' => 'margin-left:0;margin-right:0;',
				),
				'selectors'   => array(
					'{{WRAPPER}} .wgcr-search--page .wgcr-search-panel' => '{{VALUE}}',
				),
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
				'name'      => 'page_container_typography',
				'label'     => esc_html__( 'تایپوگرافی وضعیت', 'widgetcore' ),
				'selector'  => '{{WRAPPER}} .wgcr-search--page .wgcr-search-status',
				'condition' => array( 'display_mode' => 'page' ),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'sty_items', array( 'label' => esc_html__( 'آیتم‌ها', 'widgetcore' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
				'name'     => 'title_typo',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'widgetcore' ),
				'selector' => '{{WRAPPER}} .wgcr-search .wgcr-search-title',
			) );
			$this->add_control( 'title_color', array(
				'label'     => esc_html__( 'رنگ عنوان', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-title' => 'color:{{VALUE}};' ),
			) );
			$this->add_control( 'item_hover_bg', array(
				'label'     => esc_html__( 'پس‌زمینه آیتم فعال/هاور', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-link:hover, {{WRAPPER}} .wgcr-search .wgcr-search-item[aria-selected="true"] .wgcr-search-link' => 'background-color:{{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
				'name'      => 'excerpt_typo',
				'label'     => esc_html__( 'تایپوگرافی خلاصه', 'widgetcore' ),
				'selector'  => '{{WRAPPER}} .wgcr-search .wgcr-search-excerpt',
				'separator' => 'before',
			) );
			$this->add_control( 'excerpt_color', array(
				'label'     => esc_html__( 'رنگ خلاصه', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-excerpt, {{WRAPPER}} .wgcr-search .wgcr-search-date' => 'color:{{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
				'name'      => 'all_typo',
				'label'     => esc_html__( 'تایپوگرافی «نمایش همه»', 'widgetcore' ),
				'selector'  => '{{WRAPPER}} .wgcr-search .wgcr-search-all',
				'separator' => 'before',
			) );
			$this->add_control( 'all_color', array(
				'label'     => esc_html__( 'رنگ «نمایش همه»', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-all' => 'color:{{VALUE}};' ),
			) );
			$this->add_control( 'all_color_hover', array(
				'label'     => esc_html__( 'رنگ «نمایش همه» (هاور)', 'widgetcore' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .wgcr-search .wgcr-search-all:hover' => 'color:{{VALUE}};' ),
			) );
			$this->end_controls_section();
		}

		protected function render() {
			$s        = $this->get_settings_for_display();
			$raw      = $this->get_data( 'settings' );
			$raw      = is_array( $raw ) ? $raw : array();
			$get      = static function ( $key, $fallback = '' ) use ( $s ) {
				return isset( $s[ $key ] ) ? $s[ $key ] : $fallback;
			};
			$source   = (string) $get( 'source', 'post' );
			if ( 'custom' === $source ) {
				$source = sanitize_key( (string) $get( 'custom_type' ) );
			}
			$tpl_raw  = isset( $raw['template'] ) ? absint( $raw['template'] ) : 0;
			$tpl_mode = $get( 'template_mode', 'default' );
			if ( 'custom' !== $tpl_mode && $tpl_raw > 0 && ! isset( $raw['template_mode'] ) ) {
				$tpl_mode = 'custom';
			}
			$limit = absint( $get( 'posts_per_page', 0 ) );
			if ( ! isset( $raw['posts_per_page'] ) && isset( $raw['limit'] ) ) {
				$limit = absint( $raw['limit'] );
			}

			$html = wgcr_search_render(
				array(
					'source'        => $source,
					'placeholder'   => $get( 'placeholder' ),
					'limit'         => $limit > 0 ? $limit : 12,
					'columns'       => $get( 'columns', 1 ),
					'masonry'       => $get( 'masonry' ),
					'equal_height'  => $get( 'equal_height' ),
					'thumb'         => $get( 'thumb' ),
					'excerpt'       => $get( 'excerpt' ),
					'all'           => $get( 'all_text' ),
					'template'      => 'custom' === $tpl_mode ? $tpl_raw : 0,
					'mode'          => $get( 'display_mode', 'modal' ),
					'orderby'       => $get( 'orderby', 'relevance' ),
					'order'         => $get( 'order', 'DESC' ),
					'date'          => $get( 'date_filter', 'all' ),
					'terms'         => $get( 'include_terms', array() ),
					'terms_op'      => $get( 'include_by', 'exclude' ),
					'ignore_sticky' => $get( 'ignore_sticky', 'yes' ),
					'query_id'      => $get( 'query_id' ),
					'pagination'    => $get( 'pagination_type', 'none' ),
					'spacer'        => $get( 'pagination_spacer', 'no' ),
					'more_text'     => $get( 'button_text' ),
					'more_icon'     => $get( 'button_icon', 'yes' ),
					'more_id'       => $get( 'button_id' ),
					'no_more'       => 'yes' === $get( 'custom_message' ) ? $get( 'custom_message_text' ) : '',
				),
				$this->get_id()
			);

			echo wp_kses( $html, wgcr_search_allowed_html() );
		}

		protected function content_template() {
			?>
<#
var wgcrSlug = function (v) { return String(v || '').toLowerCase().replace(/[^a-z0-9_\-]/g, ''); };
var wgcrPick = function (v, allowed, fallback) { v = String(v || '').toLowerCase(); return allowed.indexOf(v) !== -1 ? v : fallback; };
var source = wgcrSlug(settings.source) || 'post';
if (source === 'custom') { source = wgcrSlug(settings.custom_type) || 'post'; }
var limit = parseInt(settings.posts_per_page, 10) || 12;
limit = Math.max(1, Math.min(<?php echo (int) WGCR_SEARCH_MAX_RESULTS; ?>, limit));
var thumb = settings.thumb === 'yes' ? 'yes' : 'no';
var excerpt = settings.excerpt === 'yes' ? 'yes' : 'no';
var placeholder = settings.placeholder ? settings.placeholder : (source === 'course' ? '<?php echo esc_js( __( 'جستجو در دوره‌ها…', 'widgetcore' ) ); ?>' : '<?php echo esc_js( __( 'جستجو در مقالات…', 'widgetcore' ) ); ?>');
var allText = settings.all_text || '<?php echo esc_js( __( 'نمایش همه نتایج', 'widgetcore' ) ); ?>';
var tplMode = settings.template_mode || (settings.template ? 'custom' : 'default');
var template = (tplMode === 'custom' && settings.template) ? String(settings.template) : '';
var displayMode = wgcrPick(settings.display_mode, ['modal', 'page'], 'modal');
var isPage = displayMode === 'page';
var masonry = isPage && settings.masonry === 'yes';
var equal = isPage && !masonry && settings.equal_height === 'yes';
var pagination = wgcrPick(settings.pagination_type, ['none', 'numbers', 'prev_next', 'load_on_click', 'load_on_scroll'], 'none');
var extraClass = '';
if (isPage) { extraClass += ' wgcr-search--cols-' + Math.max(1, Math.min(6, parseInt(settings.columns, 10) || 1)); }
if (masonry) { extraClass += ' wgcr-search--masonry'; }
if (equal) { extraClass += ' wgcr-search--equal'; }
if (template) { extraClass += ' wgcr-search--tpl'; }
if (pagination !== 'none' && settings.pagination_spacer === 'yes') { extraClass += ' wgcr-search--spacer'; }
var terms = Array.isArray(settings.include_terms) ? settings.include_terms : [];
var orderby = wgcrPick(settings.orderby, ['relevance', 'date', 'title', 'author', 'rand', 'menu_order'], 'relevance');
var query = JSON.stringify({
	orderby: orderby,
	order: String(settings.order || '').toLowerCase() === 'asc' ? 'ASC' : 'DESC',
	date: wgcrPick(settings.date_filter, ['all', 'week', 'month', 'year'], 'all'),
	terms: terms.join(','),
	terms_op: settings.include_by === 'include' ? 'include' : 'exclude',
	ignore_sticky: settings.ignore_sticky === 'yes' ? 1 : 0,
	query_id: wgcrSlug(settings.query_id)
});
var moreId = String(settings.button_id || '').replace(/[^A-Za-z0-9_\-]/g, '');
var moreText = settings.button_text || '<?php echo esc_js( __( 'مشاهده مقالات بیشتر', 'widgetcore' ) ); ?>';
var noMore = (settings.custom_message === 'yes' && settings.custom_message_text) ? settings.custom_message_text : '<?php echo esc_js( __( 'نوشته‌ی بیشتری موجود نیست.', 'widgetcore' ) ); ?>';
var uid = 'wgcr-search-' + view.getID();
var panelHiddenAttr = isPage ? '' : ' hidden';
var inputAriaExpanded = isPage ? 'true' : 'false';
#>
<div class="wgcr-search wgcr-search--{{ displayMode }}{{ extraClass }}" data-wgcr-search data-display-mode="{{ displayMode }}" data-source="{{ source }}" data-limit="{{ limit }}" data-thumb="{{ thumb }}" data-excerpt="{{ excerpt }}" data-all="{{ allText }}" data-template="{{ template }}" data-query="{{ query }}" data-pagination="{{ pagination }}">
	<form class="wgcr-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="wgcr-search-label" for="{{ uid }}-input">{{ placeholder }}</label>
		<div class="wgcr-search-field">
			<span class="wgcr-search-icon"><?php echo wp_kses( wgcr_svg_icon( 'search' ), wgcr_svg_kses_base() ); ?></span>
			<input type="search" class="wgcr-search-input" id="{{ uid }}-input" name="s" placeholder="{{ placeholder }}" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="{{ inputAriaExpanded }}" aria-controls="{{ uid }}-list" aria-haspopup="listbox">
			<input type="hidden" name="post_type" value="{{ source }}">
			<span class="wgcr-search-spinner" aria-hidden="true"></span>
		</div>
		<div class="wgcr-search-panel"{{{ panelHiddenAttr }}}>
			<ul class="wgcr-search-list" id="{{ uid }}-list" role="listbox" aria-label="<?php esc_attr_e( 'نتایج جستجو', 'widgetcore' ); ?>"></ul>
			<div class="wgcr-search-status" aria-live="polite"></div>
			<# if (pagination !== 'none') { #>
			<div class="wgcr-search-foot">
				<nav class="wgcr-search-pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی نتایج', 'widgetcore' ); ?>" hidden></nav>
				<button type="button" class="wgcr-search-more"<# if (moreId) { #> id="{{ moreId }}"<# } #> aria-controls="{{ uid }}-list" hidden><# if (settings.button_icon !== 'no') { #><span class="wgcr-search-more-icon" aria-hidden="true"><?php echo wp_kses( wgcr_svg_icon( 'chevron' ), wgcr_svg_kses_base() ); ?></span><# } #><span class="wgcr-search-more-text">{{ moreText }}</span></button>
				<div class="wgcr-search-nomore" role="status" hidden>{{ noMore }}</div>
			</div>
			<# } #>
			<a class="wgcr-search-all" href="#" hidden>{{ allText }}</a>
		</div>
	</form>
</div>
			<?php
		}
	}
}
