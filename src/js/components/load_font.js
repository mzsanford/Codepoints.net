define(['jquery', 'components/gettext', 'webfont'], function($, gettext, WebFont) {
  var _ = gettext.gettext;

  $(function() {
    /**
    * load a font to render the codepoint figure
    */
    var cp_fig = $('.codepoint figure .fig');
    if (cp_fig.length) {
      var font_opts = $('#fonts option');
      if (font_opts.length) {
        var cp_font = font_opts.eq(0).val(),
            cp_fam  = $.trim(font_opts.eq(0).text());
        if (cp_font) {
          WebFont.load({
            custom: {
              families: [cp_font],
              urls: ['/api/font-face/'+encodeURIComponent(cp_font)+'.css']
            },
            active: function() {
              cp_fig.css({
                fontFamily: '"'+cp_fam+'", serif'
              });
              var _aside = cp_fig.closest('figure').next('aside');
              if (_aside.length) {
                _aside.find('dl:eq(0)')
                      .append('<dt>'+_('Font used above')+'</dt>' +
                              '<dd>'+cp_font+'</dd>');
              }
            }
          });
        }
      }
    }
  });
});
