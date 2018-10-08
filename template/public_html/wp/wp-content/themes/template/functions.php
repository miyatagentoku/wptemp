<?php

$theme_includes = apply_filters('theme_includes',
    array(
        'inc/functions/functions-admin.php',			// 管理画面用アクションフック
        'inc/functions/functions-actionhook.php',		// 管理画面以外でのアクションフック
        'inc/functions/functions-common.php',			// 共通関数など
        'inc/functions/functions-customposttype.php',	// カスタム投稿タイプの設定
        'inc/functions/functions-plugins.php',			// 特定のプラグイン使用時の処理の記述
        'inc/functions/functions-dao.php',				// 高度な改修用 DBアクセスオブジェクトを使用した関数など
        'inc/functions/functions-template.php'			// 案件ごとの固有の関数など
    ));
foreach ( $theme_includes as $include ) { locate_template( $include, true ); }