<?php
mysql_connect('localhost', 'betbdl', '123@123') or die('server');
mysql_select_db('betdb') or die('db');
$rs = mysql_query("SELECT league_id, league_name FROM match WHERE league_id > 0") or die(mysql_error());
$i = 0;
while($row = mysql_fetch_assoc($rs)){
	$i ++;
	echo $i."-".$row['league_id']. "-". $row['league_name'];
	echo "<hr/>";
}
?>