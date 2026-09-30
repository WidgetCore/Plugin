<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wgcr_comments_defaults' ) ) {
	function wgcr_comments_defaults() {
		return array(
			'form'            => 'yes',
			'list'            => 'yes',
			'title'           => __( 'ارسال دیدگاه', 'widgetcore' ),
			'title_tag'       => 'h3',
			'list_title'      => __( 'دیدگاه‌ها', 'widgetcore' ),
			'list_title_tag'  => 'h2',
			'show_count'      => 'yes',
			'note_before'     => '',
			'note_after'      => '',
			'author'          => 'yes',
			'author_required' => 'yes',
			'author_label'    => __( 'نام', 'widgetcore' ),
			'author_ph'       => '',
			'email'           => 'yes',
			'email_required'  => 'yes',
			'email_label'     => __( 'ایمیل', 'widgetcore' ),
			'email_ph'        => '',
			'url'             => 'yes',
			'url_label'       => __( 'وب‌سایت', 'widgetcore' ),
			'url_ph'          => '',
			'cookies'         => 'yes',
			'cookies_label'   => __( 'نام، ایمیل و وب‌سایت من در این مرورگر ذخیره شود تا دفعهٔ بعد که دیدگاه می‌نویسم دوباره وارد نکنم.', 'widgetcore' ),
			'comment_label'   => __( 'دیدگاه شما', 'widgetcore' ),
			'comment_ph'      => '',
			'rows'            => 6,
			'fields_layout'   => 'row',
			'order_comment'   => 1,
			'order_author'    => 2,
			'order_email'     => 3,
			'order_url'       => 4,
			'order_cookies'   => 5,
			'submit'          => __( 'ثبت دیدگاه', 'widgetcore' ),
			'cancel'          => __( 'لغو پاسخ', 'widgetcore' ),
			'login_only'      => 'no',
			'per_page'        => 0,
			'order'           => 'wp',
			'avatar'          => 'wp',
			'avatar_size'     => 48,
			'date'            => 'yes',
			'reply'           => 'yes',
			'reply_text'      => __( 'پاسخ دادن', 'widgetcore' ),
			'empty_text'      => __( 'هنوز دیدگاهی ثبت نشده است.', 'widgetcore' ),
			'closed_text'     => __( 'دیدگاه‌ها برای این مطلب بسته شده است.', 'widgetcore' ),
			'login_text'      => __( 'برای ثبت دیدگاه باید وارد شوید.', 'widgetcore' ),
			'loggedin_show'   => 'yes',
			'loggedin_text'   => __( 'با نام {name} وارد شده‌اید.', 'widgetcore' ),
			'loggedin_profile' => 'yes',
			'loggedin_profile_text' => __( 'ویرایش نمایه', 'widgetcore' ),
			'loggedin_logout' => 'yes',
			'loggedin_logout_text' => __( 'خروج', 'widgetcore' ),
			'pending_text'    => __( 'دیدگاه شما در انتظار تأیید است.', 'widgetcore' ),
			'post'            => 0,
		);
	}
}

if ( ! function_exists( 'wgcr_comments_heading_tags' ) ) {
	function wgcr_comments_heading_tags() {
		return array( 'h2', 'h3', 'h4', 'h5', 'div' );
	}
}

if ( ! function_exists( 'wgcr_comments_normalize_args' ) ) {
	function wgcr_comments_normalize_args( $args ) {
		$d    = wgcr_comments_defaults();
		$args = wp_parse_args( is_array( $args ) ? $args : array(), $d );

		$bool = static function ( $v ) {
			$v = strtolower( trim( (string) $v ) );
			return in_array( $v, array( 'yes', '1', 'true', 'on' ), true ) ? 'yes' : 'no';
		};
		$pick = static function ( $v, $allowed, $fallback ) {
			$v = strtolower( trim( (string) $v ) );
			return in_array( $v, $allowed, true ) ? $v : $fallback;
		};
		$text = static function ( $v, $fallback ) {
			$v = sanitize_text_field( (string) $v );
			return '' !== $v ? $v : $fallback;
		};
		$num  = static function ( $v, $min, $max, $fallback ) {
			return is_numeric( $v ) ? max( $min, min( $max, (int) $v ) ) : $fallback;
		};

		$out = array();
		foreach ( array( 'form', 'list', 'show_count', 'author', 'author_required', 'email', 'email_required', 'url', 'cookies', 'login_only', 'date', 'reply', 'loggedin_show', 'loggedin_profile', 'loggedin_logout' ) as $key ) {
			$out[ $key ] = $bool( $args[ $key ] );
		}
		foreach ( array( 'title', 'list_title', 'author_ph', 'email_ph', 'url_ph', 'comment_ph' ) as $key ) {
			$out[ $key ] = sanitize_text_field( (string) $args[ $key ] );
		}
		foreach ( array( 'author_label', 'email_label', 'url_label', 'cookies_label', 'comment_label', 'submit', 'cancel', 'reply_text', 'empty_text', 'closed_text', 'login_text', 'pending_text', 'loggedin_text', 'loggedin_profile_text', 'loggedin_logout_text' ) as $key ) {
			$out[ $key ] = $text( $args[ $key ], $d[ $key ] );
		}
		foreach ( array( 'note_before', 'note_after' ) as $key ) {
			$out[ $key ] = wp_kses_post( (string) $args[ $key ] );
		}
		$out['title_tag']      = $pick( $args['title_tag'], wgcr_comments_heading_tags(), $d['title_tag'] );
		$out['list_title_tag'] = $pick( $args['list_title_tag'], wgcr_comments_heading_tags(), $d['list_title_tag'] );
		$out['fields_layout']  = $pick( $args['fields_layout'], array( 'row', 'stack' ), 'row' );
		$out['order']          = $pick( $args['order'], array( 'wp', 'asc', 'desc' ), 'wp' );
		$out['avatar']         = $pick( $args['avatar'], array( 'wp', 'yes', 'no' ), 'wp' );
		$out['rows']           = $num( $args['rows'], 3, 15, $d['rows'] );
		$out['per_page']       = $num( $args['per_page'], 0, 100, 0 );
		$out['avatar_size']    = $num( $args['avatar_size'], 16, 128, $d['avatar_size'] );
		foreach ( array( 'order_comment', 'order_author', 'order_email', 'order_url', 'order_cookies' ) as $key ) {
			$out[ $key ] = $num( $args[ $key ], 1, 9, $d[ $key ] );
		}
		$out['post'] = absint( $args['post'] );
		return $out;
	}
}

if ( ! function_exists( 'wgcr_comments_register_assets' ) ) {
	function wgcr_comments_register_assets() {
		if ( wp_style_is( 'wgcr-comments', 'registered' ) ) {
			return;
		}
		wp_register_style( 'wgcr-comments', WGCR_URL . 'assets/comments/comments.css', array(), WGCR_VER );
	}
}
add_action( 'wp_enqueue_scripts', 'wgcr_comments_register_assets', 5 );
add_action( 'elementor/frontend/after_register_styles', 'wgcr_comments_register_assets' );
add_action( 'elementor/preview/enqueue_styles', 'wgcr_comments_preview_assets' );

if ( ! function_exists( 'wgcr_comments_preview_assets' ) ) {
	function wgcr_comments_preview_assets() {
		wgcr_comments_register_assets();
		wp_enqueue_style( 'wgcr-comments' );
	}
}

if ( ! function_exists( 'wgcr_comments_claim' ) ) {
	function wgcr_comments_claim( $key = '' ) {
		static $claimed = array();
		if ( '' === $key ) {
			$claimed = array();
			return true;
		}
		if ( isset( $claimed[ $key ] ) ) {
			return false;
		}
		$claimed[ $key ] = true;
		return true;
	}
}

if ( ! function_exists( 'wgcr_comments_owner' ) ) {
	function wgcr_comments_owner( $post_id = 0, $mark = false ) {
		static $owned = array();
		if ( $mark ) {
			if ( $post_id > 0 ) {
				$owned[ (int) $post_id ] = true;
			} else {
				$owned = array();
			}
			return true;
		}
		return isset( $owned[ (int) $post_id ] );
	}
}

if ( ! function_exists( 'wgcr_comments_owns_current' ) ) {
	function wgcr_comments_owns_current() {
		$post = get_post();
		return $post instanceof WP_Post && wgcr_comments_owner( $post->ID );
	}
}

if ( ! function_exists( 'wgcr_comments_theme_template' ) ) {
	function wgcr_comments_theme_template( $template ) {
		return wgcr_comments_owns_current() ? WGCR_DIR . 'includes/comments/index.php' : $template;
	}
}
add_filter( 'comments_template', 'wgcr_comments_theme_template', 99 );

if ( ! function_exists( 'wgcr_comments_theme_block' ) ) {
	function wgcr_comments_theme_block( $pre, $parsed_block ) {
		if ( null !== $pre || empty( $parsed_block['blockName'] ) ) {
			return $pre;
		}
		if ( in_array( $parsed_block['blockName'], array( 'core/comments', 'core/post-comments-form', 'core/post-comments' ), true ) && wgcr_comments_owns_current() ) {
			return '';
		}
		return $pre;
	}
}
add_filter( 'pre_render_block', 'wgcr_comments_theme_block', 10, 2 );

if ( ! function_exists( 'wgcr_comments_is_editor' ) ) {
	function wgcr_comments_is_editor() {
		if ( ! class_exists( '\Elementor\Plugin', false ) ) {
			return false;
		}
		$elementor = \Elementor\Plugin::$instance;
		return ( isset( $elementor->editor ) && $elementor->editor->is_edit_mode() ) || ( isset( $elementor->preview ) && $elementor->preview->is_preview_mode() );
	}
}

if ( ! function_exists( 'wgcr_comments_samples' ) ) {
	function wgcr_comments_samples( $post_id ) {
		$rows = array(
			array( 1, 0, __( 'مریم احمدی', 'widgetcore' ), __( 'این یک دیدگاه نمونه است تا ظاهر فهرست را در ویرایشگر ببینید.', 'widgetcore' ) ),
			array( 2, 1, __( 'علی رضایی', 'widgetcore' ), __( 'این یک پاسخ نمونه به دیدگاه بالاست.', 'widgetcore' ) ),
			array( 3, 0, __( 'سارا کریمی', 'widgetcore' ), __( 'دیدگاه نمونهٔ دیگری برای پیش‌نمایش.', 'widgetcore' ) ),
		);
		$now  = time();
		$out  = array();
		foreach ( $rows as $i => $row ) {
			$ts    = $now - ( ( count( $rows ) - $i ) * DAY_IN_SECONDS );
			$out[] = new WP_Comment(
				(object) array(
					'comment_ID'           => $row[0],
					'comment_post_ID'      => (int) $post_id,
					'comment_author'       => $row[2],
					'comment_author_email' => 'sample' . $row[0] . '@example.invalid',
					'comment_author_url'   => '',
					'comment_author_IP'    => '',
					'comment_date'         => gmdate( 'Y-m-d H:i:s', $ts ),
					'comment_date_gmt'     => gmdate( 'Y-m-d H:i:s', $ts ),
					'comment_content'      => $row[3],
					'comment_karma'        => 0,
					'comment_approved'     => '1',
					'comment_agent'        => '',
					'comment_type'         => 'comment',
					'comment_parent'       => $row[1],
					'user_id'              => 0,
				)
			);
		}
		return $out;
	}
}

if ( ! function_exists( 'wgcr_comments_render_item' ) ) {
	function wgcr_comments_render_item( $comment, $args, $depth ) {
		$parent = ! empty( $args['has_children'] ) ? 'parent' : '';
		?>
<li id="comment-<?php comment_ID(); ?>" <?php comment_class( $parent, $comment ); ?>>
	<article class="wgcr-comment">
	<div class="wgcr-comment-body" id="div-comment-<?php comment_ID(); ?>">
		<header class="wgcr-comment-head">
			<?php if ( $args['avatar_size'] > 0 ) : ?>
				<?php echo wp_kses( (string) get_avatar( $comment, $args['avatar_size'], '', '', array( 'class' => 'wgcr-comments-avatar' ) ), wgcr_comments_avatar_kses() ); ?>
			<?php endif; ?>
			<div class="wgcr-comment-meta">
				<span class="wgcr-comment-author"><?php comment_author_link( $comment ); ?></span>
				<?php if ( ! empty( $args['wgcr_date'] ) ) : ?>
					<time class="wgcr-comment-date" datetime="<?php comment_time( 'c' ); ?>"><?php comment_date( '', $comment ); ?></time>
				<?php endif; ?>
			</div>
		</header>
		<?php if ( '0' === (string) $comment->comment_approved ) : ?>
			<p class="wgcr-comments-pending"><?php echo esc_html( $args['wgcr_pending'] ); ?></p>
		<?php endif; ?>
		<div class="wgcr-comment-text"><?php comment_text( $comment ); ?></div>
		<?php if ( ! empty( $args['wgcr_reply'] ) ) : ?>
			<footer class="wgcr-comment-foot">
				<?php
				comment_reply_link(
					array_merge(
						$args,
						array(
							'add_below'  => 'div-comment',
							'depth'      => $depth,
							'max_depth'  => $args['max_depth'],
							'before'     => '<span class="wgcr-comment-reply">',
							'after'      => '</span>',
							'reply_text' => $args['wgcr_reply_text'],
						)
					),
					$comment
				);
				?>
			</footer>
		<?php endif; ?>
	</div>
		<?php
	}
}

if ( ! function_exists( 'wgcr_comments_end_item' ) ) {
	function wgcr_comments_end_item() {
		echo "\t</article>\n</li>\n";
	}
}

if ( ! function_exists( 'wgcr_comments_avatar_kses' ) ) {
	function wgcr_comments_avatar_kses() {
		return array(
			'img' => array(
				'alt'      => true,
				'src'      => true,
				'srcset'   => true,
				'class'    => true,
				'height'   => true,
				'width'    => true,
				'loading'  => true,
				'decoding' => true,
			),
		);
	}
}

if ( ! function_exists( 'wgcr_comments_page_url' ) ) {
	function wgcr_comments_page_url( $post_id, $page, $default ) {
		global $wp_rewrite;
		$url = get_permalink( $post_id );
		if ( (int) $page === (int) $default ) {
			return $url . '#comments';
		}
		if ( $wp_rewrite->using_permalinks() ) {
			return user_trailingslashit( trailingslashit( $url ) . $wp_rewrite->comments_pagination_base . '-' . (int) $page, 'commentpaged' ) . '#comments';
		}
		return add_query_arg( 'cpage', (int) $page, $url ) . '#comments';
	}
}

if ( ! function_exists( 'wgcr_comments_pagination' ) ) {
	function wgcr_comments_pagination( $post_id, $page, $pages ) {
		if ( $pages < 2 ) {
			return;
		}
		$default = 'newest' === get_option( 'default_comments_page' ) ? $pages : 1;
		$shown   = array_unique( array_filter( array( 1, $page - 1, $page, $page + 1, $pages ), static function ( $n ) use ( $pages ) {
			return $n >= 1 && $n <= $pages;
		} ) );
		sort( $shown );
		?>
<nav class="wgcr-comments-pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی دیدگاه‌ها', 'widgetcore' ); ?>">
	<ul class="wgcr-comments-pages">
		<?php if ( $page > 1 ) : ?>
			<li><a class="wgcr-comments-page wgcr-comments-page--prev" rel="prev" href="<?php echo esc_url( wgcr_comments_page_url( $post_id, $page - 1, $default ) ); ?>"><?php esc_html_e( 'قبلی', 'widgetcore' ); ?></a></li>
		<?php endif; ?>
		<?php
		$last = 0;
		foreach ( $shown as $n ) :
			if ( $last && $n - $last > 1 ) :
				?>
				<li><span class="wgcr-comments-gap" aria-hidden="true">…</span></li>
			<?php endif; ?>
			<?php if ( $n === $page ) : ?>
				<li><span class="wgcr-comments-page is-current" aria-current="page"><?php echo esc_html( number_format_i18n( $n ) ); ?></span></li>
			<?php else : ?>
				<?php /* translators: %d: page number. */ ?>
				<li><a class="wgcr-comments-page" href="<?php echo esc_url( wgcr_comments_page_url( $post_id, $n, $default ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'صفحه %d', 'widgetcore' ), $n ) ); ?>"><?php echo esc_html( number_format_i18n( $n ) ); ?></a></li>
			<?php endif; ?>
			<?php
			$last = $n;
		endforeach;
		?>
		<?php if ( $page < $pages ) : ?>
			<li><a class="wgcr-comments-page wgcr-comments-page--next" rel="next" href="<?php echo esc_url( wgcr_comments_page_url( $post_id, $page + 1, $default ) ); ?>"><?php esc_html_e( 'بعدی', 'widgetcore' ); ?></a></li>
		<?php endif; ?>
	</ul>
</nav>
		<?php
	}
}

if ( ! function_exists( 'wgcr_comments_input_html' ) ) {
	function wgcr_comments_input_html( $name, $type, $label, $placeholder, $required, $value, $max, $autocomplete ) {
		return sprintf(
			'<p class="comment-form-%1$s wgcr-comments-field wgcr-comments-field--%1$s"><label for="%1$s">%2$s%3$s</label><input class="wgcr-comments-input" id="%1$s" name="%1$s" type="%4$s" value="%5$s" size="30" maxlength="%6$d" autocomplete="%7$s"%8$s%9$s></p>',
			esc_attr( $name ),
			esc_html( $label ),
			$required ? ' <span class="required" aria-hidden="true">*</span>' : '',
			esc_attr( $type ),
			esc_attr( $value ),
			(int) $max,
			esc_attr( $autocomplete ),
			'' !== $placeholder ? ' placeholder="' . esc_attr( $placeholder ) . '"' : '',
			$required ? ' required aria-required="true"' : ''
		);
	}
}

if ( ! function_exists( 'wgcr_comments_build_fields' ) ) {
	function wgcr_comments_build_fields( $a, $fields ) {
		$commenter = wp_get_current_commenter();
		unset( $fields['author'], $fields['email'], $fields['url'] );
		if ( 'yes' === $a['author'] ) {
			$fields['author'] = wgcr_comments_input_html( 'author', 'text', $a['author_label'], $a['author_ph'], 'yes' === $a['author_required'], $commenter['comment_author'], 245, 'name' );
		}
		if ( 'yes' === $a['email'] ) {
			$fields['email'] = wgcr_comments_input_html( 'email', 'email', $a['email_label'], $a['email_ph'], 'yes' === $a['email_required'], $commenter['comment_author_email'], 100, 'email' );
		}
		if ( 'yes' === $a['url'] ) {
			$fields['url'] = wgcr_comments_input_html( 'url', 'url', $a['url_label'], $a['url_ph'], false, $commenter['comment_author_url'], 200, 'url' );
		}
		if ( isset( $fields['cookies'] ) ) {
			if ( 'yes' === $a['cookies'] ) {
				$fields['cookies'] = sprintf(
					'<p class="comment-form-cookies-consent wgcr-comments-field wgcr-comments-cookies"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"%1$s> <label for="wp-comment-cookies-consent">%2$s</label></p>',
					empty( $commenter['comment_author_email'] ) ? '' : ' checked',
					esc_html( $a['cookies_label'] )
				);
			} else {
				unset( $fields['cookies'] );
			}
		}
		return $fields;
	}
}

if ( ! function_exists( 'wgcr_comments_sort_fields' ) ) {
	function wgcr_comments_sort_fields( $a, $fields ) {
		$rank  = array();
		$index = 0;
		foreach ( array_keys( $fields ) as $key ) {
			$position  = isset( $a[ 'order_' . $key ] ) ? $a[ 'order_' . $key ] : 99;
			$rank[ $key ] = array( $position, $index++ );
		}
		uksort(
			$fields,
			static function ( $x, $y ) use ( $rank ) {
				return $rank[ $x ] <=> $rank[ $y ];
			}
		);
		return $fields;
	}
}

if ( ! function_exists( 'wgcr_comments_form_args' ) ) {
	function wgcr_comments_form_args( $a, $post, $editor ) {
		$heading = '' !== $a['title'] ? $a['title_tag'] : 'div';
		$class   = 'comment-reply-title wgcr-comments-form-title' . ( '' !== $a['title'] ? '' : ' wgcr-comments-sr' );
		$user    = wp_get_current_user();
		$logged = '';
		if ( 'yes' === $a['loggedin_show'] ) {
			$logged = '<p class="logged-in-as wgcr-comments-loggedin">' . str_replace( '{name}', esc_html( $user->display_name ), esc_html( $a['loggedin_text'] ) );
			if ( 'yes' === $a['loggedin_profile'] ) {
				$logged .= ' <a href="' . esc_url( get_edit_user_link() ) . '">' . esc_html( $a['loggedin_profile_text'] ) . '</a>';
			}
			if ( 'yes' === $a['loggedin_logout'] ) {
				$logged .= ' <a href="' . esc_url( wp_logout_url( get_permalink( $post ) ) ) . '">' . esc_html( $a['loggedin_logout_text'] ) . '</a>';
			}
			$logged .= '</p>';
		}
		return array(
			'comment_field'        => sprintf(
				'<p class="comment-form-comment wgcr-comments-field wgcr-comments-field--comment"><label for="comment">%1$s <span class="required" aria-hidden="true">*</span></label><textarea class="wgcr-comments-input" id="comment" name="comment" cols="45" rows="%2$d" maxlength="65525"%3$s required aria-required="true"></textarea></p>',
				esc_html( $a['comment_label'] ),
				(int) $a['rows'],
				'' !== $a['comment_ph'] ? ' placeholder="' . esc_attr( $a['comment_ph'] ) . '"' : ''
			),
			'must_log_in'          => sprintf(
				'<p class="must-log-in wgcr-comments-notice">%1$s <a href="%2$s">%3$s</a></p>',
				esc_html( $a['login_text'] ),
				esc_url( wp_login_url( get_permalink( $post ) ) ),
				esc_html__( 'ورود', 'widgetcore' )
			),
			'logged_in_as'         => $logged,
			'comment_notes_before' => '',
			'comment_notes_after'  => '' !== $a['note_after'] ? '<div class="comment-notes wgcr-comments-note">' . wp_kses_post( $a['note_after'] ) . '</div>' : '',
			'action'               => $editor ? '#' : site_url( '/wp-comments-post.php' ),
			'class_container'      => 'comment-respond wgcr-comments-respond',
			'class_form'           => 'comment-form wgcr-comments-form',
			'class_submit'         => 'submit wgcr-comments-submit',
			/* translators: %s: name of the comment author being replied to. */
			'title_reply_to'       => __( 'پاسخ به %s', 'widgetcore' ),
			'title_reply'          => $a['title'],
			'title_reply_before'   => '<' . tag_escape( $heading ) . ' id="reply-title" class="' . esc_attr( $class ) . '">',
			'title_reply_after'    => '</' . tag_escape( $heading ) . '>',
			'cancel_reply_before'  => ' <small class="wgcr-comments-cancel">',
			'cancel_reply_after'   => '</small>',
			'cancel_reply_link'    => $a['cancel'],
			'label_submit'         => $a['submit'],
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			'submit_field'         => '<p class="form-submit wgcr-comments-actions">%1$s %2$s</p>',
			'format'               => 'html5',
		);
	}
}

if ( ! function_exists( 'wgcr_comments_print_form' ) ) {
	function wgcr_comments_print_form( $a, $post, $editor ) {
		$flags = ( 'yes' === $a['author'] && 'yes' === $a['author_required'] ? '1' : '0' )
			. ( 'yes' === $a['email'] && 'yes' === $a['email_required'] ? '1' : '0' )
			. ( 'yes' === $a['login_only'] ? '1' : '0' );
		$top   = static function () use ( $a, $post, $flags ) {
			if ( '' !== $a['note_before'] ) {
				echo '<div class="comment-notes wgcr-comments-note">' . wp_kses_post( $a['note_before'] ) . '</div>';
			}
			echo '<input type="hidden" name="wgcr_cf" value="' . esc_attr( wgcr_comments_token( $post->ID, $flags ) ) . '">';
		};
		$build = static function ( $fields ) use ( $a ) {
			return wgcr_comments_build_fields( $a, $fields );
		};
		$sort  = static function ( $fields ) use ( $a ) {
			return wgcr_comments_sort_fields( $a, $fields );
		};
		$hooks = array(
			array( 'comment_form_top', $top, 10 ),
			array( 'comment_form_default_fields', $build, 20 ),
			array( 'comment_form_fields', $sort, 20 ),
		);
		if ( 'yes' === $a['login_only'] ) {
			$hooks[] = array( 'pre_option_comment_registration', '__return_true', 10 );
		}
		if ( $editor ) {
			$hooks[] = array(
				'comment_form_submit_button',
				static function ( $button ) {
					return str_replace( '<button ', '<button disabled ', $button );
				},
				20,
			);
		}
		foreach ( $hooks as $hook ) {
			add_filter( $hook[0], $hook[1], $hook[2] );
		}
		comment_form( wgcr_comments_form_args( $a, $post, $editor ), $post );
		foreach ( $hooks as $hook ) {
			remove_filter( $hook[0], $hook[1], $hook[2] );
		}
	}
}

if ( ! function_exists( 'wgcr_comments_print_list' ) ) {
	function wgcr_comments_print_list( $a, $post, $editor, $can_reply ) {
		if ( $editor ) {
			$comments = wgcr_comments_samples( $post->ID );
		} else {
			$query = array(
				'post_id' => $post->ID,
				'status'  => 'approve',
				'orderby' => 'comment_date_gmt',
				'order'   => 'ASC',
			);
			if ( is_user_logged_in() ) {
				$query['include_unapproved'] = array( get_current_user_id() );
			} else {
				$email = wp_get_unapproved_comment_author_email();
				if ( $email ) {
					$query['include_unapproved'] = array( $email );
				}
			}
			$comments = get_comments( $query );
		}
		$total = count( $comments );
		?>
<section class="wgcr-comments-listwrap">
		<?php echo wgcr_comments_claim( 'anchor' ) ? '<div class="wgcr-comments-anchor" id="comments"></div>' : ''; ?>
		<?php if ( '' !== $a['list_title'] ) : ?>
			<<?php echo tag_escape( $a['list_title_tag'] ); ?> class="wgcr-comments-title"><?php echo esc_html( $a['list_title'] ); ?><?php echo 'yes' === $a['show_count'] ? ' <span class="wgcr-comments-count">' . esc_html( number_format_i18n( $total ) ) . '</span>' : ''; ?></<?php echo tag_escape( $a['list_title_tag'] ); ?>>
		<?php endif; ?>
		<?php if ( ! $total ) : ?>
			<p class="wgcr-comments-empty"><?php echo esc_html( $a['empty_text'] ); ?></p>
		<?php else : ?>
			<?php
			$threaded = (bool) get_option( 'thread_comments' );
			$per_page = $a['per_page'] > 0 ? $a['per_page'] : ( get_option( 'page_comments' ) ? (int) get_option( 'comments_per_page' ) : 0 );
			$walker   = new Walker_Comment();
			$roots    = $threaded ? $walker->get_number_of_root_elements( $comments ) : $total;
			$pages    = $per_page > 0 ? max( 1, (int) ceil( $roots / $per_page ) ) : 1;
			$page     = (int) get_query_var( 'cpage' );
			if ( $page < 1 ) {
				$page = 'newest' === get_option( 'default_comments_page' ) ? $pages : 1;
			}
			$page     = max( 1, min( $pages, $page ) );
			$desc     = 'wp' === $a['order'] ? 'desc' === get_option( 'comment_order' ) : 'desc' === $a['order'];
			if ( 'yes' === $a['avatar'] ) {
				add_filter( 'pre_option_show_avatars', '__return_true' );
			}
			?>
			<ol class="wgcr-comments-list">
				<?php
				wp_list_comments(
					array(
						'style'             => 'ol',
						'type'              => 'all',
						'callback'          => 'wgcr_comments_render_item',
						'end-callback'      => 'wgcr_comments_end_item',
						'avatar_size'       => 'no' === $a['avatar'] ? 0 : $a['avatar_size'],
						'per_page'          => $per_page,
						'page'              => $per_page > 0 ? $page : 0,
						'max_depth'         => $threaded ? max( 2, (int) get_option( 'thread_comments_depth' ) ) : -1,
						'reverse_top_level' => $desc,
						'wgcr_date'         => 'yes' === $a['date'],
						'wgcr_reply'        => $can_reply,
						'wgcr_reply_text'   => $a['reply_text'],
						'wgcr_pending'      => $a['pending_text'],
					),
					$comments
				);
				?>
			</ol>
			<?php
			remove_filter( 'pre_option_show_avatars', '__return_true' );
			wgcr_comments_pagination( $post->ID, $page, $pages );
			?>
		<?php endif; ?>
</section>
		<?php
	}
}

if ( ! function_exists( 'wgcr_comments_print' ) ) {
	function wgcr_comments_print( $args, $uid = '', $editor = false ) {
		$a    = wgcr_comments_normalize_args( $args );
		$post = $a['post'] > 0 ? get_post( $a['post'] ) : get_post();
		if ( ! $post instanceof WP_Post ) {
			return;
		}
		if ( ! $editor ) {
			if ( ! post_type_supports( $post->post_type, 'comments' ) || post_password_required( $post ) ) {
				return;
			}
			if ( ! is_post_publicly_viewable( $post ) && ! current_user_can( 'read_post', $post->ID ) ) {
				return;
			}
		}
		wgcr_comments_register_assets();
		wp_enqueue_style( 'wgcr-comments' );
		if ( $editor ) {
			add_filter( 'comments_open', '__return_true', 99 );
		} else {
			wgcr_comments_owner( $post->ID, true );
		}

		$open      = $editor || comments_open( $post );
		$show_form = 'yes' === $a['form'] && wgcr_comments_claim( 'form' );
		$can_reply = 'yes' === $a['reply'] && $open && $show_form && (bool) get_option( 'thread_comments' );
		if ( $can_reply && ! $editor ) {
			wp_enqueue_script( 'comment-reply' );
		}
		$id    = sanitize_html_class( '' !== (string) $uid ? (string) $uid : (string) $post->ID );
		$class = 'wgcr-comments wgcr-comments--fields-' . $a['fields_layout'];
		?>
<div class="<?php echo esc_attr( $class ); ?>" id="wgcr-comments-<?php echo esc_attr( $id ); ?>">
		<?php
		if ( 'yes' === $a['list'] ) {
			wgcr_comments_print_list( $a, $post, $editor, $can_reply );
		}
		if ( $show_form ) :
			?>
	<div class="wgcr-comments-formwrap">
			<?php if ( $open ) : ?>
				<?php wgcr_comments_print_form( $a, $post, $editor ); ?>
			<?php else : ?>
				<p class="wgcr-comments-closed wgcr-comments-notice"><?php echo esc_html( $a['closed_text'] ); ?></p>
			<?php endif; ?>
	</div>
		<?php endif; ?>
</div>
		<?php
		remove_filter( 'comments_open', '__return_true', 99 );
	}
}
