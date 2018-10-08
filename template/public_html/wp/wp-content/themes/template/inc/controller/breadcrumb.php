<?php // 【パンくず用】ページ個別の読み込みソースの設定などここで行う ?>

<?php if(is_home() || is_front_page()): ?>
<?php else: ?>
	<?php get_template_part('inc/views/breadcrumb'); // 共通パンくず ?>
<?php endif; ?>