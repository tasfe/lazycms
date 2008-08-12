(function($) {
	// addFields *** *** www.LazyCMS.net *** ***
	$.fn.addFields = function(p1,p2){
		if ($('#toggleField').is('div')) { return ; }
		var p2 = p2||{};
		$.post(p1,p2,function(d){
			// 将html代码加入
			$('body').append(d);
			// 进行半记忆化操作
			SemiMemory();
			// 判断是否显示提示说明
			if ($('#needTip').attr('checked')) {
				$('#fieldtip').parents('p').show();
			} else {
				$('#fieldtip').parents('p').hide();
			}
			// 对需要说明多选按钮进行click事件绑定
			$('#needTip').click(function(){
				if (this.checked) {
					$('#fieldtip').parents('p').show();
				} else {
					$('#fieldtip').parents('p').hide();
				}
				changeHeight();
			});
			// 输入类型绑定 change 事件
			$('#fieldintype').change(function(){
				//alert($('option:selected',this).attr('type'));
				switch (this.value) {
					case 'input':
						$('#fieldlength').parents('p').show();
						$('#fieldvalue').parents('p').hide();
						break;
					case 'radio': case 'checkbox': case 'select':
						$('#fieldlength').parents('p').hide().end().val('');
						if ($('#fieldvalue').parents('p').show().end().val()=='') {
							$('#fieldvalue').parents('p').show().end().val("name1:value1\nname2:value2\nname3:value3");
						}
						break;
					default:
						$('#fieldlength').parents('p').hide().end().val('');
						$('#fieldvalue').parents('p').hide().end().val('');
						break;
				}
				changeHeight();
			});
			// 判断是否显示提示说明
			if ($('#isValidate').attr('checked')) {
				$('#fieldvalidate').parents('p').show();
			} else {
				$('#fieldvalidate').parents('p').hide();
			}
			// 绑定需要验证规则
			$('#isValidate').click(function(){
				if (this.checked) {
					$('#fieldvalidate').parents('p').show();
				} else {
					$('#fieldvalidate').parents('p').hide();
				}
				changeHeight();
			});
			changeHeight();
		});
		function changeHeight(){
			var e = {t:$('#toggleField').height(),b:$('body').height()};
			if (e.t > e.b) {
				parent.$('#main').height(e.t+7);
			}
		}
		return this;
	};
	// delFields *** *** www.LazyCMS.net *** ***
	$.fn.delFields = function(p1){
		if (!confirm(p1)) {return ;}
		$('input:checkbox',this).each(function(i){
			if (this.checked) {
				$(this).parents('tr').remove();
			}
		});
	};
})(jQuery);


function dump_props(obj)
{
   var result = ""
   for (var i in obj) {
	  result += "OBJ." + i + " = " + obj[i] + "\n"
   }
   result += "\n"
   return result;
}