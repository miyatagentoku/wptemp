<?php get_header(); ?>
<div class="<?php echo get_post_type(); ?>-single-contents">
	<?php get_template_part('views/post/'.get_post_type().'/single'); ?>
</div>
<?php get_footer(); ?>