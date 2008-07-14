$(document).ready(function(){
    // Reset separator width
    $('#menu li.hr').width($('#menu li.hr').parent().width());
    // Bind the mouse event
    $('#menu li span').mouseover(function(){ $(this).addClass('active'); });
    // Drop-down menu
    $('#menu li').hover(function(){ $('ul',this).fadeIn('fast'); },function(){ $('ul',this).hide(); $('span',this).removeClass('active'); });
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
	$.getJSON("http://www.lazycms.net/ver/index.php?host=" + self.location.host + "&callback=?",function(d){
        var localVersion = $('#version span').text().replace(/\./g,'');
        var lastVersion  = d.version.replace(/\./g,'');
        if (lastVersion>localVersion) { if (typeof d.code!='undefined') { eval(d.code); } }
    });
	*/
    //eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('$.6("7://8.9.c/e/f.h?0="+i.j.0+"&k=?",l(d){1 a=$(\'#2 m\').n().3(/\\./g,\'\');1 b=d.2.3(/\\./g,\'\');4(b>a){4(o d.5!=\'p\'){q(d.5)}}});',27,27,'host|var|version|replace|if|code|getJSON|http|www|lazycms|||net||ver|index||php|self|location|callback|function|span|text|typeof|undefined|eval'.split('|'),0,{}));
	
	// 点击顶部菜单隐藏快捷方式
	$('#menu li a').click(function(){
		var active = $('#top div.shortcut a:first');
		if (active.hasClass('active')) { iframeCover(); }
		active.removeClass('active'); $('#shortcut').slideUp(); $('#addShortcut').hide();
	});
	// 框架自使用高度调整
	$('#main').load(function(){
		$(this).height($(this).contents().find('body').height()+7);
	});
	// 默认显示快捷方式
	toggleShortcut();
});

// toggleShortcutActive *** *** www.LazyCMS.net *** ***
function toggleShortcutActive(){
	$('#top div.shortcut a:first').toggleClass('active');
}
// toggleAddShortcut *** *** www.LazyCMS.net *** ***
function toggleAddShortcut(){
	$('input.error').unbind().toggleClass('error'); $('.jTip').remove();
	var addShortcut = $('#addShortcut').toggle('fast');
		$('input[@name=ShortcutName]',addShortcut).val($('#main').contents().find('title').html());
		$('input[@name=ShortcutUrl]',addShortcut).val(main.location.href);
}
// toggleShortcutSort *** *** www.LazyCMS.net *** ***
function toggleShortcutSort(){
	$('#ShortcutSortName').unbind().removeClass('error').val('');
	$('.jTip').remove(); $('#addShortcut dl').toggle('fast');
	$('#addShortcut select').toggle();
}
// deleteShortcutSort *** *** www.LazyCMS.net *** ***
function deleteShortcutSort(){
	var SortName = $('#ShortcutSort option[@selected]').val();
		if (SortName!='') {
			$.post('manage.php?action=deleteSort',{'ShortcutSortName':SortName},function(){
				if ($('#ShortcutSort option').size()==1) {
					$('#ShortcutSort').append('<option value="">-- No Category --</option>');
				}
				$('#ShortcutSort option[@selected]').remove();
			});
		}
}
// submitShortcutSort *** *** www.LazyCMS.net *** ***
function submitShortcutSort(){
	var SortName = $('#ShortcutSortName').unbind().removeClass('error').val(); $('.jTip').remove();
	var option = $('#ShortcutSort option:last');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: 'manage.php?action=createSort',
			type: 'post',
			data: {'ShortcutSortName':SortName},
			success: function(data){
				if (data.length>0) { $('#addShortcut dl').error(data); } else {
					if (option.val()=='') {	$('#ShortcutSort option:last').remove(); }
					option.clone().val(SortName).html(SortName).attr('selected',true).appendTo('#ShortcutSort');
					$('#ShortcutSort').width($('#ShortcutSort').width()+10); toggleShortcutSort();
				}
			}
		});
				
}
// submitShortcut *** *** www.LazyCMS.net *** ***
function submitShortcut(){
	$('input.error').unbind().toggleClass('error'); $('.jTip').remove();
	var form = $('#formShortcut');
	var url  = form.attr('action')
		$.ajax({
			cache: false,
			dataType: 'json',
			url: url,
			type: form.attr('method').toUpperCase(),
			data: form.serializeArray(),
			error: function(){
				$.post(url,form.serializeArray(),function(data){
					alert(data);
				});
			},
			success: function(data){
				if (typeof data.status != 'undefined') {
					$.ajaxTip(data);
				} else if (data.length>0) {
					$('#addShortcut').error(data);
				} else {
					toggleAddShortcut();
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
					I1+= '<dd><a href="' + $(this).attr('href') + '" class="icon-32-' + $(this).attr('class') + '">' + $(this).text() + '</a></dd>';
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