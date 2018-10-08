<?php get_header(); ?>
<div class="<?php echo get_post_type(); ?>-taxonomy-contents">
	<?php get_template_part('views/post/'.get_post_type().'/taxonomy'); ?>
</div>
<?php get_footer(); ?>