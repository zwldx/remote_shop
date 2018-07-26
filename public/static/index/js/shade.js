document.write("<script language=javascript src='/static/index/js/jquery-1.8.2.min.js'></script>");
// JavaScript Document

//此函数用于删除购物车的商品
function ShowDiv(show_div,bg_div,cart_id,jq_this){
	document.getElementById(show_div).style.display='block';
	document.getElementById(bg_div).style.display='block';
	var bgdiv = document.getElementById(bg_div);
	bgdiv.style.width = document.body.scrollWidth;
	// bgdiv.style.height = $(document).height();
	$("#"+bg_div).height($(document).height());

	$(".b_sure").attr('cart_id',cart_id);
	jq(".b_sure").on('click',function(){
		//移除商品
		removeGoods(cart_id,jq_this);	
		//关闭删除确认界面
		CloseDiv(show_div,bg_div);
		
	});
};

function CloseDiv(show_div,bg_div)
{
	document.getElementById(show_div).style.display='none';
	document.getElementById(bg_div).style.display='none';
};


//此函数用于添加至购物车
function ShowDiv_1(show_div,bg_div,goods_id){
	
	$.get('/index/shopcart/addtocart',{goodsid:goods_id},function(data){	
		if(data == true)
		{
			document.getElementById(show_div).style.display='block';
			document.getElementById(bg_div).style.display='block' ;
			var bgdiv = document.getElementById(bg_div);
			bgdiv.style.width = document.body.scrollWidth;
			// bgdiv.style.height = $(document).height();
			$("#"+bg_div).height($(document).height());		
		}else{
			alert('添加失败');
		}
	});
	jq(".b_sure").on('click',function(){
		CloseDiv_1(show_div,bg_div);
		$(this).attr('href','/index/shopcart/index');		
	});
	jq(".b_buy").on('click',function(){
		CloseDiv_1(show_div,bg_div);
	});
};


function CloseDiv_1(show_div,bg_div)
{
	document.getElementById(show_div).style.display='none';
	document.getElementById(bg_div).style.display='none';
};

//ajax从购物车中移除商品
function removeGoods(cart_id,jqthis){

	$.get('/index/shopcart/removeById',{cartid:cart_id},function(data){
		if(data.total==null)
		{
			//移除最后一条商品记录行
			jqthis.parent().parent().remove();
			//购物车没有商品时，商品总价设为0，确认订单按钮不可用状态
			jq('[name="total"] b').text('￥0.00');
			//按钮不可用样式
			jq('[name="sure"] img').addClass('gray');
			//禁用确认订单链接
			jq('[name="sure"]').attr('href','javascript:void(0)');
			$(".car_tab tr").eq(1).after('<tr><td colspan="6" align="center" style="font-family:\'Microsoft YaHei\';">购物车为空！</td> </tr>'); 
			// alert('删除失败');
		}else if(data==0){
			alert(删除失败);
		}else
		{
			//移除当前商品记录行
			jqthis.parent().parent().remove();
			//金额向上取整，保留两位小数
			jq('[name="total"] b').text('￥'+Math.ceil(data.total*100)/100);
		}
	});

}

