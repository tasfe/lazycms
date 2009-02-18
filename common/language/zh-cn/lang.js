(function ($) {
	$.t = function(p1){
		var R;
		var L = new Array();
			L['error']		= '系统错误';
			L['alert']		= '系统提示';
			L['confirm']	= '操作确认';
			L['save']		= '保存';
			L['close']		= '关闭';
			L['submit']		= '确认';
			L['cancel']		= '取消';
			L['confirm/delete']		= '确定要删除吗？';
		try	{
			R = L[p1];
		} catch (e) {
			R = p1;
		}
		return R;
	}
})(jQuery);