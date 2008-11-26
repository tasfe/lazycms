$(document).ready(function(){
    $("form[method=post]").ajaxSubmit();
});








function dump(o){
	if (typeof o == 'undefined') {
		alert(o); return ;
	}
	for(k in o){
		alert(k+' : '+o[k]);
	}
}
function debug(s){ alert(s); }


(function ($) {
    $.fn.ajaxSubmit = function(){
        this.submit(function(){
    		// 先释放绑定的所有事件，清除错误样式
    		$('[error]').unbind().removeClass('error');
    		// 移除所有 Tips 信息
    		$('.jTip').remove();
            var t = $(this);
            var b = $('button[type=submit]',this);
            // 取得 action 地址
    		var u = t.attr('action'); if (u==''||typeof u=='undefined') { u = self.location.href; }
    		// 设置登录按钮
                b.attr('disabled',true);
            // 设置编辑器内容
            if (typeof tinyMCE!='undefined') {
    			var editor = tinyMCE.editors;
    			for (e in editor) {
    				$('#'+editor[e].id).val(editor[e].getContent());
    			}
    		}
    		// ajax submit
    		$.ajax({
    			cache: false,
    			url: u,
    			type: t.attr('method').toUpperCase(),
    			data: t.serializeArray(),
    			beforeSend: function(s){
    				s.setRequestHeader("AJAX_SUBMIT",true);
    			},
    			error: function(e,s){
    				debug(s);
    			},
    			success: function(data){
    				if (d = $.parseJSON(data)) {
    					switch (d.CODE)	{
    						case 'VALIDATE':
    							var c = d.DATA.length;
    							for (var i=0;i<c;i++) {
    								$('[name='+d.DATA[i].id+']').unbind().attr('error',d.DATA[i].text).addClass('error');
    							}
    							$('[error]').jTips();
    							break;
    						case 'ALERT':
    							alert(d.DATA.MESSAGE);
    							self.location.href = d.DATA.URL;
    							break;
    						case 'REDIRECT' :
    							self.location.href = d.DATA.URL;
    							break;
    						default:
    							debug(data);
    							break;
    					}
    				}
    			},
    			complete: function(){
    				b.attr('disabled',false);
    			}
    		});
            return false;
        });
    },
	$.fn.jTips = function(){
		$('body').append('<div class="jTip"><div class="jTip-body"></div><div class="jTip-foot"></div></div>');
		var jTip = $('.jTip');
		var jHeight = jTip.height();
		this.mousemove(function(e){
			jTip.css({'top':(e.clientY - jHeight - 20 ) + 'px','left':(e.clientX + 5) + 'px'});
		});
		this.hover(function(){
			jTip.fadeIn('fast').find('.jTip-body').html($(this).attr('error'));
		},function(){
			jTip.hide();
		});
	}
})(jQuery);
/*
 * JSON  - JSON for jQuery
 *
 * FILE:jquery.json.js
 *
 * Example:
 *
 * $.toJSON(Object);
 * $.parseJSON(String);
 */
(function ($) {
	$.toJSON = function(o){
		var i, v, s = $.toJSON, t;
		if (o == null) return 'null';
		t = typeof o;
		if (t == 'string') {
			v = '\bb\tt\nn\ff\rr\""\'\'\\\\';
			return '"' + o.replace(/([\u0080-\uFFFF\x00-\x1f\"])/g, function(a, b) {
				i = v.indexOf(b);
				if (i + 1) return '\\' + v.charAt(i + 1);
				a = b.charCodeAt().toString(16);
				return '\\u' + '0000'.substring(a.length) + a;
			}) + '"';
		}
		if (t == 'object') {
			if (o instanceof Array) {
				for (i=0, v = '['; i<o.length; i++) v += (i > 0 ? ',' : '') + s(o[i]);
				return v + ']';
			}
			v = '{';
			for (i in o) v += typeof o[i] != 'function' ? (v.length > 1 ? ',"' : '"') + i + '":' + s(o[i]) : '';
			return v + '}';
		}
		return '' + o;
	},
	$.parseJSON = function(s){
		try {
			return eval('(' + s + ')');
		} catch (ex) {
			// Ignore
			return false;
		}
	}
})(jQuery);