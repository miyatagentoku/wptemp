<?php // ページ個別のCSS ?>
<?php if( file_exists( get_template_directory().'/assets/css'.m_getUniqueResourcePath($post).'.css') ): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri().'/assets/css'.m_getUniqueResourcePath($post).'.css?v=1'; ?>" media="print">
<?php endif; ?>