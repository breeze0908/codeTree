/*基础交互*/

var basics={
    init:function(){
        this.leftMenu();
    },
    leftMenu:function(){
        $('.icon-burger').on('click',function(e){
            e.stopPropagation()
            $('.wrap').toggleClass('move');
            $('.menu').toggleClass('move');
            $(this).parents('.header').toggleClass('active');

        })

        $('.wrap').on('click',function(){
            if($('.header').hasClass('active')){
                $('.wrap,.menu').removeClass('move');
                $('.header').removeClass('active');
            }

        })
    }
}

export {basics}