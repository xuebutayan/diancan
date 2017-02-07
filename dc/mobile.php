<?
require "config.php";
$x=isset($_GET["x"])?$_GET["x"]:"";
$s=isset($_GET["s"])?$_GET["s"]:"";if($s=="")$s="0";
switch($x){
	case "kcz"://餐桌
	$a=Req("a");
	$q=mysql_query("Select ID,Nm,Ni,Oi From SCT Where DD=0 Order By ID Asc");
	while($r=mysql_fetch_array($q)){
		if($a==substr(md5($OpID.$r[0]),0,8)){echo "OK".$r[0]."|".$r[1]."|".$r[2]."|".$r[3];}
	}
	break;

	case "kcd"://菜单
	$q=mysql_query("Select Id,Nm,Nh From SCL Order By No Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2];
	}
	echo "@@@";
	$q=mysql_query("Select ID,Nm,Nt,Ni,Np,Ns,Nh From SCD Where DD=0 Order By No Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5]."|".$r[6];
	}
	break;

	case "kmd"://已点
	$a=Req("a");
	$q=mysql_query("Select Bz,Ps From ODR Where ID=".$a);
	if(mysql_num_rows($q)>0){$r=mysql_fetch_array($q);echo $r[0]."|".$r[1];}
	$q=mysql_query("Select B.ID,B.Nm,B.Np,A.Cp,A.Cs,A.St,A.ID,A.Td As N From OCK As A,SCD As B Where B.ID=A.Ci And A.St<14 And A.Oi=".$a." Order By A.Td Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5]."|".$r[6]."|".$r[7];
	}
	break;

	case "kxd"://下单
	$d=explode("|",Req("d"));
	$a=explode(",",$d[2]);
	$b=explode(",",$d[3]);
	$c=mysql_result(mysql_query("Select Oi From SCT Where ID=".$d[1]),0);//桌号表
	if($c!=0&&$c!=(int)$d[0])die();
	$q=mysql_query("Select Ti From ODR Where ID=".$d[0]);
	if(mysql_num_rows($q)>0){//Ti是桌号
		mysql_query("Update ODR Set Ti=".$d[1].",Yi=0,Pz=".$d[4].",St=3,Bz='".$d[5]."' Where ID=".$d[0]);
		$c=$d[0];
	}else{
		mysql_query("Insert Into ODR (Ti,Yi,Pz,Ps,Pt,St,Dt,I4,Ds,Bz) Values (".$d[1].",0,".$d[4].",0,0,3,Now(),0,Now(),'".$d[5]."')");
		$c=mysql_insert_id();
	}
	for($i=0;$i<count($a);$i++){
		$r=mysql_fetch_array(mysql_query("Select Nm,Ns From SCD Where ID=".$a[$i]));
		$e=$r[0];$f=$r[1];
		mysql_query("Insert Into OCK (Oi,Ti,Ci,Cn,Cs,Cp,St,I1,I2,I3,Dt,Td,Bz) Values (".$c.",".$d[1].",".$a[$i].",'".$e."',".$b[$i].",".$f.",3,0,0,0,Now(),0,'')");
	}
	mysql_query("Update SCT Set Ni=3,Oi=".$c." Where ID=".$d[1]);
	echo "OK".$c;
	break;

	case "ktd"://退单
	mysql_query("Update OCK Set Td=1 Where St<12 And ID=".Req("c"));
	mysql_query("Update ODR Set St=4 Where St<12 And ID=".Req("a"));
	mysql_query("Update SCT Set Ni=4 Where Ni<12 And Ni>0 And ID=".Req("b"));
	echo "OK";
	break;

	case "kjz"://结账
	mysql_query("Update ODR Set Pt=".Req("c")." Where St<12 And ID=".Req("a"));
	mysql_query("Update SCT Set Ni=11 Where Ni>0 And Ni<12 And ID=".Req("b"));
	echo "OK";
	break;

	case "key"://支付
	$a=Req("a");
	$b=Req("b");
	$c=Req("c");
	$d=str_replace(":","",date("H:i:s"));
	$e=$a."|".$b."|".$c."|".$OpID."|".$d;
	echo "OK".$e."|".md5($e.$OpPK);
	//echo "<script>window.location='http://www.1000vw.com/Qw/pay.php?t=".$e."|".md5($e.$OpPK)."';</sc ript>";
	break;

	case "kjt"://支付
	$a=explode("|",Req("a"));
	if($a[6]!=md5($a[2].$OpPK.$a[4]))die();
	mysql_query("Update ODR Set Ps=Ps+".$a[2].",Pt=".(5-$a[3]*1).",Bz='".$a[5]."',Ds=Now(),I4=0,St=12 Where ID=".$a[1]." And Ti=".$a[0]);
	mysql_query("Update SCT Set Ni=12 Where ID=".$a[0]);
	echo "OK";
	break;

	case "khj"://呼叫
	mysql_query("Update SCT Set Ni=2 Where ID=".Req("b"));
	echo "OK";
	break;

	default:
	$p=mysql_result(mysql_query("Select Nm From CFG Where ID=1"),0);
	$t=Req("t");
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=320, initial-scale=1, user-scalable=no">
<script type="text/javascript">document.write('<meta name="viewport" content="width=320, initial-scale='+parseInt(document.documentElement.clientWidth)/320+', user-scalable=no">');</script>
<title><? echo $p; ?> - 顾客点单</title>
<style type="text/css">
div{font-size:16px;}
input[type=text]{font-size:16px;border:1px solid #F90;padding:3px;text-align:center;}
.dml1{margin:2px 0px 2px 2px;line-height:25px;border:1px solid #F90;text-align:center;font-size:14px;border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;}
.dml2{margin:2px 0px 2px 2px;line-height:25px;border:1px solid #F90;text-align:center;font-size:14px;background-color:#F90;color:#FFF;border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;}
.ddl1{margin:4px;background-color:#FFE;border:1px dashed #FDA;height:80px;line-height:40px;font-size:18px;}
.ddl2{float:left;width:72px;height:72px;margin:4px;overflow:hidden;background-image:url(i/n.jpg);}
.ddl3{float:right;margin:3px 5px 0px 0px;font-size:24px;width:32px;height:32px;line-height:32px;text-align:center;background-color:#F90;color:#FFF;border-radius:5px;}
.ddl4{font-size:28px;color:#F00;}
.ddl5{float:right;width:112px;}
.ddl5 span{font-size:18px;width:26px;height:26px;line-height:26px;text-align:center;background-color:#F90;color:#FFF;border-radius:5px;display:inline-block;}
.ddl6{font-size:12px;color:#999;}
.ddl7{float:right;font-size:12px;color:#999;}
.dzf1{float:left;width:114px;margin:15px 10px 0px 0px;background-color:#F90;color:#FFF;font-size:18px;text-align:center;line-height:32px;}
.dzf2{float:left;width:114px;margin-top:15px;background-color:#F90;color:#FFF;font-size:18px;text-align:center;line-height:32px;}
</style>
<script type="text/javascript">
function $(o){return document.getElementById(o);}
function $$(o){return document.getElementsByName(o);}
function xml(){var x;try{x=new ActiveXObject('Msxml2.XMLHTTP');}catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(f){x=false;}}if(!x&&typeof XMLHttpRequest!='undefined'){x=new XMLHttpRequest();}xvr=true;return x;}
function num(n){var o=event.srcElement;if(o.tagName=='INPUT'&&o.type=='text'){var v=n?parseFloat(o.value):parseInt(o.value);if(!v)v=0;o.value=v;}}
function rsz(o,s){var w=o.width;var h=o.height;if(w<h){h=h*s/w;w=s;}else{w=s*w/h;h=s;}o.width=w;o.height=h;o.style.margin=(s-h)/2+'px '+(s-w)/2+'px';}
function psz(o,w,h){w=parseInt(w);h=parseInt(h)-4;var s=w>h?w:h;rsz(o,s);}
var uri='mobile.php?';
var ori;//订单ID
var tid;//餐桌ID
var cid;//大图ID
var cno;//待下单数
var cnt;//总点单数
var lcb=[];//菜类
var lcd=[];//菜品
var mdc=[];//点单
var tst=['空桌待客','顾客入座','顾客呼叫','顾客下单','顾客退单','厨房退单','提交厨房','厨房接收','制作完成','正在上菜','顾客用餐','申请结账','结账完成','打扫清台','退单完成'];
function atwh(){
	var pw=parseInt(document.documentElement.clientWidth);
	var ph=parseInt(document.documentElement.clientHeight);
	if(pw>320)pw=320;if(ph>560)ph=560;dd.style.width=pw+'px';
	dd.style.height=ph+'px';dx.style.width=pw+'px';dx.style.height=ph+'px';dml.style.height=ph-50+'px';dcl.style.height=ph-52+'px';bigp.style.height=ph-20+'px';dkl.style.height=ph-128+'px';
}
//选桌
function kcz(n,m){
	var x=xml();
	x.open('Get',uri+'x=kcz&a='+n+'&'+Math.random(),true);
	x.onreadystatechange=function(){
		if(x.readyState==4&&x.status==200){
			var t=x.responseText;
			if(t.substr(0,2)=='OK'){
				t=t.substr(2).split('|');
				tid=t[0];ori=t[3];dz1.innerText=t[1];dz2.innerText=t[1];
				if(t[2]=='0'){
					document.write('Table Close!');
				}else{
					if(m)kcd();setTimeout('kcz(\''+n+'\')',10000);
				}
			}else document.write('Err Tid!');
		}
	};x.send();
}
//菜单
function kcd(){var x=xml();x.open('Get',uri+'x=kcd&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=(x.responseText+'@@@').split('@@@');var a=t[0].split('$');for(var i=1;i<a.length;i++){a[i]=a[i].split('|');lcb.push(a[i]);}var b=t[1].split('$');for(var i=1;i<b.length;i++){b[i]=b[i].split('|');lcd.push(b[i]);}var s='';for(var i=0;i<lcb.length;i++)s+='<div id="dmi'+i+'" class="dml2" style="display:'+(lcb[i][2]=='0'?'block':'none')+';" onClick="kcl('+i+');">'+lcb[i][1]+'</div>';dml.innerHTML=s;kcl(0);}};x.send();}
//菜类
function kcl(n){for(var i=0;i<lcb.length;i++)$('dmi'+i).className='dml2';$('dmi'+n).className='dml1';var s='';for(var i=0;i<lcd.length;i++){if(lcd[i][6]=='0'&&lcd[i][2].indexOf(','+lcb[n][0]+',')>-1)s+='<div class="ddl1"><div class="ddl2" onclick="kcp('+i+');"><img src="i/'+(lcd[i][4]=='1'?'c'+lcd[i][0]:'n')+'.jpg" onload="rsz(this,72);"></div>'+lcd[i][1]+'<br /><span class="ddl4">'+lcd[i][5]/100+'</span>元<div class="ddl3" onclick="kdd('+i+');">点</div></div>';}dcl.innerHTML=s;}
//大图
function kcp(n){if(lcd[n][4]=='0')return;bigp.style.display='block';bigc.style.display='block';bigd.style.display='block';bigi.style.display='block';bigi.innerHTML='<span style="font-size:20px;font-weight:bold;">'+lcd[n][1]+'</span>'+(lcd[n][3]==''?'':('<br>　　'+lcd[n][3]));bigp.innerHTML='<img src="i/c'+lcd[n][0]+'.jpg" onload="psz(this,300,bigp.style.height);">';cid=n;}
//点单
function kdd(n){var c=0;for(var i=0;i<mdc.length;i++)if(mdc[i][0]==lcd[n][0])c=1;if(!c)mdc.push(lcd[n]);dcc.innerText=mdc.length;tip.innerText=lcd[n][1]+' 已加入点单！';tip.style.display='block';setTimeout('tip.style.display=\'none\'',2000);}
//已点
function kmd(){
	if(mdc.length==0&&ori=='0')return;
	var s='';
	for(var i=0;i<mdc.length;i++)
		s+='<div class="ddl1" id="md'+i+'"><div class="ddl2"><img src="i/'+(mdc[i][4]=='1'?'c'+mdc[i][0]:'n')+'.jpg" onload="rsz(this,72);"></div>'+mdc[i][1]+'<span id="mt'+i+'"></span><br /><span id="mp'+i+'" class="ddl4">'+mdc[i][5]/100+'</span>元<div class="ddl5"><span onclick="kms('+i+',-1,'+mdc[i][0]+');">-</span> <input type="text" id="mc'+i+'" style="width:30px;" value="1" onblur="num();kms('+i+',0,'+mdc[i][0]+');" /> <span onclick="kms('+i+',1,'+mdc[i][0]+');">+</span><input type="hidden" id="mi'+i+'" value="'+mdc[i][0]+'"></div></div>';
	dkl.innerHTML=s;dd.style.display='none';dx.style.display='block';
	dxd.innerText=(mdc.length==0)?'结账付款':'立即下单';
	cno=mdc.length;cnt=cno;kmj();dkf.innerText='0';if(ori=='0')return;
	var x=xml();x.open('Get',uri+'x=kmd&a='+ori+'&'+Math.random(),true);
	x.onreadystatechange=function(){
		if(x.readyState==4&&x.status==200){
			var d=x.responseText.split('$');s='';
			for(var j=1;j<d.length;j++){
				d[j]=d[j].split('|');
				s+='<div class="ddl1" id="md'+(i+j-1)+'"><div class="ddl2"><img src="i/'+(d[j][2]=='1'?'c'+d[j][0]:'n')+'.jpg" onload="rsz(this,72);"></div>'+d[j][1]+' <span class="ddl6">['+tst[d[j][5]]+']</span><br /><span id="mp'+(i+j-1)+'" class="ddl4">'+d[j][3]/100+'</span>元 × '+d[j][4]+'<input type="hidden" id="mc'+(i+j-1)+'" value="'+d[j][4]+'" />'+['<div id="mt'+(i+j-1)+'" class="ddl3" onclick="ktd('+d[j][6]+');">退</div>','<div class="ddl7" id="mt'+(i+j-1)+'">[已申请退单]</div>','<div class="ddl7" id="mt'+(i+j-1)+'">[已同意退单]</div>'][d[j][7]]+'<input type="hidden" id="mi'+(i+j-1)+'" value="'+d[j][0]+'"></div></div>';
			}
			dkl.innerHTML+=s;d[0]=d[0].split('|');if(d[0][0]!='')dbz.value=d[0][0];dbx.value=d[0][0];dkf.innerText=d[0][1]/100;cnt=i+j-1;kmj();
		}
	};x.send();}
//数量
function kms(o,n,r){
	var m=parseInt($('mc'+o).value);
	if(!m)m=1;m+=n;$('mc'+o).value=m;if(m==0){if(confirm('您确认要删除此菜品吗？？')){$('md'+o).style.display='none';for(var i=0;i<mdc.length;i++){if(mdc[i][0]==String(r))mdc.splice(i,1);}}else{$('mc'+o).value=1;}}kmj();}
//总价
function kmj(){var s=0;for(var i=0;i<cnt;i++){if($('mt'+i).innerText.indexOf('已同意退单')==-1)s+=$('mp'+i).innerText*$('mc'+i).value;}dkc.innerText=s;}
//下单
function kxd(){
	var a=[];var b=[];//b保存点餐的数量
	for(var i=0;i<cno;i++){
		var c=$('mc'+i).value;
		if(c!='0'){
			a.push($('mi'+i).value);b.push(c);}
		}
		if(mdc.length==0){
			if(ori!='0')dj.style.display='block';return;
		}
		if(!confirm('请核对菜品及数量无误后下单！\n确认要下单吗？'))return;
		var x=xml();x.open('Get',uri+'x=kxd&d='+escape(ori+'|'+tid+'|'+a.join(',')+'|'+b.join(',')+'|'+dkc.innerText*100)+'|'+dbx.value+(dbz.value=='如：清淡，微辣，不要香菜，牛排7成熟等'?'':dbz.value).replace(dbx.value,'')+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t.substr(0,2)=='OK'){ori=t.substr(2);mdc=[];dcc.innerText='0';alert('下单已成功！');kmd();}else{alert('下单失败，请稍后重试或呼叫服务员！\n如是同桌已先下单，请刷新本页面！');}}};x.send();}
//退单
function ktd(n){if(!confirm('确认要退单吗？'))return;var x=xml();x.open('Get',uri+'x=ktd&a='+ori+'&b='+tid+'&c='+n+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t.substr(0,2)=='OK'){alert('退单已提交，请等待服务员处理！');kmd();}else{alert('退单请求失败，请稍后重试或呼叫服务员！\n已结账完成的菜品不能退单！！');}}};x.send();}
//结账
function kjz(n,m){var x=xml();x.open('Get',uri+'x=kjz'+'&a='+ori+'&b='+tid+'&c='+n+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(!m)alert('您的结账申请已发出，请稍候......');dj.style.display='none';}};x.send();}
//支付
function kjf(n){if(!confirm('您的结账申请已发出！\n您选择的是微信或支付宝付款，您也可以自助完成结账！\n点击确认进入自助结账\n点击取消等待服务员来结账'))return;var m=navigator.userAgent.toLowerCase();if(n==4&&m.indexOf('micromessenger')<0){alert('您选择微信支付，请用微信扫一扫扫描桌上二维码进入本页面再支付！');return;}if(n==5&&(m.indexOf('aliapp')<0&&m.indexOf('alipay')<0)){alert('您选择支付宝付款，请用支付宝扫一扫扫描桌上二维码进入本页面再支付！');return;}alert('您正在使用手机自助支付，支付过程中点餐界面可能会被关闭，请在支付完成后务必重新进入此点餐界面，点击结账，点击下面的查询微信支付宝支付状态按钮！');df.style.display='block';dfe.innerHTML='正在生成支付二维码......';var x=xml();x.open('Get',uri+'x=key&a='+ori+'&b='+(dkc.innerText*1-dkf.innerText*1)+'&c='+n+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){if(x.responseText.substr(0,2)=='OK'){kjp(x.responseText.substr(2));}else{alert('提交失败，请稍后重试！');}}};x.send();}
function kjp(t){var x=xml();x.open('Get','http://www.1000vw.com/Qw/key.php?t='+t+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){if(x.responseText.substr(0,2)=='OK'){var k=x.responseText.substr(2);t=t.split('|');dfq.innerText=t[1];if(t[2]=='4'){dfe.innerHTML='<div style="width:231px;height:231px;margin:10px auto;display:block;overflow:hidden;"><div style="position:absolute;margin:91px 0px 0px 91px;width:48px;height:48px;border:1px solid #CCC;border-radius:5px;background-color:#FFF;background-image:url(https://pay.weixin.qq.com/wxzf_guide/img/logo.png);background-repeat:no-repeat;background-position:4px 6px;"></div><div><img src="http://www.liantu.com/api.php?el=Q&m=5&w=231&text='+escape(k)+'" data-tap-disabled="true"></div></div><span style="font-size:12px;">请长按上面二维码图片在弹出菜单中点击识别二维码即可完成支付！</span>';}if(t[2]=='5'){dfe.innerHTML='<div style="width:231px;height:231px;margin:0px auto;overflow:hidden;"><iframe src="https://mapi.alipay.com/gateway.do?_input_charset=utf-8&notify_url=http://www.taoewm.com/Qw/nzfb.php&out_trade_no='+t[3]+'-'+t[0]+'-c-'+t[4]+'&partner=2088911048354746&payment_type=1&qr_pay_mode=4&qrcode_width=231&seller_email=taoewm@163.com&service=create_direct_pay_by_user&subject=<? echo $p; ?>-餐费&total_fee='+t[1]+'&sign='+k+'&sign_type=MD5" scrolling="no" frameborder="0" width="231" height="231"></iframe></div>';}}else{alert('提交失败，请稍后重试！');}}};x.send();}
//查询
function kjc(){var x=xml();x.open('Get','http://www.1000vw.com/Qw/sync.php?x=<? echo $OpID; ?>-'+ori+'-c-&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.split('OK');var s=0;for(var i=1;i<t.length;i++){var h=t[i].split('|');if(dbx.value.indexOf(h[2])<0){alert('手机扫码支付已成功！\n\n单　号：'+h[0]+'\n金　额：'+h[1]/100+'元\n时　间：'+h[3]+'\n交易号：'+h[2]);var d='['+['A:','W:'][h[4]]+(h[1]/100)+':'+h[2]+']';dbx.value=dbx.value.replace(d,'')+d;s=1;kjt(h.join('|')+'|'+dbx.value);}}if(s==0)alert('无此单新的支付记录！');}};x.send();}
//同步
function kjt(t){t=t.split('|');var x=xml();x.open('Get',uri+'x=kjt&a='+tid+'|'+ori+'|'+t[1]+'|'+t[4]+'|'+t[2]+'|'+t[6]+'|'+t[5]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){if(x.responseText!='OK')kjt(t);}};x.send();}
//呼叫
function khj(){var x=xml();x.open('Get',uri+'x=khj'+'&b='+tid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){if(x.responseText=='OK'){alert('您的服务请求已发出，请稍候......');}else alert('呼叫失败，请人工呼叫！！');}};x.send();}
</script>
</head>
<body style="margin:0px;padding:0px;" onLoad="atwh();kcz('<? echo $t; ?>',1);">
<!--点菜-->
<div id="dd" style="display:block;">
  <div style="width:320px;height:50px;background-color:#666;">
    <div style="float:left;margin:3px;width:44px;height:44px;"><img src="i/i.jpg" width="44" height="44"></div>
    <div style="float:left;color:#FFF;font-size:18px;line-height:20px;margin-top:6px;"><? echo $p; ?><br><span id="dz1" style="font-size:14px;"></span>　<span style="width:40px;font-size:14px;background-color:#F90;color:#FFF;text-align:center;display:inline-block;" onClick="khj();">呼叫</span></div>
    <div style="float:right;width:100px;line-height:30px;text-align:center;margin:10px;background-color:#F90;color:#FFF;" onClick="kmd();">我的点单(<span id="dcc">0</span>)</div>
  </div>
  <div id="dml" style="float:left;width:60px;overflow:auto;"></div>
  <div id="dcl" style="float:left;width:258px;overflow:auto;border:1px solid #F90;">正在加载，请稍候......</div>
</div>
  <div id="bigc" style="position:absolute;top:15px;left:15px;width:32px;height:32px;text-align:center;line-height:32px;border-radius:16px;background-color:#F90;color:#FFF;font-size:28px;z-index:9;display:none;" onClick="bigp.style.display='none';bigc.style.display='none';bigd.style.display='none';bigi.style.display='none';">×</div>
  <div id="bigd" style="position:absolute;top:15px;left:275px;width:32px;height:32px;text-align:center;line-height:32px;border-radius:16px;background-color:#F90;color:#FFF;font-size:28px;z-index:8;display:none;" onClick="kdd(cid);bigp.style.display='none';bigc.style.display='none';bigd.style.display='none';bigi.style.display='none';">＋</div>
  <div id="bigi" style="position:absolute;width:280px;left:20px;bottom:20px;color:#FFF;z-index:7;display:none;font-size:14px;text-shadow:2px 2px 2px #666;"></div>
  <div id="bigp" style="position:absolute;width:300px;left:10px;top:10px;background-color:#FFF;overflow:scroll;z-index:6;display:none;"></div>
  <div id="tip" style="position:absolute;width:180px;bottom:60px;left:70px;line-height:30px;background-color:#F90;color:#FFF;text-align:center;border-radius:15px;box-shadow:3px 3px 3px #999;z-index:5;display:none;"></div>
<!--下单-->
<div id="dx" style="display:none;">
  <div style="width:320px;height:50px;background-color:#666;">
    <div style="float:left;margin:3px;width:44px;height:44px;"><img src="i/i.jpg" width="44" height="44"></div>
    <div style="float:left;color:#FFF;font-size:18px;line-height:20px;margin-top:6px;"><? echo $p; ?><br><span id="dz2" style="font-size:14px;"></span>　<span style="width:40px;font-size:14px;background-color:#F90;color:#FFF;text-align:center;display:inline-block;" onClick="khj();">呼叫</span></div>
    <div style="float:right;width:100px;line-height:30px;text-align:center;margin:10px;background-color:#F90;color:#FFF;" onClick="dx.style.display='none';dd.style.display='block';bigp.style.display='none';bigc.style.display='none';bigd.style.display='none';bigi.style.display='none';dcc.innerText=mdc.length;">继续点单</div>
  </div>
  <div id="dkl" style="width:320px;overflow:auto;"></div>
  <div style="height:28px;font-size:12px;line-height:28px;padding:0px 10px;color:#999;background-color:#EEE;">备注： <input type="text" style="font-size:12px;padding:1px;width:240px;color:#999;text-align:left;" id="dbz" value="如：清淡，微辣，不要香菜，牛排7成熟等" onFocus="if(this.value='如：清淡，微辣，不要香菜，牛排7成熟等')this.value='';"><input type="hidden" id="dbx"></div>
  <div style="position:absolute;width:320px;height:50px;background-color:#666;">
    <div style="float:left;line-height:30px;margin-top:10px;color:#FFF;"> 总计：<span id="dkc" style="font-size:30px;color:#F00;"></span>元<span style="font-size:12px;">/已付<span id="dkf"></span>元</span></div>
    <div id="dxd" style="float:right;width:100px;line-height:30px;text-align:center;font-size:16px;margin:10px;background-color:#F90;color:#FFF;" onClick="kxd();">立即下单</div>
  </div>
</div>
<!--结账-->
<div id="dj" style="position:absolute;width:278px;height:248px;bottom:20px;left:20px;border:1px solid #F90;background-color:#FFF;display:none;">
  <div style="line-height:40px;background-color:#F90;color:#FFF;font-size:20px;text-align:center;"><div style="width:24px;line-height:24px;border:1px solid #FFF;float:right;margin:6px 10px 0px 0px;font-size:20px;" onClick="dj.style.display='none';">×</div>请选择付款方式</div>
  <div style="padding:5px 20px 20px 20px;">
    <div class="dzf1" onClick="kjz(0);">现金付款</div>
    <div class="dzf2" onClick="kjz(1);">团购券付款</div>
    <div class="dzf1" onClick="kjz(2);">刷卡付款</div>
    <div class="dzf2" onClick="kjz(3);">会员卡付款</div>
    <div class="dzf1" onClick="kjz(4,1);kjf(4);">微信付款</div>
    <div class="dzf2" onClick="kjz(5,1);kjf(5);">支付宝付款</div>
    <div class="dzf2" style="width:240px;font-size:16px;" onClick="kjc();">查询微信、支付宝支付状态</div>
  </div>
</div>
<div id="df" style="position:absolute;width:278px;height:380px;bottom:20px;left:20px;border:1px solid #F90;background-color:#FFF;display:none;">
  <div style="line-height:40px;background-color:#F90;color:#FFF;font-size:20px;text-align:center;"><div style="width:24px;line-height:24px;border:1px solid #FFF;float:right;margin:6px 10px 0px 0px;font-size:20px;" onClick="df.style.display='none';">×</div>手机支付</div>
  <div id="dfe" style="width:231px;height:277px;margin:15px auto;"></div>
  <div style="text-align:center;">支付金额：<span id="dfq"></span>元</div>
</div>
</body>
</html>
<?
}
?>
