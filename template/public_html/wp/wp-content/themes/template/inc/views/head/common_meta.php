<?php // ページ共通のmeta情報の設定 ?>

        <meta charset="utf-8">

        <?php // title ?>
        <title><?php echo m_getTitle($post); ?></title>

        <!--[if lt IE 9]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
        <meta id="viewport" name='viewport' content="<?php echo m_getUserAgent()==='tb'?'initial-scale=0.6':'width=device-width, initial-scale=1.0'; ?>">

        <meta name="format-detection" content="telephone=no">

        <meta name="description" content="<?php echo m_getDescription($post); ?>">

        <meta name="keywords" content="<?php echo m_getKeywords($post->ID); ?>">

        <link rel="canonical" href="<?php echo m_getCanonical(); ?>">

        <?php // favicon 開始 ?>
        <link rel="shortcut icon" href="/assets/images/common/favicon.ico">
        <link rel="icon" type="image/vnd.microsoft.icon" href="/assets/images/common/favicon.ico">
        <?php // favicon 終了 ?>