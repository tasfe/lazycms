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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */

var xhePlugins = window.xhePlugins = {
    // Flv
    Flv:{c:'xhePlugins_btnFlv',t:_('Insert Flv Video'),h:1,e:function(){
        var _this = this,jParent = _this.getParent('embed[type=application/x-shockwave-flash][lazytype=Flv]');
        var jFlv  = $('<div>' + _('Flv URL:') + ' <input type="text" id="xheFlvUrl" value="http://" class="xheText" /></div>'+
                      '<div>' + _('Dimension:') + ' <input type="text" id="xheFlvWidth" style="width:40px;" value="480" /> x <input type="text" id="xheFlvHeight" style="width:40px;" value="400" /></div>'+
                      '<div style="text-align:right;"><input type="button" id="xheSave" value="' + _('Ok') + '" /></div>'),
            jUrl    = $('#xheFlvUrl',jFlv),
            jWidth  = $('#xheFlvWidth',jFlv),
            jHeight = $('#xheFlvHeight',jFlv),
            jSave   = $('#xheSave',jFlv);

        jSave.click(function(){
            _this.loadBookmark();
            _this.pasteHTML('<embed width="'+jWidth.val()+'" height="'+jHeight.val()+'" lazysrc="'+jUrl.val()+'" flashvars="file='+jUrl.val()+'" src="'+LazyCMS.WEB_ROOT+'common/editor/plugins/mediaplayer/player.swf" quality="high" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash" lazytype="Flv" />');
            _this.hidePanel();
            return false;
        });
        _this.showDialog(jFlv);
        
        if(jParent.length==1)
		{
			jUrl.val(jParent.attr('lazysrc'));
			jWidth.val(jParent.attr('width'));
			jHeight.val(jParent.attr('height'));
		}
    }},
    // google map
    GoogleMap:{c:'xhePlugins_btnGoogleMap',t:_('Insert Google map'),e:function(){
        var _this=this;
        _this.showIframeModal(_('Google Maps'),'{editorRoot}plugins/googlemap/map.html',function(r){
            _this.pasteHTML('<img src="' + r.src + '" alt="' + r.title + '" />');
        },538,404);
    }},
    // Pagebreak
    Pagebreak:{c:'xhePlugins_btnPageBreak',t:_('Insert Pagebreak'),e:function(){
        this.pasteHTML('<img class="xhePageBeak" src="' + LazyCMS.WEB_ROOT + 'common/images/t.gif" alt="&lt;!--pagebeak--&gt;" />');
    }}
};

var xheFilter = window.xheFilter = {
    SetSource: function(source) {
        var html = String(source);
            html = html.replace(/(<embed(?:\s+[^>]*?)?)(flashvars\s*=\s*"file=)([^"]+)("(?:\s+[^>]*?)?(?:src\s*=\s*"[^"]*mediaplayer\/player.swf")(?:\s+[^>]*?)?(?:\s+type\s*=\s*"\s*application\/x-shockwave-flash\s*"|\s+classid\s*=\s*"\s*clsid:d27cdb6e-ae6d-11cf-96b8-4445535400000\s*")[^>]*?\/>)/ig,function(all,start,flashvars,file,end){
                return start + 'lazysrc="' + file + '" lazytype="Flv" ' + flashvars + file + end;
            });
            html = html.replace(/<!--pagebeak-->/ig,'<img class="xhePageBeak" src="' + LazyCMS.WEB_ROOT + 'common/images/t.gif" alt="&lt;!--pagebeak--&gt;" />');
        return html;
    },
    GetSource: function(source) {
        var html = String(source);
            html = html.replace(/(<embed(?:\s+[^>]*?)?)(?:lazysrc\s*=\s*"[^"]*"\s)([^>]*?)(?:lazytype\s*=\s*"[^"]*"\s)([^>]*?\/>)/ig,function(all,start,center,end){
                return start + center + end;
            });
            html = html.replace(/<img\s*class\s*=\s*"xhePageBeak"([^>]*?)alt\s*=\s*"([^"]*?)"\s*\/>/ig,function(all,src,alt){
                return alt.replace(/&lt;/ig,'<').replace(/&gt;/ig,'>');
            });
        return html;
    }
};