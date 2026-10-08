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

		protected function add_field_block( $key, $label, $with_required, $with_placeholder, $d ) {
			$this->wgcr_heading_control( 'h_' . $key, $label );
			if ( 'author' === $key || 'email' === $key || 'url' === $key || 'cookies' === $key ) {
				$this->wgcr_switch_control( $key, esc_html__( 'نمایش فیلد', 'widgetcore' ), $d[ $key ] );
			}
			$shown = in_array( $key, array( 'author', 'email', 'url', 'cookies' ), true ) ? array( $key => 'yes' ) : array();
			if ( $with_required ) {
				$this->wgcr_switch_control( $key . '_required', esc_html__( 'اجباری', 'widgetcore' ), $d[ $key . '_required' ], $shown );
			}
			$this->wgcr_text_control( $key . '_label', esc_html__( 'برچسب', 'widgetcore' ), $d[ $key . '_label' ], $shown );
			if ( $with_placeholder ) {
				$this->wgcr_text_control( $key . '_ph', esc_html__( 'متن راهنما', 'widgetcore' ), $d[ $key . '_ph' ], $shown );
			}
			$this->wgcr_number_control( 'order_' . $key, esc_html__( 'ترتیب نمایش', 'widgetcore' ), $d[ 'order_' . $key ], 1, 9, $shown );
		}

		protected function register_controls() {
			$d = wgcr_comments_defaults();
			$w = '{{WRAPPER}} .wgcr-comments';

			$this->start_controls_section( 'sec_form', array( 'label' => esc_html__( 'فرم دیدگاه', 'widgetcore' ) ) );
			$this->wgcr_switch_control( 'form', esc_html__( 'نمایش فرم', 'widgetcore' ), $d['form'] );
			$this->wgcr_text_control( 'title', esc_html__( 'عنوان فرم', 'widgetcore' ), $d['title'], array( 'form' => 'yes' ) );
			$this->wgcr_select_control(
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
			$this->wgcr_text_control( 'note_before', esc_html__( 'یادداشت بالای فیلدها', 'widgetcore' ), '', array( 'form' => 'yes' ), \Elementor\Controls_Manager::TEXTAREA );
			$this->wgcr_text_control( 'note_after', esc_html__( 'یادداشت زیر فیلدها', 'widgetcore' ), '', array( 'form' => 'yes' ), \Elementor\Controls_Manager::TEXTAREA );
			$this->wgcr_text_control( 'submit', esc_html__( 'متن دکمه ارسال', 'widgetcore' ), $d['submit'], array( 'form' => 'yes' ) );
			$this->wgcr_text_control( 'cancel', esc_html__( 'متن لغو پاسخ', 'widgetcore' ), $d['cancel'], array( 'form' => 'yes' ) );
			$this->wgcr_switch_control( 'login_only', esc_html__( 'فقط کاربران واردشده', 'widgetcore' ), $d['login_only'], array( 'form' => 'yes' ) );
			$this->end_controls_section();

			$this->start_controls_section(
				'sec_fields',
				array(
					'label'     => esc_html__( 'فیلدها', 'widgetcore' ),
					'condition' => array( 'form' => 'yes' ),
				)
			);
			$this->wgcr_select_control(
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
			$this->wgcr_number_control( 'rows', esc_html__( 'تعداد سطرهای متن دیدگاه', 'widgetcore' ), $d['rows'], 3, 15 );
			$this->end_controls_section();

			$this->start_controls_section( 'sec_list', array( 'label' => esc_html__( 'فهرست دیدگاه‌ها', 'widgetcore' ) ) );
			$this->wgcr_switch_control( 'list', esc_html__( 'نمایش فهرست', 'widgetcore' ), $d['list'] );
			$this->wgcr_text_control( 'list_title', esc_html__( 'عنوان فهرست', 'widgetcore' ), $d['list_title'], array( 'list' => 'yes' ) );
			$this->wgcr_select_control(
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
			$this->wgcr_switch_control( 'show_count', esc_html__( 'نمایش تعداد دیدگاه‌ها', 'widgetcore' ), $d['show_count'], array( 'list' => 'yes' ) );
			$this->wgcr_number_control( 'per_page', esc_html__( 'تعداد در هر صفحه (0 = طبق وردپرس)', 'widgetcore' ), $d['per_page'], 0, 100, array( 'list' => 'yes' ) );
			$this->wgcr_select_control(
				'order',
				esc_html__( 'ترتیب فهرست', 'widgetcore' ),
				array(
					'wp'   => esc_html__( 'طبق تنظیمات وردپرس', 'widgetcore' ),
					'asc'  => esc_html__( 'قدیمی‌ترین اول', 'widgetcore' ),
					'desc' => esc_html__( 'جدیدترین اول', 'widgetcore' ),
				),
				$d['order']
			);
			$this->wgcr_select_control(
				'avatar',
				esc_html__( 'آواتار', 'widgetcore' ),
				array(
					'wp'  => esc_html__( 'طبق تنظیمات وردپرس', 'widgetcore' ),
					'yes' => esc_html__( 'نمایش', 'widgetcore' ),
					'no'  => esc_html__( 'عدم نمایش', 'widgetcore' ),
				),
				$d['avatar']
			);
			$this->wgcr_number_control( 'avatar_size', esc_html__( 'اندازهٔ آواتار', 'widgetcore' ), $d['avatar_size'], 16, 128, array( 'avatar!' => 'no' ) );
			$this->wgcr_switch_control( 'date', esc_html__( 'نمایش تاریخ', 'widgetcore' ), $d['date'], array( 'list' => 'yes' ) );
			$this->wgcr_switch_control( 'reply', esc_html__( 'نمایش لینک پاسخ', 'widgetcore' ), $d['reply'], array( 'list' => 'yes' ) );
			$this->wgcr_text_control( 'reply_text', esc_html__( 'متن لینک پاسخ', 'widgetcore' ), $d['reply_text'], array( 'reply' => 'yes' ) );
			$this->end_controls_section();

			$this->start_controls_section( 'sec_messages', array( 'label' => esc_html__( 'پیام‌ها', 'widgetcore' ) ) );
			$this->wgcr_text_control( 'empty_text', esc_html__( 'پیام فهرست خالی', 'widgetcore' ), $d['empty_text'] );
			$this->wgcr_text_control( 'pending_text', esc_html__( 'پیام در انتظار تأیید', 'widgetcore' ), $d['pending_text'] );
			$this->wgcr_text_control( 'closed_text', esc_html__( 'پیام بسته بودن دیدگاه‌ها', 'widgetcore' ), $d['closed_text'] );
			$this->wgcr_text_control( 'login_text', esc_html__( 'پیام نیاز به ورود', 'widgetcore' ), $d['login_text'] );
			$this->wgcr_heading_control( 'loggedin_heading', esc_html__( 'متن کاربر واردشده', 'widgetcore' ) );
			$this->wgcr_switch_control( 'loggedin_show', esc_html__( 'نمایش متن کاربر واردشده', 'widgetcore' ), $d['loggedin_show'], array( 'form' => 'yes' ) );
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
			$this->wgcr_switch_control( 'loggedin_profile', esc_html__( 'نمایش لینک ویرایش نمایه', 'widgetcore' ), $d['loggedin_profile'], $shown );
			$this->wgcr_text_control( 'loggedin_profile_text', esc_html__( 'متن لینک ویرایش نمایه', 'widgetcore' ), $d['loggedin_profile_text'], array_merge( $shown, array( 'loggedin_profile' => 'yes' ) ) );
			$this->wgcr_switch_control( 'loggedin_logout', esc_html__( 'نمایش لینک خروج', 'widgetcore' ), $d['loggedin_logout'], $shown );
			$this->wgcr_text_control( 'loggedin_logout_text', esc_html__( 'متن لینک خروج', 'widgetcore' ), $d['loggedin_logout_text'], array_merge( $shown, array( 'loggedin_logout' => 'yes' ) ) );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_layout', esc_html__( 'طرح کلی', 'widgetcore' ) );
			$this->wgcr_slider_control( 'max_width', esc_html__( 'حداکثر عرض', 'widgetcore' ), $w, 'max-width:{{SIZE}}{{UNIT}};', 280, 1400, array( 'px', '%', 'em', 'rem' ) );
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
			$this->wgcr_slider_control( 'sections_gap', esc_html__( 'فاصلهٔ فهرست و فرم', 'widgetcore' ), $w, 'gap:{{SIZE}}{{UNIT}};', 0, 120 );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_list_title', esc_html__( 'عنوان فهرست', 'widgetcore' ) );
			$this->wgcr_color_control( 'list_title_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comments-title' );
			$this->wgcr_typography_control( 'list_title_typo', $w . ' .wgcr-comments-title' );
			$this->wgcr_color_control( 'count_color', esc_html__( 'رنگ شمارنده', 'widgetcore' ), $w . ' .wgcr-comments-count' );
			$this->wgcr_slider_control( 'list_title_gap', esc_html__( 'فاصله تا فهرست', 'widgetcore' ), $w . ' .wgcr-comments-title', 'margin-bottom:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_form_title', esc_html__( 'عنوان فرم', 'widgetcore' ) );
			$this->wgcr_color_control( 'form_title_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comments-form-title' );
			$this->wgcr_typography_control( 'form_title_typo', $w . ' .wgcr-comments-form-title' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_form_card', esc_html__( 'کارت فرم', 'widgetcore' ) );
			$this->wgcr_style_box( 'form_card', $w . ' .wgcr-comments-respond' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_labels', esc_html__( 'برچسب‌ها', 'widgetcore' ) );
			$this->wgcr_color_control( 'label_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comments-field label' );
			$this->wgcr_typography_control( 'label_typo', $w . ' .wgcr-comments-field label' );
			$this->wgcr_color_control( 'required_color', esc_html__( 'رنگ ستارهٔ اجباری', 'widgetcore' ), $w . ' .wgcr-comments-field .required' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_fields', esc_html__( 'فیلدها', 'widgetcore' ) );
			$input = $w . ' .wgcr-comments-input';
			$this->start_controls_tabs( 'input_tabs' );
			$this->start_controls_tab( 'input_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'input_color', esc_html__( 'رنگ متن', 'widgetcore' ), $input );
			$this->wgcr_color_control( 'input_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $input, 'background-color' );
			$this->wgcr_color_control( 'input_ph', esc_html__( 'رنگ متن راهنما', 'widgetcore' ), $input . '::placeholder' );
			$this->wgcr_border_control( 'input_border', $input );
			$this->end_controls_tab();
			$this->start_controls_tab( 'input_focus', array( 'label' => esc_html__( 'فوکوس', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'input_focus_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $input . ':focus', 'background-color' );
			$this->wgcr_color_control( 'input_focus_border', esc_html__( 'رنگ کادر', 'widgetcore' ), $input . ':focus', 'border-color' );
			$this->wgcr_shadow_control( 'input_focus_shadow', $input . ':focus' );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->wgcr_typography_control( 'input_typo', $input );
			$this->wgcr_dimensions_control( 'input_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $input, 'border-radius' );
			$this->wgcr_dimensions_control( 'input_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $input, 'padding' );
			$this->wgcr_slider_control( 'fields_gap', esc_html__( 'فاصلهٔ بین فیلدها', 'widgetcore' ), $w . ' .wgcr-comments-form', 'gap:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->end_controls_section();

			$this->wgcr_style_text( 'sty_cookies', esc_html__( 'تیک ذخیره در مرورگر', 'widgetcore' ), $w . ' .wgcr-comments-cookies label' );

			$this->wgcr_style_section( 'sty_notes', esc_html__( 'یادداشت‌های فرم', 'widgetcore' ) );
			$this->wgcr_color_control( 'note_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comments-note' );
			$this->wgcr_typography_control( 'note_typo', $w . ' .wgcr-comments-note' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_submit', esc_html__( 'دکمه ارسال', 'widgetcore' ) );
			$btn = $w . ' .wgcr-comments-submit';
			$this->wgcr_style_states(
				'submit',
				$btn,
				$btn . ':hover,' . $btn . ':focus-visible',
				array(
					'color'  => array( esc_html__( 'رنگ متن', 'widgetcore' ), 'color' ),
					'bg'     => array( esc_html__( 'پس‌زمینه', 'widgetcore' ), 'background-color' ),
					'border' => array( esc_html__( 'رنگ کادر', 'widgetcore' ), 'border-color' ),
				)
			);
			$this->wgcr_typography_control( 'submit_typo', $btn );
			$this->wgcr_dimensions_control( 'submit_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $btn, 'border-radius' );
			$this->wgcr_dimensions_control( 'submit_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $btn, 'padding' );
			$this->wgcr_shadow_control( 'submit_shadow', $btn );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_card', esc_html__( 'کارت دیدگاه', 'widgetcore' ) );
			$this->wgcr_style_box( 'card', $w . ' .wgcr-comment' );
			$this->wgcr_slider_control( 'card_gap', esc_html__( 'فاصلهٔ بین دیدگاه‌ها', 'widgetcore' ), $w . ' .wgcr-comments-list,' . $w . ' .wgcr-comments-list .children', 'gap:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->wgcr_dimensions_control( 'reply_margin', esc_html__( 'تورفتگی و فاصلهٔ پاسخ‌ها', 'widgetcore' ), $w . ' .wgcr-comments-list .children', 'margin' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_avatar', esc_html__( 'آواتار', 'widgetcore' ) );
			$this->wgcr_dimensions_control( 'avatar_corners', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $w . ' .wgcr-comments-avatar', 'border-radius' );
			$this->wgcr_border_control( 'avatar_border', $w . ' .wgcr-comments-avatar' );
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
			$this->wgcr_dimensions_control( 'avatar_margin', esc_html__( 'فاصله از کارت دیدگاه', 'widgetcore' ), $w . ' .wgcr-comments-avatar', 'margin' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_author', esc_html__( 'نام نویسنده', 'widgetcore' ) );
			$author = $w . ' .wgcr-comment-author,' . $w . ' .wgcr-comment-author a';
			$this->wgcr_color_control( 'sty_author_color', esc_html__( 'رنگ', 'widgetcore' ), $author );
			$this->wgcr_typography_control( 'sty_author_typo', $author );
			$this->wgcr_dimensions_control( 'author_margin', esc_html__( 'فاصله از آواتار', 'widgetcore' ), $w . ' .wgcr-comment-author', 'margin' );
			$this->end_controls_section();
			$this->wgcr_style_section( 'sty_date', esc_html__( 'تاریخ', 'widgetcore' ) );
			$this->wgcr_color_control( 'sty_date_color', esc_html__( 'رنگ', 'widgetcore' ), $w . ' .wgcr-comment-date' );
			$this->wgcr_typography_control( 'sty_date_typo', $w . ' .wgcr-comment-date' );
			$this->wgcr_dimensions_control( 'date_margin', esc_html__( 'فاصله از آواتار', 'widgetcore' ), $w . ' .wgcr-comment-date', 'margin' );
			$this->end_controls_section();
			$this->wgcr_style_text( 'sty_body', esc_html__( 'متن دیدگاه', 'widgetcore' ), $w . ' .wgcr-comment-text' );

			$this->wgcr_style_section( 'sty_reply', esc_html__( 'لینک پاسخ', 'widgetcore' ) );
			$reply = $w . ' .wgcr-comment-reply a';
			$this->wgcr_style_states(
				'reply',
				$reply,
				$reply . ':hover,' . $reply . ':focus-visible',
				array(
					'color' => array( esc_html__( 'رنگ', 'widgetcore' ), 'color' ),
					'bg'    => array( esc_html__( 'پس‌زمینه', 'widgetcore' ), 'background-color' ),
				)
			);
			$this->wgcr_typography_control( 'reply_typo', $reply );
			$this->wgcr_dimensions_control( 'reply_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $reply, 'border-radius' );
			$this->wgcr_dimensions_control( 'reply_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $reply, 'padding' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_pagination', esc_html__( 'صفحه‌بندی', 'widgetcore' ) );
			$page = $w . ' .wgcr-comments-page';
			$this->wgcr_typography_control( 'page_typo', $page );
			$this->wgcr_color_control( 'page_color', esc_html__( 'رنگ', 'widgetcore' ), $page );
			$this->wgcr_color_control( 'page_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $page, 'background-color' );
			$this->wgcr_color_control( 'page_hover', esc_html__( 'رنگ در حالت هاور', 'widgetcore' ), $w . ' a.wgcr-comments-page:hover,' . $page . ':focus-visible' );
			$this->wgcr_color_control( 'page_current_color', esc_html__( 'رنگ صفحهٔ جاری', 'widgetcore' ), $page . '.is-current' );
			$this->wgcr_color_control( 'page_current_bg', esc_html__( 'پس‌زمینهٔ صفحهٔ جاری', 'widgetcore' ), $page . '.is-current', 'background-color' );
			$this->wgcr_dimensions_control( 'page_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $page, 'border-radius' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_messages', esc_html__( 'پیام‌ها', 'widgetcore' ) );
			$msg = $w . ' .wgcr-comments-notice,' . $w . ' .wgcr-comments-empty,' . $w . ' .wgcr-comments-pending';
			$this->wgcr_color_control( 'msg_color', esc_html__( 'رنگ', 'widgetcore' ), $msg );
			$this->wgcr_color_control( 'msg_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $msg, 'background-color' );
			$this->wgcr_typography_control( 'msg_typo', $msg );
			$this->wgcr_dimensions_control( 'msg_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $msg, 'border-radius' );
			$this->wgcr_dimensions_control( 'msg_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $msg, 'padding' );
			$this->wgcr_color_control( 'pending_color', esc_html__( 'رنگ پیام در انتظار تأیید', 'widgetcore' ), $w . ' .wgcr-comments-pending' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_loggedin', esc_html__( 'متن کاربر واردشده', 'widgetcore' ) );
			$li = $w . ' .wgcr-comments-loggedin';
			$this->wgcr_color_control( 'loggedin_color', esc_html__( 'رنگ متن', 'widgetcore' ), $li );
			$this->wgcr_typography_control( 'loggedin_typo', $li );
			$this->wgcr_color_control( 'loggedin_link_color', esc_html__( 'رنگ لینک‌ها', 'widgetcore' ), $li . ' a' );
			$this->wgcr_color_control( 'loggedin_link_hover', esc_html__( 'رنگ لینک‌ها در حالت هاور', 'widgetcore' ), $li . ' a:hover,' . $li . ' a:focus-visible' );
			$this->wgcr_typography_control( 'loggedin_link_typo', $li . ' a' );
			$this->wgcr_style_box( 'loggedin', $li );
			$this->wgcr_dimensions_control( 'loggedin_margin', esc_html__( 'فاصله از اطراف', 'widgetcore' ), $li, 'margin' );
			$this->end_controls_section();
		}

		protected function render() {
			wgcr_comments_print( $this->get_settings_for_display(), $this->get_id(), wgcr_comments_is_editor() );
		}
	}
}
