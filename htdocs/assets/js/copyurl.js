$(document).ready(function() {
    var buttons = $(".copy-button");
    buttons.each(function() {
        var clip = new ZeroClipboard.Client();
        clip.setHandCursor(true);
        clip.glue(this);
        var url = $(this).prev('.copy-source').data('long-url');
        clip.setText(url);
        clip.addEventListener('complete', function() {
            $('#copy-message').show().delay(1000).fadeOut();
        });
    });
});
