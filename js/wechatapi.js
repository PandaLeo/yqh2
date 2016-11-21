/**
 * Created by Administrator on 2016/10/13.
 */
function GetQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  decodeURI(r[2]); return null;
}
var appid = '';
var timestamp = '';
var signature = '';
var out_url = '';
var repeat_url = '';
var user_id = '';
var url = window.location.host+window.location.pathname;
var h5_url = window.location.href;
var id = GetQueryString('mz_id');
var rmid = GetQueryString('rmid');
var depth = GetQueryString('depth');
var lineid = GetQueryString('lineid');
var path = GetQueryString('path');
var back_url = window.location.href;
var wx_title = '';
var wx_photo = '';
var wx_remark = '';
$.ajax({
    //服务器地址
    url:"../../mz_view.php?mz_id="+id+'&rmid='+rmid,
    // 本地
    // url:"php/mz_view.php?mz_id="+id+'&rmid='+rmid,
    type:'post',
    data:{h5_url:h5_url},
    cache:false,
    async:false,
    dataType:'json',
    success : function(data){
        // 返回数据
        appid =data.appid;
        timestamp =data.timestamp;
        signature =data.signature;
        repeat_url =data.repeat_url;
        out_url = 'http://'+url+data.out_url;
        user_id = data.user_id;
        wx_title = data.wx_title;
        wx_photo = data.wx_photo;
        wx_remark = data.wx_remark;
    },
    error : function() {
        //服务器地址
        window.location.href='../../get_allow.php?response_type=code&scope=snsapi_userinfo&state=snsapi_userinfo&back_url='+back_url;
        // 本地
    //  window.location.href='php/get_allow.php?response_type=code&scope=snsapi_userinfo&state=snsapi_userinfo&back_url='+back_url;
    }
});
if(rmid==user_id){
    rmid='';
}
if(user_id) {
    //服务器地址
    $.get('../../user_record.php?module=mz&id=' + id + '&action=view&rmid=' + rmid + '&depth=' + depth + '&lineid=' + lineid + '&path=' + path);
    // 本地
    // $.get('php/user_record.php?module=mz&id='+id+'&action=view&rmid='+rmid+'&depth='+depth+'&lineid='+lineid+'&path='+path);
    wx.config({
        debug: false,
        appId: appid,
        timestamp: timestamp,
        nonceStr: 'GZOgQmEI9hKgyCLg',
        signature: signature,
        jsApiList: [
            'checkJsApi',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo'
        ]
    });
    wx.ready(function () {
        var shareData = {
            title: wx_title,
            desc: wx_remark,
            link: out_url,
            imgUrl: wx_photo,
            success: function () {
                $.get(repeat_url);
            }
        };
        wx.onMenuShareAppMessage(shareData);
        wx.onMenuShareTimeline(shareData);
        wx.onMenuShareQQ(shareData);
        wx.onMenuShareWeibo(shareData);
    });
}
//微信去掉下方刷新栏
if(navigator.userAgent.indexOf('MicroMessenger') >= 0){
    document.addEventListener('WeixinJSBridgeReady', function() {
        //WeixinJSBridge.call('hideToolbar');
    });
}



