// 创建一个模块对象，防止跟系统函数冲突
var feedback = {
	// tag *** *** www.LazyCMS.net *** ***
	tag : function(l1,l2){
		var I1 = l1 ? '<a href="javascript:;" onclick="feedback.set(this,0,'+l2+')"><img src="'+path()+'/' + module + '/images/tag1.gif" class="os" /></a>' : '<a href="javascript:;" onclick="feedback.set(this,1,'+l2+')"><img src="'+path()+'/' + module + '/images/tag0.gif" class="os" /></a>';
		return '<span>' + I1 + '</span>';
	},
	set : function(l1,l2,l3){
		var $this = $(l1);
		var $obj  = $this.parent();
		var $url  = $this.parents('form').attr('action');
			$obj.html(loadgif());
			$.post($url,{submit:'settag',action:l2,lists:l3},function(){
				$obj.html(feedback.tag(l2,l3));
			});
	}
};