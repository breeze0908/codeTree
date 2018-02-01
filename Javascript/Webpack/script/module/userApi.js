/*用户相关*/
var userApi={
    /**
     * [getuserInfo description]
     * @return {[type]} [description]
     */
    getuserInfo:function(callback){
        var url=window.config.domain+'api/user/profile';
        $.ajax({
            url:url,
            type:'get',
            dataType:'json',
            beforeSend:function(){

            },
            success:function(res){
                if(res.code === 0){
                    if(callback && typeof callback ==='function'){
                        callback(res.data)
                    }
                }
            },
            error:function(){

            }
        })
    },


}
export {userApi}