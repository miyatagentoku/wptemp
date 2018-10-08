<?php get_header(); ?>
<div class="<?php echo get_post_type(); ?>-archive-contents">
	<?php get_template_part('views/post/'.get_query_var( 'post_type' ).'/archive'); ?>
</div>
<?php get_footer(); ?>