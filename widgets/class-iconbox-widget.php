<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_iconbox_base_css' ) ) {
	function wgcr_iconbox_base_css() {
		return implode(
			"\n",
			array(
				'.wgcr-iconbox{--wgcr-iconbox-accent:#2E86DE;--wgcr-iconbox-ink:#1F2937;--wgcr-iconbox-muted:#6B7280;display:flex;flex-direction:column;align-items:center;gap:14px;text-align:center}',
				'.wgcr-iconbox[data-layout="side"]{flex-direction:row;align-items:flex-start;justify-content:flex-start;gap:16px;text-align:start}',
				'.wgcr-iconbox-ic{flex:none;display:grid;place-items:center;width:64px;height:64px;border-radius:16px;background:#EBF3FC;color:var(--wgcr-iconbox-accent);font-size:26px}',
				'.wgcr-iconbox-ic svg{display:block;width:1em;height:1em}',
				'.wgcr-iconbox-body{min-width:0}',
				'.wgcr-iconbox-title{margin:0;font-size:17px;font-weight:700;line-height:1.7;color:var(--wgcr-iconbox-ink)}',
				'.wgcr-iconbox-title a{color:inherit;text-decoration:none}',
				'.wgcr-iconbox-title a:hover{color:var(--wgcr-iconbox-accent)}',
				'.wgcr-iconbox-title a:focus{outline:none}',
				'.wgcr-iconbox-title a:focus-visible{outline:2px solid var(--wgcr-iconbox-accent);outline-offset:2px}',
				'.wgcr-iconbox-text{margin:6px 0 0;font-size:14px;line-height:2;color:var(--wgcr-iconbox-muted)}',
				'.wgcr-iconbox-text>:first-child{margin-top:0}',
				'.wgcr-iconbox-text>:last-child{margin-bottom:0}',
			)
		);
	}
}

if ( ! function_exists( 'wgcr_iconbox_register_assets' ) ) {
	function wgcr_iconbox_register_assets() {
		if ( wp_style_is( 'wgcr-iconbox', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-iconbox', false, array(), WGCR_VER );
		wp_add_inline_style( 'wgcr-iconbox', wgcr_iconbox_base_css() );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_iconbox_register_assets', 5 );

if ( ! class_exists( 'WGCR_Icon_Box_Widget' ) && class_exists( '\Elementor\Widget_Base' ) ) {

	class WGCR_Icon_Box_Widget extends \Elementor\Widget_Base {

		use WGCR_Size_Controls;

		public function get_name() { return 'wgcr-iconbox'; }
		public function get_title() { return esc_html__( 'جعبه آیکون - Icon Box', 'widgetcore' ); }
		public function get_icon() { return 'eicon-info-box'; }
		public function get_categories() { return array( 'wgcr' ); }
		public function get_style_depends() { return array( 'wgcr-iconbox' ); }
		public function get_keywords() { return array( 'icon', 'box', 'feature', 'info', __( 'جعبه آیکون', 'widgetcore' ), __( 'ویژگی', 'widgetcore' ), __( 'آیکون و متن', 'widgetcore' ) ); }

		protected function register_controls() {

			$this->wgcr_content_section( 'sec_content', esc_html__( 'محتوا', 'widgetcore' ) );
			$this->add_control( 'icon', array(
				'label'   => esc_html__( 'آیکون', 'widgetcore' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
			) );
			$this->wgcr_select_control( 'layout',
				esc_html__( 'جایگاه آیکون', 'widgetcore' ),
				array(
					'top'  => esc_html__( 'بالای متن', 'widgetcore' ),
					'side' => esc_html__( 'کنار متن', 'widgetcore' ),
				),
				'top'
			);
			$this->wgcr_text_control( 'title', esc_html__( 'عنوان', 'widgetcore' ), __( 'عنوان جعبه', 'widgetcore' ) );
			$this->wgcr_text_control( 'description', esc_html__( 'توضیحات', 'widgetcore' ), __( 'توضیح کوتاه درباره این ویژگی…', 'widgetcore' ), array(), \Elementor\Controls_Manager::TEXTAREA );
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

			$this->wgcr_content_section( 'sec_link', esc_html__( 'پیوند', 'widgetcore' ) );
			$this->add_control( 'link', array(
				'label'       => esc_html__( 'پیوند عنوان', 'widgetcore' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array( 'url' => '' ),
				'label_block' => true,
			) );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_icon', esc_html__( 'آیکون', 'widgetcore' ) );
			$this->wgcr_slider_control( 'icon_size', esc_html__( 'اندازه آیکون', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox-ic', 'font-size:{{SIZE}}{{UNIT}};', 12, 90 );
			$this->wgcr_slider_control( 'icon_box', esc_html__( 'اندازه جعبه آیکون', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox-ic', 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};', 24, 160 );
			$this->start_controls_tabs( 'icon_tabs' );
			$this->start_controls_tab( 'icon_tab_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'icon_color', esc_html__( 'رنگ آیکون', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox-ic' );
			$this->wgcr_color_control( 'icon_bg', esc_html__( 'پس‌زمینه آیکون', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox-ic', 'background-color' );
			$this->end_controls_tab();
			$this->start_controls_tab( 'icon_tab_hover', array( 'label' => esc_html__( 'هاور', 'widgetcore' ) ) );
			$this->wgcr_color_control( 'icon_color_hover', esc_html__( 'رنگ آیکون در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox:hover .wgcr-iconbox-ic' );
			$this->wgcr_color_control( 'icon_bg_hover', esc_html__( 'پس‌زمینه آیکون در هاور', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox:hover .wgcr-iconbox-ic', 'background-color' );
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->wgcr_dimensions_control( 'icon_radius', esc_html__( 'گردی گوشه‌های آیکون', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox-ic', 'border-radius' );
			$this->end_controls_section();

			$this->wgcr_style_section( 'sty_texts', esc_html__( 'عنوان و توضیحات', 'widgetcore' ) );
			$this->wgcr_color_control( 'title_color', esc_html__( 'رنگ عنوان', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox-title' );
			$this->wgcr_typography_control( 'title_typo', '{{WRAPPER}} .wgcr-iconbox-title' );
			$this->wgcr_color_control( 'text_color', esc_html__( 'رنگ توضیحات', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox-text' );
			$this->wgcr_typography_control( 'text_typo', '{{WRAPPER}} .wgcr-iconbox-text' );
			$this->wgcr_slider_control( 'texts_gap', esc_html__( 'فاصله آیکون تا متن', 'widgetcore' ), '{{WRAPPER}} .wgcr-iconbox', 'gap:{{SIZE}}{{UNIT}};', 0, 60 );
			$this->end_controls_section();

		}

		protected function render() {
			$s       = $this->get_settings_for_display();
			$title   = isset( $s['title'] ) ? (string) $s['title'] : '';
			$desc    = isset( $s['description'] ) ? (string) $s['description'] : '';
			$icon    = ! empty( $s['icon']['value'] ) ? $s['icon'] : array();
			$layout  = ( isset( $s['layout'] ) && 'side' === $s['layout'] ) ? 'side' : 'top';
			$tag     = ( isset( $s['title_tag'] ) && in_array( $s['title_tag'], array( 'h2', 'h3', 'h4', 'h5', 'h6', 'div' ), true ) ) ? $s['title_tag'] : 'h3';
			$link    = ( isset( $s['link'] ) && is_array( $s['link'] ) && ! empty( $s['link']['url'] ) ) ? $s['link'] : array();
			if ( $link && '' === esc_url( (string) $link['url'] ) ) {
				$link = array();
			}
			if ( '' === trim( $title ) && '' === trim( $desc ) && ! $icon ) {
				return;
			}
			wgcr_iconbox_register_assets();
			wp_enqueue_style( 'wgcr-iconbox' );

			echo '<div class="wgcr-iconbox" data-layout="' . esc_attr( $layout ) . '">';
			if ( $icon ) {
				echo '<span class="wgcr-iconbox-ic">';
				\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
				echo '</span>';
			}
			echo '<div class="wgcr-iconbox-body">';
			if ( '' !== trim( $title ) ) {
				$this->add_inline_editing_attributes( 'title', 'none' );
				echo '<' . tag_escape( $tag ) . ' class="wgcr-iconbox-title">';
				if ( $link ) {
					$rel = array();
					echo '<a href="' . esc_url( $link['url'] ) . '"';
					if ( ! empty( $link['is_external'] ) ) {
						echo ' target="_blank"';
						$rel[] = 'noopener';
						$rel[] = 'noreferrer';
					}
					if ( ! empty( $link['nofollow'] ) ) {
						$rel[] = 'nofollow';
					}
					if ( $rel ) {
						echo ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"';
					}
					echo '><span ';
					$this->print_render_attribute_string( 'title' );
					echo '>' . esc_html( $title ) . '</span></a>';
				} else {
					echo '<span ';
					$this->print_render_attribute_string( 'title' );
					echo '>' . esc_html( $title ) . '</span>';
				}
				echo '</' . tag_escape( $tag ) . '>';
			}
			if ( '' !== trim( $desc ) ) {
				$this->add_inline_editing_attributes( 'description', 'advanced' );
				echo '<div class="wgcr-iconbox-text" ';
				$this->print_render_attribute_string( 'description' );
				echo '>' . wp_kses_post( $desc ) . '</div>';
			}
			echo '</div>';
			echo '</div>';
		}

		protected function content_template() {
			?>
<#
var layout = settings.layout === 'side' ? 'side' : 'top';
var tag = ['h2', 'h3', 'h4', 'h5', 'h6', 'div'].indexOf(settings.title_tag) !== -1 ? settings.title_tag : 'h3';
var title = settings.title || '';
var desc = settings.description || '';
var hasIcon = settings.icon && settings.icon.value;
var ic = '';
if (hasIcon) {
	try {
		if (typeof elementor !== 'undefined' && elementor.helpers && elementor.helpers.renderIcon) {
			var rendered = elementor.helpers.renderIcon(view, settings.icon, { 'aria-hidden': 'true' }, 'i', 'object');
			if (rendered) { ic = (typeof rendered === 'string') ? rendered : (rendered.value || ''); }
		}
	} catch (e) { ic = ''; }
}
var link = settings.link && settings.link.url ? settings.link : null;
var linkAttrs = '';
if (link) {
	var rel = [];
	linkAttrs = ' href="' + link.url + '"';
	if (link.is_external) { linkAttrs += ' target="_blank"'; rel.push('noopener'); rel.push('noreferrer'); }
	if (link.nofollow) { rel.push('nofollow'); }
	if (rel.length) { linkAttrs += ' rel="' + rel.join(' ') + '"'; }
}
view.addInlineEditingAttributes('title', 'none');
view.addInlineEditingAttributes('description', 'advanced');
#>
<# if (title.trim() || desc.trim() || hasIcon) { #>
<div class="wgcr-iconbox" data-layout="{{{ layout }}}">
	<# if (hasIcon) { #><span class="wgcr-iconbox-ic">{{{ ic }}}</span><# } #>
	<div class="wgcr-iconbox-body">
		<# if (title.trim()) { #>
		<{{{ tag }}} class="wgcr-iconbox-title"><# if (link) { #><a{{{ linkAttrs }}}><# } #><span {{{ view.getRenderAttributeString('title') }}}>{{ title }}</span><# if (link) { #></a><# } #></{{{ tag }}}>
		<# } #>
		<# if (desc.trim()) { #>
		<div class="wgcr-iconbox-text" {{{ view.getRenderAttributeString('description') }}}>{{{ desc }}}</div>
		<# } #>
	</div>
</div>
<# } #>
			<?php
		}
	}
}
