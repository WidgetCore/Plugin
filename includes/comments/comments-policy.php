<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_comments_token' ) ) {
	function wgcr_comments_token( $post_id, $flags ) {
		$payload = '1.' . (int) $post_id . '.' . $flags;
		return $payload . '.' . substr( hash_hmac( 'sha256', $payload, wp_salt( 'auth' ) ), 0, 40 );
	}
}

if ( ! function_exists( 'wgcr_comments_parse_token' ) ) {
	function wgcr_comments_parse_token( $token ) {
		if ( ! is_string( $token ) || ! preg_match( '/^1\.([0-9]{1,20})\.([01]{3})\.([0-9a-f]{40})$/', $token, $m ) ) {
			return false;
		}
		if ( ! hash_equals( wgcr_comments_token( (int) $m[1], $m[2] ), $token ) ) {
			return false;
		}
		return array(
			'post'  => (int) $m[1],
			'name'  => '1' === $m[2][0],
			'email' => '1' === $m[2][1],
			'login' => '1' === $m[2][2],
		);
	}
}

if ( ! function_exists( 'wgcr_comments_policy_die' ) ) {
	function wgcr_comments_policy_die( $message ) {
		wp_die(
			'<p>' . esc_html( $message ) . '</p>',
			esc_html__( 'خطا در ثبت دیدگاه', 'widgetcore' ),
			array(
				'response'  => 400,
				'back_link' => true,
			)
		);
	}
}

if ( ! function_exists( 'wgcr_comments_policy_validate' ) ) {
	function wgcr_comments_policy_validate( $data, $policy ) {
		if ( is_user_logged_in() || ! is_array( $data ) ) {
			return $data;
		}
		$author = isset( $data['comment_author'] ) ? trim( (string) $data['comment_author'] ) : '';
		$email  = isset( $data['comment_author_email'] ) ? trim( (string) $data['comment_author_email'] ) : '';
		if ( ( $policy['name'] && '' === $author ) || ( $policy['email'] && '' === $email ) ) {
			wgcr_comments_policy_die( __( 'لطفاً فیلدهای الزامی را پر کنید.', 'widgetcore' ) );
		}
		if ( '' !== $email && ! is_email( $email ) ) {
			wgcr_comments_policy_die( __( 'نشانی ایمیل معتبر وارد کنید.', 'widgetcore' ) );
		}
		return $data;
	}
}

if ( ! function_exists( 'wgcr_comments_policy_apply' ) ) {
	function wgcr_comments_policy_apply( $token, $post_id ) {
		$policy = wgcr_comments_parse_token( $token );
		if ( ! $policy || $policy['post'] !== (int) $post_id ) {
			return false;
		}
		add_filter( 'pre_option_require_name_email', '__return_zero' );
		if ( $policy['login'] ) {
			add_filter( 'pre_option_comment_registration', '__return_true' );
		}
		add_filter(
			'preprocess_comment',
			static function ( $data ) use ( $policy ) {
				return wgcr_comments_policy_validate( $data, $policy );
			},
			1
		);
		return true;
	}
}

add_action( 'pre_comment_on_post', 'wgcr_comments_policy_boot', 1 );

if ( ! function_exists( 'wgcr_comments_policy_boot' ) ) {
	function wgcr_comments_policy_boot( $post_id ) {
		$token = filter_input( INPUT_POST, 'wgcr_cf', FILTER_UNSAFE_RAW );
		wgcr_comments_policy_apply( is_string( $token ) ? $token : '', (int) $post_id );
	}
}
