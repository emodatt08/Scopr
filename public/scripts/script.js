$("document").ready(function(){
    $('.result').click(function(e){
        var url =  $(this).attr('href');
        var id = $(this).data('id');
        sendClick(id, url);
        return false;
    })
});


function sendClick(linkId, url){
   $.post("clicks", {id:linkId, url:url}).done(function(response){
        if(response.responseCode == 200){
            window.location.href = url;
        }       
   });
}