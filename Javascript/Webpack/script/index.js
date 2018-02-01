import  './../style/iconfont.css';
import  './../style/swiper.css';

import {basics} from './module/basics.js';
import {collectApi} from './module/UserApi.js';
import Swiper from './vendor/swiper-4.0.7.js';
$(function(){

    var  isLogin=false;
    var  token=$.cookie('uid');

    /*初始化基础功能*/
    basics.init();

    /*获取用户信息*/
    if(token){
        userApi.getuserInfo(function(){
            var ids=[];
            for(var i=0;i<$('.pro-list .pro-item').length;i++){
                ids.push($('.pro-list .pro-item').eq(i).find('.action-collect').attr('data-res-id'))
            }
            collectApi.status(ids.join(','),function(data){
                for(var t in data){
                    if(data[t]===1){
                        $('.pro-list').find('.action-collect[data-res-id='+t+']').find('.icon-collect-empty').addClass('hidden');
                        $('.pro-list').find('.action-collect[data-res-id='+t+']').find('.icon-collect-full').removeClass('hidden');
                    }
                }
            })
            isLogin=true;
        })

    }

    if($('.swiper-wrapper').find('.swiper-slide').length>1){
        new Swiper ('.swiper-container', {
            direction: 'horizontal',
            loop: true,
            autoplay:true,
            pagination: {
                el: '.swiper-pagination',
            }
        })
    }


    /*操作事件*/
    $('.action-collect').on('click',function(){
        if(!isLogin){
            window.location.href=window.config.domain+'/user/login.html'
        }
        var click=$(this).attr('data-click');
        var obj=$(this);
        var flag=$(this).find('i.hidden').hasClass('icon-collect-full');
        if(click==0){
            $(this).attr('data-click','1');
            if(flag){
                collectApi.collec(obj,function(){
                    obj.attr('data-click','0');
                },function(){
                    obj.attr('data-click','0');
                });
            }else{
                collectApi.uncollec(obj,function(){
                    obj.attr('data-click','0');
                },function(){
                    obj.attr('data-click','0');
                });
            }
        }
    })
})