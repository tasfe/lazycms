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

window.PROCESS = new Array();

// 设置系统CSS
$.setStyle();

// 设置全局 AJAX 默认选项
$.ajaxSetup({
    cache: false,
    beforeSend: function(s){
        window.loading.css({'z-index':$('.mask,.dialogUI').getMaxzIndex() + 1}).appendTo('body');
    },
    complete: function(){
        window.loading.remove();
    }
});

$(document).ready(function(){
    // 显示可编辑下拉框
    $.selectEdit();
    // 绑定submit提交事件
    $("form[method=post]:not([ajax=false])").ajaxSubmit();
    // Reset separator width
    $('#menu li.hr').each(function(){
        $(this).width($(this).parent().width());
    });
    // Bind the mouse event
    $('#menu li div').mouseover(function(){ $(this).addClass('active'); });
    // Drop-down menu
    $('#menu li').hover(function(){ $('ul',this).fadeIn(); },function(){ $('ul',this).hide(); $('div',this).removeClass('active'); });
    // Config Mouse over effect
    $('#menu li li:not(.hr)').mouseover(function(){
        $(this).width($(this).parent().width()-($.browser.msie && $.browser.version=='6.0'?4:2));
    });
    // 自动设置tab
    var t = $('#box fieldset legend[rel=tab]').text(); if (t!=='') { $('#tabs li.active a').text(t); }
    // 批量去除连接虚线
    $('a').focus(function(){ this.blur(); });   
    // 绑定展开事件
    $('a > .a1,a > .a2').parent()
        .attr('href','javascript:;')
        .click(function(){
            var u = getURI();
            var t = $(this);
            var c = (t.attr('cookie')!=='false')?true:false;
            var e = $(t.attr('rel'),t.parents('fieldset')).toggle();
                t.find('img').toggleClass('a1').toggleClass('a2');
            if (c) {
                $.cookie('collapse_' + u.File + '_' + t.attr('i'),e.css('display'),{expires:365,path:u.Path});
            }
        });
    // 执行半记忆操作
    $(document).SemiMemory();
    $('a:not([cookie=false]) > .a1,a:not([cookie=false]) > .a2').collapsed();
    // Get last version
	/*
    $.getJSON("http://lazycms.net/ver/index.php?host=" + self.location.host + "&callback=?",function(d){
        var localVersion = $('#version').attr('version').replace(/\./g,'');
        var lastVersion  = d.version.replace(/\./g,''); $('#version span').text(d.version);
        if (lastVersion>localVersion) { if (typeof d.code!='undefined') { eval(d.code); } }
    });
	*/
    // 显示帮助
    $('[help]').help();
	// 执行任务进程
    $('body').process();
});

/*
 * LazyCMS JS library for jQuery
 * http://www.lazycms.net
 *
 * Copyright (C) 2007-2008 LazyCMS.net All rights reserved.
 * LazyCMS is free software. This version use Apache License 2.0
 * See LICENSE.txt for copyright notices and details.
 */
(function($) {
    // 折叠
    $.fn.collapsed = function(){
        var u = getURI();
        this.each(function(i){
            var t = $(this).parent(); t.attr('i',i);
            var r = $(t.attr('rel'),t.parents('fieldset'));
            var c = $.cookie('collapse_' + u.File + '_' + i);
            switch (c) {
                case 'block':
                    $('img',t).removeClass('a1').addClass('a2');
                    r.show();
                    break;
                case 'none':
                    $('img',t).removeClass('a2').addClass('a1');
                    r.hide();
                    break;
                default:
                    if ($('img',t).hasClass('a1')) {
                        r.hide();
                    } else {
                        r.show();
                    }
                    break;
            }
        });
        return this;
    }
    // 半记忆操作
    $.fn.SemiMemory = function(){
        var u = getURI();
        // checkbox
        $('input:checkbox[cookie=true]',this).each(function(i){
            var c = $.cookie('checkbox_' + u.File + '_' + $(this).attr('id'));
            if (c!==null) {
                this.checked = (c=='true') ? true : false;
            }
        }).click(function(){
            $.cookie('checkbox_' + u.File + '_' + $(this).attr('id'),this.checked,{expires:365,path:u.Path});
        });
        return this;
    }
    /**
     * 可编辑下拉框
     *
     * @example: <select edit="true" default="value"></select>
     */
    $.selectEdit = function(){
        // 重新调整位置
        $('select[edit=yes]').each(function(){
            try {
                var s = $(this); if (s.is('select')==false) { return ; };
                $(this).prev().css({top:s.position().top+'px'});
            } catch (e) {}
        });
        // 替换控件
        $('select[edit=true]').each(function(){
            try {
                var s = $(this); if (s.is('select')==false) { return ; };
                var v = (s.attr('default')!='' && (typeof s.attr('default'))!='undefined')?s.attr('default'):s.val();
                var i = $('<input type="text" name="' + s.attr('name') + '" value="' + v + '" />')
                    .click(function(){ $(this).select(); })
                    .css({width:(s.width() - 18) + 'px',height:(s.height() - 2) + 'px',position:'absolute',top:s.position().top+'px',border:'none',margin:($.browser.msie?1:2)+'px 0 0 1px',padding:($.browser.msie?2:0)+'px 0 0 2px'})
                    .insertBefore(s);
                var c = $('<select name="edit_select_' + s.attr('name') + '" edit="yes">' + s.html() + '</select>')
                    .change(function(){ $(this).prev().val(this.value);}).val(i.val());
                    if (s.attr('id')!=='') { c.attr('id',s.attr('id')); }
                    i.blur(function(){
                        c.val(this.value);
                    });
                    s.replaceWith(c);
            } catch (e) {}
        });
    }
    // Explorer
    $.fn.Explorer = function(path,exts){
        var This = this; var field = this.selector; path = path || '/'; exts = exts || '*';
        $.post(common() + '/modules/system/gateway.php',{action:'explorer',path:path,field:field,exts:exts},function(data){
            if (JSON = $.result(data)) {
                $.dialogUI({name:'explorer',style:{width:'600px',overflow:'hidden'},title:JSON.TITLE, body:JSON.BODY},function(s){
                    var dialog = this;
                    $('td[rel=preview]',s).click(function(){
						$('td[rel=preview]',s).find('div').andSelf().css({background:'#FFF',color:'#333333'});
						$(this).find('div').andSelf().css({background:'#316AC5',color:'#FFF'});
                        // 显示加载图片
                        window.loading.css({'z-index':$('.mask,.dialogUI').getMaxzIndex() + 1}).appendTo('body');
                        var src = $(this).attr('src');
                        var img = new Image();
                            img.src = src;
                            img.onload = function(){
                                var width = Math.min($(document).width()*0.9,img.width);
                                $.dialogUI({ name:'preview',style:{width:Math.max(150,width+25)},title:$.t('picture/preview'),body:'<img src="' + src + '" width="' + width + '" alt="' + src + '" />'},function(s){
                                    var dialog = this; window.loading.remove();
                                    $('div.body',s).css({'text-align':'center'}).click(function(){
                                        dialog.remove();
                                    });
                                }); 
                            }
                    });
                    $('td a[rel=insert]',s).click(function(){
                        var src = $(this).attr('src');
                        var id  = field.replace('#','');
                        if (typeof tinyMCE != 'undefined' && typeof tinyMCE.get(id) != 'undefined') {
							var ext = src.substr(src.lastIndexOf('.') + 1).toLowerCase(); var html = '';
							if ($.inArray(ext,['bmp','gif','jpg','jpeg','png'])!=-1) {
								html = '<img src="' + src + '" />';
							} else {
								html = '<a href="' + src + '">' + src.substr(src.lastIndexOf('/') + 1) + '</a>';
							}
                            tinyMCE.get(id).execCommand('mceInsertContent', false, html); dialog.remove();
                        } else {
                            This.val(src); dialog.remove();
                        }
                        return false;
                    });
                    $('td a[rel=delete]',s).click(function(){
                        var src = $(this).attr('src');
                        $.confirm($.t('confirm/delete'),function(r){
                            if (r) {
                                // 删除图片
                                $.post(common() + '/modules/system/gateway.php',{action:'explorer_delete',file:src},function(data){
                                    if ($.result(data)) {
                                        This.Explorer(path,exts);
                                    };
                                });
                            }
                        });
                    });
                });
            }
        });
        return this;
    }
    // 创建文件夹
    $.fn.CreateFolder = function(p,e){
        var This = this;
        $.get(common() + '/modules/system/gateway.php',{action:'explorer_create',path:p},function(data){
            if (JSON = $.result(data)) {
                $.dialogUI({
                    name:'CreateFolder',title:JSON.TITLE, body:JSON.BODY,
                    style:{width:'350px',overflow:'hidden'}
                },function(s){
                    var t = this;
                    $('[rel=cancel]',s).click(function(){
                        t.remove();
                    });
                    $('[help]').help();
                    $('form',s).ajaxSubmit(function(r){
                        if (r) {
                            This.Explorer(p,e);
                        }
                    });
                });
            }
        });
    }
    // 上传文件
    $.fn.UpLoadFile = function(p,e){
        var field = this.selector;
        $.post(common() + '/modules/system/gateway.php',{action:'explorer',path:p,field:field,exts:e,CMD:'upload'},function(data){
            if (JSON = $.result(data)) {
                $.dialogUI({name:'explorer',style:{width:'600px',overflow:'hidden'},title:JSON.TITLE, body:JSON.BODY});
            }
        });
    }
    // 帮助提示
    $.fn.help = function(s){
        if (typeof s != 'undefined') {
            var t = this;
            $('img',t).attr('src',common() + '/images/loading.gif').removeClass('h5');
            $.post(common() + '/modules/system/gateway.php',{action:'help',module:MODULE,path:s},function(data){
                if (JSON = $.result(data)) {
                    $.dialogUI({name:'help',style:{width:'600px',overflow:'hidden'}, title:JSON.TITLE, body:JSON.BODY});
                }
                $('img',t).attr('src',common() + '/images/white.gif').addClass('h5');
            });
        } else {
            return this.each(function(){
                var p = $(this).attr('help');
                $(this).siblings('a[rel=help]').remove();
                $('<a href="javascript:;" rel="help"><img class="h5 os" src="../system/images/white.gif" /></a>').click(function(){
                    var img = $('img',this).attr('src',common() + '/images/loading.gif').removeClass('h5');
                    $.post(common() + '/modules/system/gateway.php',{action:'help',module:MODULE,path:p},function(data){
                        img.attr('src',common() + '/images/white.gif').addClass('h5');
                        if (JSON = $.result(data)) {
                            var pos = img.offset();
                                if ((img.offset().left + 20 + 350) > $(document).width()) {
                                    pos.left = pos.left - 2 - 350;
                                } else {
                                    pos.left = pos.left + 20;
                                }
                            $('.dialogUI[name=help]').remove();
                            $.dialogUI({
                                mask:false, name:'help',
                                title:JSON.TITLE, body:JSON.BODY,
                                style:$.extend({width:'350px',overflow:'hidden'},{top:pos.top + 8,left:pos.left})
                            });
                        } else {
                            return ;
                        }
                    });
                }).insertAfter(this);
                $('a').focus(function(){ this.blur(); });
            });
        }
    }
	$.fn.Minimized = function(a){
		var u = getURI();
		var d = this.css('display');
		//$.cookie('AJAX_PROCESS',d,{expires:365,path:});
		this.slideToggle(a);
	}
	// 任务进程
	$.fn.process = function(){
		var This = this;
		var process = $('<dl id="process"><dt><strong>' + $.t('process') + '</strong><a href="javascript:;" onclick="$(\'#process > dd\').Minimized(\'fast\');"></a></dt></dl>').css({display:'none'});
		$.post(common() + '/modules/system/gateway.php',{action:'create',submit:'process'},function(data){
			if (JSON = $.result(data)) {
				// 有数据，创建进程列表
				var length = JSON.length;
				if (length) {
					var percent = 0; var DATA = {};
					for (var i=0;i<length;i++) {
						// 追加到队列
						DATA = JSON[i]; window.PROCESS.push(DATA);
						// 任务没有达到100%的情况下才显示
						if (parseInt(DATA.OVER) < parseInt(DATA.TOTAL)) {
							// 防止0整除
							if (DATA.OVER==0 && DATA.TOTAL==0) { DATA.TOTAL = DATA.OVER = 1; } percent = DATA.OVER/DATA.TOTAL;
							process.append('<dd id="' + DATA.PARAM.lists + '"><div class="process"><div style="width:' + (percent*180).toFixed(2) + 'px"></div><span>' + (percent*100).toFixed(2) + '%</span></div></dd>');
						}
					}
					// 有任务，显示
					if ($('dd',process).size()>0) {
						process.slideDown('fast');
					}
					// 执行进程队列
					$.execProcess();
				}
			}
		});
		// 添加到页面
		process.appendTo(This);
	}
	// 执行进程
	$.execProcess = function(){
		// 先删除进度到100%的任务
		var DATA = window.PROCESS.shift();
		if (typeof DATA == 'undefined') { return ; }
		// 执行进程
		$.post(common() + '/modules/system/gateway.php?action=create',DATA.PARAM,function(data){
			$.result(data);
		});
	}
})(jQuery);





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