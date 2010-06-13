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
function model_list_init() {
    var form = $('#modellist');
    // 绑定全选事件
    $('input[name=select]',form).click(function(){
        $('input[name^=list]:checkbox,input[name=select]:checkbox',form).attr('checked',this.checked);
    });
    $('tbody tr',form).hover(function(){
        $('td',this).css({'background-color':'#FFFFCC'});
        $('.row-actions',this).css({'visibility': 'visible'});
    },function(){
        $('td',this).css({'background-color':'#FFFFFF'});
        $('.row-actions',this).css({'visibility': 'hidden'});
    });
	// 绑定删除事件
	$('span.delete a',form).click(function(){
		var url = this.href;
		LazyCMS.confirm(_('Confirm Delete?'),function(r){
			if (r) {
				$.getJSON(url,function(data){
					LazyCMS.ajaxResult(data);
				});
			}
		});
		
		return false;
	});
	// 绑定提交事件
	form.actions(function(data){
        LazyCMS.ajaxResult(data);
    });
}
// 添加用户页面初始化
function model_manage_init() {
	var wrap = $('#fields'),
	// 表格拖动
	table_drag = function(){
	    $('table.data-table',wrap).tableDnD({
    		onDragClass: 'Drag'
    	}).find('tr').hover(function(){
    		$(this).addClass('Over');
    	},function(){
    		$(this).removeClass('Over');
    	});
	},
	// 字段为空
	field_empty = function(){
	    if ($('tbody tr',wrap).size()==0) {
	        $('tbody',wrap).append('<tr class="empty"><td colspan="5" class="tc">' + _('No record!') + '</td></tr>');
	    }
	},
	// 绑定动作
	actions = function(){
	    $('tbody tr',wrap).hover(function(){
            $('.row-actions',this).css({'visibility': 'visible'});
        },function(){
            $('.row-actions',this).css({'visibility': 'hidden'});
        });
        // 绑定编辑事件
    	$('span.edit a,strong.edit a',wrap).click(function(){
    	    var id     = this.href.replace(self.location,'').replace('#',''),
    	        field  = $('#field-index-' + id + ' textarea[name^=field]').val();
				model_field_manage(id,parse_str(field));
    		return false;
    	});
        // 绑定删除事件
    	$('span.delete a',wrap).click(function(){
    	    var id = this.href.replace(self.location,'').replace('#','');
    		LazyCMS.confirm(_('Confirm Delete?'),function(r){
    			if (r) {
    				model_field_delete(id,field_empty);
    			}
    		});
    		return false;
    	});
	},
	// 增加减少函数
	set_rules = function(type){
        var v = $('#field_v');
        var s = $('#field_sv').val();
            if (type=='+') {
                v.val(v.val() + s + ';\n');
            } else if (type=='-') {
                v.val(v.val().replace( s +';\n',''));
            }
            v.scrollTop(2000);
    };
	
	// 绑定表格拖动事件
	if ($('tbody tr.empty',wrap).is('tr')===false) {
	   table_drag(); actions();
	}
	// 绑定规则点击
	$('#modelmanage span.default-rules > a').click(function(){
	    var val = this.href.replace(self.location,'').replace('#','');
	    $('#modelmanage input[name=path]').val(val); return false;
	});
	$('#modelmanage div.rules > a').click(function(){
	    var val = this.href.replace(self.location,'').replace('#','');
	    $('#modelmanage input[name=path]').insertVal(val); return false;
	});
	// 绑定展开事件
	$('#fields').each(function(i){
	    var fieldset = $(this);
	    $('a.toggle,h3',this).click(function(){
	        fieldset.toggleClass('closed');
	    });
	});
    // 绑定全选事件
    $('input[name=select]',wrap).click(function(){
        $('input[name^=list]:checkbox,input[name=select]:checkbox',wrap).attr('checked',this.checked);
    });
	// 绑定删除按钮
	$('button.delete',wrap).click(function(){
	    LazyCMS.confirm(_('Confirm Delete?'),function(r){
			if (r) {
				$('input:checkbox[name^=listids]:checked',wrap).each(function(){
                    model_field_delete(this.value,field_empty);
                });
			}
		});
	});
    // 提交事件
    $('form#modelmanage').ajaxSubmit(function(data){
        LazyCMS.ajaxResult(data);
    });
	// 绑定添加事件
	$('button.addnew',wrap).click(function(){
		model_field_manage();
	});
	// 添加静态方法
	arguments.callee.wrap       = wrap;
	arguments.callee.actions    = actions;
	arguments.callee.table_drag = table_drag;
	arguments.callee.set_rules  = set_rules;
}
// 管理字段
function model_field_manage(id,params) {
	var wrap = model_manage_init.wrap, actions = model_manage_init.actions, table_drag = model_manage_init.table_drag, set_rules  = model_manage_init.set_rules;
		id = id || '', params = $.extend(params||{},(params?{id:id}:{}));
	$.post(LazyCMS.ADMIN_ROOT + 'model.php?action=field',params,function(r){
        var title = _('Add New','model','field');
            title = id?_('Edit','model','field'):title;
		LazyCMS.dialog({
            name:'field', title:title, styles:{ 'width':'400px' }, top:100, body:r, remove:function() {
                LazyCMS.removeDialog('field'); $('#field-index-' + id + ' textarea').removeClass('edit');
            }
        },function(){
            
            if ($('form#model-field-table',this).is('form')==false) return ;

            $('#field-index-' + id + ' textarea').addClass('edit');
            
            var dialog = this, switch_type = function(type){
                $('#field_serialize,#field_attrs,#field_length,#field_default',dialog).hide(0,function(){
                    LazyCMS.selectEdit();
                });
                switch (type) {
                    case 'input':
                        $('#field_length,#field_default',dialog).show(0,function(){
                            LazyCMS.selectEdit();
                        });
                       break;
                    case 'textarea':
                        $('#field_default',dialog).show(0,function(){
                            LazyCMS.selectEdit();
                        });
                       break;
                    case 'radio': case 'checkbox': case 'select':
                        $('#field_serialize,#field_default',dialog).show(0,function(){
                            LazyCMS.selectEdit();
                        });
                       break;
                    case 'basic': case 'editor':
                        $('#field_attrs,#field_default',dialog).show(0,function(){
                            LazyCMS.selectEdit();
                        });
                       break;
                    case 'date':
                        $('#field_default',dialog).show(0,function(){
                            LazyCMS.selectEdit();
                        });
                       break;
                }
            },
            // 切换显示帮助
            switch_help = function(){
                $('#field_help',dialog).toggle(0,function(){
                    LazyCMS.selectEdit();
                });
            },
            // 切换显示验证规则
            switch_verify = function(){
                $('#field_verify',dialog).toggle(0,function(){
                    LazyCMS.selectEdit();
                });
            };
            switch_type($('#field_t').val());
            // 绑定验证按钮
            $('#field_is_help').click(switch_help);
            if ($('#field_is_help').attr('checked')) {
                switch_help();
            }
            // 绑定验证按钮
            $('#field_is_verify').click(switch_verify);
            if ($('#field_is_verify').attr('checked')) {
                switch_verify();
            }
            // 绑定增加、减少事件
            $('#field_verify td a[rule]',dialog).click(function(){
                set_rules($(this).attr('rule'));
            });
            // 绑定类型切换事件
            $('#field_t').change(function(){
                switch_type(this.value);
            });
            // 绑定保存按钮
            $('button[rel=save]',this).click(function(){
                var error = [],fields = [], type = $('#field_t').val(), index = 0, selector = 'input[name=l],input[name=n],input[name=so],select[name=t],[name=w]',
                    label = $.trim($('input[name=l]',dialog).val()), name = $.trim($('input[name=n]',dialog).val());
                // 取消样式
                $('.input_error,.textarea_error',dialog).removeClass('input_error').removeClass('textarea_error');
                // 获取已经添加的字段
                $('td textarea:not(.edit)',wrap).each(function(){
                    fields.push(parse_str(this.value).n);
                });
                // 开始验证
                if (label=='') error.push({'id':'l','text':_('The label field is empty.','model')});
                if (name=='') {
                    error.push({'id':'n','text':_('The name field is empty.','model')});
                } else if ($.inArray(name,fields)!=-1) {
                    error.push({'id':'n','text':_('The name already exists.','model')});
                }
                if (error.length > 0) {
                    $(dialog).error(error);
                    LazyCMS.selectEdit();
                    return false;
                }
                $('tbody tr.empty',wrap).remove();
                if ($('#field_is_help').attr('checked')==true) {
                    selector+= ',textarea[name=h]';
                }
                if ($('#field_is_verify').attr('checked')==true) {
                    selector+= ',textarea[name=v]';
                }
                switch (type) {
                    case 'input':
                       selector+= ',[name=c],input[name=d]';
                       break;
                    case 'textarea':
                       selector+= ',input[name=d]';
                       break;
                    case 'radio': case 'checkbox': case 'select':
                       selector+= ',textarea[name=s],input[name=d]';
                       break;
                    case 'basic': case 'editor':
                       selector+= ',input[name^=a],input[name=d]';
                       break;
                    case 'date':
                       selector+= ',input[name=d]';
                       break;
                }

                var row = function(index) {
                    var tr = '<tr id="field-index-' + index + '" index="' + index + '">';
                        tr+= '<td class="check-column"><input type="checkbox" value="' + index + '" name="listids[]"></td>';
                        tr+= '<td><strong><a href="javascript:;">' + $('input[name=l]',dialog).val() + '</a></strong><br>';
                        tr+= '<div class="row-actions" style="visibility: hidden;"><span class="edit"><a href="#' + index + '">' + _('Edit') + '</a> | </span><span class="delete"><a href="#' + index + '">' + _('Delete') + '</a></span></div>';
                        tr+= '<textarea class="hide" name="field[]">' + $(selector,dialog).serialize() + '</textarea></td>';
                        tr+= '<td>' + $('input[name=n]',dialog).val() + '</td>';
                        tr+= '<td>' + $('select[name=t]',dialog).val() + '</td>';
                        tr+= '<td>' + $('input[name=d]',dialog).val() + '</td>';
                        tr+= '</tr>';
                    return tr;
                };

                var id = $('input:hidden[name=id]',dialog).val();
                if (id) {
                    $('#field-index-' + id,wrap).replaceWith(row(id));
                } else {
                    $('tbody tr',wrap).each(function(){ index = Math.max(0,parseInt($(this).attr('index'))); }); index++;
                    $('tbody',wrap).append(row(index));
                }
                table_drag(); actions(); LazyCMS.removeDialog('field');
            });
        });
	});
}



// 删除字段
function model_field_delete(id,callback) {
    $('#field-index-' + id).remove(); if ($.isFunction(callback)) callback();
}