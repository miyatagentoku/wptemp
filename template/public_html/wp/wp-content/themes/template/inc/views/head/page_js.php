<?php // ページ個別のJS ?>
<?php if( file_exists( get_template_directory().'/assets/js/'.m_getUniqueResourcePath($post).'.js') ): ?>
	<script src="<?php echo get_template_directory_uri().'/assets/js/'.m_getUniqueResourcePath($post).'.js?v=1'; ?>"></script>
<?php endif; ?>