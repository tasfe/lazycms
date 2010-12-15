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
 * | Copyright (C) 2007-2010 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. See LICENSE for copyright notices and details.  |
 * +---------------------------------------------------------------------------+
 */
function model_list_init() {
	// 绑定提交事件
	$('#modellist').actions();
}
// 删除模型
function model_delete(modelid){
    LazyCMS.confirm(_('Confirm Delete?'),function(r){
        if (r) {
            LazyCMS.postAction('model.php', {method:'bulk', action:'delete'}, modelid);
        }
    });
}
// 修改模型状态模型
function model_state(action,modelid){
    return LazyCMS.postAction('model.php', {method:'bulk', action:action}, modelid);
}
// 添加模型面初始化
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
				model_field_manage(id,LazyCMS.parse_str(field));
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
    // 绑定切换事件
	$('#modelmanage select#type').change(function(){
        //LazyCMS.redirect(this.value);
    });
	// 绑定表格拖动事件
	if ($('tbody tr.empty',wrap).is('tr')===false) {
	   table_drag(); actions();
	}
	// 绑定规则点击
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
    $('form#modelmanage').ajaxSubmit();
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
	$.post(LazyCMS.ADMIN + 'model.php?method=field',params,function(r){
        var title = id ? _('Edit') : _('Add New');
		LazyCMS.dialog({
            name:'field', title:title, styles:{ 'top':-100, 'width':'440px' }, body:r, remove:function() {
                LazyCMS.removeDialog('field'); $('#field-index-' + id + ' textarea').removeClass('edit');
            }
        },function(r){
            
            if ($('form#model-field-table',this).is('form')==false) return ;

            $('#field-index-' + id + ' textarea').addClass('edit');
            
            var dialog = this, switch_type = function(type){
                $('#field_serialize,#field_toolbar,#field_length,#field_default',dialog).hide();
                switch (type) {
                    case 'input':
                        $('#field_length,#field_default',dialog).show();
                       break;
                    case 'textarea':
                        $('#field_default',dialog).show();
                       break;
                    case 'radio': case 'checkbox': case 'select':
                        $('#field_serialize,#field_default',dialog).show();
                       break;
                    case 'basic': case 'editor':
                        $('#field_toolbar,#field_default',dialog).show();
                       break;
                    case 'date':
                        $('#field_default',dialog).show();
                       break;
                }
            },
            // 切换显示帮助
            switch_help = function(){
                $('#field_help',dialog).toggle();
            },
            // 切换显示验证规则
            switch_verify = function(){
                $('#field_verify',dialog).toggle();
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
            LazyCMS.eselect();
            // 绑定保存按钮
            $('button[rel=save]',this).click(function(){
                var error = [],fields = [], type = $('#field_t').val(), index = 0, selector = 'input[name=l],input[name=n],input[name=so],select[name=t],[name=w]',
                    label = $.trim($('input[name=l]',dialog).val()), name = $.trim($('input[name=n]',dialog).val());
                // 取消样式
                $('.input_error,.textarea_error',dialog).removeClass('input_error').removeClass('textarea_error');
                // 获取已经添加的字段
                $('td textarea:not(.edit)',wrap).each(function(){
                    fields.push(LazyCMS.parse_str(this.value).n);
                });
                // 开始验证
                if (label=='') error.push({'id':'l','text':_('The label field is empty.')});
                if (name=='') {
                    error.push({'id':'n','text':_('The name field is empty.')});
                } else if ($.inArray(name,fields)!=-1) {
                    error.push({'id':'n','text':_('The name already exists.')});
                }
                if (error.length > 0) {
                    $(dialog).error(error);
                    return false;
                }
                $('tbody tr.empty',wrap).remove();
                if ($('#field_is_help').attr('checked') == true) {
                    selector+= ',textarea[name=h]';
                }
                if ($('#field_is_verify').attr('checked') == true) {
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



/**
 * TableDnD plug-in for JQuery, allows you to drag and drop table rows
 * You can set up various options to control how the system will work
 * Copyright (c) Denis Howlett <denish@isocra.com>
 * Licensed like jQuery, see http://docs.jquery.com/License.
 *
 * Configuration options:
 *
 * onDragStyle
 *     This is the style that is assigned to the row during drag. There are limitations to the styles that can be
 *     associated with a row (such as you can't assign a border--well you can, but it won't be
 *     displayed). (So instead consider using onDragClass.) The CSS style to apply is specified as
 *     a map (as used in the jQuery css(...) function).
 * onDropStyle
 *     This is the style that is assigned to the row when it is dropped. As for onDragStyle, there are limitations
 *     to what you can do. Also this replaces the original style, so again consider using onDragClass which
 *     is simply added and then removed on drop.
 * onDragClass
 *     This class is added for the duration of the drag and then removed when the row is dropped. It is more
 *     flexible than using onDragStyle since it can be inherited by the row cells and other content. The default
 *     is class is tDnD_whileDrag. So to use the default, simply customise this CSS class in your
 *     stylesheet.
 * onDrop
 *     Pass a function that will be called when the row is dropped. The function takes 2 parameters: the table
 *     and the row that was dropped. You can work out the new order of the rows by using
 *     table.rows.
 * onDragStart
 *     Pass a function that will be called when the user starts dragging. The function takes 2 parameters: the
 *     table and the row which the user has started to drag.
 * onAllowDrop
 *     Pass a function that will be called as a row is over another row. If the function returns true, allow
 *     dropping on that row, otherwise not. The function takes 2 parameters: the dragged row and the row under
 *     the cursor. It returns a boolean: true allows the drop, false doesn't allow it.
 * scrollAmount
 *     This is the number of pixels to scroll if the user moves the mouse cursor to the top or bottom of the
 *     window. The page should automatically scroll up or down as appropriate (tested in IE6, IE7, Safari, FF2,
 *     FF3 beta
 * dragHandle
 *     This is the name of a class that you assign to one or more cells in each row that is draggable. If you
 *     specify this class, then you are responsible for setting cursor: move in the CSS and only these cells
 *     will have the drag behaviour. If you do not specify a dragHandle, then you get the old behaviour where
 *     the whole row is draggable.
 *
 * Other ways to control behaviour:
 *
 * Add class="nodrop" to any rows for which you don't want to allow dropping, and class="nodrag" to any rows
 * that you don't want to be draggable.
 *
 * Inside the onDrop method you can also call $.tableDnD.serialize() this returns a string of the form
 * <tableID>[]=<rowID1>&<tableID>[]=<rowID2> so that you can send this back to the server. The table must have
 * an ID as must all the rows.
 *
 * Other methods:
 *
 * $("...").tableDnDUpdate()
 * Will update all the matching tables, that is it will reapply the mousedown method to the rows (or handle cells).
 * This is useful if you have updated the table rows using Ajax and you want to make the table draggable again.
 * The table maintains the original configuration (so you don't have to specify it again).
 *
 * $("...").tableDnDSerialize()
 * Will serialize and return the serialized string as above, but for each of the matching tables--so it can be
 * called from anywhere and isn't dependent on the currentTable being set up correctly before calling
 *
 * Known problems:
 * - Auto-scoll has some problems with IE7  (it scrolls even when it shouldn't), work-around: set scrollAmount to 0
 *
 * Version 0.2: 2008-02-20 First public version
 * Version 0.3: 2008-02-07 Added onDragStart option
 *                         Made the scroll amount configurable (default is 5 as before)
 * Version 0.4: 2008-03-15 Changed the noDrag/noDrop attributes to nodrag/nodrop classes
 *                         Added onAllowDrop to control dropping
 *                         Fixed a bug which meant that you couldn't set the scroll amount in both directions
 *                         Added serialize method
 * Version 0.5: 2008-05-16 Changed so that if you specify a dragHandle class it doesn't make the whole row
 *                         draggable
 *                         Improved the serialize method to use a default (and settable) regular expression.
 *                         Added tableDnDupate() and tableDnDSerialize() to be called when you are outside the table
 */
jQuery.tableDnD = {
    /** Keep hold of the current table being dragged */
    currentTable : null,
    /** Keep hold of the current drag object if any */
    dragObject: null,
    /** The current mouse offset */
    mouseOffset: null,
    /** Remember the old value of Y so that we don't do too much processing */
    oldY: 0,

    /** Actually build the structure */
    build: function(options) {
        // Set up the defaults if any

        this.each(function() {
            // This is bound to each matching table, set up the defaults and override with user options
            this.tableDnDConfig = jQuery.extend({
                onDragStyle: null,
                onDropStyle: null,
                // Add in the default class for whileDragging
                onDragClass: "tDnD_whileDrag",
                onDrop: null,
                onDragStart: null,
                scrollAmount: 5,
                serializeRegexp: /[^\-]*$/, // The regular expression to use to trim row IDs
                serializeParamName: null, // If you want to specify another parameter name instead of the table ID
                dragHandle: null // If you give the name of a class here, then only Cells with this class will be draggable
            }, options || {});
            // Now make the rows draggable
            jQuery.tableDnD.makeDraggable(this);
        });

        // Now we need to capture the mouse up and mouse move event
        // We can use bind so that we don't interfere with other event handlers
        jQuery(document)
            .bind('mousemove', jQuery.tableDnD.mousemove)
            .bind('mouseup', jQuery.tableDnD.mouseup);

        // Don't break the chain
        return this;
    },

    /** This function makes all the rows on the table draggable apart from those marked as "NoDrag" */
    makeDraggable: function(table) {
        var config = table.tableDnDConfig;
        if (table.tableDnDConfig.dragHandle) {
            // We only need to add the event to the specified cells
            var cells = jQuery("td."+table.tableDnDConfig.dragHandle, table);
            cells.each(function() {
                // The cell is bound to "this"
                jQuery(this).mousedown(function(ev) {
                    jQuery.tableDnD.dragObject = this.parentNode;
                    jQuery.tableDnD.currentTable = table;
                    jQuery.tableDnD.mouseOffset = jQuery.tableDnD.getMouseOffset(this, ev);
                    if (config.onDragStart) {
                        // Call the onDrop method if there is one
                        config.onDragStart(table, this);
                    }
                    return false;
                });
            })
        } else {
            // For backwards compatibility, we add the event to the whole row
            var rows = jQuery("tr", table); // get all the rows as a wrapped set
            rows.each(function() {
                // Iterate through each row, the row is bound to "this"
                var row = jQuery(this);
                if (! row.hasClass("nodrag")) {
                    row.mousedown(function(ev) {
                        if (ev.target.tagName == "TD") {
                            jQuery.tableDnD.dragObject = this;
                            jQuery.tableDnD.currentTable = table;
                            jQuery.tableDnD.mouseOffset = jQuery.tableDnD.getMouseOffset(this, ev);
                            if (config.onDragStart) {
                                // Call the onDrop method if there is one
                                config.onDragStart(table, this);
                            }
                            return false;
                        }
                    }).css("cursor", "move"); // Store the tableDnD object
                }
            });
        }
    },

    updateTables: function() {
        this.each(function() {
            // this is now bound to each matching table
            if (this.tableDnDConfig) {
                jQuery.tableDnD.makeDraggable(this);
            }
        })
    },

    /** Get the mouse coordinates from the event (allowing for browser differences) */
    mouseCoords: function(ev){
        if(ev.pageX || ev.pageY){
            return {x:ev.pageX, y:ev.pageY};
        }
        return {
            x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
            y:ev.clientY + document.body.scrollTop  - document.body.clientTop
        };
    },

    /** Given a target element and a mouse event, get the mouse offset from that element.
        To do this we need the element's position and the mouse position */
    getMouseOffset: function(target, ev) {
        ev = ev || window.event;

        var docPos    = this.getPosition(target);
        var mousePos  = this.mouseCoords(ev);
        return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};
    },

    /** Get the position of an element by going up the DOM tree and adding up all the offsets */
    getPosition: function(e){
        var left = 0;
        var top  = 0;
        /** Safari fix -- thanks to Luis Chato for this! */
        if (e.offsetHeight == 0) {
            /** Safari 2 doesn't correctly grab the offsetTop of a table row
            this is detailed here:
            http://jacob.peargrove.com/blog/2006/technical/table-row-offsettop-bug-in-safari/
            the solution is likewise noted there, grab the offset of a table cell in the row - the firstChild.
            note that firefox will return a text node as a first child, so designing a more thorough
            solution may need to take that into account, for now this seems to work in firefox, safari, ie */
            e = e.firstChild; // a table cell
        }

        while (e.offsetParent){
            left += e.offsetLeft;
            top  += e.offsetTop;
            e     = e.offsetParent;
        }

        left += e.offsetLeft;
        top  += e.offsetTop;

        return {x:left, y:top};
    },

    mousemove: function(ev) {
        if (jQuery.tableDnD.dragObject == null) {
            return;
        }

        var dragObj = jQuery(jQuery.tableDnD.dragObject);
        var config = jQuery.tableDnD.currentTable.tableDnDConfig;
        var mousePos = jQuery.tableDnD.mouseCoords(ev);
        var y = mousePos.y - jQuery.tableDnD.mouseOffset.y;
        //auto scroll the window
        var yOffset = window.pageYOffset;
        if (document.all) {
            // Windows version
            //yOffset=document.body.scrollTop;
            if (typeof document.compatMode != 'undefined' &&
                 document.compatMode != 'BackCompat') {
               yOffset = document.documentElement.scrollTop;
            }
            else if (typeof document.body != 'undefined') {
               yOffset=document.body.scrollTop;
            }

        }

        if (mousePos.y-yOffset < config.scrollAmount) {
            window.scrollBy(0, -config.scrollAmount);
        } else {
            var windowHeight = window.innerHeight ? window.innerHeight
                    : document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
            if (windowHeight-(mousePos.y-yOffset) < config.scrollAmount) {
                window.scrollBy(0, config.scrollAmount);
            }
        }


        if (y != jQuery.tableDnD.oldY) {
            // work out if we're going up or down...
            var movingDown = y > jQuery.tableDnD.oldY;
            // update the old value
            jQuery.tableDnD.oldY = y;
            // update the style to show we're dragging
            if (config.onDragClass) {
                dragObj.addClass(config.onDragClass);
            } else {
                dragObj.css(config.onDragStyle);
            }
            // If we're over a row then move the dragged row to there so that the user sees the
            // effect dynamically
            var currentRow = jQuery.tableDnD.findDropTargetRow(dragObj, y);
            if (currentRow) {
                // TODO worry about what happens when there are multiple TBODIES
                if (movingDown && jQuery.tableDnD.dragObject != currentRow) {
                    jQuery.tableDnD.dragObject.parentNode.insertBefore(jQuery.tableDnD.dragObject, currentRow.nextSibling);
                } else if (! movingDown && jQuery.tableDnD.dragObject != currentRow) {
                    jQuery.tableDnD.dragObject.parentNode.insertBefore(jQuery.tableDnD.dragObject, currentRow);
                }
            }
        }

        return false;
    },

    /** We're only worried about the y position really, because we can only move rows up and down */
    findDropTargetRow: function(draggedRow, y) {
        var rows = jQuery.tableDnD.currentTable.rows;
        for (var i=0; i<rows.length; i++) {
            var row = rows[i];
            var rowY    = this.getPosition(row).y;
            var rowHeight = parseInt(row.offsetHeight)/2;
            if (row.offsetHeight == 0) {
                rowY = this.getPosition(row.firstChild).y;
                rowHeight = parseInt(row.firstChild.offsetHeight)/2;
            }
            // Because we always have to insert before, we need to offset the height a bit
            if ((y > rowY - rowHeight) && (y < (rowY + rowHeight))) {
                // that's the row we're over
                // If it's the same as the current row, ignore it
                if (row == draggedRow) {return null;}
                var config = jQuery.tableDnD.currentTable.tableDnDConfig;
                if (config.onAllowDrop) {
                    if (config.onAllowDrop(draggedRow, row)) {
                        return row;
                    } else {
                        return null;
                    }
                } else {
                    // If a row has nodrop class, then don't allow dropping (inspired by John Tarr and Famic)
                    var nodrop = jQuery(row).hasClass("nodrop");
                    if (! nodrop) {
                        return row;
                    } else {
                        return null;
                    }
                }
                return row;
            }
        }
        return null;
    },

    mouseup: function(e) {
        if (jQuery.tableDnD.currentTable && jQuery.tableDnD.dragObject) {
            var droppedRow = jQuery.tableDnD.dragObject;
            var config = jQuery.tableDnD.currentTable.tableDnDConfig;
            // If we have a dragObject, then we need to release it,
            // The row will already have been moved to the right place so we just reset stuff
            if (config.onDragClass) {
                jQuery(droppedRow).removeClass(config.onDragClass);
            } else {
                jQuery(droppedRow).css(config.onDropStyle);
            }
            jQuery.tableDnD.dragObject   = null;
            if (config.onDrop) {
                // Call the onDrop method if there is one
                config.onDrop(jQuery.tableDnD.currentTable, droppedRow);
            }
            jQuery.tableDnD.currentTable = null; // let go of the table too
        }
    },

    serialize: function() {
        if (jQuery.tableDnD.currentTable) {
            return jQuery.tableDnD.serializeTable(jQuery.tableDnD.currentTable);
        } else {
            return "Error: No Table id set, you need to set an id on your table and every row";
        }
    },

    serializeTable: function(table) {
        var result = "";
        var tableId = table.id;
        var rows = table.rows;
        for (var i=0; i<rows.length; i++) {
            if (result.length > 0) result += "&";
            var rowId = rows[i].id;
            if (rowId && rowId && table.tableDnDConfig && table.tableDnDConfig.serializeRegexp) {
                rowId = rowId.match(table.tableDnDConfig.serializeRegexp)[0];
            }

            result += tableId + '[]=' + rowId;
        }
        return result;
    },

    serializeTables: function() {
        var result = "";
        this.each(function() {
            // this is now bound to each matching table
            result += jQuery.tableDnD.serializeTable(this);
        });
        return result;
    }

}

jQuery.fn.extend(
    {
        tableDnD : jQuery.tableDnD.build,
        tableDnDUpdate : jQuery.tableDnD.updateTables,
        tableDnDSerialize: jQuery.tableDnD.serializeTables
    }
);
