var timer;

$("document").ready(function(){
    $('.result').click(function(e){
        var url =  $(this).attr('href');
        var id = $(this).data('id');
        sendClick(id, url);
        return false;
    });
    
    
});

$("document").ready(function(){
    var grid = $('.imageResults');
    grid.on("layoutComplete", function(){
        $(".gridItem img").css("visibility", "visible");
    });

    grid.masonry({
        itemSelector: ".gridItem",
        columnWidth: 200,
        gutter: 5,
        transitionDuration:0,
        isInitLayout:false
    });

    $("[data-fancybox]").fancybox({
        caption : function( instance, item ) { 
            var caption = $(this).data('caption') || '';
            var siteUrl = $(this).data('siteurl') || '';
            if ( item.type === 'image' ) {
                caption = (caption.length ? caption + '<br />' : '') +
                '<a href="' + item.src + '">' + caption + '</a><br/>'+ 
                '<a href="' + item.src + '">View image</a><br/>'+
                '<a href="' + siteUrl + '">Visit Page</a>' ;
            }
    
            return caption;
        },

        afterShow : function( instance, item ) { 
            sendImageClick(item.src)
        }
    });
});
 
function sendImageClick(url){
    $.post("image/clicks", {url:url}).done(function(response){     
    });
 }

function sendClick(linkId, url){
   $.post("clicks", {id:linkId, url:url}).done(function(response){
        if(response.responseCode == 200){
            window.location.href = url;
        }       
   });
}
function loadImage(src, className){
    var image = $('<img>');
    image.on("load", function(){
        $("." + className + " a").append(image);
        clearTimeout(timer);
        timer = setTimeout(function(){
            $(".imageResults").masonry();
        },500 );      
    });

    image.on("error", function(){
        $("." +className).remove();
        $.post('broken',{url:src});
    });

    image.attr("src", src);

}