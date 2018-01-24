/**
 * cookie处理类
 * @type Object  --注意替换domain
 */
var Cookie = {
    set: function(name, value, opt) {
        opt || (opt = {});
        var t = new Date(),
            exp = opt.exp;
        if (typeof exp === 'number') {
            t.setTime(t.getTime() + exp * 3600000); //60m * 60s * 1000ms
        } else if (exp === 'forever') {
            t.setFullYear(t.getFullYear() + 50); //专业种植cookie 50年
        } else if (value === null) { //删除cookie
            value = '';
            t.setTime(t.getTime() - 3600000);
        } else if (exp instanceof Date) { //传的是一个时间对象
            t = exp;
        } else {
            t = '';
        }
        document.cookie = name + '=' + encodeURIComponent(value) + (t && '; expires=' + t.toUTCString()) +
        '; domain=' + (opt.domain || '.cnblogs.com') + '; path=' + (opt.path || '/') + (opt.secure ? '; secure' : '');
    },
    get: function(name) {
        name += '=';
        var cookies = (document.cookie || '').split(';'),
            cookie,
            nameLength = name.length,
            i = cookies.length;
        while (i--) {
            cookie = cookies[i].replace(/^\s+/, '');
            if (cookie.slice(0, nameLength) === name) {
                return decodeURIComponent(cookie.slice(nameLength)).replace(/\s+$/, '');
            }
        }
        return '';
    }
};
//右侧切花
function SideBarSlider() {
    var main = document.getElementById('mainContent');
    var rside = document.getElementById('sideBar');
    var show = parseInt(Cookie.get("s_show_status"));
    var c_width = document.body.clientWidth;
    var status = show ? 0 : 1;
    console.log(status);

    if (status) {
        //开启右侧
        main.style.width = 75 + '%';
        rside.style.width = (c_width < 996 ? 100 : 24) + '%';
    } else {
        //关闭右侧
        main.style.width = 99 + '%';
        rside.style.width = 0;
    }

    Cookie.set("s_show_status", status);
    return false;
}

//初始化
(function() {
var show = parseInt(Cookie.get("s_show_status"));
if (!show) {
    Cookie.set("s_show_status", 1);
    SideBarSlider();
}

var others = ['cnblogs_c1', 'under_post_news', 'cnblogs_c2', 'HistoryToday'];
for (var i = 0; i < others.length; i++) {
    (d = document.getElementById(others[i])) && (d.style.display = 'none');
}
})();


// 打赏
(function() {
$(".btn-MSupport").hover(function() {
    $(".MSupport").css("width", "240px");
})
$(".MSupport").mouseleave(function() {
    $(".MSupport").css("width", "0");
})
$(".MSupport-main").mouseleave(function() {
    $(".MSupport").css("width", "0");
})

$(".not-full li:first").hover(function() {
    $(this).addClass("myR-on").siblings().removeClass("myR-on");
    $(".MSupport-account").html("支付宝打赏");
    $(".MSupport-code img").attr("src", "http://images.cnblogs.com/cnblogs_com/one-villager/1088144/o_alipay.jpg");
})

$(".not-full li:last").hover(function() {
    $(this).addClass("myR-on").siblings().removeClass("myR-on");
    $(".MSupport-account").html("微信打赏");
    $(".MSupport-code img").attr("src", "http://images.cnblogs.com/cnblogs_com/one-villager/1088144/o_weixin.png");
})
})();