var debug = false;
// 导入Plugins方法 *** *** www.LazyCMS.net *** ***
jQuery.extend({
    // 弹出层 *** *** www.LazyCMS.net *** ***
    poping: function(opts,pops){
		var path  = $("script[@src*=jquery]").attr("src").replace("/js/jquery.js","");
        var _opts = {width:'500px','margin-left':'-250px','margin-top':'-160px',textAlign:'left','border-style':'solid', 'border-width':'2px', 'border-color':'#CECECE #666 #666 #CECECE',height:'auto'};
        var _pops = {title:{},main:{}};
        $.extend(_opts,opts);
        $.extend(_pops,pops);
        $.extend($.blockUI.defaults.overlayCSS,{backgroundColor:'#000',opacity:'0.8',cursor:'default'});
        $.extend($.blockUI.defaults.pageMessageCSS,_opts);
        $.blockUI('<div class="title"><span>Loading...</span><a href="javascript:void(0);" class="close">×</a></div><div class="main"><img class="os" src="' + path + '/images/loading.gif" />Loading...</div>',{cursor:'default'});
        $("div.blockMsg").attr('id','poping');
        $("div.blockMsg .title").css(_pops.title);
        $("div.blockMsg .main").css(_pops.main);
        $("div[@class=blockUI],div#poping .close").click(function(){
            $.unblockUI();return false;
        });
    },
    // 使用post方法提交，并更新弹出层 *** *** www.LazyCMS.net *** ***
    posts:function(options,params){
        if (typeof options == 'object'){
            var _options = options||{};
        } else {
            var _params = params||{};
            var _options = {url:options,params:_params,opts:{},pops:{}};
        }
        $.poping(_options.opts,_options.pops);
        $.post(_options.url,_options.params,function(data){
            if (debug){alert(data);}
            $.updatepop(data);
        });
    },
    // 使用get方法提交，并更新弹出层 *** *** www.LazyCMS.net *** ***
    loading: function(options,params){
        if (typeof options == 'object'){
            var _options = options||{};
        } else {
            var _params = params||{};
            var _options = {url:options,params:_params,opts:{},pops:{}};
        }
        $.poping(_options.opts,_options.pops);
        $.get(_options.url,_options.params,function(data){
            if (debug){alert(data);};
            $.updatepop(data);
        });
    },
    // 更新弹出层 *** *** www.LazyCMS.net *** ***
    updatepop: function(data){
        if ((typeof data) == 'string'){
            data = $.parseJSON(data);
        }
        $("div#poping .title span").html(data.title);
        $("div#poping .main").html(data.main);
        $("div#poping .close").click(function(){
            $.unblockUI();return false;
        });
    }
});

// *** *** www.LazyCMS.net *** ***
jQuery.extend(jQuery.fn,{
    // 获取编辑器对象 *** *** www.LazyCMS.net *** ***
    editor: function (){
		var obj = FCKeditorAPI.GetInstance(this.attr('name'));
        $.extend(obj,{
            // 计算编辑器内文字长度 *** *** www.LazyCMS.net *** ***
            length: function (){
                var oDOM = this.EditorDocument;
                var iLength;
                if(document.all){
                    iLength = oDOM.body.innerText.length;
                } else {
                    var r = oDOM.createRange();
                        r.selectNodeContents( oDOM.body );
                    iLength = r.toString().length ;
                }
                return iLength;
            },
            // 读取和设置内容 *** *** www.LazyCMS.net *** ***
            html:function(string){
                if (typeof string=='undefined'){
                    return this.GetXHTML(true);
                } else {
                    this.SetHTML(string);
                    return this;
                }
            },
			// 追加内容 *** *** www.LazyCMS.net *** ***
			insert:function(string){
				if (this.EditMode == FCK_EDITMODE_WYSIWYG){
					this.InsertHtml(string);
				} else {
					 alert('You must be on WYSIWYG mode!');
				}
				return this;
			}
        });
        return obj;
    },
	gm:function(l1,params,opts,url){
		var _params = params||{};
		var _opts   = opts||{};
		var form    = this.parents('form');
		var _url    = url||form.attr('action');
		if (l1!="" || l1!="-") {
			var I1 = escape(l1);
		} 
		var isconfirm;
		if (I1=='delete') {
			isconfirm = confirm(lz_delete);
		} else if (I1=='clear') {
			isconfirm = confirm(lz_clear);
		} else {
			isconfirm = true;
		}
		if (I1!='-' && isconfirm) {
			var lists = "";
			$('input:checkbox',form).each(function(){
				if(this.checked){
					if(lists==""){
						lists = this.value;
					}else{
						lists += "," + this.value;
					}
				}
			});
			var _object = {
				url:_url,
				params:{'submit':I1,'lists':lists},
				opts:_opts
				};
			$.extend(_object.params,_params);
			$.posts(_object);
		}
	},
	// 循环下拉列表 *** *** www.LazyCMS.net *** ***
	updown: function(l1,l2,l3){
		var form = this.parents('form');
		var I2 = (l1=='up') ? "&uarr;" : "&darr;";
		var I3 = new Array(0,1,2,3,4,5,6,7,8,9,10,15,20);
		var I1 = "<select onchange=\"javascript:$.post('" + form.attr('action') + "',{submit:'updown',updown:'" + l1 + "',num:this.value,lists:'" + l2 + "',upid:'" + l3 + "'},function(a){if(a!==''){ alert('ERROR: Parameter error!'); } else { self.location.reload(); }});if(this.options[this.selectedIndex].value){this.options[0].selected=true;}\">";
		for(var i=0;i<I3.length;i++){
			if (i===0){ I3[i] = ""; }
			I1 += '<option value="' + I3[i] + '">' + I2 + I3[i] + '</option>';
		}
		I1 += '</select>';
		this.parent().html(I1);
		return this;
	},
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
			$('img',this).attr('src',path() + '/images/loading.gif');
			$.post(form.attr('action'),{submit:'getsub',lists:l1,space:l2},function(data){
				var parseData = $.parseJSON(data);
				$('img',$this).attr('src',path() + '/images/os/dir0.gif');
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
			$('img',this).attr('src',path() + '/images/loading.gif');
			$.post(form.attr('action'),{submit:'isopen',lists:l1},function(){
				$('img',$this).attr('src',path() + '/images/os/dir1.gif');
				$('.sub' + l1,tbody).hide();
			});
		}
		return this;
	},
	readonly : function(l1,l2){
		if (typeof $(l1).attr('readonly') == "undefined" && typeof this.attr('checked') != "undefined") {
			$(l1).val(l2);
			$(l1).attr('readonly','true');
		} else {
			$(l1).val('');
			$(l1).removeAttr('readonly');
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
				$('.___value').show().find('textarea').val(defval);
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
		if (typeof arguments[2] != "undefined")	{
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
	jump : function(l1){
		var url = l1.replace('$',this.val());
		self.location.href = url;
	},
	getPoping : function(l1,l2,l3){
		var obj = $(this);
		var params = l3||{};
			obj.parents(l1).next('.pop').remove();
			$.post(l2,params,function(data){
				obj.parents(l1).after(data);
			});
	},
	setFirst : function(l1,l2){
		if (this.attr("checked")) {
			var editor = this.parent().parent().find('iframe[@src*=fckeditor.html]').parent().find('input:hidden').editor().html();
				if (editor != "") {
					var firstIMG = $('img:first',editor).attr('src');
					if (typeof firstIMG != "undefined") {
						firstIMG = firstIMG.replace('http://'+l2,'');
						$(l1).val(firstIMG);
					} else {
						alert("Please insert a picture to the Editor!");
						this.attr("checked",false);
					}
				} else {
					alert("editor content is empty!");
					this.attr("checked",false);
				}
		} else {
			$(l1).val('');
		}
		return this;
	},
	toEnglish : function(l1,l2){
		var title = $(l1).val();
		if (title!="") {
			this.val(CC2PY(title) + l2);
		} else {
			this.val('');
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
function path(){ return $("script[@src*=jquery]").attr("src").replace("/js/jquery.js",""); }
function insertEditor(l1){ var editor = $('iframe[@src*=fckeditor.html]'); if (editor.is('iframe')) { editor.parent().find('input:hidden').editor().insert(l1); } else { alert('Did not find editor!'); }}
function ico(l1,l2){ var IMG  = '<img src="'+path()+'/images/os/'+l1+'.gif" alt="'+l1.toUpperCase()+'" class="os" />';	var HREF = '<a href="'+l2+'" title="'+l1.toUpperCase()+'">'+IMG+'</a>';	if (typeof l2 == "undefined") {	return IMG;	} else { return HREF; }}
function index(l1,l2,l3,l4){ if (l1==1)	{ return '<img src="'+path()+'/images/os/index_gray.gif" class="os"  title="NO INDEX" />';}	return l2 ? ico('index',l3) : '<a href="'+l4+'"><img src="'+path()+'/images/os/index_gray.gif" class="os"  title="NO INDEX" /></a>';}
function home(l1,l2){ return l1 ? '<img src="'+path()+'/images/os/on.gif" class="os" />' : '<a href="'+l2+'" title="SET HOME"><img src="'+path()+'/images/os/on_gray.gif" class="os" /></a>'; }
function ison(l1){ return l1 ? '<img src="'+path()+'/images/os/on.gif" class="os" />' : '<img src="'+path()+'/images/os/on_gray.gif" class="os" />'; }
function state(l1,l2,l3){ return l1==0 ? '<a href="'+l2+'" title="LOCKED"><img src="'+path()+'/images/os/on.gif" class="os" /></a>' : '<a href="'+l3+'" title="UNLOCKED"><img src="'+path()+'/images/os/on_gray.gif" class="os" /></a>'; }
function isExist(l1,l2,l3){ var I2 = l3.split(':');	return l2 ? browse(I2[1]) : '<a href="javascript:;" onclick="$(this).gm(\'' + I2[0] + '\',{lists:'+l1+'});" title="' + I2[0].toUpperCase() + '"><img src="'+path()+'/images/os/tip.gif" class="os" /></a>'; }
function browse(l1){ return '<a href="'+l1+'" title="BROWSE" target="_blank"><img src="'+path()+'/images/os/brow.gif" class="os" /></a>'; }
function updown(l1,l2,l3){ return '<span><a href="javascript:void(0);" onclick="$(this).updown(\'' + l1 + '\',' + l2 + ',' + l3 + ');" title="'+l1.toUpperCase()+'"><img src="'+path()+'/images/os/'+l1+'.gif" class="os" /></a></span>'; }
function labelError(l1,l2){ $('#'+l1).parent().append('<label class="error" for="'+l1+'">'+l2+'</label>'); }
function cklist(l1){ return '<input name="list" id="list_'+l1+'" type="checkbox" value="'+l1+'"/>'; }
function selevel(){var a=$('span#levels');if($('input#adminlevel').attr("checked")){a.slideUp("fast")}else{a.slideDown("fast")}}
function checkALL(e){$.each($(e.form).find('input:checkbox'),function(i,a){ if (checkALL.arguments[1]!=undefined) { this.checked = true; } else { this.checked = !this.checked; }});}
function winSize(){ var e = {}; if (self.innerHeight) { e.h = self.innerHeight; e.w = self.innerWidth; }else if(document.documentElement){ e.h = document.documentElement.clientHeight; e.w = document.documentElement.clientWidth; } else { e.h = document.body.clientHeight; e.w = document.body.clientWidth;} return e; }
function loadImage(){ var arrImage = loadImage.arguments; var objImage = new Image(); for (var i=0;i<arrImage.length;i++) {	objImage.src = arrImage[i];	} }
