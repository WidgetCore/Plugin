<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! trait_exists( 'WGCR_Size_Controls' ) ) {

	trait WGCR_Size_Controls {

		protected function wgcr_hover_selectors( $selectors ) {
			$hover = array();
			foreach ( (array) $selectors as $selector => $rule ) {
				$parts = array();
				foreach ( explode( ',', $selector ) as $part ) {
					$parts[] = trim( $part ) . ':hover';
				}
				$hover[ implode( ',', $parts ) ] = $rule;
			}
			return $hover;
		}

		protected function wgcr_size_control( $id, $args, $with_hover = true ) {
			if ( ! $with_hover ) {
				$this->add_responsive_control( $id, $args );
				return;
			}
			$tabs = array();
			if ( isset( $args['condition'] ) ) {
				$tabs['condition'] = $args['condition'];
			}
			$this->start_controls_tabs( $id . '_size_tabs', $tabs );
			$this->start_controls_tab( $id . '_size_normal', array( 'label' => esc_html__( 'عادی', 'widgetcore' ) ) );
			$this->add_responsive_control( $id, $args );
			$this->end_controls_tab();
			$this->start_controls_tab( $id . '_size_hover', array( 'label' => esc_html__( 'هاور', 'widgetcore' ) ) );
			unset( $args['default'], $args['desktop_default'], $args['tablet_default'], $args['mobile_default'], $args['description'] );
			$args['selectors'] = $this->wgcr_hover_selectors( isset( $args['selectors'] ) ? $args['selectors'] : array() );
			$this->add_responsive_control( $id . '_hover', $args );
			$this->end_controls_tab();
			$this->end_controls_tabs();
		}

		protected function wgcr_content_section( $id, $label ) {
			$this->start_controls_section( $id, array( 'label' => $label ) );
		}

		protected function wgcr_style_section( $id, $label ) {
			$this->start_controls_section(
				$id,
				array(
					'label' => $label,
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);
		}

		protected function wgcr_switch_control( $id, $label, $default = 'yes', $condition = array() ) {
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

		protected function wgcr_text_control( $id, $label, $default = '', $condition = array(), $type = null ) {
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

		protected function wgcr_number_control( $id, $label, $default, $min, $max, $condition = array() ) {
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

		protected function wgcr_select_control( $id, $label, $options, $default ) {
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

		protected function wgcr_heading_control( $id, $label ) {
			$this->add_control(
				$id,
				array(
					'label'     => $label,
					'type'      => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);
		}

		protected function wgcr_color_control( $id, $label, $selector, $property = 'color', $extra = array() ) {
			$this->add_control(
				$id,
				array_merge(
					array(
						'label'     => $label,
						'type'      => \Elementor\Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => array( $selector => $property . ':{{VALUE}};' ),
					),
					$extra
				)
			);
		}

		protected function wgcr_typography_control( $id, $selector, $defaults = array(), $extra = array() ) {
			$fields = array();
			if ( isset( $defaults['weight'] ) ) {
				$fields['font_weight'] = array( 'default' => $defaults['weight'] );
			}
			if ( isset( $defaults['size'] ) ) {
				$fields['font_size'] = array( 'default' => array( 'size' => $defaults['size'], 'unit' => 'px' ) );
			}
			if ( isset( $defaults['line_height'] ) ) {
				$fields['line_height'] = array( 'default' => array( 'size' => $defaults['line_height'], 'unit' => 'px' ) );
			}
			$group = array_merge(
				array(
					'name'     => $id,
					'label'    => esc_html__( 'تایپوگرافی', 'widgetcore' ),
					'selector' => $selector,
				),
				$extra
			);
			if ( $fields ) {
				$group['fields_options'] = $fields;
			}
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), $group );
		}

		protected function wgcr_border_control( $id, $selector, $extra = array(), $defaults = array() ) {
			$fields = array();
			if ( isset( $defaults['type'] ) ) {
				$fields['border'] = array( 'default' => $defaults['type'] );
			}
			if ( isset( $defaults['width'] ) ) {
				$fields['width'] = array(
					'default' => array(
						'top'      => $defaults['width'],
						'right'    => $defaults['width'],
						'bottom'   => $defaults['width'],
						'left'     => $defaults['width'],
						'unit'     => 'px',
						'isLinked' => true,
					),
				);
			}
			if ( isset( $defaults['color'] ) ) {
				$fields['color'] = array( 'default' => $defaults['color'] );
			}
			$group = array_merge(
				array(
					'name'     => $id,
					'label'    => esc_html__( 'کادر', 'widgetcore' ),
					'selector' => $selector,
				),
				$extra
			);
			if ( $fields ) {
				$group['fields_options'] = $fields;
			}
			$this->add_group_control( \Elementor\Group_Control_Border::get_type(), $group );
		}

		protected function wgcr_shadow_control( $id, $selector, $extra = array() ) {
			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				array_merge(
					array(
						'name'     => $id,
						'label'    => esc_html__( 'سایه', 'widgetcore' ),
						'selector' => $selector,
					),
					$extra
				)
			);
		}

		protected function wgcr_dimensions_control( $id, $label, $selector, $property, $extra = array() ) {
			$this->wgcr_size_control(
				$id,
				array_merge(
					array(
						'label'      => $label,
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', 'em', 'rem', '%' ),
						'selectors'  => array( $selector => $property . ':{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
					),
					$extra
				)
			);
		}

		protected function wgcr_slider_control( $id, $label, $selector, $template, $min, $max, $units = array( 'px' ) ) {
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

		protected function wgcr_style_box( $id, $selector, $with_padding = true ) {
			$this->wgcr_color_control( $id . '_bg', esc_html__( 'پس‌زمینه', 'widgetcore' ), $selector, 'background-color' );
			$this->wgcr_border_control( $id . '_border', $selector );
			$this->wgcr_dimensions_control( $id . '_radius', esc_html__( 'گردی گوشه‌ها', 'widgetcore' ), $selector, 'border-radius' );
			$this->wgcr_shadow_control( $id . '_shadow', $selector );
			if ( $with_padding ) {
				$this->wgcr_dimensions_control( $id . '_padding', esc_html__( 'فاصلهٔ داخلی', 'widgetcore' ), $selector, 'padding' );
			}
		}

		protected function wgcr_style_text( $id, $label, $selector ) {
			$this->wgcr_style_section( $id, $label );
			$this->wgcr_color_control( $id . '_color', esc_html__( 'رنگ', 'widgetcore' ), $selector );
			$this->wgcr_typography_control( $id . '_typo', $selector );
			$this->end_controls_section();
		}

		protected function wgcr_style_states( $id, $selector, $hover_selector, $props ) {
			$this->start_controls_tabs( $id . '_tabs' );
			foreach ( array( 'normal' => array( esc_html__( 'عادی', 'widgetcore' ), $selector ), 'hover' => array( esc_html__( 'هاور', 'widgetcore' ), $hover_selector ) ) as $state => $info ) {
				$this->start_controls_tab( $id . '_' . $state, array( 'label' => $info[0] ) );
				foreach ( $props as $suffix => $prop ) {
					$this->wgcr_color_control( $id . '_' . $state . '_' . $suffix, $prop[0], $info[1], $prop[1] );
				}
				$this->end_controls_tab();
			}
			$this->end_controls_tabs();
		}
	}
}
