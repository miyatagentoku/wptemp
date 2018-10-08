<?php

/* ----------------------------------------------------
*
* 共通 関数
*
-------------------------------------------------------*/

/*  【共通】ユーザーエージェントを判定して文字列で返す
*   return $device : pc パソコン
*                    sp スマートフォン
*                    tb タブレット
/*---------------------------------------------------------*/
function m_getUserAgent() {

    $ua = mb_strtolower($_SERVER['HTTP_USER_AGENT']);

    if ( strpos($ua,'iphone') !== false ){
        $device = 'sp';
    } elseif ( strpos( $ua, 'ipod' ) !== false ){
        $device = 'sp';
    } elseif (
        ( strpos( $ua, 'android' ) !== false )
        && ( strpos( $ua, 'mobile' ) !== false )
    ) {
        $device = 'sp';
    } elseif (
        ( strpos( $ua, 'windows' ) !== false )
        && ( strpos( $ua, 'phone' ) !== false )
    ) {
        $device = 'sp';
    } elseif (
        ( strpos( $ua, 'firefox' ) !== false )
        && ( strpos( $ua, 'mobile' ) !== false )
    ){
        $device = 'sp';
    } elseif ( strpos( $ua, 'blackberry' ) !== false ){
        $device = 'sp';
    } elseif( strpos( $ua,'ipad') !== false ){
        $device = 'tb';
    }elseif(
        ( strpos($ua,'windows') !== false )
        && ( strpos($ua, 'touch') !== false && ( strpos($ua, 'tablet pc') == false ) )
    ){
        $device = 'tb';
    } elseif (
        ( strpos( $ua, 'android' ) !== false )
        && ( strpos( $ua, 'mobile' ) === false )
    ){
        $device = 'tb';
    } elseif (
        ( strpos( $ua, 'firefox' ) !== false )
        && ( strpos( $ua, 'tablet' ) !== false )
    ){
        $device = 'tb';
    } elseif (
        ( strpos( $ua, 'kindle' ) !== false )
        || (strpos($ua, 'silk') !== false )
    ){
        $device = 'tb';
    } elseif (
        ( strpos( $ua, 'playbook' ) !== false )
    ){
        $device = 'tb';
    } else {
        $device = 'pc';
    }

    return $device;
}

/*  【共通】<link rel="canonical">の値を生成して返す
/*---------------------------------------------------------*/
function m_getCanonical() {

    // 【変数】現在ページのURL取得用
    $http = is_ssl() ? 'https' . '://' : 'http' . '://' ;

    if(is_404()) {
        $res = $http . $_SERVER["HTTP_HOST"] . '/404/';
    } else {
        $url = $http . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ;
        // 【分岐】ページ送り対策 /page/ 以降が邪魔なので削除
        $res = strstr( $url, '/page/' ) == true ? strstr( $url, '/page/' , true ) . '/' : $url ;
    }

    return $res;
}

/*  【共通】各ページの<title>の値を生成して返す
-------------------------------------------------------*/
function m_getTitle( $post ) {

    $seo_title = get_post_meta( $post->ID, 'cf_seo_title', true );

    $title = '';

    if( !empty( $seo_title ) ) {
        $title = $seo_title;
    } elseif( is_404() ) {
        $title = 'お探しのページは見つかりません。｜'.bloginfo('name');
    } elseif( is_home() || is_front_page() ) {
        $title = bloginfo('name');
    } elseif( is_post_type_archive() ){
        $title = get_post_type_object( get_post_type() )->label.'一覧｜'.bloginfo('name');
    } elseif( is_tax() ) {
        $title = wp_get_post_terms( $post->ID, get_post_type().'_category')[0]->name.'｜'.get_post_type_object( get_post_type() )->label.'一覧｜'.bloginfo('name');
    } elseif( is_single() ) {

        $title = get_the_title();

        if( get_taxonomy( get_post_type().'_category' ) ) {

            $arr = m_getSinlePageTerm( $post );

            // 親カテゴリ
            $post_term_parent = $arr['parent'];

            // このページに紐づくカテゴリ
            $post_term = $arr['target'];

            // 親カテゴリが存在する場合
            if( $post_term_parent ) $title .= ' | '.$post_term_parent->name;

            // カテゴリが存在する場合
            if( $post_term ) $title .= ' | '.$post_term->name;

        }

        $title .= '｜'.get_post_type_object( get_post_type() )->label.'一覧｜'.bloginfo('name');

    } elseif( is_page() ) {
        $title = get_the_title();

        if ( $ancestors = get_post_ancestors( $post ) ) {
            for ( $i = 0, $len = count( $ancestors ); $i < $len; $i++ ) {
                $title .= ' | '.get_post( $ancestors[$i] )->post_title;
            }
        }

        $title .= ' | '.bloginfo('name');

    } else {
        $title = get_the_title().'｜'.bloginfo('name');
    }

    return $title;

}

/*  【共通】各ページの<meta name="description">の値を生成して返す
-------------------------------------------------------*/
function m_getDescription( $post ) {
    $seo_description = get_post_meta( $post->ID, 'cf_seo_description', true);

    $description = '';

    if( !empty( $seo_description ) ) {
        $description = $seo_description;
    } elseif ( is_404() ) {
        $description = '404エラーページです。';
    } elseif ( is_post_type_archive() ) {
        $description = get_post_type_object( get_post_type() )->label.'一覧ページです。';
    } elseif ( is_tax() ) {
        $description = wp_get_post_terms( $post->ID, get_post_type().'_category')[0]->name.'カテゴリ '.get_post_type_object( get_post_type() )->label.'一覧ページです。';
    } else {
        $description = bloginfo('description');
    }

    return $description;
}

/*  【共通】各ページの<meta name="keywords">の値を生成して返す
-------------------------------------------------------*/
function m_getKeywords( $post ) {
    $seo_keywords = get_post_meta( $post->id, 'cf_seo_keywords', true);

    $keywords = '';

    if(!empty($seo_keywords)) {
        $keywords = $seo_keywords;
    }

    return $keywords;
}

/*  【共通】親子関係から階層的なスラッグを生成して返す
-------------------------------------------------------*/
function m_getPageHierarchySlug( $id ) {
    $ancestors = get_post_ancestors( $id ); // すべての先祖ページの投稿IDを配列で取得
    $ancestors = array_reverse ( $ancestors ); // 取得した先祖ページの投稿IDを逆順にする
    $parent_slug = '';
    foreach ( $ancestors as $ancestor ) {
        $parent_slug .= '/'.get_post($ancestor)->post_name; // 先祖ページのスラッグを取得
    }
    return $parent_slug;
}

/*  【single:共通】そのページに紐づくタームを取得する、親タームが存在する場合そのタクソノミーを優先する
-------------------------------------------------------*/
function m_getSinlePageTerm ( $post ) {

    $post_terms = wp_get_post_terms( $post->ID , get_post_type().'_category', array( 'orderby' => 'description', 'order' => 'ASC' ) );

    // 親カテゴリ
    $post_term_parent = null;

    // このページに紐づくカテゴリ
    $post_term = null;

    for( $i = 0; $i < count( $post_terms ); $i++ ) {

        $t = get_term( $post_terms[$i], get_post_type().'_category' );

        // 親カテゴリが存在する場合（優先）
        if( $t->parent > 0 ) {
            $post_term_parent = get_term( $t->parent, get_post_type().'_category' );
            $post_term = $t;

            break;

        } else {

            // 親カテゴリが存在しないカテゴリの場合、最初に取得されたものを優先
            if( $post_term == null ) {
                $post_term = $t;
            }
        }
    }

    return array(
        'parent' => $post_term_parent,
        'target' => $post_term
    );
}

/*  【共通】各ページ専用のファイルパスを生成して返す（拡張子抜き）
-------------------------------------------------------*/
function m_getUniqueResourcePath($post) {

    $resource_path = '';

    if( is_404() ) {// 404
        $resource_path = '/404/404';
    } elseif( is_home() || is_front_page() ) {// TOP
        $resource_path = '/top/top';
    } elseif( is_page() ) {// 固定ページ
        $resource_path = '/page'.m_getPageHierarchySlug($post->ID).'/'.$post->post_name.'/'.$post->post_name;
    } elseif( is_post_type_archive() || is_tax() || is_search() ) {// 一覧 カテゴリ 検索結果ページ
        if ( !( $post_type = get_post_type() ) ) $post_type = get_query_var('post_type');
        $resource_path = '/post/'.get_post_type().'/'.get_post_type();
    } elseif( is_single() ) {// 詳細ページ
        $resource_path = '/post/'.get_post_type().'/'.get_post_type().'_single';
    }

    return $resource_path;
}


/*  パンくずリスト
    $post : 該当ページの $post オブジェクト
    $term : タクソノミーを持つページの場合、そのページのターム
    $term : タクソノミーを持つページの場合、そのページのタクソノミー

    return txt=>表示文字列
           url=>リンク先URL
/*---------------------------------------------------------*/
function m_getBreadcrumb($post){

    if( !is_home() && !is_admin() ){ /* !is_admin は管理ページ以外という条件分岐 */

        $res = [
            array(
                'txt'=>'HOME',
                'url'=>home_url('/')
            )
        ];

        if (is_404()){/* 404 Not Found ページ */
            array_push(
                $res,
                array(
                    'txt'=>'お探しの記事は見つかりませんでした。'
                )
            );

        } elseif ( is_page() ){/* 固定ページ */
            if( $ancestors = get_post_ancestors( $post ) ){
                $ancestors = array_reverse( $ancestors );
                foreach($ancestors as $ancestor){
                    array_push(
                        $res,
                        array(
                            'txt'=>get_the_title($ancestor),
                            'url'=>get_permalink($ancestor)
                        )
                    );
                }
            }
            array_push(
                $res,
                array(
                    'txt'=>$post->post_title
                )
            );

        } elseif ( is_tax() ) {/* タクソノミー一覧ページ */

            array_push(
                $res,
                array(
                    'txt'=> get_post_type_object( get_post_type() )->label.'一覧',
                    'url'=> home_url('/'.get_post_type().'/'),
                )
            );

            $post_term = get_term_by('slug', wp_get_post_terms($post->ID, get_post_type().'_category')[0]->slug, get_post_type().'_category');

            // 親カテゴリが存在する場合
            if( $post_term->parent > 0 ) {

                $post_term_parent = get_term( $post_term->parent, get_post_type().'_category' );

                array_push(
                    $res,
                    array(
                        'txt'=> $post_term_parent->name,
                        'url'=> home_url('/'.get_post_type().'_category'.'/'.$post_term_parent->slug.'/')
                    )
                );
            }

            array_push(
                $res,
                array(
                    'txt'=> $post_term->name
                )
            );

        } elseif ( is_single() ){/* 詳細ページ */

            array_push(
                $res,
                array(
                    'txt'=> get_post_type_object( get_post_type() )->label.'一覧',
                    'url'=> home_url( '/'.get_post_type().'/' ),
                )
            );

            if( get_taxonomy( get_post_type().'_category' ) ) {

                $arr = m_getSinlePageTerm( $post );

                // 親カテゴリ
                $post_term_parent = $arr['parent'];

                // このページに紐づくカテゴリ
                $post_term = $arr['target'];

                // 親カテゴリが存在する場合
                if( $post_term_parent ) {

                    array_push(
                        $res,
                        array(
                            'txt'=> $post_term_parent->name,
                            'url'=> home_url( '/'.get_post_type().'/'.$post_term_parent->slug.'/' ),
                        )
                    );

                }

                // カテゴリが存在する場合
                if( $post_term ) {
                    array_push(
                        $res,
                        array(
                            'txt'=> $post_term->name,
                            'url'=> home_url( '/'.get_post_type().'/'.$post_term->slug.'/' ),
                        )
                    );
                }

            }

            array_push(
                $res,
                array(
                    'txt'=> $post->post_title
                )
            );

        } elseif ( is_search() ) {/* 検索結果 ページ */

            array_push(
                $res,
                array(
                    'txt'=> get_post_type_object( get_query_var('post_type') )->label.'一覧',
                    'url'=> home_url( '/'.get_query_var('post_type').'/' ),
                )
            );

            array_push(
                $res,
                array(
                    'txt'=> '検索結果',
                )
            );

        } elseif ( is_post_type_archive() ) {/* カスタム投稿一覧ページ */

            array_push(
                $res,
                array(
                    'txt'=> get_post_type_object( get_query_var('post_type') )->label.'一覧'
                )
            );

        } else {/* その他のページ */

            array_push(
                $res,
                array(
                    'txt'=>wp_title('', false)
                )
            );

        }
    }

    return $res;
}


/*  ページナビゲーション
/*---------------------------------------------------------*/
function m_getPageNavigation( $wp_rewrite, $wp_query, $paged , $option = null ) {

    $result = null;

    // max_num_pages が存在する、かつ1以上
    if(($wp_query->max_num_pages) > 1) {

        $paginate_base = get_pagenum_link(1);

        if ( strpos( $paginate_base, '?') || ! $wp_rewrite->using_permalinks() ) {
            $paginate_format = '';
            $paginate_base = add_query_arg('paged', '%#%');
        } else {
            $paginate_format = ( substr( $paginate_base, -1 ,1 )=='/'?'':'/').user_trailingslashit('page/%#%/', 'paged');
            $paginate_base .= '%_%';
        }

        // デフォルト設定 【必須】
        $args = array(
            'base' => $paginate_base,
            'format' => $paginate_format,
            'total' => $wp_query->max_num_pages,
            'current' => ($paged ? $paged : 1)
        );

        // オプションが指定されている場合は上書き
        if( $option ) {

            foreach ( $option as $key => $value ) $args[$key] = $value;

        } else {
            // デフォルト設定 【基本】
            $args['mid_size'] = 3;
            $args['prev_text'] = 'prev';
            $args['next_text'] = 'next';
        }

        $result = paginate_links( $args );

    }

    return $result;
}


/*  特定の固定ページであるか、またはそのページと子孫関係にあるページか判定する
*   $post : $post オブジェクト
*   $slug : 固定ページのスラッグ
/*---------------------------------------------------------*/
function m_isPageGroup( $post, $slug ) {

    // slug が重複しているページが存在するかもしれないので、親が存在しないことを条件に加える
    $flg = $post->post_parent == 0 && $post->post_name == $slug;

    if ( $flg == false && ( $ancestors = array_reverse( get_post_ancestors( $post->ID ) ) ) ) {
        $flg = get_post( $ancestors[0] )->post_name == $slug;
    }

    return $flg;
}

function m_getPageGroupSlug( $post ) {

    if ( $post->post_parent > 0 ) {
        $slug = get_post( array_reverse( get_post_ancestors( $post ) )[0] )->post_name;
    } else {
        $slug = $post->post_name;
    }

    return $slug;
}


/*  画像パスからBASE64形式に変換された画像パスを生成し文字列で返す
*   $imgpath : 変換対象の画像のパス
/*---------------------------------------------------------*/
function m_getBase64ImagePath( $imgpath ) {

    if ( ( $img = file_get_contents( $imgpath ) ) ) {

        $path = 'data:images/';

        switch ( exif_imagetype( $imgpath ) ) {

            case IMAGETYPE_GIF:
            $path.='gif';
            break;

            case IMAGETYPE_JPEG:
            $path.='jpeg';
            break;

            case IMAGETYPE_PNG:
            $path.='png';
            break;

            case IMAGETYPE_SWF:
            $path.='swf';
            break;

            case IMAGETYPE_PSD:
            $path.='psd';
            break;

            case IMAGETYPE_BMP:
            $path.='bmp';
            break;

            case IMAGETYPE_TIFF_II:
            case IMAGETYPE_TIFF_MM:
            $path.='tiff';
            break;

            case IMAGETYPE_JPC:
            $path.='jpc';
            break;

            case IMAGETYPE_JP2:
            $path.='jp2';
            break;

            case IMAGETYPE_JPX:
            $path.='jpx';
            break;

            case IMAGETYPE_JB2:
            $path.='jb2';
            break;

            case IMAGETYPE_SWC:
            $path.='swf';
            break;

            case IMAGETYPE_IFF:
            $path.='iff';
            break;

            case IMAGETYPE_WBMP:
            $path.='bmp';
            break;

            case IMAGETYPE_XBM:
            $path.='xbm';
            break;

            case IMAGETYPE_ICO:
            $path.='ico';
            break;

            default:break;
        }

        $path.=';base64,'.base64_encode( $img );

    } else {
        $path = $imgpath;
    }

    return $path;
}

function m_getInstagramAccessToken() {
    $client_id = '【クライアントID】';
    $redirect_uri = '【リダイレクトさせたいURL】';
    $query = 'https://www.instagram.com/oauth/authorize/?client_id=【クライアント ID】&redirect_uri=【リダイレクト URI】&response_type=token';
    $access_token = '【インスタグラムアクセストークン】';
}