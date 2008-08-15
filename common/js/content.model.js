(function($) {
	// getFields *** *** www.LazyCMS.net *** ***
	$.fn.getFields = function(p1,p2){
		var p3  = $(p1);
		var p2  = p2||{}; p2 = (typeof p2)=='string'?$.parseJSON(p2):p2;
		var url = p3.attr('action');
		var id = 'CONTENT_MODEL_' + Math.floor(Math.random()*100000);
		var t = this.replaceWith('<img id="' + id + '" src="' + path() + '/images/icon/loading.gif" class="os" />');
			$('#formFields').remove();
		$.post(url,p2,function(d){
			// 将html代码加入
			$('body').append(d);
			// 进行半记忆化操作
			SemiMemory();
			// 判断是否显示提示说明
			if ($('#needTip').attr('checked')) {
				slideDown('#fieldtip');
			} else {
				slideUp('#fieldtip');
			}
			// 对需要说明多选按钮进行click事件绑定
			$('#needTip').click(function(){
				if (this.checked) {
					slideDown('#fieldtip');
				} else {
					slideUp('#fieldtip');
				}
			});
			// 输入类型绑定 change 事件
			$('#fieldintype').change(function(){
				switch (this.value) {
					case 'input':
						slideDown('#fieldlength'); slideUp('#fieldvalue');
						break;
					case 'radio': case 'checkbox': case 'select':
						$('#fieldlength').parents('p').slideUp('fast',function(){changeHeight();}).end().val('');
						if ($('#fieldvalue').parents('p').slideDown('fast',function(){changeHeight();}).end().val()=='') {
							$('#fieldvalue').parents('p').slideDown('fast',function(){changeHeight();}).end().val("name1:value1\nname2:value2\nname3:value3");
						}
						break;
					default:
						$('#fieldlength').parents('p').slideUp('fast',function(){changeHeight();}).end().val('');
						$('#fieldvalue').parents('p').slideUp('fast',function(){changeHeight();}).end().val('');
						break;
				}
			});
			// 判断是否显示提示说明
			if ($('#isValidate').attr('checked')) {
				slideDown('#fieldvalidate');
			} else {
				slideUp('#fieldvalidate');
			}
			// 绑定需要验证规则
			$('#isValidate').click(function(){
				if (this.checked) {
					slideDown('#fieldvalidate');
				} else {
					slideUp('#fieldvalidate');
				}
				$('#fieldvalidate').val('');
			});
			if ($('#fieldid').val()=='') {
				$('#fieldid').val($('tbody tr',p3).size()+1);
			}
			$('#formFields').attr('insert',p1).tips('tip','[@tip]'); 
			$('#'+id).replaceWith(t);
		});
		function slideDown(p1){
			$(p1).parents('p').slideDown('fast',function(){changeHeight();});
		}
		function slideUp(p1){
			$(p1).parents('p').slideUp('fast',function(){changeHeight();});
		}
		function changeHeight(){
			var e = {t:$('#toggleFields').height(),b:$('body').height()};
			if (e.t > e.b) {
				parent.$('#main').height(e.t+17);
			} else {
				parent.$('#main').height(e.b+7);
			}
		}
		return this;
	};
	// setValidate *** *** www.LazyCMS.net *** ***
	$.fn.setValidate = function(p1,p2){
		var v = $(p1).val();
		var s = $('option:selected',this).val();
			if (p2) {
				$(p1).val(v+s+';\n');
			} else {
				$(p1).val(v.replace(s+';\n',''));
			}
			return this;
	}
	// delFields *** *** www.LazyCMS.net *** ***
	$.fn.delFields = function(p1,p2){
		if (!confirm(p2)) {return ;}
		var listids = '';
		$('input:checkbox:checked',this).each(function(){
			listids += (listids=='')?this.value:','+this.value;
			$(this).parents('tr').remove();
		});
		$(p1).val(listids);
		return this;
	};
	// submitFields *** *** www.LazyCMS.net *** ***
	$.fn.submitFields = function(){
		$('.jTip').remove(); $('input.error,textarea.error').unbind().toggleClass('error');
		var f = this.parents('form').tips('tip','[@tip]');
		var u = f.attr('action'); if (u==''||typeof u=='undefined') { u = self.location.href; }
		var t = $(f.attr('insert'));
		$.ajax({
			cache: false,
			url: u,
			type: f.attr('method').toUpperCase(),
			data: f.serializeArray(),
			success: function(data){
				if (d = $.parseJSON(data)) {
					if (typeof d.status == 'undefined') {
						f.error(d);
					} else {
						var tr = $('#TR_'+d.id);
						if (tr.is('tr')) {
							tr.replaceWith(d.tr);
						} else {
							t.append(d.tr);
						}
						tableDnD(f.attr('insert'));
						f.remove(); changeHeight();
					}
				} else {
					debug(data);
				}
			}
		});
		return this;
	}
})(jQuery);

// tableDnD *** *** www.LazyCMS.net *** ***
function tableDnD(selector){
	$(selector).tableDnD({
		onDragClass: 'Drag',
		onDrop: function(table, row) {
			//alert($.tableDnD.serialize());
		}
	}).find('tr').hover(function(){
		$(this).addClass('Over');
	},function(){
		$(this).removeClass('Over');
	});
}