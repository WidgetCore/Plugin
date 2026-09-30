<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WGCR_Comments_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Comments_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-comments'; }
		public function get_title() { return esc_html__( 'دیدگاه‌ها - Comments', 'widgetcore' ); }
		public function get_icon() { return 'eicon-comments'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-comments' ); }
		public function get_keywords() { return array( 'comments', 'comment', 'reply', 'discussion', __( 'دیدگاه', 'widgetcore' ), __( 'نظر', 'widgetcore' ), __( 'کامنت', 'widgetcore' ) ); }

		protected function add_switch( $id, $label, $default = 'yes', $condition = array() ) {
			$this->add_control(
				$id,
				array(
					'label'        => $label,
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'بله', 'widgetcore' ),
					'label_off'    => esc_html__( 'خیر', 'widgetcore' ),
					'return_value' => 'yes',
					'default'      => $default,
					'condition'    => $condition,
				)
			);
		}

		protected function add_text( $id, $label, $default = '', $condition = array(), $type = null ) {
			$this->add_control(
				$id,
				array(
					'label'       => $label,
					'type'        => null === $type ? \Elementor\Controls_Manager::TEXT : $type,
					'default'     => $default,
					'label_block' => true,
					'condition'   => $condition,
				)
			);
		}

		protected function add_number( $id, $label, $default, $min, $max, $condition = array() ) {
			$this->add_control(
				$id,
				array(
					'label'     => $label,
					'type'      => \Elementor\Controls_Manager::NUMBER,
					'min'       => $min,
					'max'       => $max,
					'step'      => 1,
					'default'   => $default,
					'condition' => $condition,
				)
			);
		}

		protected function add_select( $id, $label, $options, $default ) {
			$this->add_control(
				$id,
				array(
					'label'   => $label,
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $options,
					'default' => $default,
				)
			);
		}

		protected function add_heading( $id, $label ) {
			$this->add_control(
				$id,
				array(
					'label'     => $label,
					'type'      => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);
		}

		protected function add_field_block( $key, $label, $with_required, $with_placeholder, $d ) {
			$this->add_heading( 'h_' . $key, $label );
			if ( 'author' === $key || 'email' === $key || 'url' === $key || 'cookies' === $key ) {
				$this->add_switch( $key, esc_html__( 'نمایش فیلد', 'widgetcore' ), $d[ $key ] );
			}
			$shown = in_array( $key, array( 'author', 'email', 'url', 'cookies' ), true ) ? array( $key => 'yes' ) : array();
			if ( $with_required ) {
				$this->add_switch( $key . '_required', esc_html__( 'اجباری', 'widgetcore' ), $d[ $key . '_required' ], $shown );
			}
			$this->add_text( $key . '_label', esc_html__( 'برچسب', 'widgetcore' ), $d[ $key . '_label' ], $shown );
			if ( $with_placeholder ) {
				$this->add_text( $key . '_ph', esc_html__( 'متن راهنما', 'widgetcore' ), $d[ $key . '_ph' ], $shown );
			}
			$this->add_number( 'order_' . $key, esc_html__( 'ترتیب نمایش', 'widgetcore' ), $d[ 'order_' . $key ], 1, 9, $shown );
		}

		protected function style_start( $id, $label ) {
			$this->start_controls_section(
				$id,
				array(
					'label' => $label,
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);
		}

		protected function style_color( $id, $label, $selector, $property = 'color' ) {
			$this->add_control(
				$id,
				array(
					'label'     => $label,
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array( $selector => $property . ':{{VALUE}};' ),
				)
			);
		}

		protected function style_typo( $id, $selector ) {
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => $id,
					'label'    => esc_html__( 'تایپوگرافی', 'widgetcore' ),
					'selector' => $selector,
				)
			);
		}

		protected function style_dimensions( $id, $label, $selector, $property ) {
			$this->wgcr_size_control(
				$id,
				array(
					'label'      => $label,
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', 'rem', '%' ),
					'selectors'  => array( $selector => $property . ':{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				)
			);
		}

		protected function style_slider( $id, $label, $selector, $template, $min, $max, $units = array( 'px' ) ) {
			$this->wgcr_size_control(
				$id,
				array(
					'label'      => $label,
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => $units,
					'range'      => array(
						'px'  => array(
							'min' => $min,
							'max' => $max,
						),
						'%'   => array(
							'min' => 0,
							'max' => 100,
						),
						'em'  => array(
							'min' => 0,
							'max' => 10,
						),
						'rem' => array(
							'min' => 0,
							'max' => 10,
						),
					),
					'selectors'  => array( $selector => $template ),
				)
			);
		}

		protected function style_border( $id, $selector ) {
			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'     => $id,
					'label'    => esc_html__( 'کادر', 'widgetcore' ),
					'selector' => $selector,
				)
			);
		}

		protected function style_shadow( $id, $selector ) {
			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => $id,
					'label'    => esc_html__( 'سایه', 'widgetcore' ),
					'selector' => $selector,
				)
			);
		}

		protected function style_box( $id, $selector, $with_padding = true ) {
			$this->style_color( $id . '_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $selector, 'background-color' );
			$this->style_border( $id . '_border', $selector );
			$this->style_dimensions( $id . '_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $selector, 'border-radius' );
			$this->style_shadow( $id . '_shadow', $selector );
			if ( $with_padding ) {
				$this->style_dimensions( $id . '_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $selector, 'padding' );
			}
		}

		protected function style_text( $id, $label, $selector ) {
			$this->style_start( $id, $label );
			$this->style_color( $id . '_color', esc_html__( 'رنگ', 'widgetcore' ), $selector );
			$this->style_typo( $id . '_typo', $selector );
			$this->end_controls_section();
		}

		protected function style_states( $id, $selector, $hover_selector, $props ) {
			$this->start_controls_tabs( $id . '_tabs' );
			foreach ( array( 'normal' => array( esc_html__( 'عادی', 'widgetcore' ), $selector ), 'hover' => array( esc_html__( 'هاور', 'widgetcore' ), $hover_selector ) ) as $state => $info ) {
				$this->start_controls_tab( $id . '_' . $state, array( 'label' => $info[0] ) );
				foreach ( $props as $suffix => $prop ) {
					$this->style_color( $id . '_' . $state . '_' . $suffix, $prop[0], $info[1], $prop[1] );
				}
				$this->end_controls_tab();
			}
			$this->end_controls_tabs();
		}

		protected function register_controls() {
			$d = wgcr_comments_defaults();
			$w = '{{WRAPPER}} .wgcr-comments';

			$this->start_controls_section( 'sec_form', array( 'label' => esc_html__( 'فرم دیدگاه', 'widgetcore' ) ) );
			$this->add_switch( 'form', esc_html__( 'نمایش فرم', 'widgetcore' ), $d['form'] );
			$this->add_text( 'title', esc_html__( 'عنوان فرم', 'widgetcore' ), $d['title'], array( 'form' => 'yes' ) );
			$this->add_select(
				'title_tag',
				esc_html__( 'تگ عنوان فرم', 'widgetcore' ),
				array(
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'div' => 'DIV',
				),
				$d['title_tag']
			);
			$this->add_text( 'note_before', esc_html__( 'یادداشت بالای فیلدها', 'widgetcore' ), '', array( 'form' => 'yes' ), \Elementor\Controls_Manager::TEXTAREA );
			$this->add_text( 'note_after', esc_html__( 'یادداشت زیر فیلدها', 'widgetcore' ), '', array( 'form' => 'yes' ), \Elementor\Controls_Manager::TEXTAREA );
			$this->add_text( 'submit', esc_html__( 'متن دکمه ارسال', 'widgetcore' ), $d['submit'], array( 'form' => 'yes' ) );
			$this->add_text( 'cancel', esc_html__( 'متن لغو پاسخ', 'widgetcore' ), $d['cancel'], array( 'form' => 'yes' ) );
			$this->add_switch( 'login_only', esc_html__( 'فقط کاربران واردشده', 'widgetcore' ), $d['login_only'], array( 'form' => 'yes' ) );
			$this->end_controls_section();

			$this->start_controls_section(
				'sec_fields',
				array(
					'label'     => esc_html__( 'فیلدها', 'widgetcore' ),
					'condition' => array( 'form' => 'yes' ),
				)
			);
			$this->add_select(
				'fields_layout',
				esc_html__( 'چیدمان نام، ایمیل و وب‌سایت', 'widgetcore' ),
				array(
					'row'   => esc_html__( 'کنار هم', 'widgetcore' ),
					'stack' => esc_html__( 'زیر هم', 'widgetcore' ),
				),
				$d['fields_layout']
			);
			$this->add_field_block( 'author', esc_html__( 'نام', 'widgetcore' ), true, true, $d );
			$this->add_field_block( 'email', esc_html__( 'ایمیل', 'widgetcore' ), true, true, $d );
			$this->add_field_block( 'url', esc_html__( 'وب‌سایت', 'widgetcore' ), false, true, $d );
			$this->add_field_block( 'cookies', esc_html__( 'تیک ذخیره در مرورگر', 'widgetcore' ), false, false, $d );
			$this->add_field_block( 'comment', esc_html__( 'متن دیدگاه', 'widgetcore' ), false, true, $d );
			$this->add_number( 'rows', esc_html__( 'تعداد سطرهای متن دیدگاه', 'widgetcore' ), $d['rows'], 3, 15 );
			$this->end_controls_section();

			$this->start_controls_section( 'sec_list', array( 'label' => esc_html__( 'فهرست دیدگاه‌ها', 'widgetcore' ) ) );
			$this->add_switch( 'list', esc_html__( 'نمایش فهرست', 'widgetcore' ), $d['list'] );
			$this->add_text( 'list_title', esc_html__( 'عنوان فهرست', 'widgetcore' ), $d['list_title'], array( 'list' => 'yes' ) );
			$this->add_select(
				'list_title_tag',
				esc_html__( 'تگ عنوان فهرست', 'widgetcore' ),
				array(
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'div' => 'DIV',
				),
				$d['list_title_tag']
			);
			$this->add_switch( 'show_count', esc_html__( 'نمایش تعداد دیدگاه‌ها', 'widgetcore' ), $d['show_count'], array( 'list' => 'yes' ) );
			$this->add_number( 'per_page', esc_html__( 'تعداد در هر صفحه (0 = طبق وردپرس)', 'widgetcore' ), $d['per_page'], 0, 100, array( 'list' => 'yes' ) );
			$this->add_select(
				'order',
				esc_html__( 'ترتیب فهرست', 'widgetcore' ),
				array(
					'wp'   => esc_html__( 'طبق تنظیمات وردپرس', 'widgetcore' ),
					'asc'  => esc_html__( 'قدیمی‌ترین اول', 'widgetcore' ),
					'desc' => esc_html__( 'جدیدترین اول', 'widgetcore' ),
				),
				$d['order']
			);
			$this->add_select(
				'avatar',
				esc_html__( 'آواتار', 'widgetcore' ),
				array(
					'wp'  => esc_html__( 'طبق تنظیمات وردپرس', 'widgetcore' ),
					'yes' => esc_html__( 'نمایش', 'widgetcore' ),
					'no'  => esc_html__( 'عدم نمایش', 'widgetcore' ),
				),
				$d['avatar']
			);
			$this->add_number( 'avatar_size', esc_html__( 'اندازهٔ آواتار', 'widgetcore' ), $d['avatar_size'], 16, 128, array( 'avatar!' => 'no' ) );
			$this->add_switch( 'date', esc_html__( 'نمایش تاریخ', 'widgetcore' ), $d['date'], array( 'list' => 'yes' ) );
			$this->add_switch( 'reply', esc_html__( 'نمایش لینک پاسخ', 'widgetcore' ), $d['reply'], array( 'list' => 'yes' ) );
			$this->add_text( 'reply_text', esc_html__( 'متن لینک پاسخ', 'widgetcore' ), $d['reply_text'], array( 'reply' => 'yes' ) );
			$this->end_controls_section();

			$this->start_controls_section( 'sec_messages', array( 'label' => esc_html__( 'پیام‌ها', 'widgetcore' ) ) );
			$this->add_text( 'empty_text', esc_html__( 'پیام فهرست خالی', 'widgetcore' ), $d['empty_text'] );
			$this->add_text( 'pending_text', esc_html__( 'پیام در انتظار تأیید', 'widgetcore' ), $d['pending_text'] );
			$this->add_text( 'closed_text', esc_html__( 'پیام بسته بودن دیدگاه‌ها', 'widgetcore' ), $d['closed_text'] );
			$this->add_text( 'login_text', esc_html__( 'پیام نیاز به ورود', 'widgetcore' ), $d['login_text'] );
			$this->add_heading( 'loggedin_heading', esc_html__( 'متن کاربر واردشده', 'widgetcore' ) );
			$this->add_switch( 'loggedin_show', esc_html__( 'نمایش متن کاربر واردشده', 'widgetcore' ), $d['loggedin_show'], array( 'form' => 'yes' ) );
			$this->add_control(
				'loggedin_text',
				array(
					'label'       => esc_html__( 'متن', 'widgetcore' ),
					'description' => esc_html__( 'برای نمایش نام کاربر، {name} را در متن بنویسید. این متن فقط به کاربر واردشده نشان داده می‌شود.', 'widgetcore' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => $d['loggedin_text'],
					'label_block' => true,
					'condition'   => array(
						'form'          => 'yes',
						'loggedin_show' => 'yes',
					),
				)
			);
			$shown = array(
				'form'          => 'yes',
				'loggedin_show' => 'yes',
			);
			$this->add_switch( 'loggedin_profile', esc_html__( 'نمایش لینک ویرایش نمایه', 'widgetcore' ), $d['loggedin_profile'], $shown );
			$this->add_text( 'loggedin_profile_text', esc_html__( 'متن لینک ویرایش نمایه', 'widgetcore' ), $d['loggedin_profile_text'], array_merge( $shown, array( 'loggedin_profile' => 'yes' ) ) );
			$this->add_switch( 'loggedin_logout', esc_html__( 'نمایش لینک خروج', 'widgetcore' ), $d['loggedin_logout'], $shown );
			$this->add_text( 'loggedin_logout_text', esc_html__( 'متن لینک خروج', 'widgetcore' ), $d['loggedin_logout_text'], array_merge( $shown, array( 'loggedin_logout' => 'yes' ) ) );
			$this->end_controls_section();

			$this->style_start( 'sty_layout', esc_html__( 'طرح کلی', 'widgetcore' ) );
			$this->style_slider( 'max_width', esc_html__( 'حداکثر عرض', 'widgetcore' ), $w, 'max-width:{{SIZE}}{{UNIT}};', 280, 1400, array( 'px', '%', 'em', 'rem' ) );
			$this->add_control(
				'align',
				array(
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
					'selectors_dictionary' => array(
						'right'  => 'margin-left:auto;margin-right:0;',
						'center' => 'margin-left:auto;margin-right:auto;',
						'left'   => 'margin-left:0;margin-right:auto;',
					),
					'selectors'            => array( $w => '{{VALUE}}' ),
				)
			);
			$this->style_slider( 'sections_gap', esc_html__( 'فاصلهٔ فهرست و فرم', 'widgetcore' ), $w, 'gap:{{SIZE}}{{UNIT}};', 0, 120 );
			$this->end_controls_section();

			$this->style_start( 'sty_list_title', esc_html__( 'عنوان فهرست', 'widgetcore' ) );
			$this->style_color( 'list_title_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comments-title' );
			$this->style_typo( 'list_title_typo', $w . ' .wgcr-comments-title' );
			$this->style_color( 'count_color', esc_html__( 'رنگ شمارنده', 'widgetcore' ), $w . ' .wgcr-comments-count' );
			$this->style_slider( 'list_title_gap', esc_html__( 'فاصله تا فهرست', 'widgetcore' ), $w . ' .wgcr-comments-title', 'margin-bottom:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->end_controls_section();

			$this->style_start( 'sty_form_title', esc_html__( 'عنوان فرم', 'widgetcore' ) );
			$this->style_color( 'form_title_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comments-form-title' );
			$this->style_typo( 'form_title_typo', $w . ' .wgcr-comments-form-title' );
			$this->end_controls_section();

			$this->style_start( 'sty_form_card', esc_html__( 'کارت فرم', 'widgetcore' ) );
			$this->style_box( 'form_card', $w . ' .wgcr-comments-respond' );
			$this->end_controls_section();

			$this->style_start( 'sty_labels', esc_html__( 'برچسب‌ها', 'widgetcore' ) );
			$this->style_color( 'label_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comments-field label' );
			$this->style_typo( 'label_typo', $w . ' .wgcr-comments-field label' );
			$this->style_color( 'required_color', esc_html__( 'رنگ ستارهٔ اجباری', 'widgetcore' ), $w . ' .wgcr-comments-field .required' );
			$this->end_controls_section();

			$this->style_start( 'sty_fields', esc_html__( 'فیلدها', 'widgetcore' ) );
			$input = $w . ' .wgcr-comments-input';
			$this->start_controls_tabs( 'input_tabs' );
			$this->start_controls_tab( 'input_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->style_color( 'input_color', esc_html__( 'رنگ متن', 'widgetcore' ), $input );
			$this->style_color( 'input_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $input, 'background-color' );
			$this->style_color( 'input_ph', esc_html__( 'رنگ متن راهنما', 'widgetcore' ), $input . '::placeholder' );
			$this->style_border( 'input_border', $input );
			$this->end_controls_tab();
			$this->start_controls_tab( 'input_focus', array( 'label' => esc_html__( 'فوکوس', 'widgetcore' ) ) );
			$this->style_color( 'input_focus_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $input . ':focus', 'background-color' );
			$this->style_color( 'input_focus_border', esc_html__( 'رنگ کادر', 'widgetcore' ), $input . ':focus', 'border-color' );
			$this->style_shadow( 'input_focus_shadow', $input . ':focus' );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->style_typo( 'input_typo', $input );
			$this->style_dimensions( 'input_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $input, 'border-radius' );
			$this->style_dimensions( 'input_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $input, 'padding' );
			$this->style_slider( 'fields_gap', esc_html__( 'فاصلهٔ بین فیلدها', 'widgetcore' ), $w . ' .wgcr-comments-form', 'gap:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->end_controls_section();

			$this->style_text( 'sty_cookies', esc_html__( 'تیک ذخیره در مرورگر', 'widgetcore' ), $w . ' .wgcr-comments-cookies label' );

			$this->style_start( 'sty_notes', esc_html__( 'یادداشت‌های فرم', 'widgetcore' ) );
			$this->style_color( 'note_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comments-note' );
			$this->style_typo( 'note_typo', $w . ' .wgcr-comments-note' );
			$this->end_controls_section();

			$this->style_start( 'sty_submit', esc_html__( 'دکمه ارسال', 'widgetcore' ) );
			$btn = $w . ' .wgcr-comments-submit';
			$this->style_states(
				'submit',
				$btn,
				$btn . ':hover,' . $btn . ':focus-visible',
				array(
					'color'  => array( esc_html__( 'رنگ متن', 'widgetcore' ), 'color' ),
					'bg'     => array( esc_html__( 'پس‌زمینه', 'widgetcore' ), 'background-color' ),
					'border' => array( esc_html__( 'رنگ کادر', 'widgetcore' ), 'border-color' ),
				)
			);
			$this->style_typo( 'submit_typo', $btn );
			$this->style_dimensions( 'submit_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $btn, 'border-radius' );
			$this->style_dimensions( 'submit_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $btn, 'padding' );
			$this->style_shadow( 'submit_shadow', $btn );
			$this->end_controls_section();

			$this->style_start( 'sty_card', esc_html__( 'کارت دیدگاه', 'widgetcore' ) );
			$this->style_box( 'card', $w . ' .wgcr-comment' );
			$this->style_slider( 'card_gap', esc_html__( 'فاصلهٔ بین دیدگاه‌ها', 'widgetcore' ), $w . ' .wgcr-comments-list,' . $w . ' .wgcr-comments-list .children', 'gap:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->style_dimensions( 'reply_margin', esc_html__( 'تورفتگی و فاصلهٔ پاسخ‌ها', 'widgetcore' ), $w . ' .wgcr-comments-list .children', 'margin' );
			$this->end_controls_section();

			$this->style_start( 'sty_avatar', esc_html__( 'آواتار', 'widgetcore' ) );
			$this->style_dimensions( 'avatar_corners', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $w . ' .wgcr-comments-avatar', 'border-radius' );
			$this->style_border( 'avatar_border', $w . ' .wgcr-comments-avatar' );
			$this->wgcr_size_control(
				'avatar_width',
				array(
					'label'       => esc_html__( 'عرض آواتار', 'widgetcore' ),
					'description' => esc_html__( 'برای تصویر واضح‌تر، «اندازهٔ آواتار» را در تب محتوا هم زیاد کنید.', 'widgetcore' ),
					'type'        => \Elementor\Controls_Manager::SLIDER,
					'size_units'  => array( 'px', 'em', 'rem' ),
					'range'       => array(
						'px'  => array(
							'min' => 16,
							'max' => 200,
						),
						'em'  => array(
							'min' => 1,
							'max' => 12,
						),
						'rem' => array(
							'min' => 1,
							'max' => 12,
						),
					),
					'selectors'   => array( $w . ' .wgcr-comments-avatar' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};max-width:none;' ),
				)
			);
			$this->style_dimensions( 'avatar_margin', esc_html__( 'فاصله از کارت دیدگاه', 'widgetcore' ), $w . ' .wgcr-comments-avatar', 'margin' );
			$this->end_controls_section();

			$this->style_start( 'sty_author', esc_html__( 'نام نویسنده', 'widgetcore' ) );
			$author = $w . ' .wgcr-comment-author,' . $w . ' .wgcr-comment-author a';
			$this->style_color( 'sty_author_color', esc_html__( 'رنگ', 'widgetcore' ), $author );
			$this->style_typo( 'sty_author_typo', $author );
			$this->style_dimensions( 'author_margin', esc_html__( 'فاصله از آواتار', 'widgetcore' ), $w . ' .wgcr-comment-author', 'margin' );
			$this->end_controls_section();
			$this->style_start( 'sty_date', esc_html__( 'تاریخ', 'widgetcore' ) );
			$this->style_color( 'sty_date_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comment-date' );
			$this->style_typo( 'sty_date_typo', $w . ' .wgcr-comment-date' );
			$this->style_dimensions( 'date_margin', esc_html__( 'فاصله از آواتار', 'widgetcore' ), $w . ' .wgcr-comment-date', 'margin' );
			$this->end_controls_section();
			$this->style_text( 'sty_body', esc_html__( 'متن دیدگاه', 'widgetcore' ), $w . ' .wgcr-comment-text' );

			$this->style_start( 'sty_reply', esc_html__( 'لینک پاسخ', 'widgetcore' ) );
			$reply = $w . ' .wgcr-comment-reply a';
			$this->style_states(
				'reply',
				$reply,
				$reply . ':hover,' . $reply . ':focus-visible',
				array(
					'color' => array( esc_html__( 'رنگ', 'widgetcore' ), 'color' ),
					'bg'    => array( esc_html__( 'پس‌زمینه', 'widgetcore' ), 'background-color' ),
				)
			);
			$this->style_typo( 'reply_typo', $reply );
			$this->style_dimensions( 'reply_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $reply, 'border-radius' );
			$this->style_dimensions( 'reply_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $reply, 'padding' );
			$this->end_controls_section();

			$this->style_start( 'sty_pagination', esc_html__( 'صفحه‌بندی', 'widgetcore' ) );
			$page = $w . ' .wgcr-comments-page';
			$this->style_typo( 'page_typo', $page );
			$this->style_color( 'page_color', esc_html__( 'رنگ', 'widgetcore' ), $page );
			$this->style_color( 'page_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $page, 'background-color' );
			$this->style_color( 'page_hover', esc_html__( 'رنگ در حالت هاور', 'widgetcore' ), $w . ' a.wgcr-comments-page:hover,' . $page . ':focus-visible' );
			$this->style_color( 'page_current_color', esc_html__( 'رنگ صفحهٔ جاری', 'widgetcore' ), $page . '.is-current' );
			$this->style_color( 'page_current_bg', esc_html__( 'پس‌زمینهٔ صفحهٔ جاری', 'widgetcore' ), $page . '.is-current', 'background-color' );
			$this->style_dimensions( 'page_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $page, 'border-radius' );
			$this->end_controls_section();

			$this->style_start( 'sty_messages', esc_html__( 'پیام‌ها', 'widgetcore' ) );
			$msg = $w . ' .wgcr-comments-notice,' . $w . ' .wgcr-comments-empty,' . $w . ' .wgcr-comments-pending';
			$this->style_color( 'msg_color', esc_html__( 'رنگ', 'widgetcore' ), $msg );
			$this->style_color( 'msg_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $msg, 'background-color' );
			$this->style_typo( 'msg_typo', $msg );
			$this->style_dimensions( 'msg_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $msg, 'border-radius' );
			$this->style_dimensions( 'msg_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $msg, 'padding' );
			$this->style_color( 'pending_color', esc_html__( 'رنگ پیام در انتظار تأیید', 'widgetcore' ), $w . ' .wgcr-comments-pending' );
			$this->end_controls_section();

			$this->style_start( 'sty_loggedin', esc_html__( 'متن کاربر واردشده', 'widgetcore' ) );
			$li = $w . ' .wgcr-comments-loggedin';
			$this->style_color( 'loggedin_color', esc_html__( 'رنگ متن', 'widgetcore' ), $li );
			$this->style_typo( 'loggedin_typo', $li );
			$this->style_color( 'loggedin_link_color', esc_html__( 'رنگ لینک‌ها', 'widgetcore' ), $li . ' a' );
			$this->style_color( 'loggedin_link_hover', esc_html__( 'رنگ لینک‌ها در حالت هاور', 'widgetcore' ), $li . ' a:hover,' . $li . ' a:focus-visible' );
			$this->style_typo( 'loggedin_link_typo', $li . ' a' );
			$this->style_box( 'loggedin', $li );
			$this->style_dimensions( 'loggedin_margin', esc_html__( 'فاصله از اطراف', 'widgetcore' ), $li, 'margin' );
			$this->end_controls_section();
		}

		protected function render() {
			wgcr_comments_print( $this->get_settings_for_display(), $this->get_id(), wgcr_comments_is_editor() );
		}
	}
}
