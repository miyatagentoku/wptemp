<?php // サイドバーの設定 ?>

<?php if ( is_page() ) : ?>

	<?php if ( m_isPageGroup( $post, 'ir' ) ) : // IR情報ページおよびその配下専用のサイドバー読み込み ?>

		<?php get_template_part('inc/views/page/ir/sidebar'); ?>

	<?php endif; ?>

<?php elseif ( get_post_type() == 'voice' ): ?>

	<?php get_template_part('inc/views/post/voice/sidebar'); // 共通サイドバー読み込み ?>

<?php else: ?>

	<?php get_template_part('inc/views/sidebar'); // 共通サイドバー読み込み ?>

<?php endif; ?>