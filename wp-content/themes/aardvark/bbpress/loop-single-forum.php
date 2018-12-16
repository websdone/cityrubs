<?php

/**
 * Forums Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>

	<li class="bbp-forum-info">

		<?php if ( bbp_is_user_home() && bbp_is_subscriptions() ) : ?>

			<span class="bbp-row-actions">

				<?php do_action( 'bbp_theme_before_forum_subscription_action' ); ?>

				<?php bbp_forum_subscription_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>

				<?php do_action( 'bbp_theme_after_forum_subscription_action' ); ?>

			</span>

		<?php endif; ?>

		<?php do_action( 'bbp_theme_before_forum_title' ); ?>

		<a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>" title="<?php bbp_forum_title(); ?>">
			
			<?php bbp_forum_title(); ?> 		
			
			<?php if ( bbp_is_forum_archive() ) { ?>
				<?php do_action( 'bbp_theme_before_forum_description' ); ?>
				<div class="bbp-forum-content"><?php the_content(); ?></div>
				<?php do_action( 'bbp_theme_after_forum_description' ); ?>
			<?php } ?>	
					
		</a>

		<?php do_action( 'bbp_theme_after_forum_title' ); ?>
		
		<?php if ( ! bbp_is_forum_archive() ) { ?>
			<?php do_action( 'bbp_theme_before_forum_description' ); ?>
			<div class="bbp-forum-content"><?php the_content(); ?></div>
			<?php do_action( 'bbp_theme_after_forum_description' ); ?>
		<?php } ?>	

		<?php do_action( 'bbp_theme_before_forum_sub_forums' ); ?>

		<?php ghostpool_bbp_list_forums( array (
		'before'            => '<ul class="bbp-forums-list">',
		'after'             => '</ul>',
		'link_before'       => '<li class="bbp-forum">',
		'link_after'        => '</li>',
		'count_before'      => '<div class="topic-reply-counts">' . esc_html__( 'Topics', 'aardvark' ) . ': ',
		'count_after'       => '</div>',
		'count_sep'         => '<br />' . esc_html__( 'Posts', 'aardvark' ) . ': ',
		'separator'         => '<div class="gp-clear"></div>',
		'forum_id'          => '',
		'show_topic_count'  => true,
		'show_reply_count'  => false,
		'show_post_count'  => true,
		'show_freshness_link' => true,
		)); ?>

		<?php do_action( 'bbp_theme_after_forum_sub_forums' ); ?>

		<?php bbp_forum_row_actions(); ?>

	</li>

	<li class="bbp-forum-topic-count">
		<div class="topic-reply-counts"><?php esc_html_e( 'Topics', 'aardvark' ); ?>: <?php bbp_forum_topic_count(); ?></div>
		<div class="topic-reply-counts"><?php esc_html_e( 'Posts', 'aardvark' ); ?>: <?php bbp_show_lead_topic() ? bbp_forum_reply_count() : bbp_forum_post_count(); ?></div>
	</li>

	<li class="bbp-forum-freshness">

		<p class="bbp-topic-meta">

			<?php do_action( 'bbp_theme_before_topic_author' ); ?>

			<span class="bbp-topic-freshness-author"><?php bbp_author_link( array( 'post_id' => bbp_get_forum_last_active_id(), 'size' => 14 ) ); ?></span>

			<?php do_action( 'bbp_theme_after_topic_author' ); ?>

		</p>
				
		<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>

		<?php bbp_forum_freshness_link(); ?>

		<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>
		
	</li>

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->