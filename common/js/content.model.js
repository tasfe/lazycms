/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL               LLLL  |
 * | LL                            LL   L  LLL   LL  LL   L             LL  LL |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL         LL  LL      LL |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL      LL  LL     LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL     LL  LL    LL   |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL      LLLL    LL    |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL      LLLL   LL     |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL        LL    LLLLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */

(function($) {
    $.fn.__Fields = function(method,data){
		data = data||{};
		var This = this;
        $.ajax({
            url: this.attr('action'),
            type: method,
            data: data,
            success: function(data){
				var JSON = $.result(data);
				if (JSON) {
					$.dialogUI({
						name:'fields', title:JSON.TITLE, body:JSON.BODY,
						style:{width:'400px',overflow:'hidden'},
						close:function(){
							$('.dialogUI,[rel=mask]').remove();
						}
					},function(s){
						var t = this;
						$('[rel=cancel]',s).click(function(){
							$('.dialogUI').remove(); t.close();
						});
						s.__FieldTypeChange();
						s.__IsHelp();
                        s.__IsValidate();
						$('select[rel=change]',s).change(function(){
							s.__FieldTypeChange();
						});
						$('[help]').help();
						$.selectEdit();
						$('#formFields').ajaxSubmit(function(data){
							This.appendFields(data); t.close();
						});
					});
				}
            }
        });
    }
	/**
     * 追加字段
     */
    $.fn.appendFields = function(data){
		var tr = $('tr[n=' + $(data).attr('n') + ']',this);
		if (tr.is('tr')) {
			var dataVal = $.parseJSON($('textarea',data).val());
			var trtdVal = $.parseJSON($('textarea',tr).val());
			var isReplace = ($.inArray(dataVal.intype,['input','basic','editor']) || dataVal.intype != trtdVal.intype) ? true : false;
			$('td',tr).each(function(i){
				if (!isReplace && i>=4) { return ; }
				$('td:eq(' + i + ')',data).replaceAll(this);
			});
		} else {
			$('tbody',this).append(data);
		}
		$("#tableFields").__tableDnD();
	}
    /**
     * 添加字段
     */
    $.fn.addFields = function(){
		var n = 0;
		$('tr[n]',this).each(function(){
			n = Math.max(n,$(this).attr('n'));
		});
        this.__Fields('GET',{fieldid:n+1});
    }
    /**
     * 修改字段
     */
    $.fn.editFields = function(id){
        this.__Fields('POST',{JSON:$('textarea[fieldid=' + id + ']').val()});
    }
    /**
     * 删除字段
     */
    $.fn.delFields = function(){
		$('input:checked',this).parents('tr').remove();
    }
	/**
     * 是否需要帮助判断
     */
	$.fn.__IsHelp = function(){
        if ($('#isHelp').attr('checked')) {
            $('#fieldhelp').parents('p').slideDown('fast',function(){
                $('[help]').help(); $.selectEdit();
            });
        } else {
            $('#fieldhelp').parents('p').slideUp('fast',function(){
                $.selectEdit();
            });
        }
        $('#isHelp').click(function(){
            if (this.checked) {
                $('#fieldhelp').parents('p').slideDown('fast',function(){
                    $('[help]').help(); $.selectEdit();
                });
            } else {
                $('#fieldhelp').parents('p').slideUp('fast',function(){
                    $.selectEdit();
                });
				$('#fieldhelp').val('');
            }
        });
		return this;
	}
	// 绑定是否验证事件
    $.fn.__IsValidate = function(){
        // 判断是否显示提示说明
        if ($('#isValidate').attr('checked')) {
            $('#fieldrules').parents('p').slideDown('fast',function(){
                $('[help]').help(); $.selectEdit();
            });
        } else {
            $('#fieldrules').parents('p').slideUp('fast',function(){
                $.selectEdit();
            });
        }
        // 绑定需要验证规则
        $('#isValidate').click(function(){
            if (this.checked) {
                $('#fieldrules').parents('p').slideDown('fast',function(){
                    $('[help]').help(); $.selectEdit();
                });
            } else {
                $('#fieldrules').parents('p').slideUp('fast',function(){
                    $.selectEdit();
                });
            }
			$('#fieldvalidate').val('');
        });
        // 绑定增加、减少事件
        $('[rule=+]',this).click(function(){
            setRules(true);
        });
        $('[rule=-]',this).click(function(){
            setRules(false);
        });
        // 增加减少函数
        function setRules(p){
            var v = $('#fieldvalidate');
            var s = $('#fieldrules').val();
                if (p) {
                    v.val(v.val() + s + ';\n');
                } else {
                    v.val(v.val().replace( s +';\n',''));
                }
        }
        return this;
    }
    /**
     * 更改字段类型触发的事件
     */
    $.fn.__FieldTypeChange = function(){
        var s = $('select[rel=change]',this).val();
        switch (s) {
            case 'input':
                $('#fieldvalue,#fieldoption').parents('p').slideUp('fast');
                $('#fieldlength,#fielddefault').parents('p').slideDown('fast',function(){
                    $('[help]').help(); $.selectEdit();
                });
                break;
            case 'radio': case 'checkbox': case 'select':
                $('#fieldlength,#fieldoption').parents('p').slideUp('fast');
                $('#fieldvalue,#fielddefault').parents('p').slideDown('fast',function(){
                    $('[help]').help(); $.selectEdit();
                });
                break;
            case 'basic': case 'editor':
                $('#fieldlength,#fieldvalue').parents('p').slideUp('fast');
                if (s=='basic') {
                    $('#option_break,#option_setimg').each(function(){
                        $(this).attr('checked',false).hide().next().hide();
                    });
                } else {
                    $('#option_break,#option_setimg').each(function(){
                        $(this).show().next().show();
                    });
                }
                $('#fieldoption').parents('p').slideDown('fast',function(){
                    $('[help]').help(); $.selectEdit();
                });
                break;
            case 'upfile':
                $('#fieldlength,#fieldoption,#fieldvalue,#fielddefault').parents('p').slideUp('fast');
                break;
            default:
                $('#fieldlength,#fieldoption,#fieldvalue').parents('p').slideUp('fast');
                $('#fielddefault').parents('p').slideDown('fast',function(){
                    $('[help]').help(); $.selectEdit();
                });
                break;
        }
        return this;
    }
	/**
     * 表格移动
     */
	$.fn.__tableDnD = function(){
		return this.tableDnD({
			onDragClass: 'Drag'
		}).find('tr').hover(function(){
			$(this).addClass('Over');
		},function(){
			$(this).removeClass('Over');
		});
	}
	/**
     * 设置自动获取关键词
     */
	$.fn.isKeyword = function(id){
		if ($('img',this).hasClass('b5')) {
			$('#tableFields > tbody .b5').removeClass('b5').addClass('b6');
			$('input[name=iskeyword]').val('');
		} else {
			var name = $('#tableFields tbody tr[n=' + id + '] td:eq(1)').text();
			$('#tableFields > tbody .b5').removeClass('b5').addClass('b6');
			$('img',this).removeClass('b6').addClass('b5');
			$('input[name=iskeyword]').val(name);
		}
	}
	/**
     * 设置自动获取简述
     */
	$.fn.Description = function(id){
		if ($('img',this).hasClass('b7')) {
			$('#tableFields > tbody .b7').removeClass('b7').addClass('b8');
			$('input[name=description]').val('');
		} else {
			var name = $('#tableFields tbody tr[n=' + id + '] td:eq(1)').text();
			$('#tableFields > tbody .b7').removeClass('b7').addClass('b8');
			$('img',this).removeClass('b8').addClass('b7');
			$('input[name=description]').val(name);
		}
	}
    /**
     * 自动上传
     */
    $.fn.autoUpFile = function(){
        var f = this.parents('form');
            this.hide().after('<input type="text" value="UpLoading..." class="in w400 uploading" />');
            f.parents('fieldset').after('<iframe src="about:blank" id="tempform" name="tempform" style="display:none;width:0px;height:0px;border:none;"></iframe>');
            f.submit();
    }
})(jQuery);