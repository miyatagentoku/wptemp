<?php // OGPタグ設定 ?>

<?php if ( is_page() ) : ?>

	<?php if ( m_isPageGroup( $post, 'template' ) ) : // 特定のページおよびその配下専用のOGPタグ読み込み ?>

		<?php get_template_part( 'inc/views/page/template/ogp' ); ?>

	<?php endif; ?>

<?php else: ?>

<?php // ページ共通のOGPタグ設定 ?>
	<meta property="og:title" content="">
	<meta property="og:type" content="">
	<meta property="og:url" content="">
	<meta property="og:site_name" content="">
	<meta property="og:image" content="">
	<meta property="og:description" content="">

<?php endif; ?>