// JavaScript Document


function addUpdate(jia){		
	var c = jia.parent().find(".n_ipt").val();
	c=parseInt(c)+1;	
	jia.parent().find(".n_ipt").val(c);
}

function jianUpdate(jian){    
	var c = jian.parent().find(".n_ipt").val();
	if(c==1){    
		c=1;    
	}else{    
		c=parseInt(c)-1;    
		jian.parent().find(".n_ipt").val(c);
	}
}    




 

function addUpdate1(jia,cart_id){	
	var t = jia.parent().find(".car_ipt");		
	var c = jia.parent().find(".car_ipt").val();
	c=parseInt(c)+1;	
	jia.parent().find(".car_ipt").val(c);
	add_sub_change(cart_id,t);
}

function jianUpdate1(jian,cart_id){    
	var t = jian.parent().find(".car_ipt");
	var c = jian.parent().find(".car_ipt").val();
	if(c==1){    
		c=1; 
	}else{    
		c=parseInt(c)-1;    
		jian.parent().find(".car_ipt").val(c);
		add_sub_change(cart_id,t);
	}
	
}   



function add_sub_change(cart_id,jq_goods_num){
	var goods_num = $(jq_goods_num).val();
	var jq_this = jq_goods_num;

	//使用闭包，为了在回调函数中使用当前对象(this)
	(function(t){
		// alert(t);
		jq.get('/index/shopcart/modifyNumber',{cartid:cart_id,num:goods_num},function(data){
			if(data==false){
				alert('商品更新失败');
			}else{
				//修改商品金额小计的值
				//金额向上取整，保留两位小数
				t.parent().parent().next().text('￥'+Math.ceil(data.num*data.price*100)/100);
				jq('[name="total"] b').text('￥'+Math.ceil(data.total*100)/100);
			}
		});
	})(jq_this);
	
}

