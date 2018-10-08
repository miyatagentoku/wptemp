<?php get_header(); ?>
<?php if(is_home() || is_front_page()): ?>
	<?php get_template_part('home'); ?>
<?php elseif(is_page()): ?>
<div class="<?php echo $post->post_name; ?>-contents">
	<?php get_template_part('views/page'.m_getPageHierarchySlug($post->ID).'/'.$post->post_name.'/page'); ?>
</div>
<?php endif; ?>
<?php get_footer(); ?>