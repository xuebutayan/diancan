<?
require "config.php";
$x=isset($_GET["x"])?$_GET["x"]:"";
$s=isset($_GET["s"])?$_GET["s"]:"";if($s=="")$s="0";
if($x!="lgn"&&$x!="u"&&$x!=""){
	if(mysql_num_rows(mysql_query("Select ID From SCY Where Ni='".$s."'"))==0)die();
}
switch($x){
	case "u":
	echo '<html><head></head><body style="margin:2px;padding:0px;"><form action="index.php?x=v&s='.$s.'&a='.Req("a").'" name="frm" method="post" enctype="multipart/form-data"><div style="position:absolute;top:0px;left:0px;"><input type="file" name="file" style="width:80px;height:24px;opacity:0;filter:Alpha(Opacity=0);" onChange="if(\'.jpg,.png,.gif,.bmp,jpeg\'.indexOf(this.value.toLowerCase().substr(this.value.length-4))==-1){alert(\'请选择图片文件！\');document.frm.reset();}else{txt.innerText=\'上传中...\';document.frm.submit();}" /></div><div id="txt" style="width:80px;line-height:24px;text-align:center;background-color:#09F;color:#FFF;font-size:14px;">选择文件</div></form></body></html>';
	break;

	case "v":
	$a=Req("a");
	if($_FILES["file"]["error"]==0){
		if(file_exists("i/z.jpg"))unlink("i/z.jpg");
		move_uploaded_file($_FILES["file"]["tmp_name"],"i/z.jpg");
	}
	echo "<script>parent.svup('".$a."');location.href='index.php?x=u&s=".$s."&a=".$a."';</script>";
	break;

	case "lgn"://登陆
	$a=Req("a");
	$b=Req("b");
	$q=mysql_query($test="Select ID,Nn,Nr,Ni From SCY Where DD=0 And Nu='".$a."' And Np='".substr(md5($b),8,16)."'");
	if(mysql_num_rows($q)==0)die('Err');
	$r=mysql_fetch_array($q);
	if(strpos("ABESABSAESAEBSAS",$r[2])>-1){
		$c=Rnd();
		echo "OK|".$r[0]."|".$r[1]."|".$r[2]."|".$c;
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
	//echo "<script>window.location='http://www.1000vw.com/Qw/pay.php?t=".$e."|".md5($e.$OpPK)."';</scri pt>";
	break;

	case "ewm"://二维码
	$a=explode("|",mysql_result(mysql_query("Select Ni From CFG Where ID=1"),0));
	$a_u = $a[3];
	$a=urlencode($a[3]);
	echo '<html><head><title>二维码</title><style>.a{float:left;width:300px;text-align:center;margin:10px;}</style></head><body><div class="a"><img src="http://www.liantu.com/api.php?el=M&m=10&w=281&text='.$a.'waiter%2Ephp"><br />店员二维码</div><div class="a"><img src="http://www.liantu.com/api.php?el=M&m=10&w=281&text='.$a.'cooker%2Ephp"><br />厨房二维码</div>';
	$q=mysql_query("Select ID,Nm From SCT Order By ID Asc");
	while($r=mysql_fetch_array($q)){
		$str = $a.'mobile%2Ephp%3Ft%3D'.substr(md5($OpID.$r["ID"]),0,8);
		$a_u = urldecode($str);
		echo '<div class="a"><img src="http://www.liantu.com/api.php?el=L&m=10&w=281&text='.$str.'"><br />'.$r["Nm"].$a_u.'</div>';
	}
	echo '</body></html>';
	break;

	case "ljb"://设置读取
	$r=mysql_fetch_array(mysql_query("Select Nm,Ni,Nz,Np From CFG Where ID=1"));
	echo $r[0]."|".$r[1]."@@".$r[2]."@@".$r[3];
	break;

	case "sjb"://设置保存
	mysql_query("Update CFG Set Nm='".Req("a")."',Ni='".Req("b")."' Where ID=1");
	if(Req("c")=="1"&&file_exists("i/z.jpg")){
		copy("i/z.jpg","i/i.jpg");
		unlink("i/z.jpg");
	}
	echo "OK";
	break;

	case "szk"://折扣保存
	mysql_query("Update CFG Set Nz='".Req("a")."'");
	echo "OK";
	break;

	case "sdy"://打印保存
	mysql_query("Update CFG Set Np='".Req("a")."'");
	if(Req("b")=="1"&&file_exists("i/z.jpg")){
		copy("i/z.jpg","i/p.jpg");
		unlink("i/z.jpg");
	}
	echo "OK";
	break;

	case "tdy"://打印测试
	$a=explode("|",Req("a"));
	$b=array("none","block");
	echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>#dyyl{width:'.$a[1].'mm;margin:'.$a[2].'mm 0mm;line-height:20px;font-family:宋体;font-size:12px;}#dyyl div{font-family:宋体;font-size:12px;}.dyrl div{float:right;width:32px;text-align:right;}</style></head><body><div id="dyyl"><img src="i/p.jpg" style="width:'.$a[1].'mm;display:'.$b[$a[9]].';" /><div id="dyt1" style="text-align:center;font-size:'.$a[4].'px;line-height:'.((int)$a[4]+8).'px;">'.$a[3].'</div><div style="float:left;">单号：8888<br />桌号：8</div><div style="float:right;">下单员：88<br />收银员：8</div><div style="clear:both;"></div>下单时间：2015-1-1 12:00:00<br />结账时间：2015-1-1 13:00:00<div class="dyrl"><div>金额</div><div>数量</div><div>单价</div>品名</div><hr /><div class="dyrl"><div>88</div><div>1</div><div>88</div>油焖大虾</div><div class="dyrl"><div>18</div><div>1</div><div>18</div>鱼香肉丝</div><div class="dyrl"><div>18</div><div>1</div><div>18</div>宫保鸡丁</div><div class="dyrl"><div>12</div><div>1</div><div>12</div>麻辣豆腐</div><div class="dyrl"><div>20</div><div>4</div><div>5</div>青岛啤酒</div><hr /><div style="text-align:left;">合计： 156 元<br />折扣： 限时九折 会员九折<br />实收： <span style="font-size:18px;font-family:黑体;">126</span> 元[现金支付]</div><div id="dyt2" style="text-align:center;clear:both;font-size:'.$a[6].'px;line-height:'.((int)$a[6]+8).'px;">'.$a[5].'</div><div id="dyt3" style="text-align:left;font-size:'.$a[8].'px;line-height:'.((int)$a[8]+8).'px;">'.$a[7].'</div></div></body></html>';
	break;

	case "chy"://会员充值
	$a=Req("a");
	$m=Req("m");
	$n=Req("n");
	$f=curl_init();
	curl_setopt($f,CURLOPT_TIMEOUT,30);
	curl_setopt($f,CURLOPT_RETURNTRANSFER,TRUE);
	curl_setopt($f,CURLOPT_URL,"http://www.1000vw.com/Qw/mbr.php?x=chy&m=".$m."&n=".$n."&a=".$a);
	$e=curl_exec($f);
	curl_close($f);
	if($e=="OK"){
		$a=explode("|",$a);
		$b=explode("-",$a[3]);
		mysql_query("Insert Into UCZ (Ni,Nc,Oi,Yi,Ps,Pc,Pd) Values (".$a[0].",'".$a[1]."',0,".$b[1].",".$a[4].",".$a[5].",Now())");
		echo "OK";
	}
	break;

	case "phy"://会充打印
	$b=Req("a");
	$c=Req("b");
	$r=mysql_fetch_array(mysql_query("Select Np From CFG Where ID=1"));
	$a=explode("|",$r[0]);
	$d=array("none","block");
	if($c==""){
		$r=mysql_fetch_array(mysql_query("Select * From UCZ Where Oi=0 And Ni=".$b." Order By ID Desc Limit 1"));
	}else $r=mysql_fetch_array(mysql_query("Select * From UCZ Where ID=".$c));
	echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>#dyyl{width:'.$a[1].'mm;margin:'.$a[2].'mm 0mm;line-height:20px;font-family:宋体;font-size:12px;}#dyyl div{font-family:宋体;font-size:12px;}.dyrl div{float:right;width:32px;text-align:right;}</style></head><body><div id="dyyl"><img src="i/p.jpg" style="width:'.$a[1].'mm;display:'.$d[$a[9]].';"><div style="text-align:center;font-size:'.$a[4].'px;line-height:'.((int)$a[4]+8).'px;">'.$a[3].' - 会员充值</div>会员ＩＤ：'.$r["Ni"].'<br />会员卡号：'.$r["Nc"].'<br />操作员工：'.$r["Yi"].'<br />支付金额：'.($r["Ps"]/100).'<br />充值金额：'.($r["Pc"]/100).'<br />充值时间：'.$r["Pd"].'<div style="text-align:center;clear:both;font-size:'.$a[6].'px;line-height:'.((int)$a[6]+8).'px;">'.$a[5].'</div><div style="text-align:left;font-size:'.$a[8].'px;line-height:'.((int)$a[8]+8).'px;">'.$a[7].'</div></div></body></html>';
	break;

	case "lyg"://员工列表
	$q=mysql_query("Select ID,Nm,Nn,Nu,Np,Nd,Nr From SCY Where DD=0 Order By ID Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5]."|".$r[6];
	}
	break;

	case "syg"://员工保存
	$a=explode("|",Req("a"));
	if($a[4]=='')$a[4]="123456";
	$b=substr(md5($a[4]),8,16);
	if($a[0]=="0"){
		mysql_query("Insert Into SCY (Nm,Nn,Nu,Np,Nd,Nr,Ni,DD) Values ('".$a[1]."','".$a[2]."','".$a[3]."','".$b."',Now(),'".$a[6]."','',0)");
	}else{
		$c=mysql_result(mysql_query("Select Np From SCY Where ID=".$a[0]),0);
		mysql_query("Update SCY Set Nm='".$a[1]."',Nn='".$a[2]."',Nu='".$a[3]."',Nr='".$a[6]."',Np='".($c==$a[4]?$a[4]:$b)."' Where ID=".$a[0]);
	}
	echo "OK";
	break;

	case "dyg"://员工删除
	mysql_query("Update SCY Set DD=1 Where ID=".Req("a"));
	echo "OK";
	break;

	case "lcz"://餐桌读取
	$q=mysql_query("Select ID,Nm,No,Ns,Ni,Oi From SCT Where DD=0 Order By No Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5]."|".substr(md5($OpID.$r[0]),0,8);
	}
	break;

	case "scz"://餐桌保存
	$a=explode("|",Req("a"));
	if($a[0]=="0"){
		mysql_query("Insert Into SCT (Nm,No,Ns,Ni,Oi,DD) Values ('".$a[1]."',".$a[2].",".$a[3].",0,0,0)");
	}else{
		mysql_query("Update SCT Set Nm='".$a[1]."',No=".$a[2].",Ns=".$a[3]." Where ID=".$a[0]);
	}
	echo "OK";
	break;

	case "dcz"://餐桌删除
	mysql_query("Update SCT Set DD=1 Where ID=".Req("a"));
	echo "OK";
	break;

	case "llb"://类别列表
	$q=mysql_query("Select ID,Nm,No,Nh,Ns From SCL Order By No Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4];
	}
	break;

	case "slb"://类别保存
	$a=explode("|",Req("a"));
	$q=mysql_query("Select * From SCL Where ID=".$a[0]);
	if(mysql_num_rows($q)==0){
		mysql_query("Insert Into SCL (Nm,No,Nh,Ns) Values ('".$a[1]."',".$a[2].",".$a[3].",0)");
	}else{
		mysql_query("Update SCL Set Nm='".$a[1]."',No=".$a[2].",Nh=".$a[3]." Where ID=".$a[0]);
	}
	echo "OK";
	break;

	case "dlb"://类别删除
	mysql_query("Delete From SCL Where ID=".Req("a"));
	echo "OK";
	break;

	case "lcd"://菜品列表
	$q=mysql_query("Select ID,Nm,Nt,No,Ni,Np,Ns,Nh From SCD Where DD=0 Order By No Asc");
	while($r=mysql_fetch_array($q)){
		echo "\$".$r[0]."|".$r[1]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5]."|".$r[6]."|".$r[7];
	}
	break;

	case "scd"://菜品保存
	$a=explode("|",Req("a"));
	$q=mysql_query("Select Nt,Np From SCD Where ID=".$a[0]);
	if(mysql_num_rows($q)==0){
		mysql_query("Insert Into SCD (Nm,Nt,No,Ni,Np,Ns,Nh,DD) Values ('".$a[1]."','".$a[2]."',".$a[3].",'".$a[4]."',".$a[5].",".$a[6].",".$a[7].",0)");
		$a[0]=mysql_insert_id();
	}else{
		$r=mysql_fetch_array($q);
		$b=($r[1]==1||$a[5]=="1")?1:0;
		mysql_query("Update SCL Set Ns=Ns-1 Where ID In (0".$r[0]."0)");
		mysql_query("Update SCD Set Nm='".$a[1]."',Nt='".$a[2]."',No=".$a[3].",Ni='".$a[4]."',Np=".$b.",Ns=".$a[6].",Nh=".$a[7]." Where ID=".$a[0]);
	}
	mysql_query("Update SCL Set Ns=Ns+1 Where ID In (0".$a[2]."0)");
	if($a[5]=="1"){
		if(file_exists("i/z.jpg")){
			copy("i/z.jpg","i/c".$a[0].".jpg");
			unlink("i/z.jpg");
		}
	}
	echo "OK";
	break;

	case "dcd"://菜品删除
	$a=Req("a");
	if(file_exists("i/".$a.".jpg"))unlink("i/".$a.".jpg");
	$b=mysql_result(mysql_query("Select Nt From SCD Where ID=".$a),0);
	mysql_query("Update SCL Set Ns=Ns-1 Where ID In (0".$b."0)");
	mysql_query("Update SCD Set DD=1,Nt=',0,' Where ID=".$a);
	echo "OK";
	break;

	case "dtb"://点单读取
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

	case "drz"://点单入座
	mysql_query("Update SCT Set Ni=1 Where Ni<2 And ID=".Req("a"));
	echo "OK";
	break;

	case "dxd"://点单下单
	$a=explode("|",Req("a"));
	if($a[1]=="")$a[1]="0";
	if($a[1]=="0"){
		mysql_query("Insert Into ODR (Ti,Yi,Pz,Ps,Pt,St,Dt,I4,Ds,Bz) Values (".$a[0].",".$a[6].",".$a[4].",0,0,3,Now(),0,Now(),'".$a[5]."')");
		$b=mysql_insert_id();
	}else{
		mysql_query("Update ODR Set Ti=".$a[0].",Yi=".$a[6].",Pz=Pz+".$a[4].",St=3,Bz='".$a[5]."' Where ID=".$a[1]);
		$b=$a[1];
	}
	$c=explode(",",$a[2]);
	$d=explode(",",$a[3]);
	for($i=0;$i<count($c);$i++){
		$r=mysql_fetch_array(mysql_query("Select Nm,Ns From SCD Where ID=".$c[$i]));
		$e=$r[0];$f=$r[1];
		mysql_query("Insert Into OCK (Oi,Ti,Ci,Cn,Cp,Cs,St,I1,I2,I3,Dt,Td,Bz) Values (".$b.",".$a[0].",".$c[$i].",'".$e."',".$f.",".$d[$i].",3,".$a[6].",0,0,Now(),0,'')");
	}
	mysql_query("Update SCT Set Ni=3,Oi=".$b." Where ID=".$a[0]);
	echo "OK";
	break;

	case "dcf"://点单入厨
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

	case "dsl"://点单改数
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

	case "dtd"://点单退单
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

	case "dhz"://更换餐桌
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

	case "djk"://结账延迟
	mysql_query("Update SCT Set Ni=10 Where Ni<12 And ID=".Req("a"));
	echo "OK";
	break;

	case "dfk"://点单付款
	$a=explode("|",Req("a"));
	$b=Req("b");
	$a[2]=(int)$a[2];
	$a[5]=(int)$a[5];
	if($a[3]=="3"){
		$f=curl_init();
		curl_setopt($f,CURLOPT_TIMEOUT,30);
		curl_setopt($f,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($f,CURLOPT_URL,"http://www.1000vw.com/Qw/mbr.php?x=khy&m=".$a[8]."&n=".$a[9]."&a=".$a[6]."&b=".$a[7]."&c=".$a[2]."&d=".$a[1]."&e=".$b);
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

	case "dqt"://点单清台
	$a=Req("a");
	$b=Req("b");
	if($b=="")$b=0;
	mysql_query("Update SCT Set Ni=0,Oi=0 Where Ni In (1,12) And ID=".$a);
	mysql_query("Update ODR Set St=12 Where ID=".$b);
	mysql_query("Update OCK Set St=12 Where Oi=".$b." And Ti=".$a);
	echo "OK";
	break;

	case "dpt"://点单打印
	$b=Req("b");
	$a=explode("|",mysql_result(mysql_query("Select Np From CFG Where ID=1"),0));
	$q=mysql_query("Select Cp,Cs,Cn From OCK Where Td<2 And Oi=".$b);
	$c="";
	while($r=mysql_fetch_array($q)){
		$c=$c.'<div class="dyrl"><div>'.($r[0]*$r[1]/100).'</div><div>'.$r[1].'</div><div>'.($r[0]/100).'</div>'.$r[2].'</div>';
	}
	$d=array("none","block");
	$e=array("现金","团购券","刷卡","会员卡","微信","支付宝");
	$r=mysql_fetch_array(mysql_query("Select ID,Yi,I4,Dt,Ds,Pz,Bz,Ps,Pt,Ti From ODR Where ID=".$b));
	$f=mysql_result(mysql_query("Select Nm From SCT Where ID=".$r[9]),0);
	echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>#dyyl{width:'.$a[1].'mm;margin:'.$a[2].'mm 0mm;line-height:20px;font-family:宋体;font-size:12px;}#dyyl div{font-family:宋体;font-size:12px;}.dyrl div{float:right;width:32px;text-align:right;}</style></head><body><div id="dyyl"><img src="i/p.jpg" style="width:'.$a[1].'mm;display:'.$d[$a[9]].';"><div id="dyt1" style="text-align:center;font-size:'.$a[4].'px;line-height:'.((float)$a[4]+8).'px;">'.$a[3].'</div><div style="float:left;">单号：'.$r[0].'<br />桌号：'.$f.'</div><div style="float:right;">服务员：'.$r[1].'<br />收银员：'.$r[2].'</div><div style="clear:both;"></div>下单时间：'.$r[3].'<br />结账时间：'.$r[4].'<div class="dyrl"><div>金额</div><div>数量</div><div>单价</div>品名</div><hr />'.$c.'<hr /><div style="text-align:left;">合计： '.($r[5]/100).' 元<br />备注： '.$r[6].'<br />实收： <span style="font-size:18px;font-family:黑体;">'.($r[7]/100).'</span> 元['.$e[$r[8]].'支付]</div><div id="dyt2" style="text-align:center;clear:both;font-size:'.$a[6].'px;line-height:'.((float)$a[6]+8).'px;">'.$a[5].'</div><div id="dyt3" style="text-align:left;font-size:'.$a[8].'px;line-height:'.((float)$a[8]+8).'px;">'.$a[7].'</div></div>';
	break;

	case "cnt"://读取统计
	$a=Req("a");
	$b=Req("b");
	$a=date("Y-m-d",strtotime($a));
	$b=date("Y-m-d",strtotime("+1 Day",strtotime($b)));
	echo (round(strtotime($b)-strtotime($a))/86400)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Count(ID) As N From ODR Where St=12 And Ds>'".$a."' And Ds<'".$b."'"));echo $r[0]."|";
	$r=mysql_fetch_array(mysql_query("Select Count(A.ID) As N From OCK As A,ODR As B Where B.St=12 And A.Td<2 And A.Oi=B.ID And B.Ds>'".$a."' And B.Ds<'".$b."'"));echo $r[0]."|";
	$r=mysql_fetch_array(mysql_query("Select Count(A.ID) As N From OCK As A,ODR As B Where B.St=12 And A.Td=2 And A.Oi=B.ID And B.Ds>'".$a."' And B.Ds<'".$b."'"));echo $r[0]."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=0 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=1 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=2 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=3 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=4 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=5 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From UCZ Where Oi=0 And Pd>'".$a."' And Pd<'".$b."'"));echo ($r[0]/100)."|";
	$d="";
	$q=mysql_query("Select Ci,Cn,Count(ID) As N From OCK Where Dt>'".$a."' And Dt<'".$b."' Group By Ci,Cn Order By Count(ID) Desc Limit 3");
	while($r=mysql_fetch_array($q)){
		$d=$d."\$".$r["Cn"]."#".$r["N"];
	}
	$d=$d."|";
	$q=mysql_query("Select A.Yi,B.Nn,Count(A.ID) As N From ODR As A,SCY As B Where B.ID=A.Yi And A.Dt>'".$a."' And A.Dt<'".$b."' Group By A.Yi,B.Nn Order By Count(A.ID) Desc Limit 3");
	while($r=mysql_fetch_array($q)){
		$d=$d."\$".$r["Nn"]."#".$r["N"];
	}
	$c=$b-$a;
	$b=$a;
	$a=$a-$c;
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Count(ID) As N From ODR Where St=12 And Ds>'".$a."' And Ds<'".$b."'"));echo $r[0]."|";
	$r=mysql_fetch_array(mysql_query("Select Count(A.ID) As N From OCK As A,ODR As B Where B.St=12 And A.Td<2 And A.Oi=B.ID And B.Ds>'".$a."' And B.Ds<'".$b."'"));echo $r[0]."|";
	$r=mysql_fetch_array(mysql_query("Select Count(A.ID) As N From OCK As A,ODR As B Where B.St=12 And A.Td=2 And A.Oi=B.ID And B.Ds>'".$a."' And B.Ds<'".$b."'"));echo $r[0]."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=0 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=1 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=2 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=3 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=4 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From ODR Where St=12 And Pt=5 And Ds>'".$a."' And Ds<'".$b."'"));echo ($r[0]/100)."|";
	$r=mysql_fetch_array(mysql_query("Select Sum(Ps) As N From UCZ Where Oi=0 And Pd>'".$a."' And Pd<'".$b."'"));echo ($r[0]/100)."|";
	echo $d;
	break;

	case "cns"://统计报表
	$a=Req("a");
	$b=Req("b");
	$a=date("Y-m-d",strtotime($a));
	$b=date("Y-m-d",strtotime("+1 Day",strtotime($b)));
	$r=mysql_fetch_array(mysql_query("Select Np From CFG Where ID=1"));
	$d=explode("|",$r[0]);
	echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>统计报表</title><style>.a{height:30px;line-height:30px;border-bottom:1px dashed #CCC;}.a1{float:left;width:72px;height:30px;}.a2{float:left;width:120px;height:30px;}.a3{float:left;width:240px;height:30px;}.a4{float:left;width:106px;height:30px;}.a5{float:left;width:48px;height:30px;}.b{width:1000px;background-color:#EEE;font-size:14px;line-height:30px;margin:20px auto 0px auto;}a{text-decoration:none;color:#09F;}</style><script>function dp(n,m){var o=document.getElementById(navigator.userAgent.indexOf(\'MSIE\')>=0?\'LdpIE\':\'LdpGC\');if(typeof(o.VERSION)==\'undefined\'){alert(\'未安装打印控件！\');return;}o.PRINT_INIT(\'Print:\'+n);o.SET_PRINT_PAGESIZE(3,'.($d[0]*10).','.($d[2]*10).',\'CreateCustomPage\');o.ADD_PRINT_HTM(0,0,'.($d[1]*10).',500,\'URL:index.php?x=\'+(m?\'phy\':\'dpt\')+\'&s='.$s.'&b=\'+n);o.PREVIEW();}</script></head><body style="text-align:center;font-size:12px;font-family:宋体;"><object id="LdpIE" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width="0" height="0"><embed id="LdpGC" type="application/x-print-lodop" width="0" height="0"></embed></object><div style="width:1000px;margin:20px auto;"><div style="font-size:20px;">统计报表　['.$a.' - '.(date("Y-m-d",strtotime("-1 Day",strtotime($b)))).']</div><div class="b"><a name="dd"></a>点单详情</div>';
	$c=array("现金","团购券","刷卡","会员卡","微信","支付宝");
	echo '<div class="a"><div class="a1">单号</div><div class="a1">桌号</div><div class="a1">应付</div><div class="a1">实收</div><div class="a2">下单时间</div><div class="a1">结账时间</div><div class="a1">付款方式</div><div class="a3">备注信息</div><div class="a1">服务员</div><div class="a1">收银员</div><div class="a5">打单</div></div>';
	$q=mysql_query("Select A.*,B.Nm From ODR As A,SCT As B Where B.ID=A.Ti And A.St=12 And A.Ds>'".$a."' And A.Ds<'".$b."' Order By A.ID Desc");
	while($r=mysql_fetch_array($q))echo '<div class="a"><div class="a1">'.$r["ID"].'</div><div class="a1">'.$r["Nm"].'</div><div class="a1">'.($r["Pz"]/100).'</div><div class="a1">'.($r["Ps"]/100).'</div><div class="a2">'.$r["Dt"].'</div><div class="a1">'.date("H:i:s",strtotime($r["Ds"])).'</div><div class="a1">'.$c[$r["Pt"]].'支付</div><div class="a3">'.$r["Bz"].'</div><div class="a1">'.$r["Yi"].'</div><div class="a1">'.$r["I4"].'</div><div class="a5"><a href="javascript:void();" onclick="dp('.$r["ID"].');">打单</a></div></div>';
	echo '<div class="b"><a name="dc"></a>点菜详情</div><div class="a"><div class="a1">单号</div><div class="a1">桌号</div><div class="a3">菜品</div><div class="a1">价格</div><div class="a1">数量</div><div class="a1">点单</div><div class="a1">厨师</div><div class="a1">传菜</div><div class="a3">下单时间</div></div>';
	$q=mysql_query("Select A.*,B.Nm From OCK As A,SCT As B Where B.ID=A.Ti And A.Td=0 And A.Dt>'".$a."' And A.Dt<'".$b."' Order By A.ID Desc");
	while($r=mysql_fetch_array($q))echo '<div class="a"><div class="a1">'.$r["Oi"].'</div><div class="a1">'.$r["Nm"].'</div><div class="a3">'.$r["Cn"].'</div><div class="a1">'.($r["Cp"]/100).'</div><div class="a1">'.$r["Cs"].'</div><div class="a1">'.$r["I1"].'</div><div class="a1">'.$r["I2"].'</div><div class="a1">'.$r["I3"].'</div><div class="a3">'.$r["Dt"].'</div></div>';
	echo '<div class="b"><a name="cz"></a>充值详情</div><div class="a"><div class="a3">卡号</div><div class="a2">单号</div><div class="a2">员工</div><div class="a4">实付金额</div><div class="a4">充值/消费金额</div><div class="a3">时间</div><div class="a5">打单</div></div>';
	$q=mysql_query("Select * From UCZ Where Pd>'".$a."' And Pd<'".$b."' Order By ID Desc");
	while($r=mysql_fetch_array($q))echo '<div class="a"><div class="a3">'.$r["Nc"].'</div><div class="a2">'.$r["Oi"].'</div><div class="a2">'.$r["Yi"].'</div><div class="a4">'.($r["Ps"]/100).'</div><div class="a4">'.($r["Pc"]/100).'</div><div class="a3">'.$r["Pd"].'</div><div class="a5"><a href="javascript:void();" onclick="if('.$r["Pc"].'>0)dp('.$r["ID"].',1);">打单</a></div></div>';
	echo '<div class="b"><a name="td"></a>退单详情</div><div class="a"><div class="a1">单号</div><div class="a1">桌号</div><div class="a3">菜品</div><div class="a1">价格</div><div class="a1">数量</div><div class="a1">点单</div><div class="a1">厨师</div><div class="a1">传菜</div><div class="a3">备注</div></div>';
	$q=mysql_query("Select * From OCK Where Td=2 And Dt>'".$a."' And Dt<'".$b."' Order By ID Desc");
	while($r=mysql_fetch_array($q))echo '<div class="a"><div class="a1">'.$r["Oi"].'</div><div class="a1">'.$r["Ti"].'</div><div class="a3">'.$r["Cn"].'</div><div class="a1">'.($r["Cp"]/100).'</div><div class="a1">'.$r["Cs"].'</div><div class="a1">'.$r["I1"].'</div><div class="a1">'.$r["I2"].'</div><div class="a1">'.$r["I3"].'</div><div class="a3">'.$r["Bz"].'</div></div>';
	echo '<div class="b"><a name="cp"></a>菜品详情</div><div class="a"><div class="a3">菜品</div><div class="a1">销量</div><div class="a3">菜品</div><div class="a1">销量</div><div class="a3">菜品</div><div class="a1">销量</div></div><div class="a">';
	$q=mysql_query("Select ID,Nm From SCD Where DD=0 Order By ID Asc");
	$i=0;
	while($r=mysql_fetch_array($q)){
		if($i%3==0)echo '<div class="a">';
		$e=mysql_fetch_array(mysql_query("Select Sum(Cs) As N From OCK Where Ci=".$r["ID"]." And Dt>'".$a."' And Dt<'".$b."'"));
		echo '<div class="a3">'.$r["Nm"].'</div><div class="a1">'.($e[0]?$e[0]:0).'</div>';
		if($i%3==2)echo "</div>";
		$i=$i+1;
	}
	if($i%3!=2)echo "</div>";
	echo '<div class="b"><a name="yg"></a>员工详情</div><div class="a"><div class="a4">点单</div><div class="a1">单数</div><div class="a1">金额</div><div class="a4">收银</div><div class="a1">单数</div><div class="a1">金额</div><div class="a4">厨师</div><div class="a1">单数</div><div class="a1">金额</div><div class="a4">传菜</div><div class="a1">单数</div><div class="a1">金额</div></div><div style="float:left;width:250px;">';
	$q=mysql_query("Select ID,Nm From SCY Where DD=0 And Nr Like '%A%'");
	while($r=mysql_fetch_array($q)){
		$e=mysql_fetch_array(mysql_query("Select Count(ID) As N,Sum(Ps) As M From ODR Where Yi=".$r["ID"]." And Dt>'".$a."' And Dt<'".$b."'"));
		echo '<div class="a"><div class="a4">'.$r["Nm"].'</div><div class="a1">'.$e["N"].'</div><div class="a1">'.($e["M"]?$e["M"]/100:0).'</div></div>';
	}
	echo '</div><div style="float:left;width:250px;">';
	$q=mysql_query("Select ID,Nm From SCY Where DD=0 And Nr Like '%B%'");
	while($r=mysql_fetch_array($q)){
		$e=mysql_fetch_array(mysql_query("Select Count(ID) As N,Sum(Ps) As M From ODR Where I4=".$r["ID"]." And Dt>'".$a."' And Dt<'".$b."'"));
		echo '<div class="a"><div class="a4">'.$r["Nm"].'</div><div class="a1">'.$e["N"].'</div><div class="a1">'.($e["M"]?$e["M"]/100:0).'</div></div>';
	}
	echo '</div><div style="float:left;width:250px;">';
	$q=mysql_query("Select ID,Nm From SCY Where DD=0 And Nr Like '%C%'");
	while($r=mysql_fetch_array($q)){
		$e=mysql_fetch_array(mysql_query("Select Count(ID) As N,Sum(Cp*Cs) As M From OCK Where I2=".$r["ID"]." And Dt>'".$a."' And Dt<'".$b."'"));
		echo '<div class="a"><div class="a4">'.$r["Nm"].'</div><div class="a1">'.$e["N"].'</div><div class="a1">'.($e["M"]?$e["M"]/100:0).'</div></div>';
	}
	echo '</div><div style="float:left;width:250px;">';
	$q=mysql_query("Select ID,Nm From SCY Where DD=0 And Nr Like '%D%'");
	while($r=mysql_fetch_array($q)){
		$e=mysql_fetch_array(mysql_query("Select Count(ID) As N,Sum(Cp*Cs) As M From OCK Where I3=".$r["ID"]." And Dt>'".$a."' And Dt<'".$b."'"));
		echo '<div class="a"><div class="a4">'.$r["Nm"].'</div><div class="a1">'.$e["N"].'</div><div class="a1">'.($e["M"]?$e["M"]/100:0).'</div></div>';
	}
	echo '</div></div><div style="clear:both;line-height:30px;">Created At '.date("Y-m-d H:i:s",time()).'</div></body></html>';
	break;

	default:
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>千味餐厅点餐支付管理系统</title>
<style type="text/css">
html{overflow:hidden;}
body{margin:0px;padding:0px;background-color:#F6F6F6;}
div {text-align:left;font-family:微软雅黑, 宋体;font-size:14px;}
span {font-size:12px;}
a:link,a:visited {text-decoration:none;color:#09F;}
a:hover,a:active {text-decoration:underline;color:#F00;}
input[type=text],input[type=password],textarea {font-size:14px;padding:5px;border:1px solid #B5E6FD;}
.dvs {position:absolute;left:0px;top:60px;height:600px;background-color:#FFF;display:none;}
.btn {font-size:14px;font-family:宋体;background-color:#09F;color:#FFF;border:1px solid #09F;padding:0px 12px;line-height:22px;cursor:pointer;display:inline-block;}
.dtop {width:50px;height:50px;margin:5px;text-align:center;cursor:pointer;}
.dsl1 {width:148px;border:1px solid #EEE;line-height:40px;text-align:center;background-color:#FFF;color:#333;cursor:pointer;}
.dsl2 {width:148px;border:1px solid #EEE;line-height:40px;text-align:center;background-color:#333;color:#FFF;cursor:pointer;}
.dsrt {color:#09F;font-size:20px;line-height:32px;border-bottom:1px solid #09F;margin-bottom:10px;}
.dsrm {padding:10px;line-height:36px;border-left:1px solid #DEF;border-right:1px solid #DEF;}
.dsre {background-color:#09F;color:#FFF;line-height:32px;text-align:center;font-weight:bold;}
.dsrc {float:right;width:20px;line-height:20px;text-align:center;border:1px solid #FFF;margin:5px;cursor:pointer;font-family:宋体;}
.dsrb {height:37px;background-color:#DEF;clear:both;} .dsrl {float:left;margin:5px;} .dsrr {float:right;margin:5px;}
.ds1la1 {text-align:left;padding:4px 10px;background-color:#DEF;line-height:40px;margin:2px;cursor:pointer;}
.ds1la2 {text-align:left;padding:4px 10px;background-color:#09F;color:#FFF;line-height:40px;margin:2px;cursor:pointer;}
.ds1la3 {border:1px solid #000;width:14px;height:14px;font-size:11px;font-family:Tahoma;line-height:14px;text-align:center;color:#000;display:inline-block;}
.ds1lb1 {float:left;width:240px;line-height:35px;height:70px;background-color:#F3F9FF;border:1px solid #9CF;margin:5px;cursor:pointer;}
.ds1lb2 {float:left;width:60px;height:60px;margin:5px;}
.ds2l {float:left;width:100px;height:100px;text-align:center;background-color:#F3F9FF;line-height:50px;border:1px solid #09F;margin:5px;cursor:pointer;}
.ds3l {border:1px solid #CCC;background-color:#F3F9FF;line-height:32px;height:32px;clear:both;margin:5px 0px;cursor:pointer;}
.ds3i {float:left;width:160px;text-align:center;}
.nst3 {float:left;width:120px;height:68px;text-align:left;padding:5px 10px;background-color:#F3F9FF;line-height:32px;border:1px solid #09F;margin:5px;cursor:pointer;}
.nst2 {float:left;width:120px;height:68px;text-align:left;padding:5px 10px;background-color:#F90;color:#FFF;line-height:32px;border:1px solid #F00;margin:5px;cursor:pointer;}
.nst1 {float:left;width:120px;height:68px;text-align:left;padding:5px 10px;background-color:#09F;color:#FFF;line-height:32px;border:1px solid #00F;margin:5px;cursor:pointer;}
.ntl1 {border-bottom:1px dashed #09F;height:30px;}
.ntl2 {float:left;width:240px;text-align:center;}
.ntl3 {float:left;width:80px;text-align:center;}
.ntl4 {float:left;width:60px;text-align:center;}
.zkl1 {display:inline-block;line-height:30px;width:80px;border:1px dashed #09F;background-color:#F9FCFF;margin:0px 10px;text-align:center;cursor:pointer;}
.zkl0 {display:inline-block;line-height:30px;width:80px;border:1px dashed #09F;background-color:#09F;color:#FFF;margin:0px 10px;text-align:center;cursor:pointer;}
.duls {border:1px solid #CCC;background-color:#F3F9FF;line-height:32px;height:32px;clear:both;margin:5px 0px;cursor:pointer;}
.duli {float:left;width:160px;text-align:center;}
.cnti {float:left;width:300px;border:1px solid #09F;margin:10px;}
.cntt {line-height:40px;font-size:20px;padding:0px 10px;background-color:#09F;color:#FFF;}
.cntm {float:left;width:160px;padding:20px 10px;font-size:36px;color:#F00;}
.cntr {float:left;width:120px;fong-size:14px;line-height:42px;}
.cntz div{float:left;width:88px;text-align:center;line-height:36px;}
.cntc {padding:0px 10px;line-height:36px;}
.xnl2 {float:left;padding:6px 8px;margin:5px;background-color:#09F;color:#FFF;font-size:12px;cursor:pointer;}
.xnl1 {float:left;padding:5px 7px;margin:5px;border:1px dashed #09F;font-size:12px;cursor:pointer;}
.xnc1 {float:left;width:166px;line-height:30px;height:30px;font-size:12px;border:1px dashed #09F;cursor:pointer;margin:5px;}
.xnc2 {float:left;margin-left:5px;}
.xnc3 {float:right;margin-right:5px;}
.xnc4 {width:240px;line-height:30px;height:30px;margin:4px;border-bottom:1px dashed #09F;}
.xnc3 span {font-size:12px;cursor:pointer;background-color:#09F;color:#FFF;width:16px;height:16px;line-height:16px;display:inline-block;text-align:center;}
.xnc3 input[type=text] {font-size:12px;width:24px;height:18px;padding:0px;text-align:center;}
</style>
<script type="text/javascript">
function $(o){return document.getElementById(o);}
function $$(o){return document.getElementsByName(o);}
function xml(){var x;try{x=new ActiveXObject('Msxml2.XMLHTTP');}catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(f){x=false;}}if(!x&&typeof XMLHttpRequest!='undefined'){x=new XMLHttpRequest();}xvr=true;return x;}
function num(n){var o=event.srcElement;if(o.tagName=='INPUT'&&o.type=='text'){var v=n?parseFloat(o.value):parseInt(o.value);if(!v)v=0;o.value=v;}}
function ymd(y,m,o){if(event.srcElement.tagName=='INPUT'){if(event.srcElement.id=='')event.srcElement.id='dtSelIpt';o=String(event.srcElement.id);}var t=document.getElementById(o).value;var d=t==''?new Date():new Date(t.split('-')[0]*1,t.split('-')[1]*1-1,1);if(!y)y=d.getFullYear();if(!m&&m!=0)m=d.getMonth();if(m<0){m=11;y--;}if(m>11){m=0;y++;}var e=[31,y%4==0?29:28,31,30,31,30,31,31,30,31,30,31];var s='<li onclick="ymd('+(y-1)+','+m+',\''+o+'\');">&lt;&lt;</li><li onclick="ymd('+y+','+(m-1)+',\''+o+'\');">&lt;</li><li style="width:82px;">'+y+' - '+(m+1)+'</li><li onclick="ymd('+y+','+(m+1)+',\''+o+'\');">&gt;</li><li onclick="ymd('+(y+1)+','+m+',\''+o+'\');">&gt;&gt;</li><li>日</li><li>一</li><li>二</li><li>三</li><li>四</li><li>五</li><li>六</li>';var n=new Date(y,m,1).getDay();for(i=0;i<n;i++)s+='<li style="color:#CCC;">'+(e[(m-1<0)?11:(m-1)]-n+i+1)+'</li>';for(i=1;i<=e[m];i++)s+='<li onclick="document.getElementById(\''+o+'\').value=\''+y+'-'+(m+1)+'-'+i+'\';document.getElementById(\'dtSelDiv\').style.display=\'none\';">'+i+'</li>';var n=new Date(y,m,e[m]).getDay();for(i=n+1;i<7;i++)s+='<li style="color:#CCC;">'+(i-n)+'</li>';if(!document.getElementById('dtSelDiv')){var c='#dtSelDiv{position:absolute;z-index:999;width:197px;background-color:#FFF;border:2px solid #DEF;padding:1px;box-shadow:1px 1px 1px 1px #999;left:'+event.srcElement.offsetLeft+'px;top:'+(event.srcElement.offsetTop+event.srcElement.clientHeight)+'px;}#dtSelDiv li{width:26px;list-style:none;float:left;text-align:center;line-height:22px;font-size:12px;margin:1px;background-color:#DEF;cursor:pointer;}';if(document.all){document.createStyleSheet().cssText=c;}else{var w=document.createElement('style');w.type='text/css';w.textContent=c;document.body.appendChild(w);}var e=document.createElement('div');e.id='dtSelDiv';document.body.appendChild(e);}document.getElementById('dtSelDiv').innerHTML=s;document.getElementById('dtSelDiv').style.display='block';}
function md5(t){function RL(v,b){return (v<<b)|(v>>>(32-b));}function AU(m,n){var m4,n4,m8,n8,r;m8=(m&0x80000000);n8=(n&0x80000000);m4=(m&0x40000000);n4=(n&0x40000000);r=(m&0x3FFFFFFF)+(n&0x3FFFFFFF);if(m4&n4){return(r^0x80000000^m8^n8);}if(m4|n4){if(r&0x40000000){return(r^0xC0000000^m8^n8);}else{return(r^0x40000000^m8^n8);}}else{return(r^m8^n8);}}function F(x,y,z){return (x&y)|((~x)&z);}function G(x,y,z){return (x&z)|(y&(~z));}function H(x,y,z){return (x^y^z);}function I(x,y,z){return (y^(x|(~z)));}function FF(a,b,c,d,x,s,ac){a=AU(a,AU(AU(F(b,c,d),x),ac));return AU(RL(a,s),b);}function GG(a,b,c,d,x,s,ac){a=AU(a,AU(AU(G(b,c,d),x),ac));return AU(RL(a,s),b);}function HH(a,b,c,d,x,s,ac){a=AU(a,AU(AU(H(b,c,d),x),ac));return AU(RL(a,s),b);}function II(a,b,c,d,x,s,ac){a=AU(a,AU(AU(I(b,c,d),x),ac));return AU(RL(a,s),b);}function CW(s){var c;var l=s.length;var n=((l+8-((l+8)%64))/64+1)*16;var a=Array(n-1);var p=0;var b=0;while(b<l){c=(b-(b%4))/4;p=(b%4)*8;a[c]=(a[c]|(s.charCodeAt(b)<<p));b++;}c=(b-(b%4))/4;p=(b%4)*8;a[c]=a[c]|(0x80<<p);a[n-2]=l<<3;a[n-1]=l>>>29;return a;}function WH(n){var v="",t="",b,i;for(i=0;i<=3;i++){b=(n>>>(i*8))&255;t='0'+b.toString(16);v=v+t.substr(t.length-2,2);}return v;}function UE(s){s=s.replace(/\r\n/g,"\n");var t='';for(var n=0;n<s.length;n++){var c=s.charCodeAt(n);if(c<128){t+=String.fromCharCode(c);}else if((c>127)&&(c<2048)){t+=String.fromCharCode((c>>6)|192);t+=String.fromCharCode((c&63)|128);}else{t+=String.fromCharCode((c>>12)|224);t+=String.fromCharCode(((c>>6)&63)|128);t+=String.fromCharCode((c&63)|128);}}return t;}var x=Array();var k,AA,BB,CC,DD,a,b,c,d,S11=7,S12=12,S13=17,S14=22,S21=5,S22=9,S23=14,S24=20,S31=4,S32=11,S33=16,S34=23,S41=6,S42=10,S43=15,S44=21;x=CW(UE(t));a=0x67452301;b=0xEFCDAB89;c=0x98BADCFE;d=0x10325476;for(k=0;k<x.length;k+=16){AA=a;BB=b;CC=c;DD=d;a=FF(a,b,c,d,x[k+0],S11,0xD76AA478);d=FF(d,a,b,c,x[k+1],S12,0xE8C7B756);c=FF(c,d,a,b,x[k+2],S13,0x242070DB);b=FF(b,c,d,a,x[k+3],S14,0xC1BDCEEE);a=FF(a,b,c,d,x[k+4],S11,0xF57C0FAF);d=FF(d,a,b,c,x[k+5],S12,0x4787C62A);c=FF(c,d,a,b,x[k+6],S13,0xA8304613);b=FF(b,c,d,a,x[k+7],S14,0xFD469501);a=FF(a,b,c,d,x[k+8],S11,0x698098D8);d=FF(d,a,b,c,x[k+9],S12,0x8B44F7AF);c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);a=FF(a,b,c,d,x[k+12],S11,0x6B901122);d=FF(d,a,b,c,x[k+13],S12,0xFD987193);c=FF(c,d,a,b,x[k+14],S13,0xA679438E);b=FF(b,c,d,a,x[k+15],S14,0x49B40821);a=GG(a,b,c,d,x[k+1],S21,0xF61E2562);d=GG(d,a,b,c,x[k+6],S22,0xC040B340);c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);b=GG(b,c,d,a,x[k+0],S24,0xE9B6C7AA);a=GG(a,b,c,d,x[k+5],S21,0xD62F105D);d=GG(d,a,b,c,x[k+10],S22,0x2441453);c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);b=GG(b,c,d,a,x[k+4],S24,0xE7D3FBC8);a=GG(a,b,c,d,x[k+9],S21,0x21E1CDE6);d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);c=GG(c,d,a,b,x[k+3],S23,0xF4D50D87);b=GG(b,c,d,a,x[k+8],S24,0x455A14ED);a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);d=GG(d,a,b,c,x[k+2],S22,0xFCEFA3F8);c=GG(c,d,a,b,x[k+7],S23,0x676F02D9);b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);a=HH(a,b,c,d,x[k+5],S31,0xFFFA3942);d=HH(d,a,b,c,x[k+8],S32,0x8771F681);c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);a=HH(a,b,c,d,x[k+1],S31,0xA4BEEA44);d=HH(d,a,b,c,x[k+4],S32,0x4BDECFA9);c=HH(c,d,a,b,x[k+7],S33,0xF6BB4B60);b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);d=HH(d,a,b,c,x[k+0],S32,0xEAA127FA);c=HH(c,d,a,b,x[k+3],S33,0xD4EF3085);b=HH(b,c,d,a,x[k+6],S34,0x4881D05);a=HH(a,b,c,d,x[k+9],S31,0xD9D4D039);d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);b=HH(b,c,d,a,x[k+2],S34,0xC4AC5665);a=II(a,b,c,d,x[k+0],S41,0xF4292244);d=II(d,a,b,c,x[k+7],S42,0x432AFF97);c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);b=II(b,c,d,a,x[k+5],S44,0xFC93A039);a=II(a,b,c,d,x[k+12],S41,0x655B59C3);d=II(d,a,b,c,x[k+3],S42,0x8F0CCC92);c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);b=II(b,c,d,a,x[k+1],S44,0x85845DD1);a=II(a,b,c,d,x[k+8],S41,0x6FA87E4F);d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);c=II(c,d,a,b,x[k+6],S43,0xA3014314);b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);a=II(a,b,c,d,x[k+4],S41,0xF7537E82);d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);c=II(c,d,a,b,x[k+2],S43,0x2AD7D2BB);b=II(b,c,d,a,x[k+9],S44,0xEB86D391);a=AU(a,AA);b=AU(b,BB);c=AU(c,CC);d=AU(d,DD);}return (WH(b)+WH(c)).toLowerCase();}
function css(n){for(var i=0;i<document.styleSheets.length;i++){var r=document.styleSheets[i].cssRules?document.styleSheets[i].cssRules:document.styleSheets[i].rules;for(var j=0;j<r.length;j++){if(r[j].selectorText==n)return r[j].style;}}}
function show(o){$(o).style.left=(pw-parseInt($(o).style.width))/2+'px';$(o).style.top=(ph-parseInt($(o).style.height))/2+'px';$(o).style.display='block';dmsk.style.display='block';}
function hide(n){var o=n?$(n):event.srcElement.parentElement.parentElement;o.style.display='none';dmsk.style.display='none';}
var oid='<? echo $OpID; ?>';
var scc;//基本
var sct=[];//餐桌
var scl=[];//菜类
var scd=[];//菜单
var scy=[];//员工
var scz=[];//折扣
var scp=[];//打印
var shy=[];//会员
var pw,ph;
var eid;//编辑ID
var uri='index.php?';
var urm='http://www.1000vw.com/Qw/mbr.php?';
var uid=0;//用户
var rid='';//权限
var sid='';//连接
var cid;//Reload
var wid;//SyncID
var ver='V2.1.1006';
//自动更新
function nver(){var x=xml();x.open('Get','http://www.1000vw.com/Qw/open.php?x='+oid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){if(x.responseText!=ver){if(confirm('发现新版本：'+x.responseText+'\n立即下载？？'))window.open('http://www.1000vw.com/Qw/down.htm');}}};x.send();}
//自动宽高
function atwh(){pw=parseInt(document.documentElement.clientWidth);ph=parseInt(document.documentElement.clientHeight);dlgn.style.width=pw+'px';dlgn.style.height=ph+'px';dlgm.style.marginTop=(ph-300)/2+'px';dmsk.style.width=pw+'px';dmsk.style.height=ph+'px';dtbl.style.width=pw+'px';dtbl.style.height=ph-60+'px';css('.dvs').width=pw+'px';css('.dvs').height=ph-60+'px';css('.duli').width=(pw-50)/6+'px';css('.ds3i').width=(pw-220)/6+'px';}
//上传处理
function svup(d){$(d+'pp').value=1;$(d+'qq').innerText='[浏览图片]';$(d+'qq').href='./i/z.jpg';}
//登陆
function ulgn(){
	if(unm.value.replace(/\s/gi,'')==''||upw.value.replace(/\s/gi,'')=='')return;
	var x=xml();x.open('Get',uri+'x=lgn&a='+escape(unm.value)+'&b='+escape(upw.value)+'&'+Math.random(),true);
	x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.split('|');
	if(t[0]=='OK'){
		uid=t[1];dtnu.innerText='当前员工：'+t[2];rid=t[3];sid=t[4];dlgn.style.display='none';ljbs();}else{alert('用户名或密码错误！');}}};x.send();}
//会员
function shur(){dusr.style.display='block';dset.style.display='none';dcnt.style.display='none';lhys(1);}
//会员列表
function lhys(){duls.innerHTML='Loading....';var x=xml();x.open('Get',urm+'x=lhy&m='+scc[5]+'&n='+scc[6]+'&a='+escape(ducs.value)+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='P'){duls.innerHTML='主商户ID或密码错误，请检查基本设置中连锁模式的参数！';}else{t=t.substr(1).split('$');shy=[];if(t[0]!=''){for(var i=0;i<t.length;i++)shy.push(t[i].split('|'));}var s='';for(var i=0;i<shy.length;i++)s+='<div class="duls" onclick="ehys('+i+');"><div class="duli">'+shy[i][0]+'</div><div class="duli">'+shy[i][1]+'</div><div class="duli">'+shy[i][2]+'</div><div class="duli">'+shy[i][5]/100+'</div><div class="duli">'+shy[i][6]/100+'</div><div class="duli">'+shy[i][7]+'</div></div>';duls.innerHTML=s;}}};x.send();}
//会员编辑
function ehys(n){eid=n;show('due');var s=n<0?[0,'','','','',0,0,'']:shy[n];inh1.value=s[1];inh2.value=s[2];inh3.value=s[3];inh4.innerText=s[4];inh5.innerText=s[5]/100;inh6.innerText=s[6]/100;inh7.innerText=s[7];}
//会员保存
function shys(){var s=[0,inh1.value,inh2.value,inh3.value];if(s[1]==''||s[2]==''||s[3]==''){alert('资料不全，请补齐！');return;}if(eid>=0)s[0]=shy[eid][0];var x=xml();x.open('Get',urm+'x=shy&m='+scc[5]+'&n='+scc[6]+'&a='+escape(s.join('|'))+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('due');lhys();}else{alert('保存失败，请重试！！');}}};x.send();}
//会员删除
function dhys(){if(eid<0)return;if(inh5.innerText!='0'||inh6.innerText!='0'){alert('已充值过的会员卡不可删除！');return;}if(!confirm('确认删除？？'))return;var x=xml();x.open('Get',urm+'x=dhy&m='+scc[5]+'&n='+scc[6]+'&a='+shy[eid][0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('due');lhys();}else{alert('保存失败，请重试！！');}}};x.send();}
//会员充值
function chys(){if(eid==-1)return;show('duc');inh8.value=1000;inh9.value=1000;ducp.style.display='none';ducd.style.display='block';}
//充值会员
function mhys(){if(inh8.value==''||inh9.value==''){alert('请输入金额！');return;}if(!confirm('实收：'+inh8.value+'元\n充值：'+inh9.value+'元\n确认？？'))return;var s=[shy[eid][0],shy[eid][1],0,oid+'-'+uid,inh8.value*100,inh9.value*100];var x=xml();x.open('Get',uri+'x=chy&s='+sid+'&m='+scc[5]+'&n='+scc[6]+'&a='+escape(s.join('|'))+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('充值已成功！');ducp.style.display='block';ducd.style.display='none';inh5.innerText=inh8.value*1+inh5.innerText*1;}else{alert('保存失败，请重试！！');}}};x.send();}
//充值打单
function phys(){var o=document.getElementById(navigator.userAgent.indexOf('MSIE')>=0?'LdpIE':'LdpGC');if(typeof(o.VERSION)=='undefined'){alert('未安装打印控件！');return;}o.PRINT_INIT('Print:'+sid);o.SET_PRINT_PAGESIZE(3,scp[0]*10,scp[2]*10,'CreateCustomPage');o.ADD_PRINT_HTM(0,0,scp[1]*10,500,'URL:'+uri+'x=phy&s='+sid+'&a='+shy[eid][0]);o.PREVIEW();}
//统计
function shct(n){dusr.style.display='none';dset.style.display='none';dcnt.style.display='block';if(!n)n=0;var d=new Date();dt2.value=d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate();d.setDate(d.getDate()-n);dt1.value=d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate();cttj();}
function cttj(){var x=xml();x.open('Get',uri+'x=cnt&s='+sid+'&a='+escape(dt1.value)+'&b='+escape(dt2.value)+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.split('|');for(var i=0;i<21;i++){if(t[i]=='')t[i]=0;t[i]=t[i]*1;}for(var i=1;i<12;i++)$('cnt'+i).innerText=t[i];var m,n;for(var i=1;i<5;i++){m=Math.round((t[i]-t[i+11])*100)/100;$('ctt'+i).innerHTML='同比：'+m+' '+(m>0?'<font style="color:#F00;">↑</font>':'<font style="color:#0F0;">↓</font>')+'<br />日均：'+Math.round(t[i]/t[0]*100)/100;}if(t[1]==0)t[1]=1;if(t[12]==0)t[12]=1;for(var i=5;i<12;i++){m=t[i]/t[1];n=t[i+11]/t[12];if(i==11){m=t[11]/(t[22]==0?1:t[22]);n=1;}$('ctt'+i).innerHTML=Math.round(m*10000)/100+'%'+(m>n?'<font style="color:#F00;">↑</font>':'<font style="color:#0F0;">↓</font>');}m=t[23].split('$');n='';for(var i=1;i<(4<m.length?4:m.length);i++)n+='<div style="float:right;">'+m[i].split('#')[1]+'</div>'+m[i].split('#')[0]+'<br />';ctc1.innerHTML=n;m=t[24].split('$');n='';for(var i=1;i<(4<m.length?4:m.length);i++)n+='<div style="float:right;">'+m[i].split('#')[1]+'</div>'+m[i].split('#')[0]+'<br />';ctc2.innerHTML=n;}};x.send();}
//设置
function shst(n){
	if(n>1&&rid.indexOf('S')==-1){alert('无权限！');return;}
	for(var i=1;i<7;i++){
		$('dsl'+i).className='dsl1';$('dsr'+i).style.display='none';}$('dsl'+n).className='dsl2';
		$('dsr'+n).style.display='block';dstr.style.width=pw-190+'px';dstr.style.height=ph-80+'px';dset.style.display='block';dusr.style.display='none';dcnt.style.display='none';
	}
//基本读取
function ljbs(){var x=xml();x.open('Get',uri+'x=ljb&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.split('@@');scc=(t[0]+'||').split('|');scz=t[1].split('$');scp=t[2].split('|');dtnm.innerText=scc[0];for(var i=0;i<7;i++)$('in'+i).value=scc[i];in66.value=scc[6];$('ifm1').src=uri+'x=u&s='+sid+'&a=i';clearTimeout(cid);ldts();}};x.send();}
//基本保存
function sjbs(){var s=[];for(var i=1;i<7;i++)s[i-1]=$('in'+i).value;var x=xml();x.open('Get',uri+'x=sjb&s='+sid+'&a='+escape(in0.value)+'&b='+escape(s.join('|'))+'&c='+ipp.value+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('餐厅设置保存成功！');iqq.href='i/i.jpg?'+Math.random();ljbs();}else{alert('保存失败，请重试！！');}}};x.send();}
//打印读取
function ldys(){for(var i=0;i<9;i++)$('inr'+i).value=scp[i];inr9.checked=scp[9]=='1';dyyl.style.width=scp[1]+'mm';dyyl.style.padding=scp[2]+'mm '+(scp[0]*1-scp[1]*1)/2+'mm';dyt1.innerText=scp[3];dyt1.style.fontSize=scp[4]+'px';dyt1.style.lineHeight=scp[4]*1+8+'px';dyt2.innerText=scp[5];dyt2.style.fontSize=scp[6]+'px';dyt2.style.lineHeight=scp[6]*1+8+'px';dyt3.innerText=scp[7];dyt3.style.fontSize=scp[8]+'px';dyt3.style.lineHeight=scp[8]*1+8+'px';}
//打印保存
function sdys(){var s=[];for(var i=0;i<9;i++)s[i]=$('inr'+i).value;var x=xml();x.open('Get',uri+'x=sdy&s='+sid+'&a='+escape((s.join('|')+'|'+(inr9.checked?1:0)))+'&b='+ppp.value+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('打印设置保存成功！');pqq.href='i/p.jpg?'+Math.random();ljbs();}else{alert('保存失败，请重试！！');}}};x.send();}
//打印测试
function tdys(){var o=document.getElementById(navigator.userAgent.indexOf('MSIE')>=0?'LdpIE':'LdpGC');if(typeof(o.VERSION)=='undefined'){alert('未安装打印控件！');return;}var s=[];for(var i=0;i<9;i++)s[i]=$('inr'+i).value;s[9]=inr9.checked?1:0;o.PRINT_INIT('Print:'+sid);o.SET_PRINT_PAGESIZE(3,inr0.value*10,inr2.value*10,'CreateCustomPage');o.ADD_PRINT_HTM(0,0,inr1.value*10,500,'URL:index.php?x=tdy&s='+sid+'&a='+escape(s.join('|')));o.PREVIEW();}
//折扣读取
function lzks(){var t;var s='';for(var i=1;i<scz.length;i++){t=scz[i].split('|');s+='折扣'+i+'： <input type="text" id="izkn'+i+'" value="'+t[0]+'"> 满 <input type="text" id="izkm'+i+'" style="width:40px;" value="'+t[1]+'" onblur="num(1);" /> 元 打/减 <input type="text" style="width:40px;" id="izkj'+i+'" value="'+t[2]+'" onblur="num(1);" /> 折/元<br />'}ds5l.innerHTML=s;}
//折扣保存
function szks(){var s='';for(var i=0;i<scz.length;i++){if($('izkn'+i).value!=''&&$('izkm'+i).value!=''&&$('izkj'+i).value!='')s+='$'+$('izkn'+i).value+'|'+$('izkm'+i).value+'|'+$('izkj'+i).value;}var x=xml();x.open('Get',uri+'x=szk&s='+sid+'&a='+escape(s)+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){izkn0.value='';izkm0.value='';izkj0.value='';ljbs();alert('折扣设置保存成功！！');setTimeout('lzks()',500);}else alert('保存失败，请重试！！');}};x.send();}
//员工列表
function lygs(){var x=xml();x.open('Get',uri+'x=lyg&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.substr(1).split('$');scy=[];if(t[0]!=''){for(var i=0;i<t.length;i++)scy.push(t[i].split('|'));}var s='';for(var i=0;i<scy.length;i++)s+='<div class="ds3l" onclick="eygs('+i+');"><div class="ds3i">'+scy[i][0]+'</div><div class="ds3i">'+scy[i][3]+'</div><div class="ds3i">'+scy[i][1]+'</div><div class="ds3i">'+scy[i][2]+'</div><div class="ds3i">'+scy[i][6]+'</div><div class="ds3i">'+scy[i][5]+'</div></div>';ds3l.innerHTML=s;}};x.send();}
//员工编辑
function eygs(n){eid=n;show('dse3');var s=n<0?[0,'员工'+(scy.length+1),'员工'+(scy.length+1),'u'+Math.floor(Math.random()*8999+1000),'123456','','ABCD']:scy[n];iny1.value=s[1];iny2.value=s[2];iny3.value=s[3];iny4.value=s[4];var c='';var d=['A','B','C','D','E','S'];for(var i=0;i<6;i++)c+=' <input type="checkbox" name="iny5" value="'+d[i]+'"'+(s[6].indexOf(d[i])>-1?' checked="checked"':'')+'> '+['点单','收银','厨师','送餐','前台','系统'][i];dse3r.innerHTML=c;}
//员工保存
function sygs(){var o=$$('iny5');var m='';for(var i=0;i<o.length;i++){if(o[i].checked)m+=o[i].value;}var s=[0,iny1.value,iny2.value,iny3.value,iny4.value==''?'123456':iny4.value,'',m];if(eid>=0)s[0]=scy[eid][0];var x=xml();x.open('Get',uri+'x=syg&s='+sid+'&a='+escape(s.join('|'))+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('dse3');lygs();}else{alert('保存失败，请重试！！');}}};x.send();}
//员工删除
function dygs(){if(eid<0)return;if(!confirm('确认删除？？'))return;var x=xml();x.open('Get',uri+'x=dyg&s='+sid+'&a='+scy[eid][0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('dse3');lygs();}else{alert('保存失败，请重试！！');}}};x.send();}
//餐桌列表
function lczs(){var x=xml();x.open('Get',uri+'x=lcz&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.substr(1).split('$');sct=[];if(t[0]!=''){for(var i=0;i<t.length;i++)sct.push(t[i].split('|'));}var s='';for(var i=0;i<sct.length;i++)s+='<div class="ds2l" onclick="eczs('+i+');">'+sct[i][1]+'<br />'+sct[i][3]+'人座</div>';ds2l.innerHTML=s;}};x.send();}
//餐桌编辑
function eczs(n){eid=n;show('dse2');var s=n<0?[0,sct.length+1+'号桌',sct.length+1,4,0]:sct[n];int1.value=s[1];int2.value=s[2];int3.value=s[3];dsei.src='http://www.liantu.com/api.php?el=L&m=10&w=281&text='+in4.value+'mobile%2Ephp%3Ft%3D'+s[6]+'';}
//餐桌保存
function sczs(){var s=[0,int1.value==''?'未命名':int1.value,int2.value,int3.value,0];if(eid>=0)s[0]=sct[eid][0];var x=xml();x.open('Get',uri+'x=scz&s='+sid+'&a='+escape(s.join('|'))+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('dse2');lczs();}else{alert('保存失败，请重试！！');}}};x.send();}
//餐桌删除
function dczs(){if(eid<0)return;if(!confirm('确认删除？？'))return;var x=xml();x.open('Get',uri+'x=dcz&s='+sid+'&a='+sct[eid][0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('dse2');lczs();}else{alert('保存失败，请重试！！');}}};x.send();}
//菜类列表
function llbs(){
	var x=xml();
	x.open('Get',uri+'x=llb&s='+sid+'&'+Math.random(),true);
	x.onreadystatechange=function(){
		if(x.readyState==4&&x.status==200){
			var t=x.responseText.substr(1).split('$');scl=[];
			if(t[0]!=''){
				for(var i=0;i<t.length;i++)
					scl.push(t[i].split('|'));}
				var s='';
				for(var i=0;i<scl.length;i++)
					s+='<div id="ds1l'+i+'" class="ds1la1" onclick="lcds('+i+');"><span class="ds1la3" onclick="elbs('+i+');">E</span> '+scl[i][1]+'('+scl[i][4]+')'+(scl[i][3]==1?'H':'')+'</div>';ds1la.innerHTML=s;
				lcds(0);
		}
	};
	x.send();
}
//菜类编辑
function elbs(n){eid=n;show('dse1a');var s=n<0?[0,'',scl.length+1,0]:scl[n];inl1.value=s[1];inl2.value=s[2];inl3.checked=s[3]==1;}
//菜类保存
function slbs(){var s=[0,inl1.value==''?'未命名':inl1.value,inl2.value,inl3.checked?1:0,0];if(eid>=0)s[0]=scl[eid][0];var x=xml();x.open('Get',uri+'x=slb&s='+sid+'&a='+escape(s.join('|'))+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('dse1a');llbs();}else{alert('保存失败，请重试！！');}}};x.send();}
//菜类删除
function dlbs(){if(eid<0)return;if(scl[eid][4]!='0'){alert('该分类下还有菜品，不能删除！\n请先删除该分类菜品或移动到其他分类！');return;}if(!confirm('确认删除？？'))return;var x=xml();x.open('Get',uri+'x=dlb&s='+sid+'&a='+scl[eid][0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('dse1a');llbs();}else{alert('保存失败，请重试！！');}}};x.send();}
//菜品列表
function lcds(n){
	var x=xml();x.open('Get',uri+'x=lcd&s='+sid+'&'+Math.random(),true);
	x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){
		var t=x.responseText.substr(1).split('$');
		scd=[];
		for(var i=0;i<t.length;i++)
			scd.push(t[i].split('|'));
		for(var i=0;i<scl.length;i++)
			$('ds1l'+i).className='ds1la1';if(scl.length>0)$('ds1l'+n).className='ds1la2';
		var s='';
		for(var i=0;i<scd.length;i++){
			if(scd[i][0]&&scd[i][2].indexOf(','+scl[n][0]+',')>-1)
				s+='<div class="ds1lb1" onclick="ecds('+i+');"><div class="ds1lb2"><img src="i/'+(scd[i][5]==0?'n':('c'+scd[i][0]))+'.jpg" width="60" height="60" /></div>'+scd[i][1]+(scd[i][7]=='1'?'[H]':'')+'<br />售价：'+scd[i][6]/100+' 元</div>';
		}
		ds1lb.innerHTML=s;ds1lb.style.width=pw-360+'px';
	}
};
		x.send();
}
//菜品编辑
function ecds(n){eid=n;show('dse1b');var s=n<0?[0,'',',',scd.length+1,'',0,0]:scd[n];var c='';for(var i=0;i<scl.length;i++)c+=' <input type="checkbox" name="inc2" value="'+scl[i][0]+'"'+(s[2].indexOf(','+scl[i][0]+',')>-1?' checked="checked"':'')+'> '+scl[i][1];dse1bf.innerHTML=c;inc1.value=s[1];inc3.value=s[3];inc4.value=s[4];inc6.value=s[6]/100;inc7.checked=s[7]=='1';cqq.innerText=s[5]==0?'':'[浏览图片]';cqq.href='i/c'+s[0]+'.jpg';document.getElementById('ifm3').src=uri+'x=u&s='+sid+'&a=c';}
//菜品保存
function scds(){var o=$$('inc2');var m=',';for(var i=0;i<o.length;i++){if(o[i].checked)m+=o[i].value+',';}var s=[0,inc1.value==''?'未命名':inc1.value,m==','?(','+scl[0][0]+','):m,inc3.value,inc4.value,cpp.value,inc6.value*100,inc7.checked?1:0];if(eid>=0)s[0]=scd[eid][0];var x=xml();x.open('Get',uri+'x=scd&s='+sid+'&a='+escape(s.join('|'))+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('dse1b');llbs();}else{alert('保存失败，请重试！！');}}};x.send();}
//菜品删除
function dcds(){if(eid<0)return;if(!confirm('确认删除？？'))return;var x=xml();x.open('Get',uri+'x=dcd&s='+sid+'&a='+scd[eid][0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('dse1b');llbs();}else{alert('保存失败，请重试！！');}}};x.send();}
//显示大厅层
function shdt(){dusr.style.display='none';dset.style.display='none';dcnt.style.display='none';}
//大厅
var tst=['空桌待客','顾客入座','顾客呼叫','顾客下单','顾客退单','厨房退单','提交厨房','厨房接收','制作完成','正在上菜','顾客用餐','申请结账','结账完成','打扫清台','退单完成'];
var tcz=[];//餐桌数组
var tcd;   //餐桌数据
var tdc=[];//点单数组
//加载大厅
function ldts(){var x=xml();x.open('Get',uri+'x=lcz&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.substr(1).split('$');tcz=[];if(t[0]!=''){for(var i=0;i<t.length;i++)tcz.push(t[i].split('|'));}var s='';for(var i=0;i<tcz.length;i++)s+='<div id="tb'+i+'" class="'+(tcz[i][4]=='0'?'nst3':('.2.3.4.5.11.'.indexOf('.'+tcz[i][4]+'.')>-1?'nst2':'nst1'))+'" style="width:'+(pw/10-32)+'px;" onclick="lczi('+i+');">'+tcz[i][1]+'['+tcz[i][3]+'人座]<div style="text-align:center;">'+tst[tcz[i][4]]+'</div></div>';var d=new Date();dtbl.innerHTML=s+'<div style="clear:both;text-align:center;">更新于 '+d.getHours()+':'+String(100+d.getMinutes()).substr(1,2)+':'+String(100+d.getSeconds()).substr(1,2)+'</div>';cid=setTimeout('ldts()',5000);}};x.send();}
//餐桌操作
function lczi(n){var x=xml();x.open('Get',uri+'x=dtb&s='+sid+'&a='+tcz[n][0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='')t='|||||||||||0$';t=t.split('$');var d=t[1].split('|');tcd=t[0].split('|');tcd[1]=n;var s='';for(var i=1;i<d.length;i++){d[i]=d[i].split(',');s+='<div class="ntl1"><div class="ntl2">'+d[i][4]+'</div><div class="ntl3">'+d[i][5]/100+'</div><div class="ntl4"">'+d[i][6]+'</div><div class="ntl3"">'+tst[d[i][7]]+'</div><div class="ntl4"">'+d[i][8]+'</div><div class="ntl4"">'+d[i][9]+'</div><div class="ntl4"">'+d[i][10]+'</div><div class="ntl3""><a href="javascri'+'pt:void(0);" onclick="ddts(\''+d[i].join('|')+'\');">修改</a>'+['','[<span style="color:#F00;">退</span>]','[<span style="color:#093;">退</span>]'][d[i][12]]+'</div></div>';}ntbc.innerHTML=s+'<br />单号：'+tcd[0]+'　下单：'+tcd[2]+'　收银：'+tcd[8]+'　时间：'+tcd[7]+'　金额： '+tcd[3]/100+' / '+tcd[4]/100+' 元['+['现金支付','团购券支付','刷卡支付','会员卡支付','微信支付','支付宝支付'][tcd[5]||0]+']　流程：'+tst[tcz[tcd[1]][4]]+'<br />备注：'+tcd[10];ntbz.innerText=tcz[n][1];show('ntbl')}};x.send();}
//顾客入座
function ddrz(){if(tcz[tcd[1]][4]>0)return;var x=xml();x.open('Get',uri+'x=drz&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+tcd[0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('ntbl');clearTimeout(cid);ldts();}else{alert('提交失败，请稍后重试！');}}};x.send();}
//显示点单
function dddd(){show('xtbl');var x=xml();x.open('Get',uri+'x=llb&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.split('$');var l=t.length;var s='';for(var i=1;i<t.length;i++){t[i]=t[i].split('|');s+='<div id="xnl'+i+'" class="xnl1" onclick="dddl('+i+','+t[i][0]+','+l+');">'+t[i][1]+'</div>';}xtlm.innerHTML='<div id="xnl0" class="xnl2" onclick="dddl(0,0,'+l+');">全部</div>'+s;tdc=[];xtle.innerText=0;xtls.innerHTML='';dddl(0,0);}};x.send();}
//点单菜品
function dddl(n,m,l){for(var i=0;i<l;i++)$('xnl'+i).className='xnl1';$('xnl'+n).className='xnl2';var x=xml();x.open('Get',uri+'x=lcd&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.split('$');var s='';for(var i=1;i<t.length;i++){t[i]=t[i].split('|');if(t[i][2].indexOf(','+m+',')>-1||m==0)s+='<div class="xnc1" onclick="dddc('+t[i][0]+',\''+t[i][1]+'\','+t[i][6]+');"><div class="xnc2">'+t[i][1]+'</div><div class="xnc3">'+t[i][6]/100+'元</div></div>';}xtlc.innerHTML=s;}};x.send();}
//点单添加
function dddc(s,n,m){var k=0;for(var i=0;i<tdc.length;i++){if(tdc[i][0]==s)k=1;}if(s==0)k=1;if(k==0)tdc.push([s,n,m,1]);var t='';var e=0;for(i=0;i<tdc.length;i++){t+='<div class="xnc4"><div class="xnc2">'+tdc[i][1]+'</div><div class="xnc3"><span onclick="ddds('+i+',-1);">－</span> <input type="text" id="xts'+i+'" value="'+tdc[i][3]+'" onblur="num();ddds('+i+',0);"> <span onclick="ddds('+i+',1)">＋</span></div></div>';e+=tdc[i][2]*tdc[i][3];}xtls.innerHTML=t;xtle.innerText=e/100;}
//点单计算
function ddds(n,s){var m=$('xts'+n).value*1;m+=s;if(m<1){tdc.splice(n,1);}else{$('xts'+n).value=m;tdc[n][3]=m;}dddc(0);}
//点单下单
function dddx(){if(rid.indexOf('A')<0){alert('无此权限！');return;}var a=[];var b=[];for(var i=0;i<tdc.length;i++){a.push(tdc[i][0]);b.push(tdc[i][3]);}if(a.length==0)return;var x=xml();x.open('Get',uri+'x=dxd&s='+sid+'&a='+escape(tcz[tcd[1]][0]+'|'+tcd[0]+'|'+a.join(',')+'|'+b.join(',')+'|'+xtle.innerText*100+'|'+xtbz.value+'|'+uid)+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){xtbl.style.display='none';clearTimeout(cid);ldts();setTimeout('lczi('+tcd[1]+')',300);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//提交厨房
function ddcf(){if(tcd[0]=='')return;var x=xml();x.open('Get',uri+'x=dcf&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+tcd[0]+'&c='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('已提交厨房！');clearTimeout(cid);ldts();setTimeout('lczi('+tcd[1]+')',300);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//点单编辑
function ddts(t){show('dst');t=t.split('|');eid=t[0];inb1.innerText=tcz[tcd[1]][1];inb2.innerText=t[4];inb3.value=t[13];inb4.innerText=t[6];inb5.value=t[6];inb6.value=t[5]/100;}
//点单退单
function ddtd(n){if(!confirm('确认退单？？'))return;var x=xml();x.open('Get',uri+'x=dtd&s='+sid+'&a='+escape(tcz[tcd[1]][0]+'|'+tcd[0]+'|'+eid+'|'+n+'|'+inb3.value)+'&b='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('已退单完成！');dst.style.display='none';clearTimeout(cid);ldts();setTimeout('lczi('+tcd[1]+')',300);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//点单修改
function ddsl(){var x=xml();x.open('Get',uri+'x=dsl&s='+sid+'&a='+escape(tcz[tcd[1]][0]+'|'+tcd[0]+'|'+eid+'|'+inb4.innerText+'|'+inb6.value*100)+'&b='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('已修改完成！');dst.style.display='none';clearTimeout(cid);ldts();setTimeout('lczi('+tcd[1]+')',300);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//显示换桌
function ddhz(){if(tcd[0]=='')return;show('dsh');dsht.innerText=tcz[tcd[1]][1];dshs.innerHTML='';var n=0;for(var i=0;i<tcz.length;i++){if(tcz[i][4]=='0'){var m=document.createElement('option');m.text=tcz[i][1];m.value=tcz[i][0];dshs.options.add(m);}}}
//换桌执行
function ddhs(){if(tcz[tcd[1]][0]==dshs.value){dsh.style.display='none';return;}var x=xml();x.open('Get',uri+'x=dhz&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+dshs.value+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){dsh.style.display='none';hide('ntbl');clearTimeout(cid);ldts();}else{if(t=='E'){alert('目标桌状态非空桌待客，请选择空桌！');}else{alert('提交失败，请稍后重试！');}}}};x.send();}
//显示结账
function ddjz(){if(tcd[0]=='')return;show('jtbl');jtbi.innerHTML='桌号：'+tcz[tcd[1]][1]+'　单号：'+tcd[0]+'　员工：'+tcd[2]+'　时间：'+tcd[7]+'　合计/已付金额：'+tcd[3]/100+' / '+tcd[4]/100+'元['+['现金','团购券','刷卡','会员卡','微信','支付宝'][tcd[5]||0]+'支付]';var f=tcd[3]/100-tcd[4]/100;jtby.innerText=f;jtbk.innerText=f;jtbs.value=f;jtbq.value=0;jbtf.options[tcd[5]].selected=true;jtbb.value=tcd[4]=='0'?'':tcd[10];var s='';for(var i=1;i<scz.length;i++){var t=scz[i].split('|');s+='<span id="jszk'+i+'" class="zkl1" onclick="ddjs();">'+t[0]+'</span>';}jtbz.innerHTML='应用折扣： '+s;jtbh.style.display=tcd[5]=='3'?'inline-block':'none';jtba.style.display=(tcd[5]=='4'||tcd[5]=='5')?'inline-block':'none';}
//计算折扣
function ddjs(){var o=event.srcElement;if(o.tagName=='SPAN')o.className=o.className=='zkl1'?'zkl0':'zkl1';var m=tcd[3]/100-tcd[4]/100;m=m-jtbq.value*1;for(i=1;i<scz.length;i++){var t=scz[i].split('|');if($('jszk'+i).className=='zkl0'){jtbb.value=jtbb.value.replace(t[0]+'，','')+t[0]+'，';t[1]=t[1]*1;t[2]=t[2]*1;if(m>t[1]-t[2]){if(t[2]<1){m=m*t[2];}else{if(m<t[1]&&m>(t[1]-t[2])){m=t[1]-t[2];}else m-=t[2];}}}else{jtbb.value=jtbb.value.replace(t[0]+'，','');}}m=Math.floor(m*100)/100;if(m<0)m=0;jtbk.innerText=m;jtbs.value=jtbc.checked?Math.floor(m):m;}
//二维码
function dewm(){show('dsf');dsff.innerHTML='正在生成支付二维码......';var x=xml();x.open('Get',uri+'x=key&a='+tcd[0]+'&b='+jtbs.value+'&c='+jbtf.value+'&s='+sid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){if(x.responseText.substr(0,2)=='OK'){dewn(x.responseText.substr(2));}else{alert('提交失败，请稍后重试！');}}};x.send();}
function dewn(t){var x=xml();x.open('Get','http://www.1000vw.com/Qw/key.php?t='+t+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){if(x.responseText.substr(0,2)=='OK'){var k=x.responseText.substr(2);t=t.split('|');dsfe.innerText=t[1];if(jbtf.value=='4'){dsff.innerHTML='<div style="width:231px;height:231px;margin:10px auto;display:block;overflow:hidden;"><div style="position:absolute;margin:91px 0px 0px 91px;width:48px;height:48px;border:1px solid #CCC;border-radius:5px;background-color:#FFF;background-image:url(https://pay.weixin.qq.com/wxzf_guide/img/logo.png);background-repeat:no-repeat;background-position:4px 6px;"></div><div><img src="http://www.liantu.com/api.php?el=Q&m=0&w=231&text='+escape(k)+'"></div></div>';}if(jbtf.value=='5'){dsff.innerHTML='<div style="width:231px;height:231px;margin:0px auto;overflow:hidden;"><iframe src="about:blank" id="defm" name="defm" scrolling="no" frameborder="0" width="231" height="231"></iframe></div>';$('defm').src='https://mapi.alipay.com/gateway.do?_input_charset=utf-8&notify_url=http://www.taoewm.com/Qw/nzfb.php&out_trade_no='+t[3]+'-'+t[0]+'-c-'+t[4]+'&partner=2088911048354746&payment_type=1&qr_pay_mode=4&qrcode_width=231&seller_email=taoewm@163.com&service=create_direct_pay_by_user&subject='+dtnm.innerText+'-餐费&total_fee='+t[1]+'&sign='+k+'&sign_type=MD5';}clearTimeout(wid);wid=setTimeout('ddcx()',10000);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//提交付款
function ddfk(){if(rid.indexOf('B')<0){alert('无此权限！');return;}if(jtbs.value*1>0&&jbtf.selectedIndex==1){alert('抵扣金额不足，请选择其他收款方式！');return;}if(!confirm('确认结账？？'))return;clearTimeout(wid);tcd[4]=jtbs.value*100;tcd[10]=jtbb.value;var x=xml();x.open('Get',uri+'x=dfk&s='+sid+'&a='+escape(tcz[tcd[1]][0]+'|'+tcd[0]+'|'+tcd[4]+'|'+jbtf.options.selectedIndex+'|'+tcd[10]+'|'+jtbq.value*100+'|'+jtbu.value+'|'+jtbp.value+'|'+scc[5]+'|'+scc[6])+'&b='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){alert('已付款完成！');jtbu.value='';jtbp.value='';jtbl.style.display='none';clearTimeout(cid);ldts();setTimeout('lczi('+tcd[1]+')',300);}else if(t=='PE'){alert('会员卡密码错误！');}else if(t=='FE'){alert('会员卡余额不足！');}else{alert('提交失败，请稍后重试！');}}};x.send();}
//延迟付款
function ddjk(){clearTimeout(wid);var x=xml();x.open('Get',uri+'x=djk&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+uid+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){jtbl.style.display='none';clearTimeout(cid);ldts();setTimeout('lczi('+tcd[1]+')',300);}else{alert('提交失败，请稍后重试！');}}};x.send();}
//网付查询
function ddcx(){var x=xml();x.open('Get','http://www.1000vw.com/Qw/sync.php?x='+oid+'-'+tcd[0]+'-c-&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText.split('OK');var s=0;for(var i=1;i<t.length;i++){var h=t[i].split('|');if(jtbb.value.indexOf(h[2])<0){dsf.style.display='none';dsff.innerHTML='';clearTimeout(wid);alert('手机扫码支付已成功！\n\n单　号：'+h[0]+'\n金　额：'+h[1]/100+'元\n时　间：'+h[3]+'\n交易号：'+h[2]);var d='['+['W:','A:'][jbtf.options.selectedIndex-4]+(h[1]/100)+':'+h[2]+']';jtbb.value=jtbb.value.replace(d,'')+d;s=1;}}if(s==0)wid=setTimeout('ddcx()',3000);}};x.send();}
//打扫清台
function ddqt(){if(tcz[tcd[1]][4]!='0'&&tcz[tcd[1]][4]!='1'&&tcz[tcd[1]][4]!='12'){alert('已下单及未结账，不能清台！');return;}if(!confirm('确认清台？？'))return;var x=xml();x.open('Get',uri+'x=dqt&s='+sid+'&a='+tcz[tcd[1]][0]+'&b='+tcd[0]+'&'+Math.random(),true);x.onreadystatechange=function(){if(x.readyState==4&&x.status==200){var t=x.responseText;if(t=='OK'){hide('ntbl');clearTimeout(cid);ldts();}else{alert('提交失败，请稍后重试！');}}};x.send();}
//打印小票
function ddpt(){if(tcd[0]=='')return;var o=document.getElementById(navigator.userAgent.indexOf('MSIE')>=0?'LdpIE':'LdpGC');if(typeof(o.VERSION)=='undefined'){alert('未安装打印控件！');return;}o.PRINT_INIT('Print:'+sid);o.SET_PRINT_PAGESIZE(3,scp[0]*10,scp[2]*10,'CreateCustomPage');o.ADD_PRINT_HTM(0,0,scp[1]*10,500,'URL:index.php?x=dpt&s='+sid+'&b='+tcd[0]);o.PREVIEW();}
</script>
</head>
<body onLoad="atwh();nver();">
<script type="text/javascript">document.write('<object id="LdpIE" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width="0" height="0"><embed id="LdpGC" type="application/x-print-lodop" width="0" height="0"></embed></object>');</script>
<!--遮罩层-->
<div id="dmsk" style="position:absolute;background-color:#000;opacity:0.2;filter:Alpha(Opacity=20);z-index:20;display:none;"></div>
<!--登陆层-->
<div id="dlgn" style="position:absolute;width:19200px;height:10800px;background-color:#F6F6F6;z-index:99;display:block;">
  <div id="dlgm" style="width:600px;height:300px;margin:0px auto;background-color:#09F;color:#FFF;margin-top:100px;line-height:40px;text-align:center;font-size:16px;">
    <br />千味餐厅点餐支付管理系统<br /><br />
    用户名：<input type="text" id="unm" style="padding:5px:font-size:16px;width:200px;" value="" /><br />密　码：<input type="password" id="upw" style="padding:5px:font-size:16px;width:200px;" value="" /><br /><br /><input type="button" value="登　陆" style="border:1px solid #FFF;background-color:#09F;color:#FFF;height:32px;width:80px;font-size:16px;" onClick="ulgn();" />
  </div>
</div>
<!--顶部层-->
<div id="dtop" style="height:60px;background-color:#333;">
  <div style="float:left;margin:3px;"><img src="i/i.jpg" width="54" height="54" /></div>
  <div style="float:left;font-size:32px;color:#6CF;line-height:60px;" id="dtnm"></div>
  <div style="float:left;font-size:14px;color:#FFF;padding:30px 0px 0px 10px;" id="dtnu"></div>
  <div style="float:left;font-size:14px;color:#FFF;padding:30px 0px 0px 10px;">[<a href="#" onclick="this.href=uri+'x=ewm&s='+sid;" target="_blank">二维码</a>]</div>
  <div style="float:right;width:300px;background-image:url(i/t.jpg);">
    <div class="dtop" style="float:left;" onClick="shdt();"></div>
    <div class="dtop" style="float:left;" onClick="shur();"></div>
    <div class="dtop" style="float:left;" onClick="shct();"></div>
    <div class="dtop" style="float:left;" onClick="if(rid.indexOf('S')>-1){shst(1);llbs();}else{alert('无操作权限！');}"></div>
    <div class="dtop" style="float:left;" onClick="if(confirm('确认退出？？'))location.reload();"></div>
  </div>
</div>
<!--设置菜类层-->
<div id="dse1a" style="position:absolute;width:300px;height:150px;z-index:88;display:none;background-color:#FFF;">
  <div class="dsre"><div class="dsrc" onClick="hide();">×</div>添加/编辑分类</div>
  <div class="dsrm">分类名称： <input type="text" style="width:120px;" id="inl1" /><br />分类排序： <input type="text" style="width:30px;" id="inl2" onBlur="num();" /> <input type="checkbox" id="inl3" /> 隐藏</div>
  <div class="dsrb"><div class="dsrl"><span class="btn" onClick="dlbs();">删 除</span></div><div class="dsrr"><span class="btn" onClick="slbs();">保 存</span></div></div>
</div>
<!--设置菜品层-->
<div id="dse1b" style="position:absolute;width:640px;height:310px;z-index:87;display:none;background-color:#FFF;">
  <div class="dsre"><div class="dsrc" onClick="hide();">×</div>添加/编辑菜品</div>
  <div class="dsrm">菜品名称： <input type="text" style="width:200px;" id="inc1" /><br>所属分类： <span id="dse1bf"></span><br />菜品排序： <input type="text" style="width:30px;" id="inc3" onBlur="num();" /><br />菜品描述： <input type="text" id="inc4" style="width:360px;" /><br />菜品图片： <iframe id="ifm3" src="" width="84" height="26" frameborder="0" scrolling="no"></iframe> <input type="hidden" id="cpp" value="0" /><span><a id="cqq" href="" target="_blank"></a></span><br />售　　价： <input type="text" id="inc6" style="width:60px;" onBlur="num(1);" /> 元　<input type="checkbox" id="inc7" /> 隐藏</div>
  <div class="dsrb"><div class="dsrl"><span class="btn" onClick="dcds();">删 除</span></div><div class="dsrr"><span class="btn" onClick="scds();">保 存</span></div></div>
</div>
<!--设置餐桌层-->
<div id="dse2" style="position:absolute;width:420px;height:210px;z-index:89;display:none;background-color:#FFF;">
  <div class="dsre"><div class="dsrc" onClick="hide();">×</div>添加/编辑餐桌</div>
  <div style="float:right"><img src="" id="dsei" width="140" /></div>
  <div class="dsrm">餐桌名： <input type="text" style="width:100px;" id="int1" /><br />排　序： <input type="text" style="width:30px;" id="int2" onBlur="num();" /><br />可以坐： <input type="text" style="width:30px;" id="int3" onBlur="num();" /> 人<br />下载右边二维码粘贴在餐桌上</div>
  <div class="dsrb"><div class="dsrl"><span class="btn" onClick="dczs();">删 除</span></div><div class="dsrr"><span class="btn" onClick="sczs();">保 存</span></div></div>
</div>
<!--设置员工层-->
<div id="dse3" style="position:absolute;width:420px;height:240px;z-index:85;display:none;background-color:#FFF;">
  <div class="dsre"><div class="dsrc" onClick="hide();">×</div>添加/编辑员工</div>
  <div class="dsrm">员工姓名： <input type="text" style="width:200px;" id="iny1" /><br />员工昵称： <input type="text" style="width:200px;" id="iny2" /><br />登陆名称： <input type="text" style="width:200px;" id="iny3" /><br />登陆密码： <input type="text" style="width:200px;" id="iny4" /> <a href="javascript:void(0);" onClick="iny4.value=Math.floor(Math.random()*899999+100000)">随机</a><br />员工权限：<span id="dse3r"></span></div>
  <div class="dsrb"><div class="dsrl"><span class="btn" onClick="dygs();">删 除</span></div><div class="dsrr"><span class="btn" onClick="sygs();">保 存</span></div></div>
</div>
<!--统计层-->
<div id="dcnt" class="dvs">
  <div style="padding:20px;"><div class="dsrt">数据统计　　<input type="text" id="dt1" size="8" onFocus="ymd();" /> - <input type="text" size="8" id="dt2" onFocus="ymd();" /> <span class="btn" onClick="cttj();">统 计</span> <span class="btn" onClick="window.open('index.php?x=cns&s='+sid+'&a='+dt1.value+'&b='+dt2.value);">详 单</span> <span>[<a href="javascript:void(0);" onClick="shct(0);">日报</a>]</span> <span>[<a href="javascript:void(0);" onClick="shct(6);">周报</a>]</span> <span>[<a href="javascript:void(0);" onClick="shct(29);">月报</a>]</span> <span>[<a href="javascript:void(0);" onClick="shct(364);">年报</a>]</span>　　　<span class="btn" onClick="dcnt.style.display='none';">返 回</span></div>
    <div class="cnti"><div class="cntt">营业额</div><div id="cnt1" class="cntm">0</div><div class="cntr" id="ctt1"></div></div>
    <div class="cnti"><div class="cntt">点单数</div><div id="cnt2" class="cntm">0</div><div class="cntr" id="ctt2"></div></div>
    <div class="cnti"><div class="cntt">点菜数</div><div id="cnt3" class="cntm">0</div><div class="cntr" id="ctt3"></div></div>
    <div class="cnti"><div class="cntt">退菜数</div><div id="cnt4" class="cntm">0</div><div class="cntr" id="ctt4"></div></div>
    <div class="cnti" style="width:622px;"><div class="cntt">支付渠道</div><div class="cntz"><div>现金支付<br /><span id="cnt5"></span><br /><span id="ctt5"></span></div><div>团购券支付<br /><span id="cnt6"></span><br /><span id="ctt6"></span></div><div>刷卡支付<br /><span id="cnt7"></span><br /><span id="ctt7"></span></div><div>会员卡支付<br /><span id="cnt8"></span><br /><span id="ctt8"></span><br /></div><div>微信支付<br /><span id="cnt9"></span><br /><span id="ctt9"></span></div><div>支付宝支付<br /><span id="cnt10"></span><br /><span id="ctt10"></span></div><div>会员充值<br /><span id="cnt11"></span><br /><span id="ctt11"></span></div></div></div>
    <div class="cnti"><div class="cntt">畅销菜品</div><div id="ctc1" class="cntc"></div></div>
    <div class="cnti"><div class="cntt">优秀员工</div><div id="ctc2" class="cntc"></div></div>
  </div>
</div>
<!--会员充值层-->
<div id="duc" style="position:absolute;width:320px;height:160px;z-index:72;display:none;background-color:#FFF;">
  <div class="dsre"><div class="dsrc" onClick="duc.style.display='none';">×</div>会员充值</div>
  <div class="dsrm">支付金额： <input type="text" size="5" id="inh8" /> 元<br />充值金额： <input type="text" size="5" id="inh9" /> 元</div>
  <div class="dsrb"><div class="dsrl" id="ducp" style="display:none;"><span class="btn" onClick="phys();">打 单</span></div><div class="dsrr" id="ducd"><span class="btn" onClick="mhys();">充 值</span></div></div>
</div>
<!--会员设置层-->
<div id="due" style="position:absolute;width:450px;height:300px;z-index:71;display:none;background-color:#FFF;">
  <div class="dsre"><div class="dsrc" onClick="hide();">×</div>添加/编辑会员</div>
  <div class="dsrm">会员卡号： <input type="text" style="width:200px;" id="inh1" /><br />会员名称： <input type="text" style="width:200px;" id="inh2" /><br />消费密码： <input type="text" style="width:200px;" id="inh3" /><br />注册时间： <font id="inh4"></font><br />帐户余额： <span id="inh5" style="font-size:18px;"></span> 元 <span>[<a href="javascript:void(0);" onClick="chys();">充值</a>]</span>　　消费金额： <span id="inh6" style="font-size:18px;"></span> 元 <span>[<a href="javascript:void(0);" onClick="window.open(urm+'x=jhy&m='+scc[5]+'&n='+scc[6]+'&a='+inh1.value);">记录</a>]</span><br />最后消费： <font id="inh7"></font></div>
  <div class="dsrb"><div class="dsrl"><span class="btn" onClick="dhys();">删 除</span></div><div class="dsrr"><span class="btn" onClick="shys();">保 存</span></div></div>
</div>
<!--会员层-->
<div id="dusr" class="dvs">
  <div style="padding:20px;"><div class="dsrt">会员管理　　<input type="text" id="ducs" /> <span class="btn" onClick="lhys();">查 找</span>　<span class="btn" onClick="ehys(-1);">新 增</span>　　　<span class="btn" onClick="dusr.style.display='none';">返 回</span></div>
    <div class="duls"><div class="duli">ID</div><div class="duli">卡号</div><div class="duli">名称</div><div class="duli">余额</div><div class="duli">消费</div><div class="duli">操作</div></div>
    <div id="duls"></div>
  </div>
</div>
<!--设置层-->
<div id="dset" class="dvs">
  <div id="dstl" style="float:left;width:150px;">
    <div id="dsl1" class="dsl2" onClick="shst(1);llbs();">菜单设置</div>
    <div id="dsl2" class="dsl1" onClick="shst(2);lczs();">餐桌设置</div>
    <div id="dsl3" class="dsl1" onClick="shst(3);lygs();">员工设置</div>
    <div id="dsl4" class="dsl1" onClick="shst(4);lzks();">折扣设置</div>
    <div id="dsl5" class="dsl1" onClick="shst(5);ldys();">打印设置</div>
    <div id="dsl6" class="dsl1" onClick="shst(6);ljbs();">基本设置</div>
    <div class="dsl1" onClick="dset.style.display='none';">返回餐厅</div>
    <div class="dsl1"><a href="http://www.1000vw.com/Qw/user.php" target="_blank">千味系统</a></div>
  </div>
  <div id="dstr" style="float:left;padding:10px 20px;overflow-y:auto;">
    <div id="dsr1"><div class="dsrt">菜单设置　　<span class="btn" onClick="elbs(-1);">添加分类</span>　<span class="btn" onClick="if(scl.length<1){alert('请至少添加一个菜品类别！');return false;}ecds(-1);">添加菜品</span></div>
      <div id="ds1la" style="float:left;width:150px;"></div>
      <div id="ds1lb" style="float:left;width:800px;"></div>
    </div>
    <div id="dsr2"><div class="dsrt">餐桌设置　　<span class="btn" onClick="eczs(-1);">添加餐桌</span></div>
      <div id="ds2l"></div>
    </div>
    <div id="dsr3"><div class="dsrt">员工设置　　<span class="btn" onClick="eygs(-1);">添加员工</span>　<span>权限：A=点单，B=收银，C=厨师，D=送餐，E=前台，S=系统。</span></div>
      <div class="ds3l"><div class="ds3i">ID</div><div class="ds3i">用户名</div><div class="ds3i">姓名</div><div class="ds3i">昵称</div><div class="ds3i">权限</div><div class="ds3i">最后登陆</div></div>
      <div id="ds3l"></div>
    </div>
    <div id="dsr4"><div class="dsrt">折扣设置</div>
      <div id="ds5l" class="dsrm"></div>
      <div class="dsrm">折扣+： <input type="text" id="izkn0"> 满 <input type="text" style="width:40px;" id="izkm0" onBlur="num(1);" /> 元 打/减 <input type="text" style="width:40px;" id="izkj0" onBlur="num(1);" /> 折/元<br />说明：后面折扣率大于1为满减模式，小于1为折扣模式。如满百减五分别填写100和5，会员九折则填写1和0.9。<br />删除某个折扣请将折扣名称清空保存即可。<br />　　　　 <span class="btn" onClick="szks();">保 存</span></div>
    </div>
    <div id="dsr5"><div class="dsrt">打印设置</div>
      <div class="dsrm">
        <div style="float:left;width:600px;">顶部Logo： <iframe id="ifm2" src="index.php?x=u&s=&a=p" width="84" height="26" frameborder="0" scrolling="no"></iframe> <input type="hidden" id="ppp" value="0" /><span><a id="pqq" href="i/p.jpg" target="_blank">[浏览图片]</a> </span> <input type="checkbox" id="inr9" /> 打印<br />纸张宽度： <input type="text" size="4" id="inr0" onBlur="num();dyyl.style.width=this.value*1-inr2.value*1*2+'mm';" /> mm<br />页面宽度： <input type="text" size="4" id="inr1" onBlur="num();dyyl.style.padding=inr2.value+'mm '+(inr0.value*1-inr1.value*1)/2+'mm';" /><br />上下边距： <input type="text" size="4" id="inr2" onBlur="num();dyyl.style.padding=inr2.value+'mm '+(inr0.value*1-inr1.value*1)/2+'mm';" /> mm<br />顶部文字： <input type="text" id="inr3" onBlur="dyt1.innerText=this.value;" /> 大小 <input type="text" size="2" id="inr4" onBlur="num();dyt1.style.fontSize=this.value+'px';dyt1.style.lineHeight=this.value*1+8+'px';" /> px<br />底部文字1： <input type="text" id="inr5" onBlur="dyt2.innerText=this.value;" /> 大小 <input type="text" size="2" id="inr6" onBlur="num();dyt2.style.fontSize=this.value+'px';dyt2.style.lineHeight=this.value*1+8+'px';" /> px<br />底部文字2： <input type="text" id="inr7" onBlur="dyt3.innerText=this.value;" /> 大小 <input type="text" size="2" id="inr8" onBlur="num();dyt3.style.fontSize=this.value+'px';dyt3.style.lineHeight=this.value*1+8+'px';" /> px<br /><span>[<a href="http://www.1000vw.com/Qw/i/Lodop.exe" target="_blank">打印控件下载</a>]</span>　 <span class="btn"  onClick="sdys();">保 存</span>　<span class="btn" onClick="tdys();">测 试</span></div>
        <style>#dyyl{width:50mm;padding:4mm;line-height:20px;font-family:宋体;font-size:12px;}#dyyl div{font-family:宋体;font-size:12px;}.dyrl div{float:right;width:32px;text-align:right;}</style>
        <div id="dyyl" style="float:left;border:1px solid #CCC;">
          <div id="dyt1" style="text-align:center;font-size:18px;line-height:26px;">我的餐厅</div>
          <div style="float:left;">单号：8888<br />桌号：8</div>
          <div style="float:right;">服务员：88<br />收银员：8</div>
          <div style="clear:both;"></div>
          下单时间：2015-7-1 12:00:00<br />结账时间：2015-7-1 13:30:00
          <div class="dyrl"><div>金额</div><div>数量</div><div>单价</div>品名</div><hr />
          <div class="dyrl"><div>88</div><div>1</div><div>88</div>油焖大虾</div>
          <div class="dyrl"><div>18</div><div>1</div><div>18</div>鱼香肉丝</div>
          <div class="dyrl"><div>18</div><div>1</div><div>18</div>宫保鸡丁</div>
          <div class="dyrl"><div>12</div><div>1</div><div>12</div>麻辣豆腐</div>
          <div class="dyrl"><div>20</div><div>4</div><div>5</div>青岛啤酒</div><hr />
          <div style="text-align:left;">合计： 156 元<br />备注： 限时九折 会员九折<br />实收： <span style="font-size:18px;font-family:黑体;">126</span> 元[现金支付]</div>
          <div id="dyt2" style="text-align:center;clear:both;font-size:18px;line-height:26px;">欢迎下次光临</div>
          <div id="dyt3" style="text-align:left;">千味餐厅管理系统 1000vw.com</div>
        </div>
      </div>
    </div>
    <div id="dsr6"><div class="dsrt">基本设置</div>
      <div class="dsrm">餐厅名称： <input type="text" id="in0" /><br />联系电话： <input type="text" id="in1" /><br />餐厅地址： <input type="text"  style="width:400px;"id="in2" /><br />餐厅简介： <input type="text" style="width:400px;" id="in3" /><br />餐厅Logo： <iframe id="ifm1" src="index.php?x=u&s=&a=i" width="84" height="26" frameborder="0" scrolling="no"></iframe> <input type="hidden" id="ipp" value="0" /><span><a id="iqq" href="i/i.jpg" target="_blank">[浏览图片]</a></span><br />主机地址： <input type="text" size="30" id="in4" /><br /><br /><strong>连锁模式</strong>　<span>如您有ID为1000的主店和ID为1001，1002，1003...等若干分店，则下面填写主店ID 1000和相应登录密码。<br />修改主店登录密码请务必同步修改此项，填写错误将不能使用会员功能。（密码保存时会使用md5(md5(password))加密）</span><br />主商户ID： <input type="text" id="in5" onblur="num();" /><br />登录密码： <input type="password" id="in6" onfocus="this.type='text';this.value='';" onblur="this.type='password';if(this.value=='')this.value=in66.value;" /><input type="hidden" id="in66" /><br />　　　　　 <span class="btn" onClick="if(in6.value!=in66.value)in6.value=md5(md5(in6.value));sjbs();">保 存</span></div>
    </div>
  </div>
</div>
<!--餐桌详情层-->
<div id="ntbl" style="position:absolute;width:800px;height:480px;background-color:#FFF;border:1px solid #09F;z-index:50;display:none;">
  <div class="dsre"><div class="dsrc" onClick="ntbl.style.display='none';dmsk.style.display='none';">×</div><span id="ntbz" style="font-size:14px;"></span></div>
  <div style="width:780px;height:395px;padding:10px;line-height:30px;overflow:auto;">
    <div class="ntl1"><div class="ntl2">菜品</div><div class="ntl3">单价</div><div class="ntl4">数量</div><div class="ntl3">状态</div><div class="ntl4">下单</div><div class="ntl4">厨师</div><div class="ntl4">送餐</div><div class="ntl3">操作</div></div>
    <div id="ntbc"></div>
  </div>
  <div class="dsrb" style="text-align:center;padding:6px 0px 0px 0px;height:27px;"><span class="btn" onClick="ddrz();">入 座</span>　<span class="btn" onClick="dddd();">点 单</span>　<span class="btn" onClick="ddcf();">交 厨</span>　<span class="btn" onClick="ddjz();">结 账</span>　<span class="btn" onClick="ddhz();">换 桌</span>　<span class="btn" onClick="ddqt();">清 台</span>　<span class="btn" onClick="ddpt();">打 单</span></div>
</div>
<!--餐桌结算层-->
<div id="jtbl" style="position:absolute;width:800px;height:480px;background-color:#FFF;border:1px solid #09F;z-index:51;display:none;">
  <div class="dsre"><div class="dsrc" onClick="jtbl.style.display='none';clearTimeout(wid);">×</div>结账</div>
  <div id="jtbi" style="padding:10px 20px;line-height:30px;"></div>
  <div id="jtbz" style="padding:10px 20px;line-height:30px;height:100px;"></div>
  <div style="padding:20px;line-height:40px;height:200px;">应付： <span id="jtby" style="font-size:24px;color:#F00;font-family:Tahoma;width:80px;border:1px solid #B5E6FD;text-align:center;display:inline-block;"></span> 元　抵扣： <input type="text" style="font-size:24px;font-family:Tahoma;width:72px;color:#F00;text-align:center;" id="jtbq" onblur="num(1);ddjs();" /> 元　折后： <span id="jtbk" style="font-size:24px;color:#F00;font-family:Tahoma;width:80px;border:1px solid #B5E6FD;text-align:center;display:inline-block;"></span> 元　实付： <input type="text" style="font-size:24px;font-family:Tahoma;width:72px;color:#F00;text-align:center;" id="jtbs" /> 元 <input type="checkbox" id="jtbc" checked="checked" onclick="ddjs();" /> 抹零<br /><br />支付方式： <select id="jbtf" style="font-size:14px;font-family:微软雅黑;" onChange="jtbh.style.display=this.selectedIndex==3?'inline-block':'none';jtba.style.display=(this.selectedIndex==4||this.selectedIndex==5)?'inline-block':'none';"><option value="0">现金支付</option><option value="1">团购券支付</option><option value="2">刷卡支付</option><option value="3">会员卡支付</option><option value="4">微信支付</option><option value="5">支付宝支付</option></select>　<span id="jtba" style="display:none;"><a href="javascript:void(0);" onclick="dewm();">扫二维码</a></span><span id="jtbh" style="display:none;">卡号： <input type="text" id="jtbu" /> 密码： <input type="password" id="jtbp" /></span><br />本单备注： <input type="text" style="width:500px;" id="jtbb" /><div style="text-align:center;"></div></div>
  <div class="dsrb"><div class="dsrl"><span class="btn" onClick="ddjk();">稍后结账</span></div><div class="dsrr"><span class="btn" style="font-size:18px;line-height:26px;" onClick="ddfk();">确定结账</span></div></div>
</div>
<!--餐桌点单层-->
<div id="xtbl" style="position:absolute;width:1000px;height:560px;background-color:#FFF;border:1px solid #09F;z-index:52;display:none;">
  <div class="dsre"><div class="dsrc" onClick="xtbl.style.display='none';">×</div>下单</div>
  <div style="float:right;width:268px;height:528px;background-color:#DEF;">
    <div style="line-height:39px;text-align:center;border-bottom:1px solid #09F;">已点菜单</div>
    <div id="xtls" style="width:268px;height:440px;overflow:auto;"></div>
    <div style="height:20px;text-align:center;">备注： <input type="text" id="xtbz" style="font-size:12px;padding:1px;width:200px;" /></div>
    <div style="height:30px;text-align:center;">总价： <span id="xtle" style="width:60px;display:inline-block;">0</span> 元　　<span class="btn" onClick="dddx();">下 单</span></div>
  </div>
  <div id="xtlm" style="float:left;width:732px;height:39px;border-bottom:1px solid #09F;"></div>
  <div id="xtlc" style="float:left;width:732px;height:487px;overflow:auto;"></div>
</div>
<!--退单层-->
<div id="dst" style="position:absolute;width:360px;height:180px;z-index:53;display:none;background-color:#FFF;">
  <div class="dsre"><div class="dsrc" onClick="dst.style.display='none';">×</div>修改</div>
  <div class="dsrm">桌号： <font id="inb1"></font>　菜品： <font id="inb2"></font><br />价格： <input type="text" size="4" id="inb6" onblur="num();" /> 元　数量： <span style="font-size:12px;cursor:pointer;background-color:#09F;color:#FFF;width:16px;height:16px;line-height:16px;display:inline-block;text-align:center;" onclick="inb4.innerText=inb4.innerText*1-1;if(inb4.innerText*1<1)inb4.innerText=1;">-</span>　<span id="inb4"></span>　<span style="font-size:12px;cursor:pointer;background-color:#09F;color:#FFF;width:16px;height:16px;line-height:16px;display:inline-block;text-align:center;" onclick="inb4.innerText=inb4.innerText*1+1;if(inb4.innerText*1>inb5.value*1)inb4.innerText=inb5.value;">+</span><input type="hidden" id="inb5" /><br />退单原因： <input type="text" style="width:120px;" id="inb3" /></div>
  <div class="dsrb"><div class="dsrl"><span class="btn" onClick="ddtd(0);">拒 退</span>　<span class="btn" onClick="ddtd(1);">退 菜</span></div><div class="dsrr"><span class="btn" onClick="ddsl();">修 改</span></div></div>
</div>
<!--换桌层-->
<div id="dsh" style="position:absolute;width:300px;height:160px;z-index:54;display:none;background-color:#FFF;">
  <div class="dsre"><div class="dsrc" onClick="dsh.style.display='none';">×</div>换桌</div>
  <div class="dsrm">原桌号： <span id="dsht"></span><br />更换至： <select id="dshs"></select></div>
  <div class="dsrb"><div class="dsrr"><span class="btn" onClick="ddhs();">更 换</span></div></div>
</div>
<!--二维码层-->
<div id="dsf" style="position:absolute;width:800px;height:480px;z-index:55;display:none;background-color:#FFF;">
  <div class="dsre"><div class="dsrc" onClick="dsf.style.display='none';clearTimeout(wid);">×</div>网付</div>
  <div id="dsff" style="width:798px;height:300px;margin-top:50px;text-align:center;"></div>
  <div style="text-align:center;font-size:20px;height:100px;">支付金额： <span id="dsfe" style="font-size:32px;color:#F00;"></span> 元</div>
</div>
<!--大厅层-->
<div id="dtbl" style="overflow:auto;"></div>
</body>
</html>
<?
}
?>