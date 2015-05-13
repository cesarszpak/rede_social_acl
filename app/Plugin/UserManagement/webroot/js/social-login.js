$(document).ready(function(){
    $('#facebook').click(function(e){
        $.oauthpopup({
            path: fbpath,
            width:600,
            height:300,
            callback: function(){
                window.location.reload();
            }
        });
        e.preventDefault();
    });
    $('#google').click(function(e){
        $.oauthpopup({
            path: gpath,
            width:600,
            height:300,
            callback: function(){
                window.location.reload();
            }
        });
        e.preventDefault();
    });
    $('#twitter').click(function(e){
        $.oauthpopup({
            path: twpath,
            width:600,
            height:300,
            callback: function(){
                window.location.reload();
            }
        });
        e.preventDefault();
    });
    $('#linkedin').click(function(e){
        $.oauthpopup({
            path: ldpath,
            width:600,
            height:300,
            callback: function(){
                window.location.reload();
            }
        });
        e.preventDefault();
    });
});