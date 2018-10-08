/* ページ内リンクの処理  */
$(function(){
     $('a[href^="#"]').on('click',function(){
        var href= $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top;
        $("html, body").animate({scrollTop:position}, 300, "swing");
        return false;
    });
});


/* ページ外リンクの処理  */
$(window).on('load', function (e) {

    var hash = location.hash;	//アンカーリンク取得
    if($(hash).length){
        e.preventDefault();
        //IE判別
        var ua = window.navigator.userAgent.toLowerCase();
        var isIE = (ua.indexOf('msie') >= 0 || ua.indexOf('trident') >= 0);
        //IEだった場合
        if (isIE) {
            setTimeout(function(){
                var position = $(hash).offset().top;
                $("html, body").scrollTop(Number(position)-headerHight);
            },500);
            //IE以外
        } else {
            var position = $(hash).offset().top;	//アンカーリンクの位置取得
            $("html, body").scrollTop(Number(position)-headerHight);	//アンカーリンクの位置まで移動
        }
    }
});


(function(document,window,undefined){

    $(function(){

        // フォーム要素１
        var ctrl_form1 = new m_modalFormController( $('フォーム１　チェックボックス') );
        // フォーム要素２
        var ctrl_form2 = new m_modalFormController( $('フォーム２　チェックボックス') );

        // 【モーダル】選択ボタンが押された場合、キャッシュの更新とテキストの更新
        $('選択ボタン').on('click', function() {
            switch( 'フォーム判別' ) {
                case 'フォーム１':
                ctrl_form1.commit();
                break;

                case 'フォーム２':
                ctrl_form2.commit();
                break;

                default:break;
            }
        });

        // 【モーダル】　クローズボタン（またはキャンセル）が押された場合、キャンセルの挙動
        $('クローズまたはキャンセル').on('click', function(){
            switch( 'フォーム判別' ) {
                case 'フォーム１':
                ctrl_form1.rollback();
                break;

                case 'フォーム２':
                ctrl_form2.rollback();
                break;

                default:break;
            }
        });

    }


    /*
        【チェックボックス・ラジオボタン用】
        モーダルでよく見られる　【選択】【キャンセル】２択式のフォームのコントローラ
    */
    function m_modalFormController( elem ) {
        this.constructor.call( this, elem );
    }

    // コンストラクタ
    m_modalFormController.prototype.constructor = function( elem ) {
        var self = this;
        // チェックボックスの実体
        self.elem = elem;
        // 過去の状態を保持
        self.cach = elem.clone(false);
    };

    // チェックボックスの変更をキャンセル
    m_modalFormController.prototype.rollback = function() {
        for (var i = 0; i < this.elem.length; i++) {
            this.elem.eq(i).prop( 'checked', this.cach.eq(i).prop('checked') );
        }
    }

    // チェックボックスの変更を保存
    m_modalFormController.prototype.commit = function() {
        this.cach = this.elem.clone(false);
    }

    // チェックされている要素を取得する
    m_modalFormController.prototype.getCheckedItem = function () {
        var arr = [];

        for (var i = 0; i < this.cach.length; i++) {
            if ( this.cach.eq(i).prop('checked') ) arr.push( this.cach.eq(i).val() );
        }

        return arr;

    }

})(document,window,undefined);