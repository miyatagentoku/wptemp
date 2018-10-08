<?php // 検索フォームの設定 ?>

<?php if ( is_page() ) : ?>
<?php get_template_part('inc/views/page/'.m_getPageGroupSlug( $post ).'/search-form'); ?>
<?php elseif ( is_tax() || is_post_type_archive() ) : ?>
<?php get_template_part('inc/views/post/'.get_post_type().'/search-form'); ?>
<?php endif; ?>