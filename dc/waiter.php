<?
require "config.php";
$x=isset($_GET["x"])?$_GET["x"]:"";
$s=isset($_GET["s"])?$_GET["s"]:"";if($s=="")$s="0";
if($x!="lgn"&&$x!=""){
	if(mysql_num_rows(mysql_query("Select ID From SCY Where Ni='".$s."'"))==0)die();
}
switch($x){
	case "lgn":
	$a=Req("a");if($a=="")$a="1";
	$b=Req("b");if($b=="")$b="1";
	$q=mysql_query("Select ID,Nn,Nr,Ni From SCY Where DD=0 And Nu='".$a."' And Np='".substr(md5($b),8,16)."'");
	if(mysql_num_rows($q)==0)die('E');
	$r=mysql_fetch_array($q);
	if(strpos("AB",$r[2])>-1){
		$c=Rnd();
		echo "OK".$r[0]."|".$r[1]."|".$r[2]."|".$c;
		mysql_query("Update SCY Set Nd=Now(),Ni='".$c."' Where Nu='".$a."'");
	}
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

	case "ktz"://读取折扣
	echo mysql_result(mysql_query("Select Nz From CFG Where ID=1"),0);
	break;

	case "ktl"://餐桌列表
	$q=mysql_query("Select ID,Nm,No,Ns,Ni,Oi From SCT Where DD=0 Order By No Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5];
	}
	break;

	case "kto"://点单读取
	$a=Req("a");
	$b=mysql_result(mysql_query("Select Oi From SCT Where ID=".$a),0);
	$c=1;
	$q=mysql_query("Select ID,Ti,Yi,Pz,Ps,Pt,St,Dt,I4,Ds,Bz From ODR Where ID=".$b);
	if(mysql_num_rows($q)>0){
		$r=mysql_fetch_array($q);
		echo $r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5]."|".$r[6]."|".$r[7]."|".$r[8]."|".$r[9]."|".$r[10]."\$";
		$c=$r[6];
		$q=mysql_query("Select ID,Oi,Ti,Ci,Cn,Cp,Cs,St,I1,I2,I3,Dt,Td,Bz From OCK Where Oi=".$b." And Ti=".$a." Order By Td Asc");
		while($r=mysql_fetch_array($q)){
			echo "|".$r[0].",".$r[1].",".$r[2].",".$r[3].",".$r[4].",".$r[5].",".$r[6].",".$r[7].",".$r[8].",".$r[9].",".$r[10].",".$r[11].",".$r[12].",".$r[13];
		}
	}
	mysql_query("Update SCT Set Ni=".$c." Where Ni>0 And ID=".$a);
	break;

	case "krz"://顾客入座
	mysql_query("Update SCT Set Ni=1 Where Ni<2 And ID=".Req("a"));
	echo "OK";
	break;
	
	case "kcd"://读取菜单
	$q=mysql_query("Select ID,Nm,Nh From SCL Order By No Asc");
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
	$q=mysql_query("Select Bz From ODR Where ID=".$a);
	if(mysql_num_rows($q)>0){$r=mysql_fetch_array($q);echo $r[0];}
	$q=mysql_query("Select B.ID,B.Nm,B.Np,A.Cp,A.Cs,A.St,A.ID,A.Td As N From OCK As A,SCD As B Where B.ID=A.Ci And A.St<14 And A.Oi=".$a." Order By A.Td Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5]."|".$r[6]."|".$r[7];
	}
	break;

	case "kxt"://下单
	$g=Req("e");
	$d=explode("|",Req("d"));
	$a=explode(",",$d[2]);
	$b=explode(",",$d[3]);
	$c=mysql_result(mysql_query("Select Oi From SCT Where ID=".$d[1]),0);
	if($c!=0&&$c!=(int)$d[0])die();
	$q=mysql_query("Select Ti From ODR Where ID=".$d[0]);
	if(mysql_num_rows($q)>0){
		mysql_query("Update ODR Set Ti=".$d[1].",Yi=".$g.",Pz=".$d[4].",St=3,Bz='".$d[5]."' Where ID=".$d[0]);
		$c=$d[0];
	}else{
		mysql_query("Insert Into ODR (Ti,Yi,Pz,Ps,Pt,St,Dt,I4,Ds,Bz) Values (".$d[1].",".$g.",".$d[4].",0,0,3,Now(),0,Now(),'".$d[5]."')");
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

	case "kjc"://点单入厨
	$a=Req("a");
	$b=Req("b");
	$c=Req("c");
	$q=mysql_query("Select ID From ODR Where Ti=".$a." And ID=".$b);
	if(mysql_num_rows($q)>0){
		mysql_query("Update ODR Set St=6,Yi=".$c." Where Ti=".$a." And ID=".$b);
		mysql_query("Update OCK Set St=6,I1=".$c." Where St<6 And Oi=".$b);
		mysql_query("Update SCT Set Ni=6 Where ID=".$a);
		echo "OK";
	}
	break;

	case "ksl"://点单改数
	$a=explode("|",Req("a"));
	mysql_query("Update OCK Set Cs=".$a[3].",Cp=".$a[4]." Where ID=".$a[2]);
	$q=mysql_query("Select Cp,Cs,Td From OCK Where Oi=".$a[1]);
	$b=0;
	$c=array(1,1,0);
	while($r=mysql_fetch_array($q)){
		$b=$b+$r[0]*$r[1]*$c[$r[2]];
	}
	mysql_query("Update ODR Set Pz=".$b." Where ID=".$a[1]);
	echo "OK";
	break;

	case "ktb"://点单退单
	$a=explode("|",Req("a"));
	$d=array(0,2);
	mysql_query("Update OCK Set Td=".$d[$a[3]].",Bz='".$a[4]."' Where ID=".$a[2]);
	$q=mysql_query("Select Cp,Cs,Td From OCK Where Oi=".$a[1]);
	$b=0;
	$c=array(1,1,0);
	while($r=mysql_fetch_array($q)){
		$b=$b+$r[0]*$r[1]*$c[$r[2]];
	}
	mysql_query("Update ODR Set St=6,Pz=".$b." Where ID=".$a[1]);
	mysql_query("Update SCT Set Ni=6 Where ID=".$a[0]);
	echo "OK";
	break;

	case "khz"://更换餐桌
	$a=Req("a");
	$b=Req("b");
	if(mysql_result(mysql_query("Select Ni From SCT Where ID=".$b),0)==0){
		$r=mysql_fetch_array(mysql_query("Select Ni,Oi From SCT Where ID=".$a));
		mysql_query("Update ODR Set Ti=".$b." Where Ti=".$a." And ID=".$r[1]);
		mysql_query("Update OCK Set Ti=".$b." Where Ti=".$a." And Oi=".$r[1]);
		mysql_query("Update SCT Set Ni=".$r[0].",Oi=".$r[1]." Where ID=".$b);
		mysql_query("Update SCT Set Ni=0,Oi=0 Where ID=".$a);
		echo "OK";
	}else echo "E";
	break;

	case "kjw"://结账延迟
	mysql_query("Update SCT Set Ni=10 Where Ni<12 And ID=".Req("a"));
	echo "OK";
	break;

	case "kjf"://点单付款
	$a=explode("|",Req("a"));
	$b=Req("b");
	$a[2]=(int)$a[2];
	$a[5]=(int)$a[5];
	if($a[3]=="3"){
		$c=explode("|",mysql_result(mysql_query("Select Ni From Cfg"),0));
		$f=curl_init();
		curl_setopt($f,CURLOPT_TIMEOUT,30);
		curl_setopt($f,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($f,CURLOPT_URL,"http://www.1000vw.com/Qw/mbr.php?x=khy&m=".$c[4]."&n=".$c[5]."&a=".$a[6]."&b=".$a[7]."&c=".$a[2]."&d=".$a[1]."&e=".$b);
		$e=curl_exec($f);
		curl_close($f);
		if(substr($e,0,2)=="OK"){
			if(substr($e,2)!="0")mysql_query("Insert Into UCZ (Ni,Nc,Oi,Yi,Ps,Pc,Pd) Values (".substr($e,2).",'".$a[6]."',".$a[1].",".$b.",0,-".$a[2].",Now())");
		}else die($e);
	}
	if($a[5]>0&&$a[2]>0)mysql_query("Insert Into ODR (Ti,Yi,Pz,Ps,Pt,St,Dt,I4,Ds,Bz) Values (".$a[0].",0,0,".$a[5].",1,12,Now(),".$b.",Now(),'单号[".$a[1]."]抵扣".($a[5]/100)."元')");
	if($a[5]>0)$a[4]=$a[4]."团购券".($a[5]/100)."元";
	if($a[2]==0)$a[2]=$a[5];
	mysql_query("Update ODR Set Ps=Ps+".$a[2].",Pt=".$a[3].",Bz='".$a[4]."',Ds=Now(),I4=".$b.",St=12 Where ID=".$a[1]." And Ti=".$a[0]);
	mysql_query("Update SCT Set Ni=12 Where ID=".$a[0]);
	echo "OK";
	break;

	case "kqt"://点单清台
	$a=Req("a");
	$b=Req("b");
	if($b=="")$b=0;
	mysql_query("Update SCT Set Ni=0,Oi=0 Where Ni In (1,12) And ID=".$a);
	mysql_query("Update ODR Set St=12 Where ID=".$b);
	mysql_query("Update OCK Set St=12 Where Oi=".$b." And Ti=".$a);
	echo "OK";
	break;

	default:
	$p=mysql_result(mysql_query("Select Nm From CFG Where ID=1"),0);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=320, initial-scale=1, user-scalable=no">
<script type="text/javascript">document.write('<meta name="viewport" content="width=320, initial-scale='+parseInt(document.documentElement.clientWidth)/320+', user-scalable=no">');</script>
<title><? echo $p; ?> - 店员前台</title>
<style type="text/css">
div {font-size:16px;}
input[type=text],input[type=password]{font-size:16px;border:1px solid #F90;padding:5px;text-align:center;}
input[type=button]{font-size:18px; background-color:#F90;color:#FFF;border:0px;padding:5px 10px;border:1px solid #FFF;}
.nst1{margin:10px 5px;padding:0px 10px;border:1px dashed #09F;line-height:32px;background-color:#09F;color:#FFF;}
.nst2{margin:10px 5px;padding:0px 10px;border:1px dashed #F90;line-height:32px;background-color:#F90;color:#FFF;}
.nst3{margin:10px 5px;padding:0px 10px;border:1px dashed #09F;line-height:32px;}
.nsto div{float:left;width:45px;text-align:center;background-color:#F90;color:#FFF;margin:10px 4px;line-height:32px;}
.nstc{border-bottom:1px dashed #F90;line-height:32px;font-size:14px;}
.nstr{float:right;width:40px;font-size:12px;text-align:center;}
.nstt{float:right;width:20px;line-height:20px;text-align:center;background-color:#F90;color:#FFF;font-size:12px;margin-top:5px;}
.dml1{margin:2px 0px 2px 2px;line-height:25px;border:1px solid #F90;text-align:center;border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;}
.dml2{margin:2px 0px 2px 2px;line-height:25px;border:1px solid #F90;text-align:center;background-color:#F90;color:#FFF;border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;}
.ddl1{margin:4px;background-color:#FFE;border:1px dashed #FDA;height:80px;line-height:40px;font-size:18px;}
.ddl2{float:left;width:72px;height:72px;margin:4px;overflow:hidden;background-image:url(i/n.jpg);}
.ddl3{float:right;margin:3px 5px 0px 0px;font-size:24px;width:32px;height:32px;line-height:32px;text-align:center;background-color:#F90;color:#FFF;border-radius:5px;}
.ddl4{font-size:28px;color:#F00;}
.ddl5{float:right;width:112px;}
.ddl5 span{font-size:18px;width:26px;height:26px;line-height:26px;text-align:center;background-color:#F90;color:#FFF;border-radius:5px;display:inline-block;}
.ddl6{font-size:12px;color:#999;}
.ddl7{float:right;font-size:12px;color:#999;}
.zkl1{display:inline-block;font-size:12px;line-height:20px;width:68px;border:1px dashed #09F;background-color:#F9FCFF;margin:0px 10px;text-align:center;cursor:pointer;}
.zkl0{display:inline-block;font-size:12px;line-height:20px;width:68px;border:1px dashed #09F;background-color:#09F;color:#FFF;margin:0px 10px;text-align:center;cursor:pointer;}
</style>
<script type="text/javascript">
function $(o){return document.getElementById(o);}
function xml(){var x;try{x=new ActiveXObject('Msxml2.XMLHTTP');}catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(f){x=false;}}if(!x&&typeof XMLHttpRequest!='undefined'){x=new XMLHttpRequest();}xvr=true;return x;}
function sck(n,v){var d=new Date();d.setTime(d.getTime()+1080000000);document.cookie=n+'='+escape(v)+'; expires='+d.toGMTString();}
function gck(n){var c=document.cookie.split("; ");for(var i=0;i<c.length;i++){var t=c[i].split("=");if(t[0]==n)return unescape(t[1]);}return '';}
function rsz(o,s){var w=o.width;var h=o.height;if(w<h){h=h*s/w;w=s;}else{w=s*w/h;h=s;}o.width=w;o.height=h;o.style.margin=(s-h)/2+'px '+(s-w)/2+'px';}
function psz(o,w,h){w=parseInt(w);h=parseInt(h)-4;var s=w>h?w:h;rsz(o,s);}
function num(n){var o=event.srcElement;if(o.tagName=='INPUT'&&o.type=='text'){var v=n?parseFloat(o.value):parseInt(o.value);if(!v)v=0;o.value=v;}}
function atwh(){var pw=parseInt(document.documentElement.clientWidth);var ph=parseInt(document.documentElement.clientHeight);if(pw>320)pw=320;if(ph>560)ph=560;dl.style.width=pw+'px';dl.style.height=ph+'px';dt.style.height=ph+'px';dz.style.height=ph+'px';dd.style.height=ph+'px';dm.style.height=ph+'px';dw.style.height=ph+'px';dtl.style.height=ph-50+'px';dtc.style.height=ph-102+'px';bigp.style.height=ph-20+'px';dml.style.height=ph-128+'px';dds.style.height=ph-52+'px';if(gck('QWCTCwUat')=='1'){usr.value=gck('QWCTCwUsr');upw.value='';uat.checked=true;}}
var oid='<? echo $OpID; ?>';
var uri='waiter.php?';
var uid=0;//用户
var pid='';//密码
var rid='';//权限
var sid='';//连接
var eid;//Edit ID
var mid;//Time ID
var wid;//Sync ID
var kid=0;//客或服
var zks='';//折扣

var ori;//订单ID
var cid;//大图ID
var cno;//待下单数
var cnt;//总点单数
var lcb=[];//菜类
var lcd=[];//菜品
var mdc=[];//点单
var tst=['空桌待客','顾客入座','顾客呼叫','顾客下单','顾客退单','厨房退单','提交厨房','厨房接收','制作完成','正在上菜','顾客用餐','申请结账','结账完成','打扫清台','退单完成'];
var tcz=[];//餐桌数组
var tcd;   //餐桌数据
function lgn(){var x=xml();x.open('Get',uri+'x=lgn'+'&a='+usr.value+'&b='+upw.value+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t.substr(0,2)=='OK'){if(uat.checked){sck('QWCTCwUsr',usr.value);sck('QWCTCwUpw','');sck('QWCTCwUat','1');}else sck('QWCTCwUat','0');dl.style.display='none';dt.style.display='block';var w=t.substr(2).split('|');uid=w[0];cker.innerText=w[1];kui.innerText=w[1];sid=w[3];rid=w[2];pid=upw.value;ktz();}else{alert('用户名或密码错误！');}}};x.send();}
//加载折扣
function ktz(){var x=xml();x.open('Get',uri+'x=ktz&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){zks=x.responseText.split('$');setTimeout('ktl()',200);}};x.send();}
//加载大厅
function ktl(){var x=xml();x.open('Get',uri+'x=ktl&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.substr(1).split('$');tcz=[];if(t[0]!=''){for(var i=0;i<t.length;i++)tcz.push(t[i].split('|'));}var p=0;var s='';for(var i=0;i<tcz.length;i++){s+='<div id="tb'+i+'" class="'+(tcz[i][4]=='0'?'nst3':('.2.3.4.5.11.'.indexOf('.'+tcz[i][4]+'.')>-1?'nst2':'nst1'))+'" onclick="kto('+i+');">'+tcz[i][1]+'['+tcz[i][3]+'人座]<div style="float:right;">'+tst[tcz[i][4]]+'</div></div>';if('.2.3.4.5.11.'.indexOf('.'+tcz[i][4]+'.')>-1)p=1;}var d=new Date();dtl.innerHTML=s+'<div style="text-align:center;">更新于 '+d.getHours()+':'+String(100+d.getMinutes()).substr(1,2)+':'+String(100+d.getSeconds()).substr(1,2)+'</div>';mid=setTimeout('ktl()',10000);if(p==1)dsy.play();}};x.send();}
//加载餐桌
function kto(n){var x=xml();x.open('Get',uri+'x=kto&a='+tcz[n][0]+'&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='')t='|||||||||||0$';t=t.split('$');var d=t[1].split('|');tcd=t[0].split('|');tcd[1]=n;var s='';for(var i=1;i<d.length;i++){d[i]=d[i].split(',');s+='<div class="nstc">'+d[i][4]+'<div class="nstt" onclick="ktd(\''+d[i].join('|')+'\');" style="color:#'+['FFF','F00','0F9'][d[i][12]]+';">改</div><div class="nstr">×'+d[i][6]+'</div><div class="nstr">'+d[i][5]/100+'元</div><div class="nstr" style="width:50px;">'+tst[d[i][7]]+'</div></div>';}dtc.innerHTML=s+'<div style="float:right;width:80px;margin-top:10px;background-color:#F90;color:#FFF;line-height:32px;text-align:center;" onClick="kdd(0);">顾客点单</div><br />单号：'+tcd[0]+'　下单：'+tcd[2]+'　收银：'+tcd[8]+'<br>时间：'+tcd[7]+'<br>金额：'+tcd[3]/100+' / '+tcd[4]/100+' 元['+['现金','团购券','刷卡','会员卡','微信','支付宝'][tcd[5]||0]+'支付]<br />备注：'+tcd[10];dzn.innerText=tcz[n][1]+'['+tst[tcz[n][4]]+']';ddn.innerText=tcz[n][1];dmn.innerText=tcz[n][1];dz.style.display='block';}};x.send();}
//顾客入座
function krz(){if(tcz[tcd[1]][4]>0)return;var x=xml();x.open('Get',uri+'x=krz&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+tcd[0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){clearTimeout(mid);dz.style.display='none';;ktl();}else{alert('提交失败，请稍后重试！');}}};x.send();}
//顾客点单
function kdd(n){if(tcz[tcd[1]][4]==0)return;kid=n;dd.style.display='block';ori=tcz[tcd[1]][5];mdc=[];ddc.innerText='0';clearTimeout(mid);kcd();}
//加载菜单
function kcd(){if(lcb.length>0&&lcd.length>0){kcs();return;}var x=xml();x.open('Get',uri+'x=kcd&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=(x.responseText+'@@@').split('@@@');var a=t[0].split('$');for(var i=1;i<a.length;i++){a[i]=a[i].split('|');lcb.push(a[i]);}var b=t[1].split('$');for(var i=1;i<b.length;i++){b[i]=b[i].split('|');lcd.push(b[i]);}kcs();}};x.send();}
//显示分类
function kcs(){var s='';for(var i=0;i<lcb.length;i++)s+='<div id="dmi'+i+'" class="dml2" style="display:'+((lcb[i][2]=='0'||kid==1)?'block':'none')+';" onClick="kcl('+i+');">'+lcb[i][1]+'</div>';ddb.innerHTML=s;kcl(0);}
//加载菜类
function kcl(n){for(var i=0;i<lcb.length;i++)$('dmi'+i).className='dml2';$('dmi'+n).className='dml1';var s='';for(var i=0;i<lcd.length;i++){if((kid==1||lcd[i][6]=='0')&&lcd[i][2].indexOf(','+lcb[n][0]+',')>-1)s+='<div class="ddl1"><div class="ddl2" onclick="kcp('+i+');"><img src="i/'+(lcd[i][4]=='1'?'c'+lcd[i][0]:'n')+'.jpg" onload="rsz(this,72);"></div>'+lcd[i][1]+'<br /><span class="ddl4">'+lcd[i][5]/100+'</span>元<div class="ddl3" onclick="kxd('+i+');">点</div></div>';}dds.innerHTML=s;}
//菜单大图
function kcp(n){if(lcd[n][4]=='0')return;bigp.style.display='block';bigc.style.display='block';bigd.style.display='block';bigi.style.display='block';bigi.innerHTML='<span style="font-size:20px;font-weight:bold;">'+lcd[n][1]+'</span>'+(lcd[n][3]==''?'':('<br>　　'+lcd[n][3]));bigp.innerHTML='<img src="i/c'+lcd[n][0]+'.jpg" onload="psz(this,300,bigp.style.height);">';cid=n;}
//点单下单
function kxd(n){var c=0;for(var i=0;i<mdc.length;i++)if(mdc[i][0]==lcd[n][0])c=1;if(!c)mdc.push(lcd[n]);ddc.innerText=mdc.length;tip.innerText=lcd[n][1]+' 已加入点单！';tip.style.display='block';setTimeout('tip.style.display=\'none\'',2000);}
//点单列表
function kmd(){if(mdc.length==0&&ori=='0')return;var s='';for(var i=0;i<mdc.length;i++)s+='<div class="ddl1" id="md'+i+'"><div class="ddl2"><img src="i/'+(mdc[i][4]=='1'?'c'+mdc[i][0]:'n')+'.jpg" onload="rsz(this,72);"></div>'+mdc[i][1]+'<span id="mt'+i+'"></span><br /><span id="mp'+i+'" class="ddl4">'+mdc[i][5]/100+'</span>元<div class="ddl5"><span onclick="kms('+i+',-1,'+mdc[i][0]+');">-</span> <input type="text" id="mc'+i+'" style="width:30px;" value="1" onblur="num();kms('+i+',0,'+mdc[i][0]+');" /> <span onclick="kms('+i+',1,'+mdc[i][0]+');">+</span><input type="hidden" id="mi'+i+'" value="'+mdc[i][0]+'"></div></div>';dml.innerHTML=s;dd.style.display='none';dm.style.display='block';cno=mdc.length;cnt=cno;kmj();if(ori=='0')return;var x=xml();x.open('Get',uri+'x=kmd&s='+sid+'&a='+ori+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var d=x.responseText.split('$');s='';for(var j=1;j<d.length;j++){d[j]=d[j].split('|');s+='<div class="ddl1" id="md'+(i+j-1)+'"><div class="ddl2"><img src="i/'+(d[j][2]=='1'?'c'+d[j][0]:'n')+'.jpg" onload="rsz(this,72);"></div>'+d[j][1]+' <span class="ddl6">['+tst[d[j][5]]+']</span><br /><span id="mp'+(i+j-1)+'" class="ddl4">'+d[j][3]/100+'</span>元 × '+d[j][4]+'<input type="hidden" id="mc'+(i+j-1)+'" value="'+d[j][4]+'" />'+['<div id="mt'+(i+j-1)+'"></div>','<div class="ddl7" id="mt'+(i+j-1)+'">[已申请退单]</div>','<div class="ddl7" id="mt'+(i+j-1)+'">[已同意退单]</div>'][d[j][7]]+'<input type="hidden" id="mi'+(i+j-1)+'" value="'+d[j][0]+'"></div></div>';}dml.innerHTML+=s;if(d[0]!='')dmb.value=d[0];cnt=i+j-1;kmj();}};x.send();}
//单品数量
function kms(o,n,r){var m=parseInt($('mc'+o).value);if(!m)m=1;m+=n;$('mc'+o).value=m;if(m==0){if(confirm('您确认要删除此菜品吗？？')){$('md'+o).style.display='none';for(var i=0;i<mdc.length;i++){if(mdc[i][0]==String(r))mdc.splice(i,1);}}else{$('mc'+o).value=1;}}kmj();}
//订单金额
function kmj(){var s=0;for(var i=0;i<cnt;i++){if($('mt'+i).innerText.indexOf('已同意退单')==-1)s+=$('mp'+i).innerText*$('mc'+i).value;}dmj.innerText=s;}
//提交订单
function kxt(){var a=[];var b=[];for(var i=0;i<cno;i++){var c=$('mc'+i).value;if(c!='0'){a.push($('mi'+i).value);b.push(c);}}if(mdc.length==0)return;if(!confirm('请核对菜品及数量无误后下单！\n确认要下单吗？'))return;var x=xml();x.open('Get',uri+'x=kxt&s='+sid+'&e='+uid+'&d='+escape(ori+'|'+tcz[tcd[1]][0]+'|'+a.join(',')+'|'+b.join(',')+'|'+dmj.innerText*100)+'|'+(dmb.value=='如：清淡，微辣，不要香菜，牛排7成熟等'?'':dmb.value)+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t.substr(0,2)=='OK'){ori=t.substr(2);mdc=[];if(kid==1){dd.style.display='none';dm.style.display='none';ktl();setTimeout('kto('+tcd[1]+')',300);}else{alert('下单已成功，请稍等服务员来确认点单！');kmd();}}else{alert('下单失败，请稍后重试或呼叫服务员！\n如是同桌已先下单，请刷新本页面！');}}};x.send();}
//提交厨房
function kjc(){if(tcd[0]=='')return;var x=xml();x.open('Get',uri+'x=kjc&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+tcd[0]+'&c='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('已提交厨房！');clearTimeout(mid);ktl();setTimeout('kto('+tcd[1]+')',300);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//退单操作
function ktd(t){t=t.split('|');eid=t[0];inb1.innerText=tcz[tcd[1]][1];inb2.innerText=t[4];inb3.value=t[13];inb4.innerText=t[6];inb5.value=t[6];inb6.value=t[5]/100;dq.style.display='block';}
//退单修改
function ksl(){var x=xml();x.open('Get',uri+'x=ksl&s='+sid+'&a='+escape(tcz[tcd[1]][0]+'|'+tcd[0]+'|'+eid+'|'+inb4.innerText+'|'+inb6.value*100)+'&b='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('已修改完成！');dq.style.display='none';clearTimeout(mid);ktl();setTimeout('kto('+tcd[1]+')',300);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//退单执行
function ktb(n){if(!confirm('确认退单？？'))return;var x=xml();x.open('Get',uri+'x=ktb&s='+sid+'&a='+escape(tcz[tcd[1]][0]+'|'+tcd[0]+'|'+eid+'|'+n+'|'+inb3.value)+'&b='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('已退单完成！');dq.style.display='none';clearTimeout(mid);ktl();setTimeout('kto('+tcd[1]+')',300);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//点单结算
function kjz(){if(tcd[0]=='')return;var s='';var t;for(var i=1;i<zks.length;i++){t=zks[i].split('|');s+='<span id="djo'+i+'" class="zkl1" onclick="kjo();">'+t[0]+'</span>';}djz.innerHTML=s;djx.innerText='单号：'+tcd[0]+'　合计：'+tcd[3]/100+'元　已结：'+tcd[4]/100+'元';var f=tcd[3]/100-tcd[4]/100;djy.innerText=f;djh.innerText=f;djs.value=f;djq.value=0;djf.options[tcd[5]].selected=true;djb.value='';dj.style.display='block';djw.style.display=tcd[5]=='3'?'inline-block':'none';dja.style.display=(tcd[5]=='4'||tcd[5]=='5')?'inline-block':'none';}
//计算折扣
function kjo(){var o=event.srcElement;if(o.tagName=='SPAN')o.className=o.className=='zkl1'?'zkl0':'zkl1';var m=tcd[3]/100;m=m-djq.value*1;for(i=1;i<zks.length;i++){var t=zks[i].split('|');if($('djo'+i).className=='zkl0'){djb.value=djb.value.replace(t[0]+'，','')+t[0]+'，';t[1]=t[1]*1;t[2]=t[2]*1;if(m>t[1]-t[2]){if(t[2]<1){m=m*t[2];}else{if(m<t[1]&&m>(t[1]-t[2])){m=t[1]-t[2];}else m-=t[2];}}}else{djb.value=djb.value.replace(t[0]+'，','');}}m=Math.floor(m*100)/100;djh.innerText=m;if(m<0)m=0;djs.value=djl.checked?Math.floor(m):m;}
//二维码
function kem(){dw.style.display='block';dwf.innerHTML='正在生成支付二维码......';var x=xml();x.open('Get',uri+'x=key&a='+tcd[0]+'&b='+djs.value+'&c='+djf.value+'&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){if(x.responseText.substr(0,2)=='OK'){ken(x.responseText.substr(2));}else{alert('提交失败，请稍后重试！');}}};x.send();}
function ken(t){var x=xml();x.open('Get','http://www.1000vw.com/Qw/key.php?t='+t+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){if(x.responseText.substr(0,2)=='OK'){var k=x.responseText.substr(2);t=t.split('|');dwq.innerText=t[1];if(djf.value=='4'){dwf.innerHTML='<div style="width:231px;height:231px;margin:10px auto;display:block;overflow:hidden;"><div style="position:absolute;margin:91px 0px 0px 91px;width:48px;height:48px;border:1px solid #CCC;border-radius:5px;background-color:#FFF;background-image:url(https://pay.weixin.qq.com/wxzf_guide/img/logo.png);background-repeat:no-repeat;background-position:4px 6px;"></div><div><img src="http://www.liantu.com/api.php?el=Q&m=0&w=231&text='+escape(k)+'"></div></div>';}if(djf.value=='5'){dwf.innerHTML='<div style="width:231px;height:231px;margin:0px auto;overflow:hidden;"><iframe src="about:blank" id="defm" name="defm" scrolling="no" frameborder="0" width="231" height="231"></iframe></div>';if(navigator.userAgent.toLowerCase().indexOf('micromessenger')>0){dwf.innerHTML='<div style="text-align:left;">您正在使用微信内置浏览器！<br>由于新版微信屏蔽了支付宝链接地址，所以无法在微信内置浏览器中显示支付宝收款二维码！<br>解决方法：点击右上角三个竖点图标，然后点击在浏览器中打开，即可在普通浏览器中使用支付宝二维码收款功能。<br>建议以后使用QQ浏览器，UC浏览器等来进入管理界面。</div>';}else $('defm').src='https://mapi.alipay.com/gateway.do?_input_charset=utf-8&notify_url=http://www.taoewm.com/Qw/nzfb.php&out_trade_no='+t[3]+'-'+t[0]+'-c-'+t[4]+'&partner=2088911048354746&payment_type=1&qr_pay_mode=4&qrcode_width=231&seller_email=taoewm@163.com&service=create_direct_pay_by_user&subject=<? echo $p; ?>-餐费&total_fee='+t[1]+'&sign='+k+'&sign_type=MD5';}clearTimeout(wid);wid=setTimeout('kcx()',10000);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//提交付款
function kjf(){if(djf.options.selectedIndex==0){alert('服务员不允许现金结账，请将现金交至前台收银结账！');return;}if(djs.value*1>0&&djf.selectedIndex==1){alert('抵扣金额不足，请选择其他收款方式！');return;}if(!confirm('确认结账？？'))return;clearTimeout(wid);tcd[4]=djs.value*100;tcd[10]=djb.value;var x=xml();x.open('Get',uri+'x=kjf&s='+sid+'&a='+escape(tcz[tcd[1]][0]+'|'+tcd[0]+'|'+tcd[4]+'|'+djf.options.selectedIndex+'|'+tcd[10]+'|'+djq.value*100+'|'+ihu.value+'|'+ihp.value)+'&b='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('已付款完成！');dj.style.display='none';clearTimeout(mid);ktl();setTimeout('kto('+tcd[1]+')',300);}else if(t=='PE'){alert('会员卡密码错误！');}else if(t=='FE'){alert('会员卡余额不足！');}else{alert('提交失败，请稍后重试！');}}};x.send();}
//延迟付款
function kjw(){clearTimeout(wid);var x=xml();x.open('Get',uri+'x=kjw&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){dj.style.display='none';clearTimeout(mid);ktl();setTimeout('kto('+tcd[1]+')',300);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//网付查询
function kcx(){var x=xml();x.open('Get','http://www.1000vw.com/Qw/sync.php?x='+oid+'-'+tcd[0]+'-c-&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.split('OK');var s=0;for(var i=1;i<t.length;i++){var h=t[i].split('|');if(djb.value.indexOf(h[2])<0){dw.style.display='none';dwf.innerHTML='';clearTimeout(wid);alert('手机扫码支付已成功！\n\n单　号：'+h[0]+'\n金　额：'+h[1]/100+'元\n时　间：'+h[3]+'\n交易号：'+h[2]);var d='['+['W:','A:'][djf.options.selectedIndex-4]+(h[1]/100)+':'+h[2]+']';djb.value=djb.value.replace(d,'')+d;s=1;}}if(s==0)wid=setTimeout('kcx()',3000);}};x.send();}
//更换餐桌
function khz(){if(tcd[0]=='')return;dh.style.display='block';dhf.innerText=tcz[tcd[1]][1];dht.innerHTML='';var n=0;for(var i=0;i<tcz.length;i++){if(tcz[i][4]=='0'){var m=document.createElement('option');m.text=tcz[i][1];m.value=tcz[i][0];dht.options.add(m);}}}
//换桌执行
function khs(){if(tcz[tcd[1]][0]==dht.value){dh.style.display='none';return;}var x=xml();x.open('Get',uri+'x=khz&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+dht.value+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){dh.style.display='none';dz.style.display='none';clearTimeout(mid);ktl();}else{if(t=='E'){alert('目标桌状态非空桌待客，请选择空桌！');}else{alert('提交失败，请稍后重试！');}}}};x.send();}
//餐桌清台
function kqt(){if(tcz[tcd[1]][4]!='0'&&tcz[tcd[1]][4]!='1'&&tcz[tcd[1]][4]!='12'){alert('已下单及未结账，不能清台！');return;}if(!confirm('确认清台？？'))return;var x=xml();x.open('Get',uri+'x=kqt&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+tcd[0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){clearTimeout(mid);dz.style.display='none';;ktl();}else{alert('提交失败，请稍后重试！');}}};x.send();}
//返回店员
function kbk(){if(iny.value==pid){iny.value='';dd.style.display='none';dm.style.display='none';dz.style.display='none';dy.style.display='none';clearTimeout(mid);ktl();}else{alert('密码错误！');}}
</script>
</head>
<body style="margin:0px;padding:0px;font-size:14px;text-align:left;background-color:#FFF;" onLoad="atwh();">
<audio id="dsy" src="i/m.mp3"></audio>
<audio id="dsp" src="i/p.mp3"></audio>
<!--登陆-->
<div id="dl" style="width:320px;text-align:center;font-size:18px;background-color:#F90;color:#FFF;"><br><br><img src="i/i.jpg" width="120" height="120"><br><? echo $p; ?><br>店员前台登录<br><br>用户名： <input type="text" size="12" id="usr"><br>密　码： <input type="password" size="12" id="upw"><br><input type="checkbox" id="uat"> 自动 <input type="button" value="登录" onClick="lgn();"></div>
<!--大厅-->
<div id="dt" style="width:320px;display:none;">
  <div style="width:320px;height:50px;top:0px;left:0px;background-color:#666;">
    <div style="float:left;margin:3px;width:44px;height:44px;"><img src="i/i.jpg" width="44" height="44"></div>
    <div style="float:left;color:#FFF;line-height:20px;margin-top:6px;font-size:18px;"><? echo $p; ?><br><span style="font-size:14px;" id="cker"></span></div>
    <div style="float:right;width:60px;line-height:30px;text-align:center;font-size:16px;margin:10px;background-color:#F90;color:#FFF;" onClick="sck('QWCTCwUpw','');if(confirm('确认退出？？'))location.reload();">退出</div>
  </div>
  <div id="dtl" style="width:320px;overflow:auto;">Loading...</div>
</div>
<!--单桌-->
<div id="dz" style="position:absolute;top:0px;left:0px;width:320px;background-color:#FFF;z-index:10;display:none;">
  <div style="width:320px;height:50px;background-color:#666;">
    <div style="float:left;margin:3px;width:44px;height:44px;"><img src="i/i.jpg" width="44" height="44"></div>
    <div style="float:left;color:#FFF;line-height:20px;margin-top:6px;font-size:18px;"><? echo $p; ?><br><span style="font-size:14px;" id="dzn"></span></div>
    <div style="float:right;width:60px;line-height:30px;text-align:center;font-size:16px;margin:10px;background-color:#F90;color:#FFF;" onClick="dz.style.display='none';">返回</div>
  </div>
  <div class="nsto"><div onClick="krz();">入座</div><div onClick="kdd(1);">点单</div><div onClick="kjc();">交厨</div><div onClick="kjz();">结账</div><div onClick="khz();">换桌</div><div onClick="kqt();">清台</div></div>
  <div id="dtc" style="width:310px;padding:0px 5px;font-size:14px;overflow:auto;"></div>
</div>
<!--退单-->
<div id="dq" style="width:278px;position:absolute;left:20px;top:70px;background-color:#FFF;border:1px solid #F90;z-index:11;display:none;">
  <div style="background-color:#F90;color:#FFF;line-height:32px;text-align:center;">修改</div>
  <div style="padding:10px 20px;line-height:30px;">桌号： <font id="inb1"></font><br />菜品： <font id="inb2"></font><br>价格： <input type="text" id="inb6" size="3" style="font-size:14px;" onBlur="num();"> 元<br>数量： <span style="font-size:14px;cursor:pointer;background-color:#F90;color:#FFF;width:20px;height:20px;line-height:20px;display:inline-block;text-align:center;" onClick="inb4.innerText=inb4.innerText*1-1;if(inb4.innerText*1<1)inb4.innerText=1;">-</span>　<span id="inb4"></span>　<span style="font-size:14px;cursor:pointer;background-color:#F90;color:#FFF;width:20px;height:20px;line-height:20px;display:inline-block;text-align:center;" onClick="inb4.innerText=inb4.innerText*1+1;if(inb4.innerText*1>inb5.value*1)inb4.innerText=inb5.value;">+</span> <input type="hidden" id="inb5" /><br />原因： <input type="text" style="width:120px;" id="inb3" /></div>
  <div style="height:40px;text-align:center;"><input type="button" value="取消" onClick="dq.style.display='none';"> <input type="button" value="拒绝" onClick="ktb(0);"> <input type="button" value="退单" onClick="ktb(1);"> <input type="button" value="修改" onClick="ksl();"></div>
</div>
<!--点单-->
<div id="dd" style="position:absolute;top:0px;left:0px;width:320px;background-color:#FFF;z-index:20;display:none;">
  <div style="width:320px;height:50px;background-color:#666;">
    <div style="float:left;margin:3px;width:44px;height:44px;"><img src="i/i.jpg" width="44" height="44" onClick="if(kid==1){dd.style.display='none';dm.style.display='none';ktl();}else dy.style.display='block';"></div>
    <div style="float:left;color:#FFF;line-height:20px;margin-top:6px;font-size:18px;"><? echo $p; ?><br><span style="font-size:14px;" id="ddn"></span></div>
    <div style="float:right;width:100px;line-height:30px;text-align:center;font-size:16px;margin:10px;background-color:#F90;color:#FFF;" onClick="if(ddc.innerText!='0'||ori!='0')kmd();">我的点单(<span id="ddc">0</span>)</div>
  </div>
  <div id="ddb" style="float:left;width:60px;overflow:auto;"></div>
  <div id="dds" style="float:left;width:258px;overflow:auto;border:1px solid #F90;">正在加载，请稍候......</div>
</div>
  <div id="bigc" style="position:absolute;top:15px;left:15px;width:32px;height:32px;text-align:center;line-height:32px;border-radius:16px;background-color:#F90;color:#FFF;font-size:28px;z-index:29;display:none;" onClick="bigp.style.display='none';bigc.style.display='none';bigd.style.display='none';bigi.style.display='none';">×</div>
  <div id="bigd" style="position:absolute;top:15px;left:275px;width:32px;height:32px;text-align:center;line-height:32px;border-radius:16px;background-color:#F90;color:#FFF;font-size:28px;z-index:28;display:none;" onClick="kxd(cid);bigp.style.display='none';bigc.style.display='none';bigd.style.display='none';bigi.style.display='none';">＋</div>
  <div id="bigi" style="position:absolute;width:280px;left:20px;bottom:20px;color:#FFF;z-index:27;display:none;font-size:14px;text-shadow:2px 2px 2px #666;"></div>
  <div id="bigp" style="position:absolute;width:300px;left:10px;top:10px;background-color:#FFF;overflow:scroll;z-index:26;display:none;"></div>
  <div id="tip" style="position:absolute;width:180px;bottom:60px;left:70px;line-height:30px;background-color:#F90;color:#FFF;text-align:center;border-radius:15px;box-shadow:3px 3px 3px #999;z-index:25;display:none;"></div>
<!--下单-->
<div id="dm" style="position:absolute;top:0px;left:0px;width:320px;background-color:#FFF;z-index:30;display:none;">
  <div style="width:320px;height:50px;background-color:#666;">
    <div style="float:left;margin:3px;width:44px;height:44px;"><img src="i/i.jpg" width="44" height="44" onClick="if(kid==1){dd.style.display='none';dm.style.display='none';ktl();}else dy.style.display='block';"></div>
    <div style="float:left;color:#FFF;font-size:18px;line-height:20px;margin-top:6px;"><? echo $p; ?><br><span style="font-size:14px;" id="dmn"></span></div>
    <div style="float:right;width:100px;line-height:30px;text-align:center;font-size:16px;margin:10px;background-color:#F90;color:#FFF;" onClick="dm.style.display='none';dd.style.display='block';bigp.style.display='none';bigc.style.display='none';bigd.style.display='none';bigi.style.display='none';ddc.innerText=mdc.length;">继续点单</div>
  </div>
  <div id="dml" style="width:320px;overflow:auto;"></div>
  <div style="height:28px;font-size:12px;line-height:28px;padding:0px 10px;color:#999;background-color:#EEE;">备注： <input type="text" style="font-size:12px;padding:1px;width:240px;color:#999;text-align:left;" id="dmb" value="如：清淡，微辣，不要香菜，牛排7成熟等" onFocus="if(this.value='如：清淡，微辣，不要香菜，牛排7成熟等')this.value='';"></div>
  <div style="width:320px;height:50px;background-color:#666;">
    <div style="float:left;line-height:30px;margin-top:10px;color:#FFF;">　总计： <span id="dmj" style="font-size:30px;color:#F00;"></span> 元</div>
    <div style="float:right;width:100px;line-height:30px;text-align:center;font-size:16px;margin:10px;background-color:#F90;color:#FFF;" onClick="kxt();">立即下单</div>
  </div>
</div>
<!--结账-->
<div id="dj" style="width:278px;position:absolute;left:20px;top:70px;background-color:#FFF;border:1px solid #F90;z-index:50;display:none;">
  <div style="background-color:#F90;color:#FFF;line-height:32px;text-align:center;">结账</div>
  <div style="padding:10px;line-height:30px;"><span id="djx"></span><br>折扣：<span id="djz"></span><br>应付：<span id="djy"></span>元　折后：<span id="djh"></span>元<br>抵扣：<input type="text" id="djq" style="width:64px;text-align:left;" onBlur="num(1);kjo();"><br>实付：<input type="text" id="djs" style="width:64px;text-align:left;"> <input type="checkbox" id="djl" checked onClick="kjo();"> 抹零<br>方式：<select id="djf" style="font-size:16px;" onChange="djw.style.display=this.selectedIndex==3?'inline-block':'none';dja.style.display=(this.selectedIndex==4||this.selectedIndex==5)?'inline-block':'none';"><option value="0">现金支付</option><option value="1">团购券支付</option><option value="2">刷卡支付</option><option value="3">会员卡支付</option><option value="4">微信支付</option><option value="5">支付宝支付</option></select>　<span id="dja" style="display:none;"><a href="javascript:void(0);" onClick="kem();return false;window.open(uri+'x=pay&a='+tcd[0]+'&b='+djs.value+'&c='+djf.value+'&s='+sid);wid=setTimeout('kcx()',10000);">扫码</a></span><span id="djw" style="display:none;">卡号：<input type="text" id="ihu" style="font-size:12px;text-align:left;width:60px;" /> 密码：<input type="password" id="ihp" style="font-size:12px;text-align:left;width:60px;" /></span><br>备注：<input type="text" id="djb" style="font-size:12px;text-align:left;width:160px;"><div style="text-align:center;"><input type="button" value="取消" onClick="dj.style.display='none';clearTimeout(wid);">　<input type="button" value="延迟" onClick="kjw();">　<input type="button" value="结账" onClick="kjf();"></div></div>
</div>
<!--换桌-->
<div id="dh" style="width:278px;position:absolute;left:20px;top:70px;background-color:#FFF;border:1px solid #F90;z-index:60;display:none;">
  <div style="background-color:#F90;color:#FFF;line-height:32px;text-align:center;">换桌</div>
  <div style="padding:20px;line-height:32px;">原桌号： <span id="dhf"></span><br>更换至： <select id="dht" style="font-size:16px;"></select><div style="text-align:center;"><br><input type="button" value="取消" onClick="dh.style.display='none';">　　<input type="button" value="更换" onClick="khs();"></div></div>
</div>
<!--二维码-->
<div id="dw" style="position:absolute;width:320px;left:0px;top:0px;z-index:55;background-color:#FFF;text-align:center;display:none;">
  <div style="line-height:50px;background-color:#666;color:#FFF;font-size:24px;">扫码支付</div>
  <div id="dwf" style="width:231px;height:231px;margin:20px auto;"></div>
  <div style="font-size:20px;">支付金额： <span id="dwq" style="font-size:36px;color:#F00;">0</span> 元<br><input type="button" value="关　闭" onClick="dw.style.display='none';clearTimeout(wid);"></div>
</div>
<!--返回-->
<div id="dy" style="width:278px;position:absolute;left:20px;top:70px;background-color:#FFF;border:1px solid #F90;z-index:66;display:none;">
  <div style="padding:20px;text-align:center;">输入员工密码返回 [<span id="kui"></span>]<br><br><input type="password" id="iny" style="width:160px;"><br><br><input type="button" value="取消" onClick="dy.style.display='none';">　　<input type="button" value="确定" onClick="kbk();"></div>
</div>
</body>
</html>
<?
}
?>
