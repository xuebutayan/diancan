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
	if(strpos("CD",$r[2])>-1){
		$c=Rnd();
		echo "OK".$r[0]."|".$r[1]."|".$r[2]."|".$c;
		mysql_query("Update SCY Set Nd=Now(),Ni='".$c."' Where Nu='".$a."'");
	}
	break;

	case "lst":
	$q=mysql_query("Select A.ID,A.Cn,A.Cs,A.St,B.Nm,A.I2,A.I3,A.Td,C.Bz From OCK As A,SCT As B,ODR As C Where B.ID=A.Ti And C.ID=A.Oi And A.St In (6,7,8,9) Order By A.ID Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5]."|".$r[6]."|".$r[7]."|".$r[8];
	}
	break;

	case "cfs":
	$a=Req("a");
	$b=Req("b");
	$c=(int)Req("c");
	$q=mysql_query("Select Ti,Oi,I2,I3 From OCK Where ID=".$a);
	if(mysql_num_rows($q)>0){
		$r=mysql_fetch_array($q);
		$d=$r[0];$e=$r[1];
		$f=$b=="6"?",I2=0":"";
		if($b=="7"||$b=="8" ){if($r[2]==0||$r[2]==$c){$f=",I2=".$c;}else die('E');}
		if($b=="9"||$b=="10"){if($r[3]==0||$r[3]==$c){$f=",I3=".$c;}else die('E');}
		mysql_query("Update OCK Set St=".$b.$f." Where ID=".$a);
		mysql_query("Update SCT Set Ni=".$b." Where ID=".$d." And Ni In (6,7,8,9)");
		mysql_query("Update ODR Set St=".$b." Where ID=".$e." And St In (6,7,8,9)");
		echo "OK";
	}
	break;

	case "cft":
	$a=Req("a");
	$b=Req("b");
	$c=Req("c");
	$d=mysql_result(mysql_query("Select Ti From OCK Where ID=".$a),0);
	$e="";
	if($b=="6"||$b=="7")$e=",I2=".$c;
	if($b=="8"||$b=="9")$e=",I3=".$c;
	mysql_query("Update OCK Set Td=1".$e.",Bz='厨房退单' Where ID=".$a);
	mysql_query("Update SCT Set Ni=5 Where ID=".$d." And Ni In (6,7,8,9)");
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
<title><? echo $p;?> - 厨房送餐</title>
<style type="text/css">
div {font-size:16px;}
input[type=text],input[type=password] {font-size:16px;border:1px solid #F90;padding:5px;text-align:center;}
input[type=button]{font-size:18px; background-color:#F90;color:#FFF;border:0px;padding:5px 10px;border:1px solid #FFF;}
.dn1{float:left;width:78px;line-height:28px;height:28px;text-align:center;border:1px solid #F90;background-color:#F90;color:#FFF;}
.dn2{float:left;width:78px;line-height:28px;height:28px;text-align:center;border:1px solid #F90;border-bottom:0px;}
.dl1{height:40px;clear:both;border:1px dashed #F90; background-color:#FFF9F3;margin:10px 5px;}
.dl2{float:left;line-height:40px;width:80px;text-align:center;}
.dl3{float:left;line-height:40px;width:180px;text-align:center;}
.dl4{float:left;line-height:40px;width:40px;text-align:center;}
.dt{background-color:#F90;color:#FFF;line-height:32px;text-align:center;}
.dc{padding:10px 20px;line-height:30px;}
.db{height:40px;text-align:center;}
</style>
<script type="text/javascript">
function $(o){return document.getElementById(o);}
function xml(){var x;try{x=new ActiveXObject('Msxml2.XMLHTTP');}catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(f){x=false;}}if(!x&&typeof XMLHttpRequest!='undefined'){x=new XMLHttpRequest();}xvr=true;return x;}
function sck(n,v){var d=new Date();d.setTime(d.getTime()+1080000000);document.cookie=n+'='+escape(v)+'; expires='+d.toGMTString();}
function gck(n){var c=document.cookie.split("; ");for(var i=0;i<c.length;i++){var t=c[i].split("=");if(t[0]==n)return unescape(t[1]);}return '';}
function atwh(){var pw=parseInt(document.documentElement.clientWidth);var ph=parseInt(document.documentElement.clientHeight);if(pw>320)pw=320;if(ph>560)ph=560;dl.style.width=pw+'px';dl.style.height=ph+'px';dv.style.width=pw+'px';dv.style.height=ph+'px';dt.style.width=pw-2+'px';dt.style.height=ph-82+'px';if(gck('QWCTCkUat')=='1'){usr.value=gck('QWCTCkUsr');upw.value=gck('QWCTCkUpw');uat.checked=true;}}
var uri='cooker.php?';
var uid=0;
var rid='';
var sid='';
var tid,mid,nid,kid=0;
function lgn(){var x=xml();x.open('Get',uri+'x=lgn'+'&a='+usr.value+'&b='+upw.value+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t.substr(0,2)=='OK'){if(uat.checked){sck('QWCTCkUsr',usr.value);sck('QWCTCkUpw',upw.value);sck('QWCTCkUat','1');}else sck('QWCTCkUat','0');dl.style.display='none';dv.style.display='block';var w=t.substr(2).split('|');uid=w[0];cker.innerText=w[1];sid=w[3];rid=w[2];rid.indexOf('C')==-1?list(8):list(6);ds6.style.display=rid.indexOf('C')==-1?'none':'block';ds7.style.display=ds6.style.display;ds8.style.display=rid.indexOf('D')==-1?'none':'block';ds9.style.display=ds8.style.display;}else{alert('用户名或密码错误！');}}};x.send();}
function list(n){if(rid.indexOf('C')==-1&&(n==6||n==7)){alert('无权限！');return;}if(rid.indexOf('D')==-1&&(n==8||n==9)){alert('无权限！');return;}clearTimeout(tid);ds6.className='dn1';ds7.className='dn1';ds8.className='dn1';ds9.className='dn1';$('ds'+n).className='dn2';var x=xml();x.open('Get',uri+'x=lst&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.split('$');var s='';for(var i=1;i<t.length;i++){t[i]=t[i].split('|');if(t[i][7]!='2'&&String(n)==t[i][3]&&(((n==7||n==9)&&t[i][5]==uid)||n==6||n==8))s+='<div class="dl1" onclick="cfsc('+n+','+t[i][0]+',\''+t[i].join('|')+'\');"><div class="dl2">'+t[i][4]+'</div><div class="dl3">'+t[i][1]+'</div><div class="dl4">'+t[i][2]+['','[T]','[T]'][t[i][7]]+'</div></div>';}var d=new Date();dt.innerHTML=(s==''?'当前没有点单！':s)+'<div style="text-align:center;">更新于 '+d.getHours()+':'+String(100+d.getMinutes()).substr(1,2)+':'+String(100+d.getSeconds()).substr(1,2)+'</div>';tid=setTimeout('list('+n+')',10000);if(kid!=t[t.length-1][0]&&t.length>1){kid=t[t.length-1][0];cfsy.play();}}};x.send();}
function cfsc(n,m,t){t=t.split('|');$('dh'+n).innerHTML='桌号：'+t[4]+'<br>菜品：'+t[1]+'<br>数量：'+t[2]+' '+['','[已申请退单]','[已同意退单]'][t[7]]+'<br>要求：'+t[8];mid=m;nid=n;$('dc'+n).style.display='block';}
function cfjs(m){var x=xml();x.open('Get',uri+'x=cfs&s='+sid+'&a='+mid+'&b='+m+'&c='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){$('dc'+nid).style.display='none';clearTimeout(tid);list(nid);}else{alert('接收失败，可能已被别的厨师接收！');}}};x.send();}
function cftd(m){if(!confirm('确认退单？？'))return;var x=xml();x.open('Get',uri+'x=cft&s='+sid+'&a='+mid+'&b='+m+'&c='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){$('dc'+nid).style.display='none';clearTimeout(tid);list(nid);}else{alert('退单失败，请重试！');}}};x.send();}
</script>
</head>
<body style="margin:0px;padding:0px;font-size:14px;text-align:left;" onLoad="atwh();">
<audio id="cfsy" src="i/m.mp3"></audio>
<!--登陆层-->
<div id="dl" style="width:320px;text-align:center;font-size:18px;background-color:#F90;color:#FFF;"><br><br><img src="i/i.jpg" width="120" height="120"><br><? echo $p;?><br>厨房送餐登录<br><br>用户名： <input type="text" size="12" id="usr"><br>密　码： <input type="password" size="12" id="upw"><br><input type="checkbox" id="uat"> 自动 <input type="button" value="登录" onClick="lgn();"></div>
<!--处理层-->
<div id="dc6" style="width:278px;position:absolute;left:20px;top:100px;background-color:#FFF;border:1px solid #F90;display:none;">
  <div class="dt">点单处理</div>
  <div id="dh6" class="dc"></div>
  <div class="db"><input type="button" value="取消" onClick="dc6.style.display='none';"> <input type="button" value="退单" onClick="cftd(6);"> <input type="button" value="完成" onClick="cfjs(8);"> <input type="button" value="接收" onClick="cfjs(7);"></div>
</div>
<div id="dc7" style="width:278px;position:absolute;left:20px;top:100px;background-color:#FFF;border:1px solid #F90;display:none;">
  <div class="dt">点单处理</div>
  <div id="dh7" class="dc"></div>
  <div class="db"><input type="button" value="取消" onClick="dc7.style.display='none';"> <input type="button" value="退单" onClick="cftd(7);"> <input type="button" value="撤单" onClick="cfjs(6);"> <input type="button" value="完成" onClick="cfjs(8);"></div>
</div>
<div id="dc8" style="width:278px;position:absolute;left:20px;top:100px;background-color:#FFF;border:1px solid #F90;display:none;">
  <div class="dt">点单处理</div>
  <div id="dh8" class="dc"></div>
  <div class="db"><input type="button" value="取消" onClick="dc8.style.display='none';"> <input type="button" value="退单" onClick="cftd(8);"> <input type="button" value="完成" onClick="cfjs(10);"> <input type="button" value="送餐" onClick="cfjs(9);"></div>
</div>
<div id="dc9" style="width:278px;position:absolute;left:20px;top:100px;background-color:#FFF;border:1px solid #F90;display:none;">
  <div class="dt">点单处理</div>
  <div id="dh9" class="dc"></div>
  <div class="db"><input type="button" value="取消" onClick="dc9.style.display='none';"> <input type="button" value="退单" onClick="cftd(9);"> <input type="button" value="完成" onClick="cfjs(10);"></div>
</div>
<!--界面层-->
<div id="dv" style="display:none;">
  <div style="width:320px;height:50px;top:0px;left:0px;background-color:#666;">
    <div style="float:left;margin:3px;width:44px;height:44px;"><img src="i/i.jpg" width="44" height="44"></div>
    <div style="float:left;color:#FFF;line-height:20px;margin-top:6px;font-size:18px;"><? echo $p;?><br><span style="font-size:14px;" id="cker"></span></div>
    <div style="float:right;width:60px;line-height:30px;text-align:center;font-size:16px;margin:10px;background-color:#F90;color:#FFF;" onClick="sck('QWCTCkUpw','');location.reload();">退出</div>
  </div>
  <div id="dn" style="clear:both;">
    <div id="ds6" class="dn2" onClick="list(6);">待接收</div>
    <div id="ds7" class="dn1" onClick="list(7);">已接收</div>
    <div id="ds8" class="dn1" onClick="list(8);">待送餐</div>
    <div id="ds9" class="dn1" onClick="list(9);">已送餐</div>
  </div>
  <div id="dt" style="clear:both;border:1px solid #F90;overflow:auto;"></div>
</div>
</body>
</html>
<?
}
?>