// addSub *** *** www.LazyCMS.net *** ***
function addSub(p1,p2,p3){
	var nbsp = ''; var e = getHP();
	var p2 = (typeof p2=='undefined') ? 1 : p2;
	var p3 = (typeof p3=='undefined') ? false : p3;
	if (p3==0||p3==false) { return ; }
	var td = $('#list_'+p1).parents('td');
	var os = td.find('i.os').css('cursor','pointer').unbind().click(function(){ 
			$.cookie('getSub_'+e.File+'_'+p1,true,{expires:365,path:e.Path}); addSub(p1,p2,p3); 
		});
	if ($.cookie('getSub_'+e.File+'_'+p1)==null || $.cookie('getSub_'+e.File+'_'+p1)=='false') { return ; }
	var tr = td.parent();
	var fm = td.parents('form');
	var tb = td.parents('tbody');
	for (var i=0; i<p2; i++) { nbsp += "&nbsp; &nbsp;"; }
	if ($('.sub' + p1 + ':visible',tb).is("tr")==false) {
		if ($('.sub' + p1,tb).css('display')=='none') {
			os.removeClass('icon-16-dir0'); os.removeClass('icon-16-dir1'); os.addClass('icon-16-dir2');
			$('.sub' + p1,tb).show(); changeHeight();
			return ;
		};
		os.hide();
		os.parent().prepend('<img src="'+path()+'/images/icon/loading.gif" class="os">');
		$.ajax({
			cache: false,
			url:fm.attr('action'),
			type: 'POST',
			data: {submit:'getsub',lists:p1,space:p2},
			success: function(data){
				if (d = $.parseJSON(data)) {
					os.prev().remove();os.show();
					os.removeClass('icon-16-dir0'); os.removeClass('icon-16-dir1'); os.addClass('icon-16-dir2');
					$(d).each(function(){
						tr.after($("td:first input",eval(this.code)).before(nbsp).end().addClass('sub'+p1).show());
						addSub(this.id,p2+1,this.sub); changeHeight();
					});
				} else {
					debug(data);
				}
			}
		});
	} else {
		os.removeClass('icon-16-dir0'); os.removeClass('icon-16-dir2'); os.addClass('icon-16-dir1');
		$.cookie('getSub_'+e.File+'_'+p1,false,{expires:365,path:e.Path});
		$('.sub' + p1,tb).hide(); changeHeight();
	}
}