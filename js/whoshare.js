$(document).ready(function(){
    if (typeof FileActions !== 'undefined' && $('#dir').length>0) {
        

        if ( $('#dir').val().substring(1,7) == 'Shared' ) {
            
            FileActions.register('file','Owner',OC.PERMISSION_READ,function(){return OC.imagePath('files_whoshare','whoshare.png')},function(filename){
                getWhoShare(filename);
            });
            
            FileActions.register('dir','Owner',OC.PERMISSION_READ,function(){return OC.imagePath('files_whoshare','whoshare.png')},function(filename){
                getWhoShare(filename);
            });

        }
    }
});


function getWhoShare(filename){

    oc_dir = $('#dir').val().substring(7);
    oc_path = oc_dir +'/'+filename;
    
    $.ajax({
        type: 'GET',
        url: OC.filePath('files_whoshare','ajax','whoshare.php'),
        dataType: 'json',
        data: { path: oc_path },
        async: false,
        success: function( result ) {
            if ( result.status == 'success' ) {
                if (($('#dropdown').length > 0)) {
                    if (oc_path != $('#dropdown').data('file')) {
                        $('#dropdown').hide('blind', function() {
                            $('#dropdown').remove();
                            $('tr').removeClass('mouseOver');
                            showWhoShare(result.data.user,filename,oc_path,result.data.photo);
                        });
                    }
                } else {
                    showWhoShare(result.data.user,filename,oc_path,result.data.photo);
                }
            } ;
        }
    });

}


function showWhoShare(user,filename,files,photo){

    var html = '<div id="dropdown" class="drop drop-who-share" data-file="'+files+'">';

    if (filename) {
        $('tr').filterAttr('data-file',filename).addClass('mouseOver');
        $(html).appendTo($('tr').filterAttr('data-file',filename).find('td.filename'));
    } 
    
    if (photo) {
        var photo_img = OC.filePath('user_photo', 'ajax', 'showphoto.php') +'?user=' + encodeURIComponent(user);
        $('<div id="whoshare"><img src="'+photo_img+'"><p>Who Share ?<br><strong>'+user+'</strong></p></div>').appendTo('#dropdown');
    } else {
        $('<div id="whoshare"><p>Who Share ?<br><strong>'+user+'</strong></p></div>').appendTo('#dropdown');
    }
    

}

$(this).click(
    function(event) {
    if ($('#dropdown').has(event.target).length === 0 && $('#dropdown').hasClass('drop-who-share')) {
        $('#dropdown').hide('blind', function() {
            $('#dropdown').remove();
            $('tr').removeClass('mouseOver');
        });
    }

    
    }
);
