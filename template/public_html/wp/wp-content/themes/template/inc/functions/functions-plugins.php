<?php
/* ----------------------------------------------------
*
* 特定のプラグインを使用した場合の処理
* 関数は class名=プラグイン名 の中にまとめること
*
-------------------------------------------------------*/

/*	【Custom Post Type Permalinks使用時】
-------------------------------------------------------*/
class m_CustomPostTypePermalinks {
    // パーマリンクの調整
    function rewriterules($mypost, $myterm, $parents) {
        $newrules = array();
        if ( $myterm->parent == 0 ) {  //親タームがなかったら
            if (empty($parents)) {
                $newrules['archive'][$mypost . '/' . '(' . $myterm->slug . ')/?$'] = 'index.php?' . $myterm->taxonomy . '=$matches[1]';  //アーカイブページ用
                $newrules['archive'][$mypost . '/' . '(' . $myterm->slug . ')/page/([0-9]+)/?$'] = 'index.php?' . $myterm->taxonomy . '=$matches[1]&paged=$matches[2]';   //2ページ以降用
                $newrules['single'][$mypost . '/' . $myterm->slug . '/([^/]+)/?$'] = 'index.php?' . $mypost . '=$matches[1]';
            } else {  //親があるターム用
                $newrules['single'][$mypost . '/'. $myterm->slug . '/' . implode('/', $parents) . '/([^/]+)/?$'] = 'index.php?' . $mypost . '=$matches[1]';  //singleページ用
                $parents[count($parents)-1] = '(' . $parents[count($parents)-1] . ')/';
                $newrules['archive'][$mypost . '/' . $myterm->slug . '/' . implode('/', $parents) . '?$'] = 'index.php?' . $myterm->taxonomy . '=$matches[1]';  //アーカイブページ用
                $newrules['archive'][$mypost . '/' . $myterm->slug . '/' . implode('/', $parents) . 'page/([0-9]+)/?$'] = 'index.php?' . $myterm->taxonomy . '=$matches[1]&paged=$matches[2]';  //2ページ以降用
            }
        } else {  //親があったら
            $parentterm = get_term_by('id', $myterm->parent, $myterm->taxonomy);
            array_unshift($parents, $myterm->slug);
            $newrules = $this->rewriterules($mypost, $parentterm, $parents);
        }
        return $newrules;
    }

    function rewriterulesOption($rules) {  //引数：既に設定されているリライトルール
        $newrules = array();  $toriaezu = array();  $single = array();  $archive = array();
        $myposts = get_post_types(array('_builtin' => false));  //全カスタムタイプ取得②
        foreach($myposts as $mypost) {
            $mytaxonomies = get_taxonomies(array('object_type' => array($mypost))); //それのタクソノミー取得③
            foreach ($mytaxonomies as $mytaxonomie) {
                $myterms = get_terms( $mytaxonomie, array('hide_empty' => 0) ); //それのターム取得④
                foreach ($myterms as $myterm) {
                    $toriaezu =  $this->rewriterules($mypost, $myterm, array()); //リライトルール配列を生成
                    $single += $toriaezu['single'];  //順番調整⑤
                    $archive += $toriaezu['archive'];
                }
            }
        }
        return $archive + $single + $rules;  //新しいルールを追加して返す⑥
    }

    //  投稿記事のスラッグが日本語などマルチバイトの場合は、{記事ID}に強制的に変更
    function autoPostSlug( $slug, $post_id, $post_status, $post_type ) {

        if( in_array( $post_type, ['search'] ) ) {

            $slug = utf8_uri_encode( $post_type ).'-'.$post_id;
        }

        return $slug;
    }

    /* タクソノミーページの重複をさけるリダイレクト
    -------------------------------------------------------*/
    function postRedirect() {
        if(is_single()){
            $current_url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
            $page_url = get_the_permalink();
            if ($current_url == $page_url) {
            }
            else {
                wp_redirect($page_url , 301);
                exit;
            }
        }
    }

    /* 実行処理
    -------------------------------------------------------*/
    function run() {
        add_filter( 'rewrite_rules_array', array( $this,'rewriterulesOption') );
        add_filter( 'wp_unique_post_slug', array( $this,'autoPostSlug'), 10, 4  );
        add_action( 'wp', array( $this,'postRedirect') );
    }
}


/* 【専用：プラグイン】Advanced Custom Fields 使用時
/*---------------------------------------------------------*/
class m_AdvancedCustomFields {

    /*  【共通】カスタムフィールドwysiwygエディタ専用処理　続きを読む　機能を持たせる
    *  $fullText : wysiwygフィールドの値
    /*---------------------------------------------------------*/
    function readmore( $fullText ){
        if(@strpos($fullText, '<!--more-->')){
            $morePos  = strpos($fullText, '<!--more-->');
            $fullText = preg_replace('/<!--(.|\s)*?-->/', '', $fullText);
            print substr($fullText,0,$morePos);
            print '<p class="m-more-txt tcBlue tar fs14">続きを読む</p>';
            print '<div class="m-more">'. substr($fullText,$morePos,-1) . "</div>";
        } else {
            print $fullText;
        }
    }
}


/*  【専用：プラグイン】WP Popular Post 使用時
/*---------------------------------------------------------*/
class m_WPPopularPost {

    /*  【専用：プラグイン】WP Popular Post の出力内容を編集
    /*---------------------------------------------------------*/
    function customPopularHTML( $mostpopular, $instance ) {
        $tags = '<ul class="p-popularList">';
        foreach( $mostpopular as $popular ) {

            $tags .= '<li class="p-popularList-item clearfix">'.'<a class="p-popularList-item-box" href="'.esc_url( get_the_permalink( $popular->id ) ).'">'.'<div class="p-popularList-item-left">';

            $tags .= has_post_thumbnail( $popular->id )?get_the_post_thumbnail($popular->id, 'full'):'<img src="'.COMMON_IMG.'noimage.jpg'.'" alt="'.get_the_title( $popular->id ).'">';

            $tags .= '</div>'.'<div class="p-popularList-item-right">'.'<h3 class="p-popularList-item-right-title">'.get_the_title( $popular->id ).'</h3>'.'</div>'.'</a>'.'</li>';
        }

        echo $tags.'</ul>';
    }

    /*  【専用：プラグイン】WP Popular Post の検索結果なしの際の出力内容を編集
    /*---------------------------------------------------------*/
    function customNoPostsFound(){
        $output = '<div class="pt50 pb30 fs16">集計中</div>';
        return $output;
    }

    /* 実行処理
    /*---------------------------------------------------------*/
    function run() {
        add_filter( 'wpp_custom_html', array( $this, 'customPopularHTML'), 10, 2 );
        add_filter( 'wpp_no_data', array( $this, 'customNoPostsFound'), 10, 1 );
    }
}