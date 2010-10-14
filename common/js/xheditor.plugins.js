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

var xplugins = window.xplugins = {
    // Flv
    Flv:{c:'xhePlugins_btnFlv',t:_('Insert Flv Video'),h:1,e:function(){
        var _this = this;
        var jFlv  = $('<div>' + _('Flv URL:') + ' <input type="text" id="xheFlvUrl" value="http://" class="xheText" /></div>'+
                      '<div>' + _('Dimension:') + ' <input type="text" id="xheFlvWidth" style="width:40px;" value="480" /> x <input type="text" id="xheFlvHeight" style="width:40px;" value="400" /></div>'+
                      '<div>' + _('Autostart:') + ' <input type="checkbox" id="xheFlvAutostart" value="true" /></div>'+
                      '<div style="text-align:right;"><input type="button" id="xheSave" value="' + _('Ok') + '" /></div>'),
            jUrl    = $('#xheFlvUrl',jFlv),
            jWidth  = $('#xheFlvWidth',jFlv),
            jHeight = $('#xheFlvHeight',jFlv),
            jSave   = $('#xheSave',jFlv),
            jAutostart = $('#xheFlvAutostart',jFlv);

        jSave.click(function(){
            _this.loadBookmark();
            _this.pasteHTML('<embed width="'+jWidth.val()+'" height="'+jHeight.val()+'" flashvars="file='+jUrl.val()+'&autostart='+jAutostart.val()+'" src="'+LazyCMS.WEB_ROOT+'common/editor/plugins/mediaplayer/player.swf" quality="high" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash" />');
            _this.hidePanel();
            return false;
        });
        _this.showDialog(jFlv);
    }},
    // google map
    Map:{c:'xhePlugins_btnMap',t:_('Insert Google map'),e:function(){
        var _this=this;
        _this.showIframeModal(_('Google Maps'),'{editorRoot}plugins/googlemap/googlemap.html',function(v){
            _this.pasteHTML('<img src="'+v+'" />');
        },538,404);
    }}
};