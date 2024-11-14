$(document).ready(function () {
    var azureauthuri = $("meta[name='azureauthuri']").attr('content');
    var html = '<div id="plugin_mantisazureoauth">\
        <a href="' + azureauthuri + '">Entrar com Microsoft</a>\
        </div>';
    $(html).insertAfter('#login-form');
});