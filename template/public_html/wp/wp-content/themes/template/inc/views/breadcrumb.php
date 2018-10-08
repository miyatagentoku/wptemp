<?php // 共通パンくず ?>

		<ol class="c-breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
<?php

	$b = m_getBreadcrumb( $post );

	for($i = 0; $i < count($b); $i++):
?>
			<li class="l-breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
<?php if($i < count($b)-1): ?>
				<a class="l-breadcrumb-item-txt l-breadcrumb-item-txtLink" itemprop="item" href="<?php echo $b[$i]['url']; ?>">
<?php else: ?>
				<span class="l-breadcrumb-item-txt" itemprop="name">
<?php endif; ?>
			<?php echo $b[$i]['txt']; ?>
<?php if($i < count($b)-1): ?>
				</a>>
<?php else: ?>
				</span>
<?php endif; ?>
				<meta itemprop="position" content="<?php echo $i+1; ?>">
			</li>
<?php endfor; ?>
		<!-- c-breadcrumb --></ol>