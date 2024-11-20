$(document).ready(function () {
    var azureauthuri = $("meta[name='azureauthuri']").attr('content');
    var microsoftlogo = $("meta[name='microsoftlogo']").attr('content');
    var html = '<div id="plugin_mantisazureoauth" class="pull-right">\
        <br>\
        <a href="' + azureauthuri + '" class="btn-microsoft" ><img src="' + microsoftlogo + '" width="20" height="20"> Entrar com Microsoft </a>\
        </div>\
        <br>\
        <br>\
        <br>\
        <br>'
        ;
    $(html).insertAfter('#login-form');
});