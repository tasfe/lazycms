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
		var $this  = this;
        var params = {width:'400px',top:$(document).height()/6,left:$(document).width()/2 - 200};
        $.ajax({
            url: this.attr('action'),
            type: method,
            data: data,
            dataType: 'json',
            success: function(data){
                if (data = $.result(data)) {
                    var dialog = $.dialogUI({title:data.TITLE})
                        .append('<div class="body"></div>')
                        .floatDiv(params)
                        .find('.body').html(data.BODY).SemiMemory().end()
                        .find('[type=submit]').click(function(){
                            $(this.form).ajaxSubmit(function(data){
								$this.appendFields(data); $.undialogUI();
                            });
                        }).focus().end()
                        .find('[rel=cancel]').click(function(){
                            $.undialogUI();
                        }).end()
						.__FieldTypeChange()
                        .__IsValidate();
                    $('select[rel=change]',dialog).change(function(){
                        dialog.__FieldTypeChange();
                    });
                    $('[help]').help();
                    $.selectEdit();
                }
            }
        });
    }
	/**
     * 追加字段
     */
    $.fn.appendFields = function(data){
		if ($('tr[n=' + $(data).attr('n') + ']',this).is('tr')) {
			$('tr[n=' + $(data).attr('n') + ']',this).replaceWith(data);
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
     * 自动上传
     */
    $.fn.autoUpFile = function(){
        var f = this.parents('form');
            this.hide().after('<input type="text" value="UpLoading..." class="in w400 uploading" />');
            f.parents('fieldset').after('<iframe src="about:blank" id="tempform" name="tempform" style="display:none;width:0px;height:0px;border:none;"></iframe>');
            f.submit();
    }
})(jQuery);