import  './../style/iconfont.css';
import  './../style/swiper.css';

import {basics} from './module/basics.js';
import {userApi} from './module/userApi.js';
import Swiper from './vendor/swiper-4.0.7.js';
import Share from './module/share.js';

$(function(){
    var  isLogin=false;
    var  token=$.cookie('uid');

    /*初始化基础功能*/
    basics.init();

    /*获取用户信息*/
    if(token){
        userApi.getuserInfo(function(){
            var ids=$('.action-collect').attr('data-res-id');
            collectApi.status(ids,function(data){
                if(data[ids]===1){
                    $('.action-collect').find('.icon-collect-empty').addClass('hidden');
                    $('.action-collect').find('.icon-collect-full').removeClass('hidden');
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

    /*分享事件*/
    var links=new Share(window.share);
    for(var i=0;i<$('.share').length;i++){
        $('.share').eq(i).find('.share-btn').each(function(index,item){
            $(item).attr('href',links[index])
        })
    }
    $('.action-close').on('click',function(){
        $('.share').hide();
        $('.share-tip,.tip-text').hide();
    })
    $('.action-share').on('click',function(){
        $('.share').show();
    })
    $('.share-weixin,.share-qq').on('click',function(e){
        e.preventDefault()
        $('.share-tip,.tip-text').show();
    })
})