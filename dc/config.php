<?
//======================================
$dbhost="localhost";	//MySql服务器
$dbuser="fangke";	//MySql用户名
$dbpass="fangke";	//MySql密码
$dbdata="fangke_diancan";	//MySql数据库
$OpID = "100000";	//商户ID
$OpPK = "dMXiVEP9Jr4EViQs";	//商户密钥
//======================================
$rc=mysql_connect($dbhost,$dbuser,$dbpass);
mysql_select_db($dbdata,$rc);
mysql_query("SET NAMES UTF8");
function Rnd(){$t="1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";$s="";for($i=0;$i<16;$i++)$s=$s.substr($t,rand(0,62),1);return $s;}
function Req($n){$t=isset($_GET[$n])?$_GET[$n]:"";$s="";$l=strlen($t);for($i=0;$i<$l;$i++){if($t[$i]=="%"&&$t[$i+1]=="u"){$v=hexdec(substr($t,$i+2,4));if($v<0x7f)$s.=chr($val);else if($v<0x800)$s.=chr(0xc0|($v>>6)).chr(0x80|($v&0x3f));else $s.=chr(0xe0|($v>>12)).chr(0x80|(($v>>6)&0x3f)).chr(0x80|($v&0x3f));$i+=5;}else if($t[$i]=="%"){$s.=urldecode(substr($t,$i,3));$i+=2;}else $s.=$t[$i];}return str_replace("\"","&#39;",$s);}
header("Content-Type:text/html;charset=UTF-8");
?>