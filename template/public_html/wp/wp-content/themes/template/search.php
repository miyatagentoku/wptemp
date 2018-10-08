<?php get_header(); ?>
<div class="<?php echo get_post_type(); ?>-search-contents">
<?php if ( is_page() ) : ?>
<?php get_template_part('views/page/'.m_getPageGroupSlug( $post ).'/search'); ?>
<?php elseif ( is_tax() || is_post_type_archive() ) : ?>
<?php get_template_part('views/post/'.get_post_type().'/search'); ?>
<?php endif; ?>

<?php get_footer(); ?>