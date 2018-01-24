

var jQuery  = function () {
    return jQuery.prototype.init();
}

jQuery.prototype = {
    constructor : jQuery,

    init : function() {
        this.version = 1.0;
        return this;
    },

    each:function() {
        console.log('this is a funtion:each');
        return this;
    }
}
jQuery.prototype.init.prototype = jQuery.prototype;




jQuery().each();



/**
 * js类型
 */
// 这个对象是用来将 toString 函数返回的字符串转成
var class2type = {
    "[object Boolean]": "boolean",
    "[object Number]": "number",
    "[object String]": "string",
    "[object Function]": "function",
    "[object Array]": "array",
    "[object Date]": "date",
    "[object RegExp]": "regexp",
    "[object Object]": "object",
    "[object Error]": "error",
    "[object Symbol]": "symbol"
}
var toString = Object.prototype.toString;

jQuery.type = function (obj) {
    if (obj == null) {
        return obj + "";
    }
    return (typeof obj === "object" || typeof obj === "function") ? (class2type[toString.call(obj)] || "object") : (typeof obj);
}



/**
 * 是否为纯粹的对象
 */

var getProto = Object.getPrototypeOf;//获取父对象
var hasOwn = class2type.hasOwnProperty;
var fnToString = hasOwn.toString;
var ObjectFunctionString = fnToString.call( Object );

jQuery.isPlainObject = function (obj) {
    var proto, Ctor;

    // 排除 underfined、null 和非 object 情况
    if (!obj || toString.call(obj) !== "[object Object]") {
        return false;
    }

    proto = getProto(obj);

    // Objects with no prototype (e.g., `Object.create( null )`) are plain
    if (!proto) {
        return true;
    }

    // Objects with prototype are plain iff they were constructed by a global Object function
    Ctor = hasOwn.call(proto, "constructor") && proto.constructor;
    return typeof Ctor === "function" && fnToString.call(Ctor) === ObjectFunctionString;
}



