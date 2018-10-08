<?php

/* ----------------------------------------------------
*
* 案件固有の設定
*
-------------------------------------------------------*/

// ページの追加
add_action( 'admin_menu', 'm_admin_custom_menu' );
function m_admin_custom_menu() {
    add_menu_page(
    	'管理画面 | トップページカスタムフィールド',
    	'トップページ',
    	'manage_options',
    	'cf_for_top',
    	'add_manual_page',
    	'/assets/images/common/icon.png',
    	3
    );
}

// ページの中身のHTML
function add_manual_page() {
	get_template_part('inc/admin/top');
}



/* ----------------------------------------------------
*
* DB Access
*
-------------------------------------------------------*/

/*  CommonDAO のインスタンスを取得する
/*---------------------------------------------------------*/
function m_connectCommonDAO() {
	return new CommonDAO( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
}

/*  SearchDAO のインスタンスを取得する
/*---------------------------------------------------------*/
function m_connectSearchDAO() {
	return new SearchDAO( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
}



/* ----------------------------------------------------
*
* プラグイン専用処理の実行
*
-------------------------------------------------------*/

// 【Custom Post Type Permalinks使用時】
$_customposttypepermalinks = new m_CustomPostTypePermalinks();
$_customposttypepermalinks->run();