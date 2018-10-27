<?php 
$serverID = $_GET["serverid"];
if(!$serverID)
	die('no serverID');
	
$filePath = dirname(__FILE__).'/';
require_once($filePath."_config.php");


	
	
$connect=mysql_connect($sql_url,$sql_user,$sql_password)or die('message=F,Could not connect: ' . mysql_error()); 
mysql_select_db($sql_db,$connect)or die('Could not select database'); 
mysql_query("set names utf8");


//自己的数据
mysql_query("
Create TABLE g2_".$sql_table."user_data(
gameid varchar(32) NOT NULL Unique Key,
uid INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
nick varchar(30),
head varchar(255),
type TINYINT UNSIGNED default 0,
hourcoin INT UNSIGNED default 100,
rmb INT UNSIGNED default 0,
diamond INT UNSIGNED default 100,
level TINYINT UNSIGNED default 1,
tec_force SMALLINT UNSIGNED default 0,
coin varchar(255),
energy varchar(255),
card Text,
prop Text,
tec Text,
atk_list Text,
def_list Text,
hang Text,
pk_common Text,
active Text,
land_key varchar(63),
last_land INT UNSIGNED,
regtime INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

//会被其它人写到的数据
mysql_query("
Create TABLE g2_".$sql_table."user_open(
gameid varchar(32) NOT NULL Unique Key,
masterstep Text,
mailtime INT UNSIGNED,
slavetime INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 


//关注的人
mysql_query("
Create TABLE g2_".$sql_table."friend(
gameid varchar(32) NOT NULL Unique Key,
friend Text
)",$connect)or die("message=F,Invalid query: " . mysql_error());


//奴隶表
mysql_query("
Create TABLE g2_".$sql_table."slave(
gameid varchar(32) NOT NULL,
nick varchar(30),
head varchar(255),
type TINYINT UNSIGNED,
hourcoin INT UNSIGNED,
tec_force SMALLINT UNSIGNED,
level TINYINT UNSIGNED,
master varchar(32),
addtime INT UNSIGNED,
protime INT UNSIGNED,
awardtime INT UNSIGNED,
logintime INT UNSIGNED,
INDEX (master),
UNIQUE (gameid),
INDEX (tec_force)
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 


//日志（邮件）
mysql_query("
Create TABLE g2_".$sql_table."mail(
id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
from_gameid varchar(16),
to_gameid varchar(16),
type TINYINT UNSIGNED,
content varchar(8138),
stat TINYINT UNSIGNED,
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."pay_log(
id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
orderno varchar(32),
orderno2 varchar(8),
goodsid varchar(32),
gameid varchar(32),
time INT UNSIGNED,
INDEX(orderno,gameid,orderno2)
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."shop(
gameid varchar(32) NOT NULL Unique Key,
shop varchar(511),
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."view(
gameid varchar(32) NOT NULL Unique Key,
viewlist Text
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."card_like(
id SMALLINT UNSIGNED NOT NULL Unique Key,
like_num INT UNSIGNED,
unlike_num INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

//往表插入数据
$sql = "insert into g2_".$sql_table."card_like(id,like_num,unlike_num) values";
$arr = array();
for($i=1;$i<=300;$i++)
{
	array_push($arr,"(".$i.",0,0)");
}
$sql2 = implode(',',$arr);
mysql_query($sql.$sql2,
$connect)or die("message=F,Invalid query: " . mysql_error()); 

//排行榜
$rankName = array('force','hang','hourcoin');
foreach($rankName as $key=>$value)
{
	mysql_query("
	Create TABLE g2_".$sql_table."rank_".$value."(
	gameid varchar(32) NOT NULL Unique Key,
	nick varchar(30),
	head varchar(255),
	type TINYINT UNSIGNED,
	score INT UNSIGNED,
	time INT UNSIGNED
	)",$connect)or die("message=F,Invalid query: " . mysql_error());

	//往表插入数据
	$sql = "insert into g2_".$sql_table."rank_".$value."(gameid,score,time) values";
	$arr = array();
	for($i=1;$i<=100;$i++)
	{
		array_push($arr,"('_".$i."',0,0)");
	}
	$sql2 = implode(',',$arr);
	mysql_query($sql.$sql2,
	$connect)or die("message=F,Invalid query: " . mysql_error()); 
}

// mysql_query("
// Create TABLE g2_".$sql_table."pk_recode(
	// id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	// gameid varchar(32) NOT NULL,
	// pkdata varchar(512),
	// time INT UNSIGNED,
	// INDEX (gameid)
// )",$connect)or die("message=F,Invalid query: " . mysql_error()); 


mysql_query("
Create TABLE g2_".$sql_table."fight(
gameid varchar(32) NOT NULL Unique Key,
info varchar(512),
shop varchar(512),
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."video(
id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
level INT UNSIGNED,
info varchar(255),
data varchar(1023),
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error());

//往表插入数据
$sql = "insert into g2_".$sql_table."video(level,time) values";
$arr = array();
for($i=1;$i<=2000;$i++)
{
	array_push($arr,"(".$i.",0)");
	array_push($arr,"(".$i.",0)");
	array_push($arr,"(".$i.",0)");
	array_push($arr,"(".$i.",0)");
	array_push($arr,"(".$i.",0)");
}
$sql2 = implode(',',$arr);
mysql_query($sql.$sql2,
$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."prop_shop(
gameid varchar(32) NOT NULL Unique Key,
shop varchar(1024),
shop_base varchar(1024),
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."pvp(
gameid varchar(32) NOT NULL Unique Key,
task varchar(1024),
online varchar(1024),
offline varchar(1024),
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

//积分数据
mysql_query("
Create TABLE g2_".$sql_table."pvp_offline(
gameid varchar(32) NOT NULL Unique Key,
data varchar(1024),
score INT UNSIGNED,
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error());

mysql_query("
Create TABLE g2_".$sql_table."answer(
gameid varchar(32) NOT NULL Unique Key,
info varchar(1024),
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."random(
gameid varchar(32) NOT NULL Unique Key,
info varchar(1024),
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."choose(
gameid varchar(32) NOT NULL Unique Key,
info varchar(1024),
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("
Create TABLE g2_".$sql_table."endless(
gameid varchar(32) NOT NULL Unique Key,
info varchar(1024),
time INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 






echo "成功".time();
?>