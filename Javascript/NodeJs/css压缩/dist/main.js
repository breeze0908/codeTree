/**
 * @license RequireJS domReady 2.0.1 Copyright (c) 2010-2012, The Dojo Foundation All Rights Reserved.
 * Available via the MIT or new BSD license.
 * see: http://github.com/requirejs/domReady for details
 */
/*jslint */
/*global require: false, define: false, requirejs: false,
  window: false, clearInterval: false, document: false,
  self: false, setInterval: false */


define('domReady',function () {
    'use strict';

    var isTop, testDiv, scrollIntervalId,
        isBrowser = typeof window !== "undefined" && window.document,
        isPageLoaded = !isBrowser,
        doc = isBrowser ? document : null,
        readyCalls = [];

    function runCallbacks(callbacks) {
        var i;
        for (i = 0; i < callbacks.length; i += 1) {
            callbacks[i](doc);
        }
    }

    function callReady() {
        var callbacks = readyCalls;

        if (isPageLoaded) {
            //Call the DOM ready callbacks
            if (callbacks.length) {
                readyCalls = [];
                runCallbacks(callbacks);
            }
        }
    }

    /**
     * Sets the page as loaded.
     */
    function pageLoaded() {
        if (!isPageLoaded) {
            isPageLoaded = true;
            if (scrollIntervalId) {
                clearInterval(scrollIntervalId);
            }

            callReady();
        }
    }

    if (isBrowser) {
        if (document.addEventListener) {
            //Standards. Hooray! Assumption here that if standards based,
            //it knows about DOMContentLoaded.
            document.addEventListener("DOMContentLoaded", pageLoaded, false);
            window.addEventListener("load", pageLoaded, false);
        } else if (window.attachEvent) {
            window.attachEvent("onload", pageLoaded);

            testDiv = document.createElement('div');
            try {
                isTop = window.frameElement === null;
            } catch (e) {}

            //DOMContentLoaded approximation that uses a doScroll, as found by
            //Diego Perini: http://javascript.nwbox.com/IEContentLoaded/,
            //but modified by other contributors, including jdalton
            if (testDiv.doScroll && isTop && window.external) {
                scrollIntervalId = setInterval(function () {
                    try {
                        testDiv.doScroll();
                        pageLoaded();
                    } catch (e) {}
                }, 30);
            }
        }

        //Check if document already complete, and if so, just trigger page load
        //listeners. Latest webkit browsers also use "interactive", and
        //will fire the onDOMContentLoaded before "interactive" but not after
        //entering "interactive" or "complete". More details:
        //http://dev.w3.org/html5/spec/the-end.html#the-end
        //http://stackoverflow.com/questions/3665561/document-readystate-of-interactive-vs-ondomcontentloaded
        //Hmm, this is more complicated on further use, see "firing too early"
        //bug: https://github.com/requirejs/domReady/issues/1
        //so removing the || document.readyState === "interactive" test.
        //There is still a window.onload binding that should get fired if
        //DOMContentLoaded is missed.
        if (document.readyState === "complete") {
            pageLoaded();
        }
    }

    /** START OF PUBLIC API **/

    /**
     * Registers a callback for DOM ready. If DOM is already ready, the
     * callback is called immediately.
     * @param {Function} callback
     */
    function domReady(callback) {
        if (isPageLoaded) {
            callback(doc);
        } else {
            readyCalls.push(callback);
        }
        return domReady;
    }

    domReady.version = '2.0.1';

    /**
     * Loader Plugin API method
     */
    domReady.load = function (name, req, onLoad, config) {
        if (config.isBuild) {
            onLoad(null);
        } else {
            domReady(onLoad);
        }
    };

    /** END OF PUBLIC API **/

    return domReady;
});

/**
 * 仿jQuery的DOM方法封装
 *
 * @module dom
 * @author lijunjun
 * @version 1.0
 * @example
    d('id_of_dom').hasClass('cls');
    d('id_of_dom').addClass('cls').hide().setHtml('inner html content');
    var trimed_str = d.trim('string to be trimed');
    var strCamelExample = d.toCamelCase('str-camel-example');
 */
define('dom',function(){
    "use strict";
    var core_version = "@VERSION",
        core_trim = core_version.trim,
        rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,        
    
    Dom = function( id ) {
        return new Dom.prototype.init( id );
    };

    Dom.prototype = {
        constructor: Dom,
 
        /**
         * 通过document的原生方法id取dom
         *
         * @constructor
         * @method module:dom#init
         * @param {String} id
         * @return {Object} dom类的实例
         */
        init:function(id){
            this.node = document.getElementById(id);
            return this;
        },

        /**
         * 节点是否有指定的class
         *
         * @method module:dom#hasClass
         * @param {String} cls
         * @return {Boolean} true if found or false if not
         */
        hasClass : function(cls) {
            return this.node.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
        },

        /**
         * 向节点添加指定的class
         *
         * @method module:dom#addClass
         * @param {String} cls
         * @return {Object} dom object for js chain
         */
        addClass : function(cls) {
            if (!this.hasClass(cls)) {
                this.node.className += " " + cls;
            }
            return this;
        },

        /**
         * 节点删除指定的class
         *
         * @method module:dom#removeClass
         * @param {String} cls
         * @return {Object} dom object for js chain
         */
        removeClass : function(cls) {
            if (this.hasClass(cls)) {
                var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
                this.node.className = Dom.trim(this.node.className.replace(reg, ' '));
            }
            return this;
        },

        /**
         * 返回原生的DOM节点，为了eventutil的绑定
         *
         * @method module:dom#getEle
         * @return {Object} document dom object
         */
        getEle : function(){
            return this.node;
        },

        /**
         * 隐藏节点
         *
         * @method module:dom#hide
         * @return {Object} dom object for js chain
         */
        hide: function(){
            this.node.style.display = 'none';
            return this;
        },

        /**
         * 显示节点
         *
         * @method module:dom#show
         * @return {Object} dom object for js chain
         */
        show: function(){
            this.node.style.display = '';
            return this;
        },

        /**
         * 设置节点的innerHTML
         *
         * @method module:dom#setHtml
         * @param  {String} html
         * @return {Object} dom object for js chain
         */
        setHtml: function(html){
            this.node.innerHTML = html;
            return this;
        },

        /**
         * 获取节点的innerHTML
         *
         * @method module:dom#getHtml
         * @return {Object} dom object for js chain
         */
        getHtml: function(){
            return this.node.innerHTML;
        },

        /**
         * 设置节点的样式
         *
         * @method module:dom#setStyle
         * @param {String} prop
         * @param {String} value
         * @return {Object} dom object for js chain
         */
        setStyle: function (prop, value){
            if(prop === 'opacity'){
                this.node.style.filter = "alpha(opacity=" + value * 100 + ")";
                this.node.style.opacity = value;
            }else{
                prop = Dom.toCamelCase(prop);
                this.node.style[prop] = value;
            }
            return this;
        },

        /**
         * 获取节点在文档中的位置
         *
         * @method module:dom#getPos
         * @return {Object} dom object for js chain
         */
        getPos: function(){
            var p={"t":0,"l":0}, o=this.node;
            while(o){
                p.t+=o.offsetTop;
                p.l+=o.offsetLeft;
                o=o.offsetParent;
            }
            return p;
        },

        /**
         * 节点在浏览器视图中是否可见
         *
         * @method module:dom#isInView
         * @return {Object} dom object for js chain
         */
        isInView: function(){
            var t1=document.body.scrollTop + document.documentElement.scrollTop,
            t2=t1 + document.documentElement.clientHeight,
            l1=document.documentElement.scrollLeft,
            l2=l1 + document.documentElement.clientWidth,
            o=this.node,
            p={};

            if(o){
                p=this.getPos();
                if((p.t>=t1||p.t+o.clientHeight>=t1)&&p.t<=t2&&(p.l>=l1||p.l+o.clientWidth>=l1)&&p.l<=l2){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    };

    /**
     * 对字符进行驼峰式处理
     *
     * @method module:dom.toCamelCase
     * @return {String} string with camel pattern
     */
    Dom.toCamelCase = function(str){            
        var parts = str.split('-'), camel = parts[0], len = parts.length, i;
        if(len > 1){
            for(i=1; i < len; i++){
                camel += parts[i].charAt(0).toUpperCase() + parts[i].substring(1);
            }
        }
        return camel;
    };

    /**
     * 去除字符左右两边的空白字符
     *
     * @method module:dom.trim
     * @return {String} string without left or right spaces
     */
    Dom.trim = function(text){
        if(core_trim && !core_trim.call("\uFEFF\xA0")){
            return text === null ? "" : core_trim.call( text );
        } else {
            return text === null ? "" : ( text + "" ).replace(rtrim, "");
        }
    };

    Dom.prototype.init.prototype = Dom.prototype;

    return Dom;
});
define('eventutil',function(){
    "use strict";
    /** 
     事件绑定的浏览器兼容
     @exports eventutil
     @author lijunjun
     @version 1.0
     @example 
     e.addEventHandler(d('id_of_dom').getEle(), 'click', function(){
        //do sth here
     });
     */
    var EventUtil = {};
    /**
      * 注册事件
      *
      * @method module:eventutil.addEventHandler
      * @param {DOMObject} oTarget 事件绑定对象
      * @param {String} sEventType 事件绑定类型
      * @param {Function} fnHandler 触发事件后运行的函数
      */
    EventUtil.addEventHandler = function( oTarget, sEventType, fnHandler ) {
        if ( oTarget.addEventListener ) {
            oTarget.addEventListener( sEventType, fnHandler, false );
        } else if ( oTarget.attachEvent ) {
            oTarget.attachEvent( "on" + sEventType, fnHandler );
        } else {
            oTarget["on" + sEventType] = fnHandler;
        }
    };
    
    /**
      * 删除事件
      *
      * @method module:eventutil.addEventHandler
      * @param {DOMObject} oTarget 事件绑定对象
      * @param {String} sEventType 事件绑定类型
      * @param {Function} fnHandler 触发事件后运行的函数
      */  
    EventUtil.removeEventHandler = function( oTarget, sEventType, fnHandler ) {
        if ( oTarget.removeEventListener ) {
            oTarget.removeEventListener( sEventType, fnHandler, false );
        } else if ( oTarget.detachEvent ) {
            oTarget.detachEvent( "on" + sEventType, fnHandler );
        } else { 
            oTarget["on" + sEventType] = null;
        }
    };

	/**
      * 判断是否鼠标out或enter事件
      *
      * @method  module:eventutil.isMouseLeaveOrEnter
      * @param  {Event} e  触发的事件
      * @param  {DOMObject} target 需要进行事件触发判断的对象
	  * @return {Boolean} 是否鼠标out或enter事件
      */
	EventUtil.isMouseLeaveOrEnter = function(e, target) {
		if (e.type != 'mouseout' && e.type != 'mouseover') {
			return false;
		} 
		var reltg = e.relatedTarget ? e.relatedTarget : e.type == 'mouseout' ? e.toElement : e.fromElement; 
		while (reltg && reltg != target) 
			reltg = reltg.parentNode; 
		
		return (reltg != target); 
	};
    
    EventUtil.moveLeaveHandler = function(e,target){
        e = e || window.event;
        var isLeave = true;
        //ie 只有toElement  relatedTarget为w3c属性
        if(e.toElement){
            isLeave =   !this.contains(target, e.toElement) ;
        }else if(e.relatedTarget){
            isLeave =   !this.contains(target, e.relatedTarget) ;
        }
        return isLeave;
    };

    EventUtil.contains = function(parentNode,childNode){
        if(parentNode.contains) {
            return parentNode.contains(childNode);
        } else{
            var obj = childNode;
            while(obj&&obj.parentNode.tagName!='undefined'){
                if(obj==parentNode){
                    return true;
                }
                obj = obj.parentNode;
            }
            return false;
        }
    };

    return EventUtil;
});


/**
 * 类首页右侧排行榜的Tab切换
 *
 * @module switchtab
 * @author lijunjun
 * @version 1.0
 * @example
s1 = new s;
s1.init({
    identifyTab:'Tab_rebo_',//标签ID的前缀
    identifyList:'List_rebo_',//内容ID的前缀
    count:5,
    cnon:'on',
    auto:true|false,//boolean,是否轮播
    interval:5000,//轮播时间间隔
});
 */

define('switchtab_focus',['dom','eventutil'], function(d,et){
	function SwitchTab(){
		this.config = {}; 
		this.tabs = [];
		this.lists = [];
		this.timer = 0;
		this.idx = 0;
        this.lag_timer = 0;
		this.lag_setTimeout_timer = 0;
        this.lag_flag = false;
	};

	SwitchTab.prototype = {
		/**
         * tab配置初始化
         *
         * @method module:switchtab#init
         * @param {Object} config
         */
		init : function(config) {
			for(conf in config){
				this.config[conf] = config[conf];
			}
			var _self = this;
			for(var i=0;i<this.config.count;i++) {
				this.tabs[i] = d(this.config.identifyTab+i);
				this.lists[i] = d(this.config.identifyList+i);	
				(function(i){
		            
		            if(_self.config.auto === true){
		            	et.addEventHandler(_self.tabs[i].getEle(), 'mouseover', function(e){
							if(et.isMouseLeaveOrEnter(e, _self.tabs[i].getEle())){
								_self.pause();
								_self.show(i);
							}
						}); 
		            	et.addEventHandler(_self.lists[i].getEle(), 'mouseover', function(e){
							if(et.isMouseLeaveOrEnter(e, _self.lists[i].getEle())){
								_self.pause();
							}
						});
			            et.addEventHandler(_self.tabs[i].getEle(), 'mouseout', function(e){
							if(et.isMouseLeaveOrEnter(e, _self.tabs[i].getEle())){
								_self.pause();
								_self.auto();
							}
						}); 
			            et.addEventHandler(_self.lists[i].getEle(), 'mouseout', function(e){
							if(et.isMouseLeaveOrEnter(e, _self.lists[i].getEle())){
								_self.pause();
								_self.auto();
							}
						}); 
		        	} else {
						et.addEventHandler(_self.tabs[i].getEle(), 'mouseover', function(e){
                            _self.lag_flag = true;
                            if(typeof _self.config.lag!== 'undefined'){
                                _self.lag_timer = setTimeout(function(){
                                    if(_self.lag_flag){
                                        _self.show(i);
                                    }
                                }, parseInt(_self.config.lag));
                            }else{
                                if(!et.isMouseLeaveOrEnter(e, _self.tabs[i].getEle())){
                                    _self.show(i);
                                }
                            }
						});
                        et.addEventHandler(_self.tabs[i].getEle(), 'mouseout', function(e){
                            _self.lag_flag = false;
                            clearTimeout(_self.lag_timer);
						});
					}
		        })(i)	
			}	
			if(this.config.auto === true) {
				this.auto();
			}
		},

		/**
         * 自动轮播
         *
         * @method module:switchtab#auto
         */
		auto:function(){
			var _self = this;
			this.timer = setInterval(function(){
				_self.next();
			}, this.config.interval);
		},

		/**
         * 展示下一个tab及list
         *
         * @method module:switchtab#next
         */
		next:function(){
			this.idx = this.idx + 1;
			if(this.idx >= this.config.count){
				this.idx = 0;
			}
			this.show(this.idx);
		},

		/**
         * 暂停轮播
         *
         * @method module:switchtab#pause
         */
		pause:function(){
			clearInterval(this.timer);
		},

		/**
         * tab切换初始化
         *
         * @method module:switchtab#show
         * @param {Num} index 显示指定的tab
         */
		show : function(index){
			for(var i=0;i<this.config.count;i++) {
				if (i != index) {
					this.tabs[i].removeClass(this.config.cnon);
					this.lists[i].removeClass(this.config.cnon);
				}
			}
			this.tabs[index].addClass(this.config.cnon);
			this.lists[index].addClass(this.config.cnon);
			
			if(this.config.callback){
				for(key in this.config.callback){
					if(index == key || 'all' == key){
						this.config.callback[key].call(this, index);
					}
				}
			}
			
			this.idx = index;
			
		}
	};

	return SwitchTab;
})
/**
 * 图片延时加载插件
 *
 * @module lazy
 * @author lijunjun
 * @version 1.0
 * @example
     lazy.init();
     lazy.run();
 */
define('lazy',['eventutil','dom'], function(e, d){
	"use strict";
	var LAZY = {};
	LAZY=(function(){
		var pResizeTimer = null,
		imgs={};

		function position(o){
			var p={Top:0,Left:0};
			while(!!o){
				p.Top+=o.offsetTop;
				p.Left+=o.offsetLeft;
				o=o.offsetParent;
			}
			return p;
		}

		function resize_run(){
			var i,
			min={},
			max={},
			_img, img, width, height, wh;
	        min.Top = document.body.scrollTop || document.documentElement.scrollTop;
			min.Left=document.documentElement.scrollLeft;
			max.Top=min.Top+1*document.documentElement.clientHeight;
			max.Left=min.Left+document.documentElement.clientWidth;

			for(i in imgs){
				if(imgs[i]){
					_img=imgs[i];
					img = document.getElementById(i);
					if(!img){img = null; continue;}
					width = img.clientWidth;
					height = img.clientHeight;
					wh=position(img);
					
					if(((wh.Top>min.Top && wh.Top<max.Top && wh.Left>min.Left && wh.Left<max.Left) || ((wh.Top+height)>min.Top && wh.Top<max.Top && (wh.Left+width)>min.Left && wh.Left<max.Left))){
						(function(imgobj,realsrc){
							setTimeout(function() {
								imgobj.src = realsrc;								
								imgobj.removeAttribute('_src');
							}, 10) ;
						})(img,_img.src) ;
						delete imgs[i];
					}
                    
				}
			}
		}

		function resize(){
			if(pResizeTimer){
				//return '';
				clearTimeout(pResizeTimer);
			}
			pResizeTimer = setTimeout(function(){
				resize_run();
			}, 50);
		}

		
		/** @lends lazy */
		return {
			/**
	         * 初始化
	         *
	         * @method module:lazy.init
	         */
			init:function(){
				var i = 0,ttiframes=document.body.getElementsByTagName("iframe"),img,config;
				for(i=0;i<document.images.length;i++){
					img = document.images[i];
					config={};
					config.id = img.id;
					config.src = img.getAttribute('_src');
					if(config.src && !config.id){
						config.id = encodeURIComponent(config.src) + Math.random();
						img.id = config.id;
					}
					if(!config.id || !config.src){
						continue;
					}
					LAZY.push(config);
				}
                /*
				for(i=0;i<ttiframes.length;i++){
					config={};
					config.id = ttiframes[i].id;
					config.src = ttiframes[i].getAttribute('_src');
					if(config.src && !config.id){
						config.id = encodeURIComponent(config.src) + Math.random();
						ttiframes[i].id = config.id;
					}
					if(!config.id || !config.src){
						continue;
					}
					LAZY.push(config);
				}*/
			},

			/**
	         * 添加需要延时加载的图片
	         *
	         * @method module:lazy.push
	         * @param config 封装好的图片对象
	         */
			push:function(config){
				imgs[config.id] = config;
			},

			/**
	         * 注册浏览器滚动事件，触发延时加载
	         *
	         * @method module:lazy.run
	         */
			run:function(){
				e.addEventHandler(window,'scroll',resize);
				e.addEventHandler(window,'resize',resize);
				resize_run();
			},
            
            resize_run:resize_run
		};
	})();
	return LAZY;
});

/**
 * date: 2015/10/27 16:07
 * author: Tan <tanda@kankan.com>
 * version: 1.0
 */
require.config({
    baseUrl: "http://misc.web.xunlei.com/chupin/js",
    urlArgs: "date=20151027"
});

typeof window.kkHeader == "undefined" && (window.kkHeader = {});
require(["domReady","lazy", 'jquery','switchtab_focus'], function(ready,LAZY, $,stf) {
    if(!getCookie)
        var getCookie=function(name){
            var arr=document.cookie.split("; ");
            var i=0;
            for(i=0;i<arr.length;i++){
                var arr2=arr[i].split("=");
                if(arr2[0]==name){
                    return arr2[1]
                }
            }
            return""
        };
    if(!setCookie)
        var setCookie=function(name,value,hours){
            var host="kankan.com";
            if(arguments.length>2){
                var expireDate=new Date(new Date().getTime()+hours*3600000);
                document.cookie=name+"="+encodeURIComponent(value)+"; path=/; domain="+host+"; expires="+expireDate.toGMTString();
            }else{
                document.cookie=name+"="+encodeURIComponent(value)+"; path=/; domain="+host
            }
        };

    var upAndSetCookie = function(str){
        var GUANGGAO_NUM;
        GUANGGAO_NUM = parseInt(getCookie(str));
        GUANGGAO_NUM = (!GUANGGAO_NUM || GUANGGAO_NUM >= 10) ? 1 : GUANGGAO_NUM + 1;
        setCookie(str, GUANGGAO_NUM, 24*365);
        return GUANGGAO_NUM;
    };

    //首页轮播图
    var lun_timer = null;
    var s0 = new stf();
    s0.init({
        identifyTab: 'focus_tigger_',
        identifyList: 'focus_title_',
        count: 9,
        cnon: 'on',
        callback: {
            all: function(i) {
                if (lun_timer !== null) clearTimeout(lun_timer);
                lun_timer = setTimeout(function() {
                    $("#focus_bg li").hide();
                    $("#focus_bg_" + i).fadeIn(500);
                }, 50);
            },
            6: function() {
                if (typeof configs_2859 === 'undefined') {
                    return false;
                }
                if (configs_2859.thirdlink) {
                    sendkkpv(configs_2859.thirdlink);
                }

                if (configs_2859.pvLink) {
                    var CHUPIN_GUANGGAO_2859 = upAndSetCookie('CHUPIN_GUANGGAO_2859');
                    if (CHUPIN_GUANGGAO_2859 == 1) {
                        sendkkpv(configs_2859.pvLink);
                    }
                    delete(configs_2859.pvLink);
                }
            },
            7: function() {
                if (typeof configs_2860 === 'undefined') {
                    return false;
                }
                if (configs_2860.thirdlink) {
                    sendkkpv(configs_2860.thirdlink);
                }

                if (configs_2860.pvLink) {
                    var CHUPIN_GUANGGAO_2860 = upAndSetCookie('CHUPIN_GUANGGAO_2860');
                    if (CHUPIN_GUANGGAO_2860 == 1) {
                        sendkkpv(configs_2860.pvLink);
                    }
                    delete(configs_2860.pvLink);
                }
            }
        },
        interval: 5000,
        auto: true
    });
    s0.config.count = getTurnStep();

    var timer = null;
    if (window.addEventListener) {
        window.addEventListener('resize', function() {
            if (timer != null) clearTimeout(timer);
            timer = setTimeout(function() {
                s0.config.count = getTurnStep();
            }, 0);
        });
    } else if (window.attachEvent) {
        window.attachEvent('onresize', function() {
            if (timer != null) clearTimeout(timer);
            timer = setTimeout(function() {
                s0.config.count = getTurnStep();
            }, 0);
        });
    }

    function getTurnStep() {
        var _width = $(this).width();
        _width = (parseInt(_width) - 20);
        if (_width > 1420) {
            return 9;
        } else if (_width > 1180) {
            return 8;
        } else {
            return 6;
        }
    }


    //焦点图广告位
    MiniSite.loadJSData('http://biz5.sandai.net/portal/008/A/cm2859.js', 'gbk', function(){
        if(typeof configs_2859 !== 'undefined'){
            configs_2859 = configs_2859[0];
            var tigger_6 = document.getElementById('focus_tigger_6'),
                title_6 = document.getElementById('focus_title_6');

            tigger_6.innerHTML = '<a href="'+configs_2859.clickLink+'" title="'+configs_2859.intro+'" blockid="9534"><img src="'+configs_2859.simg+'" width="89" height="45" alt="'+configs_2859.intro+'"><span></span></a>';
            title_6.innerHTML = '<a href="'+configs_2859.clickLink+'" title="'+configs_2859.intro+'" class="focuslink" blockid="9534"></a>';
            document.getElementById('focus_bg_6').style.backgroundImage = 'url('+configs_2859.bimg+')';
            document.getElementById('focus_bg_6').style.backgroundColor = configs_2859.color;

            $(tigger_6).bind('click',function(){sendkkpv(configs_2859.packageUrl);});
            $(title_6).bind('click',function(){sendkkpv(configs_2859.packageUrl);});
        }
    });

    MiniSite.loadJSData('http://biz5.sandai.net/portal/008/A/cm2860.js', 'gbk', function(){
        if(typeof configs_2860 !== 'undefined'){
            configs_2860 = configs_2860[0];
            var tigger_7 = document.getElementById('focus_tigger_7'),
                title_7 = document.getElementById('focus_title_7');

            document.getElementById('focus_tigger_7').innerHTML = '<a href="'+configs_2860.clickLink+'" title="'+configs_2860.intro+'" blockid="8095"><img src="'+configs_2860.simg+'" width="89" height="45" alt="'+configs_2860.intro+'"><span></span></a>';
            document.getElementById('focus_title_7').innerHTML = '<a href="'+configs_2860.clickLink+'" title="'+configs_2860.intro+'" class="focuslink" blockid="9534"></a>';
            document.getElementById('focus_bg_7').style.backgroundImage = 'url('+configs_2860.bimg+')';
            document.getElementById('focus_bg_7').style.backgroundColor = configs_2860.color;

            $(tigger_7).bind('click',function(){sendkkpv(configs_2860.packageUrl);});
            $(title_7).bind('click',function(){sendkkpv(configs_2860.packageUrl);});
        }
    });
});
