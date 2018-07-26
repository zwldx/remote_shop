// JavaScript Document

var jq = jQuery.noConflict();
jQuery(function () {
    jq(".leftNav ul li").hover(
        function () {
            jq(this).find(".fj").addClass("nuw");
            jq(this).find(".zj").show();
        }
        ,
        function () {
            jq(this).find(".fj").removeClass("nuw");
            jq(this).find(".zj").hide();
        }
    )
})


function confirmgoods(obj) {
    //alert(111);
    if (confirm('是否确认已收货') == true) {
        var orderid = jq(obj).parent().parent().parent().find('td span[name=orderid]').text();
        jq.get('/index/order/confirmgoods', { orderid: orderid }, function (data) {
            //alert(data);
            if (data == 1) {
                window.location.reload();
            }
        });
    }
}
function delorder(obj) {
    if (confirm('是否确定取消订单') == true) {
        var orderid = jq(obj).parent().parent().parent().find('td span[name=orderid]').text();
        jq.get('/index/order/delorder', { orderid: orderid }, function (data) {
            //alert(data);
            if (data == 1) {
                window.location.reload();
            }
        });
    }
}

function mailBind(){
    var mail = jq('[name=mail]').val();
    jq.get('/index/user/mailBind',{'mail':mail},function(data){
        // alert(data);
        data = eval('('+data+')');
        // alert(data.error);
        if(data.error == 0){
            jq('#is_send').text('发送成功！请点击邮箱中的链接激活');
        }else{
            jq('#is_send').text('激活邮箱发送失败，请稍后刷新重试！');
        }
    },'json');
}

function trans(){
    var source = jq('#trans_source').val();
//    alert(source);
    jq.get('/index/Tools/translate',{'source':source},function(data){
        var r = data.trans_result[0].dst;
//        alert(r);
        if(r!=null){
            jq('#trans_result').val(r);
        }
    },'json');
}
