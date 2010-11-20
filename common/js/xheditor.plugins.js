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
 *
 * xheditor plugins
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
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
            _this.pasteHTML('<embed width="'+jWidth.val()+'" height="'+jHeight.val()+'" lazysrc="'+jUrl.val()+'" flashvars="file='+jUrl.val()+'" src="'+LazyCMS.ROOT+'common/editor/plugins/mediaplayer/player.swf" quality="high" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash" lazytype="Flv" />');
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
        },522,435);
    }},
    // Pagebreak
    Pagebreak:{c:'xhePlugins_btnPageBreak',t:_('Insert Pagebreak'),e:function(){
        this.pasteHTML('<img class="xhePageBreak" src="' + LazyCMS.ROOT + 'common/images/t.gif" alt="" />');
    }},
    // Removelink
    Removelink:{c:'xhePlugins_btnRemoveLink',t:_('Remove external links'),e:function(){
        var source  = this.getSource(),
            host    = document.location.host;
            source  = source.replace(/<a[^>]+?href\s*=\s*["']?([^"']+)[^>]*>(.+?)<\/a>/ig,function(all,url,text){
                // http:// 开头，且不是本域名下的链接
                if (('http://' == url.substr(0,7) && url.substring(7,host.length+7) != host)
                        || ('https://' == url.substr(0,8) && url.substring(8,host.length+8) != host)
                        || ('ftp://' == url.substr(0,6) && url.substring(6,host.length+6) != host)) {
                    all = text;
                }
                return all;
            });
            this.setSource(source);
    }}
};



var xheFilter = window.xheFilter = {
    SetSource: function(source) {
        var html = String(source);
            html = html.replace(/\r?\n/g,'');
            html = html.replace(/(<embed(?:\s+[^>]*?)?)(flashvars\s*=\s*"file=)([^"]+)("(?:\s+[^>]*?)?(?:src\s*=\s*"[^"]*mediaplayer\/player.swf")(?:\s+[^>]*?)?(?:\s+type\s*=\s*"\s*application\/x-shockwave-flash\s*"|\s+classid\s*=\s*"\s*clsid:d27cdb6e-ae6d-11cf-96b8-4445535400000\s*")[^>]*?\/>)/ig,function(all,start,flashvars,file,end){
                return start + 'lazysrc="' + file + '" lazytype="Flv" ' + flashvars + file + end;
            });
            html = html.replace(/<!--pagebreak-->/ig,'<img class="xhePageBreak" src="' + LazyCMS.ROOT + 'common/images/t.gif" alt="" />');
        return html;
    },
    GetSource: function(source) {
        var html = String(source);
            html = html.replace(/(<embed(?:\s+[^>]*?)?)(?:lazysrc\s*=\s*"[^"]*"\s)([^>]*?)(?:lazytype\s*=\s*"[^"]*"\s)([^>]*?\/>)/ig,function(all,start,center,end){
                return start + center + end;
            });
            html = html.replace(/<img\s*class\s*=\s*"xhePageBreak"[^>]*\/>/ig,function(all){
                return '<!--pagebreak-->';
            });
        return html;
    }
};