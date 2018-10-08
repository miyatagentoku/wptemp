<?php
/* ----------------------------------------------------
*
* 共通 フック関数
*
-------------------------------------------------------*/

/* ----------------------------------------------------
*
* add_action
*
-------------------------------------------------------*/

/*  カスタマイザーのデフォルトを非表示
-------------------------------------------------------*/
function m_admin_hideCustomizer($wp_customize) {
    //    $wp_customize->remove_section( 'title_tagline' );
    //    $wp_customize->remove_section( 'nav' );
    $wp_customize->remove_section( 'static_front_page' );
}

/*  customize_register に関数をフックする
/*---------------------------------------------------------*/
function m_hookCommon_customize_register($wp_customize) {
    m_admin_hideCustomizer($wp_customize);
}
add_action('customize_register', 'm_hookCommon_customize_register');


/*  ページ毎の定数を定義
/*---------------------------------------------------------*/
function m_setStaticVars() {

    // 共通リソース
    define('COMMON_IMG', get_template_directory_uri().'/assets/images/common/');
    define('COMMON_MOVIE', get_template_directory_uri().'/assets/movie/common/');
    define('COMMON_PDF', get_template_directory_uri().'/assets/pdf/common/');

    define('COMMON_CSS', get_template_directory_uri().'/assets/css/common/');
    define('COMMON_JS', get_template_directory_uri().'/assets/js/common/');


    if ( is_home() || is_front_page() ) { // トップページ



    } elseif( is_page() ) { // 固定ページ

        $path = m_getPageHierarchySlug( get_the_ID() ).'/'.get_post( get_the_ID() )->post_name.'/';

        // ページ固有のリソース
        define( 'PAGE_IMG', get_template_directory_uri().'/assets/images/page'.$path);
        define( 'PAGE_MOVIE', get_template_directory_uri().'/assets/movie/page'.$path);
        define( 'PAGE_PDF', get_template_directory_uri().'/assets/pdf/page'.$path);

    } elseif ( is_post_type_archive() || is_tax() || is_single() ) { // カスタム投稿系ページ

        if ( !( $post_type = get_post_type() ) ) $post_type = get_query_var( 'post_type' );

        // ページ固有のリソース
        define( 'PAGE_IMG', get_template_directory_uri().'/assets/iamges/post/'.$post_type.'/');
        define( 'PAGE_MOVIE', get_template_directory_uri().'/assets/movie/post/'.$post_type.'/');
        define( 'PAGE_PDF', get_template_directory_uri().'/assets/pdf/post/'.$post_type.'/');
    }
}

/*  wp に関数をフックする
/*---------------------------------------------------------*/
function m_hookCommon_wp() {
    m_setStaticVars();
}

add_action('wp','m_hookCommon_wp');



/* ----------------------------------------------------
*
* add_filter
*
-------------------------------------------------------*/

/*  メニューの並び替え
-------------------------------------------------------*/
function m_custom_menu_order($menu_ord) {
    if (!$menu_ord) return true;

    return array(
        // 'separator1', // 仕切り
        // 'edit.php?post_type=xxx1', // カスタム投稿１
        // 'edit.php?post_type=xxx2', // カスタム投稿２
        // 'edit.php?post_type=xxx3', // カスタム投稿３
        // 'edit.php?post_type=page', // 固定ページ
        // 'separator-last', // 仕切り
        // 'upload.php', // メディア
    );
}
add_filter('custom_menu_order', 'm_custom_menu_order');
add_filter('menu_order', 'm_custom_menu_order');


/*  自動バックグラウンド更新を無効化する
-------------------------------------------------------*/
add_filter( 'automatic_updater_disabled', '__return_true' );


/* 管理画面サイドバーのサブ項目を非表示にする
-------------------------------------------------------*/
function m_admin_hideSubmenus() {
    remove_submenu_page('themes.php','theme-editor.php');   //【外観】→【テーマの編集】
}
add_filter('custom_menu_order', 'm_admin_hideSubmenus');    // Activate m_admin_hideSubmenus
add_filter('menu_order', 'm_admin_hideSubmenus');


/*  固定ページ化したカスタム投稿にはclassが付かないバグがある為、classを付与する
-------------------------------------------------------*/
function m_post_classBug( $css_class, $page ) {
    global $post;
    if ( $post->post_parent == $page->ID )
        $css_class[] = 'current_page_ancestor';
    if ( $post->ID == $page->ID ) {
        $css_class[] = 'current_page_item';
    }
    return $css_class;
}
add_filter( 'page_css_class', 'm_post_classBug', 10, 2 );


/*  投稿画面の見出しタグを制限する
-------------------------------------------------------*/
function m_admin_post_editHeading($initArray){
    global $current_screen;
    switch ($current_screen->post_type){
        case 'page':
            $initArray['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4';
            break;
        case 'news':
            $initArray['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4';
            break;
    }
    return $initArray;
}
add_filter('tiny_mce_before_init', 'm_admin_post_editHeading');


/*  .svg 拡張子画像のアップロードを許可する
-------------------------------------------------------*/
function m_admin_svgUpload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'm_admin_svgUpload');