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
	}
}
