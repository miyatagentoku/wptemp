<?php

/* ----------------------------------------------------
*
* 自作カスタム投稿タイプ
*
-------------------------------------------------------*/

/*  【共通】カスタム投稿の作成に使う標準的な設定値を返す
 *  $label : 表示名を指定する
/*---------------------------------------------------------*/
function m_custom_post_commonProperty( $label ) {

    return array(
    	'label' => $label, //表示名
    	'public'        => true, //公開状態
    	'exclude_from_search' => false, //検索対象に含めるか
    	'show_ui' => true, //管理画面に表示するか
    	'show_in_menu' => true, //管理画面のメニューに表示するか
    	'menu_position' => 5, //管理メニューの表示位置を指定
    	'has_archive'   => true, //この投稿タイプのアーカイブを作成するか
    	'supports' => array(
    		'title',
    		'thumbnail'
    	), //編集画面で使用するフィールド
    );
}
