$(document).ready(function(){
    $('.save_permissions').click(function(e){
        var data=this.id;
        var is_checked=$(this).is(":checked");
        $.ajax({
            url : url+'savepermission',
            type : 'POST',
            data: 	{info:data,is_checked:is_checked},
            success : function(x) {
                if(x=="success"){
                    //alert("Saved successfully");
                }
            },
            error : function(r) {
                alert('Could not connect to server');
            },
            async: false
        });
    });
});