(function( $, wp_ajax_url){

	$('サブミットボタン').click( submit );

	function submit() {
		console.log('更新中...');

		$.ajax({
			type: 'POST',
			url: wp_ajax_url,
			data: {
				'action' : 'register_topcf',
				'top_cont1' : $('input[name=top_cont1]:checked').val(),
				'top_cont2' : $('input[name=top_cont2]').val(),
				'top_cont3' : $('input[name=top_cont3]').val()
			}
		}).done(function( response ){
			console.log('更新完了しました');
			console.log('更新');
		}).fail(function( response ){
		});

	}

})( jQuery, wp_ajax_url);