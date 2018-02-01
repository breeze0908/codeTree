/**
 * [分享组件]
 * 1.传递 url 文案 图片 返回生成的分享链接
 * 2.图片协议为https会导致QQ空间分享的图片列表有问题
 * 3.腾讯微博生成的url地址是当前分享的网站地址和传入的没关系
 */
;(function(undefined) {
        "use strict"
        var _global;

        /**
         * [_share_api 各平台分享的API]
         * @type {Object}
         */
        var _share_api={
                qzone:"http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?",
                tsina:"http://service.weibo.com/share/share.php?",
                tqq:"http://share.v.t.qq.com/index.php?c=share&a=index",
                douban:"http://shuo.douban.com/!service/share?"
        };

        /**
         * [extend 合并对象]
         * @param  {[type]} o        [description]
         * @param  {[type]} n        [description]
         * @param  {[type]} override [description]
         * @return {[type]}          [description]
         */
        function extend(o,n,override) {
            for(var key in n){
                if(n.hasOwnProperty(key) && (!o.hasOwnProperty(key) || override)){
                    o[key]=n[key];
                }
            }
            return o;
        }

        /**
         * [Share 分享]
         * @param {[type]} opt [description]
         */
        function Share(opt){
            return this._initial(opt);
        };
        Share.prototype={
            constructor: this,
            /**
             * [_initial description]
             * @param  {[type]} opt [ 分享的 地址 描述 图片]
             * @return {[type]}     [ array=》分享链接]
             */
            _initial:function(opt){
                var _this=this;
                var _seting={
                    url:"",
                    title:"",
                    summary:"",
                    pics:"",
                    lists:[]//通过数组里面的值来判断页面上面需要生成哪些 分享链接['qzone','tsina','tqq','douban']
                }
                var links=[];
                _this._seting = extend(_seting,opt,true);
                if(_this._seting.lists.length>0){
                    for(var i=0;i<_this._seting.lists.length;i++){
                        switch (_this._seting.lists[i]){
                            case 'qzone':links.push(_this.qzone())
                            break;
                            case 'tsina':links.push(_this.tsina())
                            break;
                            case 'tqq':links.push(_this.tqq())
                            break;
                            case 'douban':links.push(_this.douban())
                            break;
                            case 'weixin':links.push(_this.weixin())
                            break;
                            default : return false
                            break;
                        }

                    }
                }
                return links;
            },
            /**
             * [qzone 分享到qq空间]
             * @return {[type]} [string 分享链接]
             */
            qzone: function(){
                return  _share_api.qzone+"url="+this._seting.url+"&title="+this._seting.title+"&summary="+this._seting.summary+"&pics="+this._seting.pics;
            },
            /**
             * [tsina 分享到新浪微博]
             * @return {[type]} [string 分享链接]
             */
            tsina:function(){
                return  _share_api.tsina+"url="+this._seting.url+"&title="+this._seting.summary+"&content=utf-8"+"&pic="+this._seting.pics;
            },
            /**
             * [tqq 分享到腾讯微博]
             * @return {[type]} [string 分享链接]
             */
            tqq:function(){
                return  _share_api.tqq+"&url="+this._seting.url+"&title="+this._seting.summary+"&pic="+this._seting.pics;
            },
            /**
             * [douban 分享到豆瓣]
             * @return {[type]} [string 分享链接]
             */
            douban:function(){
                return  _share_api.douban+"href="+this._seting.url+"&name="+this._seting.title+""+"&text="+this._seting.summary+"&image="+this._seting.pics;
            },
               /**
             * [douban 分享到weixin]
             * @return {[type]} [string 分享链接]
             */
            weixin:function(){
                var _this=this;
                /*wx.onMenuShareTimeline({
                    title: _this._seting.title, // 分享标题
                    link: _this._seting.url, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                    imgUrl: _this._seting.pics, // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });*/
                return  "javascript:;";
            }
        }

        // 最后将插件对象暴露给全局对象
        _global = (function(){ return this || (0, eval)('this'); }());
        if (typeof module !== "undefined" && module.exports) {
            module.exports = Share;
        } else if (typeof define  === "function" && define.amd) {
        define(function(){return Share;});
        } else {
        !('Share' in _global) && (_global.Share = Share);
        }
}());