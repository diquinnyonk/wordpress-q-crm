(function($){
    console.log('ready one');
    $('.table__delete').on('click',function(e){
        console.log('yes clicked');
        //e.preventDefault();
        var x = window.confirm('Are you sure you wish to delete?');
        console.log(x);
        if (x === true){
            //window.alert('Deleting');
        }else{
            e.preventDefault();
        }
    });
    $(".table__email").fancybox({
        maxWidth    : 800,
        maxHeight   : 600,
        fitToView   : false,
        width       : '70%',
        height      : '70%',
        autoSize    : false,
        closeClick  : false,
        openEffect  : 'none',
        closeEffect : 'none'
    });


})(jQuery);