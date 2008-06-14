// *** *** www.LazyCMS.net *** ***
jQuery.extend(jQuery.fn,{
    addsub : function(l1,l2,l3){
        if (typeof l3 == "undefined") {
            l3 = true;
        }
        if (!l3) { return ; }
        var nbsp = '';
        for (var i=0; i<l2; i++) {
            nbsp += "&nbsp; &nbsp;";
        }
        var $this = this;
        var tr    = this.parents('tr');
        var style = $(tr).attr("class") != "selected" ? " " + $(tr).attr("class") : "";
        var form  = this.parents('form');
        if ($('.sub' + l1 + ':visible',this.parents('tbody')).is("tr")==false) {
            $('img',this).attr('src',path() + '/system/images/loading.gif');
            $.post(form.attr('action'),{submit:'getsub',lists:l1,space:l2},function(data){
                var parseData = $.parseJSON(data);
                $('img',$this).attr('src',path() + '/system/images/os/dir0.gif');
                $.each(parseData,function(k,v){
                    var evalData = eval(v.js);
                    var thisData = $("td:first input",evalData).before(nbsp).end().show().html();
                        thisData = '<tr class="sub' + l1 + style + '">' + thisData + '</tr>';
                        tr.after(thisData);
                        if(v.issub=="1" && v.isopen){
                            $("#dir" + v.sortid).addsub(v.sortid,l2+1);
                        }
                });
            });
        } else {
            var tbody = this.parents('tbody');
            $('img',this).attr('src',path() + '/system/images/loading.gif');
            $.post(form.attr('action'),{submit:'isopen',lists:l1},function(){
                $('img',$this).attr('src',path() + '/system/images/os/dir1.gif');
                $('.sub' + l1,tbody).hide();
            });
        }
        return this;
    },
    selectType : function(l1){
        var type  = $('option[@value='+this.val()+']',this).attr('type');$(l1).val(type);
        var value = this.val();
        var defval = "name1:value1\nname2:value2\nname3:value3";
        switch (value) {
            case 'input':
                $('.___length').show();
                $('.___value').hide().find('textarea').val('');
                break;
            case 'radio': case 'checkbox': case 'select':
                $('.___length').hide().find('input:text').val('');
                if ($('.___value').show().find('textarea').val()=="") {
                    $('.___value').show().find('textarea').val(defval);
                }
                break;
            default:
                $('.___length').hide().find('input:text').val('');
                $('.___value').hide().find('textarea').val('');
                break;
        }
        switch (type) {
            case 'text': case 'mediumtext':
                $('.___toggle').hide().find('input:checkbox').attr('checked',false);
                break;
            default:
                $('.___toggle').show();
                break;
        }
        return this;
    },
    setTemplate : function(){
        var value = this.val();
        var input = arguments;
        var name  = $('option[@value='+value+']',this).attr('name');
        var model = $(input[0]).val();
        var sort  = $(input[1]).val();
        var page  = $(input[2]).val();
        if (sort.indexOf('/$/')!=-1) {
            $(input[1]).val(sort.replace('/$/','/' + name + '[list]/'));
        } else {
            $(input[1]).val(sort.replace('/' + model + '[list]/','/' + name + '[list]/'));
        }
        if (page.indexOf('/$/')!=-1) {
            $(input[2]).val(page.replace('/$/','/' + name + '[page]/'));
        } else {
            $(input[2]).val(page.replace('/' + model + '[page]/','/' + name + '[page]/'));
        }
        $(input[0]).val(name);
        return this;
    },
    browseFiles : function(l1){
        var value  = this.val();
            value = value.substr(0,7).toLowerCase() == 'http://' ? "" : value;
        var vPath  = value.substr(0,value.lastIndexOf('/'));
        var params = {from:this.attr('id')};
        if (typeof arguments[2] != "undefined") {
            if (vPath == "") {
                vPath = arguments[1];
            }
            params.path = vPath != "" ?  vPath : "/" ;
        } else {
            if (typeof arguments[1] == "object") {
                $.extend(params,arguments[1]);
            } else if (typeof arguments[1] == "undefined") {
                params.path = vPath != "" ?  vPath : "/";
            } else {
                var cPath = arguments[1];
                params.path = cPath != "" ?  cPath : "/" ;
            }
        }
        $.posts({
            url:l1,
            params:params,
            opts:{width:'600px','margin-left':'-300px','margin-top':'-200px',height:'400px'},
            pops:{main:{height:'90%'}}
        });
        return this;
    },
    getPoping : function(l1,l2,l3){
        var obj = $(this);
        var params = l3||{};
            obj.parents(l1).next('.pop').remove();
            $.post(l2,params,function(data){
                obj.parents(l1).after(data);
            });
    },
    change2input : function(l1){
        if (this.attr('checked')) {
            var keepcode = escape($(l1).html());
            $(l1).replaceWith('<input class="in2" type="text" id="' + $(l1).attr('id') + '" name="' + $(l1).attr('id') + '" value="' + $(l1).val() + '" /><input name="keepcode" id="keepcode" type="hidden" value="" />');
            $('#keepcode').val(keepcode);
        } else {
            var keepcode = unescape($('#keepcode').val())
            $(l1).replaceWith('<select name="' + $(l1).attr('id') + '" id="' + $(l1).attr('id') + '">' + keepcode + '</select>');
        }
    }
});
// inputValue *** *** www.LazyCMS.net *** ***
function inputValue(l1,l2){
    var I1 = '';
    var I2 = l2.split(',');
    $.each(I2,function(i,name){
        var val = name + ":'" + $("[@name=" + name + "]").val() + "'";
        if (I1.length==0) {
            I1 += val;
        } else {
            I1 += "," + val;
        }
    });
    I1 = "{" + I1 + "}";
    I1 = $.parseJSON(I1);
    $.extend(I1,l1);
    return I1;
}

// *** *** www.LazyCMS.net *** ***
function index(l1,l2,l3,l4){ if (l1==1) { return '<img src="'+path()+'/system/images/os/index_gray.gif" class="os"  title="NO INDEX" />';} return l2 ? ico('index',l3) : '<a href="'+l4+'"><img src="'+path()+'/system/images/os/index_gray.gif" class="os"  title="NO INDEX" /></a>';}
function home(l1,l2){ return l1 ? '<img src="'+path()+'/system/images/os/on.gif" class="os" />' : '<a href="'+l2+'" title="SET HOME"><img src="'+path()+'/system/images/os/on_gray.gif" class="os" /></a>'; }
function image(l1){ return l1!='' ? '<a href="'+l1+'" class="___images"><img src="'+path()+'/system/images/os/image.gif" class="os" /></a>' : '';}
function state(l1,l2,l3){ return l1==0 ? '<a href="'+l2+'" title="LOCKED"><img src="'+path()+'/system/images/os/on.gif" class="os" /></a>' : '<a href="'+l3+'" title="UNLOCKED"><img src="'+path()+'/system/images/os/on_gray.gif" class="os" /></a>'; }
function isExist(l1,l2,l3){ var I2 = l3.split(':'); return l2 ? browse(I2[1]) : '<a href="javascript:;" onclick="$(this).gm(\'' + I2[0] + '\',{lists:'+l1+'});" title="' + I2[0].toUpperCase() + '"><img src="'+path()+'/system/images/os/tip.gif" class="os" /></a>'; }
function selevel(){var a=$('span#levels');if($('input#adminlevel').attr("checked")){a.slideUp("fast")}else{a.slideDown("fast")}}
function loading(l1,l2,l3){
    var url = l2 || {};
    var toolbar = window.parent.$('#toolbar');
    var loading = $('#' + l1,toolbar);
        if (loading.is('div')==false) {
			var html = '<div id="' + l1 + '" class="loading" name="' + l3 + '" title="' + l3 + ':0%"><div>0%</div><span style="width:0px;">&nbsp;</span></div>';
				html+= '<script type="text/javascript">getLoading(\'' + url + '\');</script>'; toolbar.append(html);
        }
}

function getLoading(url){
	$.ajax({
		url:url,
		dataType:'json',
		error : function(){	getLoading(url); },
		success : function(data){
			$('#'+data.id).attr('title',$('#'+data.id).attr('name') + ':' + data.percent + '%');
			$('#'+data.id+' div').text(data.percent + '%');
			$('#'+data.id+' span').css('width',data.percent + 'px');
			if (data.percent<100) {
				window.setTimeout("getLoading('" + data.url + "')",data.sleep*1000);
			} else {
				window.setTimeout("$('#"+data.id+"',window.parent.$('#toolbar')).fadeOut('slow',function(){$('#"+data.id+"',window.parent.$('#toolbar')).remove()});",3000);
			}
		}
	});
}