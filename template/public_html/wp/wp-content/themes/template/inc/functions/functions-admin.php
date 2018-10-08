<?php
/* ----------------------------------------------------
*
* 共通 【管理画面用】フック関数
*
-------------------------------------------------------*/


/* ----------------------------------------------------
*
* after setup theme
*
-------------------------------------------------------*/

/*  after_setup_theme に関数をフックする
/*---------------------------------------------------------*/
function m_admin_theme_setup() {

    /*  アイキャッチ画像を有効にする
    /*---------------------------------------------------------*/
    add_theme_support( 'post-thumbnails' );

}
add_action( 'after_setup_theme', 'm_admin_theme_setup' );



/* ----------------------------------------------------
*
* init
*
-------------------------------------------------------*/

/*  wp_head(); で自動的に出力される余分なタグを削除する
/*---------------------------------------------------------*/
function m_admin_removeHeadTags () {
    //ブラウザが先読みするために使うタグ
    remove_action('wp_head', 'parent_post_rel_link', 10, 0 ); // 親投稿 link rel="up" [depricated]
    remove_action('wp_head', 'start_post_rel_link', 10, 0 );  // ルートの親投稿 link rel="start" [depricated]
    remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0 ); // link rel="next" & link rel="prev"
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head'); // link rel=next,prev
    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'feed_links_extra', 3);//コメントのフィード
    //現在の文書に対す索引（インデックス）を示すリンクタグ
    remove_action('wp_head', 'index_rel_link' );
    //デフォルト形式のURL(?p=投稿ID)を明示するタグ
    remove_action('wp_head', 'wp_shortlink_wp_head' );
    //EditURI
    remove_action('wp_head', 'rsd_link' );
    //wlwmanifest
    remove_action('wp_head', 'wlwmanifest_link' );
    //generator
    remove_action('wp_head', 'wp_generator' );
    //shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head' );
    //oEmbed関連
    remove_action('wp_head', 'rest_output_link_wp_head' );
    remove_action('wp_head', 'wp_oembed_add_discovery_links' );
    remove_action('wp_head', 'wp_oembed_add_host_js' );
}


/*  Emoji関連
/*---------------------------------------------------------*/
function m_admin_disableEmojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    // add_filter( 'tiny_mce_plugins', 'm_admin_disableEmojis_tinymce' );
}


/*  標準エディタを無効にする
/*---------------------------------------------------------*/
function m_admin_removePostEditorSupport() {
    remove_post_type_support( 'page', 'editor' );
}


/*  init に関数をフックする
/*---------------------------------------------------------*/
function m_hookAdmin_init() {
    m_admin_removeHeadTags();
    m_admin_disableEmojis();
    m_admin_removePostEditorSupport();
}
add_action( 'init', 'm_hookAdmin_init' );



/* ----------------------------------------------------
*
* widgets_init
*
-------------------------------------------------------*/

/*  不要なウィジェット項目を非表示にする
-------------------------------------------------------*/
function m_admin_hideWidgets() {
    unregister_widget('WP_Widget_Pages');            // 固定ページ
    //    unregister_widget('WP_Widget_Calendar');         // カレンダー
    unregister_widget('WP_Widget_Archives');         // アーカイブ
    unregister_widget('WP_Widget_Meta');             // メタ情報
    //    unregister_widget('WP_Widget_Search');           // 検索
    //    unregister_widget('WP_Widget_Text');             // テキスト
    //    unregister_widget('WP_Widget_Categories');       // カテゴリー
    //    unregister_widget('WP_Widget_Recent_Posts');     // 最近の投稿
    unregister_widget('WP_Widget_Recent_Comments');  // 最近のコメント
    //    unregister_widget('WP_Widget_RSS');              // RSS
    //    unregister_widget('WP_Widget_Tag_Cloud');        // タグクラウド
    //    unregister_widget('WP_Nav_Menu_Widget');         // カスタムメニュー
    unregister_widget( 'Akismet_Widget' );           // Akismetウィジェット
}



/*  widgets_init に関数をフックする
/*---------------------------------------------------------*/
function m_hookAdmin_widgets_init() {
    m_admin_hideWidgets();
}
add_action( 'widgets_init', 'm_hookAdmin_widgets_init' );



/* ----------------------------------------------------
*
* admin_menu
*
-------------------------------------------------------*/

/* 管理画面サイドメニューの項目を非表示にする
-------------------------------------------------------*/
function m_admin_hideMenus () {
    // remove_menu_page( 'index.php' );                  // ダッシュボード
     remove_menu_page( 'edit.php' );                   // 投稿
    // remove_menu_page( 'upload.php' );                 // メディア
    // remove_menu_page( 'edit.php?post_type=page' );    // 固定ページ
    remove_menu_page( 'edit-comments.php' );             // コメント
    // remove_menu_page( 'themes.php' );                 // 外観
    // remove_menu_page( 'plugins.php' );                // プラグイン
    // remove_menu_page( 'users.php' );                  // ユーザー
    // remove_menu_page( 'tools.php' );                  // ツール
    // remove_menu_page( 'options-general.php' );        // 設定
}

/*  admin_menu に関数をフックする
/*---------------------------------------------------------*/
function m_hookAdmin_admin_menu() {
    m_admin_hideMenus();
}
add_action('admin_menu', 'm_hookAdmin_admin_menu');



/* ----------------------------------------------------
*
* admin_init
*
-------------------------------------------------------*/

/*  アップデート通知を非表示にする
-------------------------------------------------------*/
function m_admin_removeUpdate() {
    remove_action( 'admin_notices', 'update_nag', 3 );
    remove_action( 'admin_notices', 'maintenance_nag', 10 );
    remove_action( 'load-update-core.php', 'wp_update_plugins' );
    remove_action( 'load-update-core.php', 'wp_update_themes' );
}

/*  editor-style.cssの追加
/*---------------------------------------------------------*/
function m_admin_add_editor_css() {
    add_editor_style('editor-style.css');
}

/*  admin_init に関数をフックする
/*---------------------------------------------------------*/
function m_hookAdmin_admin_init(){
    m_admin_removeUpdate();
    m_admin_add_editor_css();
}
add_action( 'admin_init', 'm_hookAdmin_admin_init' );



/* ----------------------------------------------------
*
* admin_print_styles
*
-------------------------------------------------------*/

/*  管理画面専用のcssを読み込み
-------------------------------------------------------*/
function m_readAdminCSS() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri().'/assets/css/common/wp_admin.css?v=1" media="all">';
}

/*  admin_print_styles に関数をフックする
/*---------------------------------------------------------*/
function m_hookAdmin_admin_print_styles() {
    m_readAdminCSS();
}
add_action('admin_print_styles', 'm_hookAdmin_admin_print_styles');



/* ----------------------------------------------------
*
* before_admin_bar_render
*
-------------------------------------------------------*/

/* 管理バーの項目を非表示にする
-------------------------------------------------------*/
function m_admin_hideBarmenus() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );            // ロゴ
    // $wp_admin_bar->remove_menu( 'site-name' );          // サイト名
    // $wp_admin_bar->remove_menu( 'view-site' );          // サイト名 -> サイトを表示
    // $wp_admin_bar->remove_menu( 'dashboard' );          // サイト名 -> ダッシュボード (公開側)
    $wp_admin_bar->remove_menu( 'themes' );             // サイト名 -> テーマ (公開側)
    $wp_admin_bar->remove_menu( 'widgets' );            // サイト名 -> ウィジェット (公開側)
    $wp_admin_bar->remove_menu( 'menus' );              // サイト名 -> メニュー (公開側)
    $wp_admin_bar->remove_menu( 'customize' );          // サイト名 -> カスタマイズ (公開側)
    $wp_admin_bar->remove_menu( 'comments' );           // コメント
    $wp_admin_bar->remove_menu( 'updates' );            // 更新
    // $wp_admin_bar->remove_menu( 'view' );               // 投稿を表示
    $wp_admin_bar->remove_menu( 'new-content' );        // 新規
    $wp_admin_bar->remove_menu( 'new-post' );           // 新規 -> 投稿
    $wp_admin_bar->remove_menu( 'new-media' );          // 新規 -> メディア
    $wp_admin_bar->remove_menu( 'new-link' );           // 新規 -> リンク
    $wp_admin_bar->remove_menu( 'new-page' );           // 新規 -> 固定ページ
    $wp_admin_bar->remove_menu( 'new-user' );           // 新規 -> ユーザー
    // $wp_admin_bar->remove_menu( 'edit' );               // 編集
    // $wp_admin_bar->remove_menu( 'my-account' );         // マイアカウント
    // $wp_admin_bar->remove_menu( 'user-info' );          // マイアカウント -> プロフィール
    $wp_admin_bar->remove_menu( 'edit-profile' );       // マイアカウント -> プロフィール編集
    // $wp_admin_bar->remove_menu( 'logout' );             // マイアカウント -> ログアウト
    $wp_admin_bar->remove_menu( 'search' );             // 検索 (公開側)
}

/*  wp_before_admin_bar_render に関数をフックする
/*---------------------------------------------------------*/
function m_hookAdmin_wp_before_admin_bar_render() {
    m_admin_hideBarmenus();
}
add_action( 'wp_before_admin_bar_render', 'm_hookAdmin_wp_before_admin_bar_render' );