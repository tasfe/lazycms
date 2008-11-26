$(document).ready(function(){
    // Reset separator width
    $('#menu li.hr').each(function(){
        $(this).width($(this).parent().width());
    });
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
	// eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('$.6("7://8.9/c/e.f?0="+h.i.0+"&j=?",k(d){1 a=$(\'#2 l\').m().3(/\\./g,\'\');1 b=d.2.3(/\\./g,\'\');4(b>a){4(n d.5!=\'o\'){p(d.5)}}});',26,26,'host|var|version|replace|if|code|getJSON|http|lazycms|net|||ver||index|php||self|location|callback|function|span|text|typeof|undefined|eval'.split('|'),0,{}))
	// 批量去除连接虚线
	$('a').focus(function(){ this.blur(); });
});