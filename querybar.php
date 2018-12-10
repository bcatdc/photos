<?
$search_results = 0 ;

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

// Make a MySQL Connection
require "../../mysql_creds.php";

?>

<div id="SearchPane" class="collapse" style="border:1px solid #333; padding:15px; ">
<form action="index.php" method="get">
<div style="float:left;">
Start: &nbsp;  &nbsp; <input name="start" type=date min=1983-11-14 value="<? echo $_GET['start']; ?>"><BR><BR>
Stop: &nbsp;  &nbsp; <input name="stop" type=date value="<? echo $_GET['stop']; ?>"><BR>
</div>
<div style="float:left; padding-left:15px;">
Rating:<input name="rate" type=range  min=0 max=5 value="0" style="width:200px;"><BR>
View:
<select name="view">
  <option value="all">All</option>
  <option value="best">Best</option>
  <option value="pano">Panos</option>
  <option value="video">Videos</option>
  <option value="planet">Tiny Planet</option>
</select>

Oldest First
<input type="checkbox" name="flip" value="1">
Random
<input type="checkbox" name="rand" value="1">
<input class="btn btn-default" type="submit" value="search">
</div>
<div style="clear:both"></div>
</div>
</form>

<?


if(strlen($_GET['start'])>0){

$rate = $_GET['rate'];
$start = strtotime($_GET['start']);
$stop = strtotime($_GET['stop']);

}else{

$start = strtotime('1983-8-22') ;
$stop = time() + (7 * 24 * 60 * 60);
$rate = 0;
}

$orderby = 'time DESC';

if($_GET['flip']==1){
$orderby = 'time ASC';
}

if($_GET['rand']==1){
$orderby = 'RAND()';
}

$i=0;
if($_GET['page']=='' ){ $p=0; } else { $p = ($_GET['page']) * 500; }

if($_GET['view']=='best'){
$result = mysql_query("SELECT * FROM media WHERE rate >= $rate AND time > $start AND time < $stop AND tag LIKE '%best%' ORDER BY $orderby LIMIT $p, 500");

//echo "SELECT * FROM media WHERE rate >= ". $rate ." AND time >". $start ." AND time < " . $stop ." AND rate = '5' ORDER BY time $orderby LIMIT ". $p . ", 500";

}elseif($_GET['view']=='pano'){
$result = mysql_query("SELECT * FROM media WHERE rate >= $rate AND time > $start AND time < $stop AND tag LIKE '%pano%' ORDER BY $orderby LIMIT $p, 500");
}elseif($_GET['view']=='public'){
$result = mysql_query("SELECT * FROM media WHERE rate >= $rate AND time > $start AND time < $stop AND tag LIKE '%public%' ORDER BY $orderby LIMIT $p, 500");
}elseif($_GET['view']=='video'){
$result = mysql_query("SELECT * FROM media WHERE rate >= $rate AND time > $start AND time < $stop AND type = 'mp4' ORDER BY $orderby LIMIT $p, 500");
}elseif($_GET['view']=='planet'){
$result = mysql_query("SELECT * FROM media WHERE rate >= $rate AND file LIKE '%planet%' ORDER BY $orderby LIMIT $p, 500");
}else{
$result = mysql_query("SELECT * FROM media WHERE rate >= $rate AND time > $start AND time < $stop ORDER BY $orderby LIMIT $p, 500");
}

$rowCount = mysql_num_rows($result);

$view = $_GET['view'];
$page = $_GET['page'];

if ($page >= 1){
echo '<center><a href="index.php?start='. $_GET['start'] .'&stop='. $_GET['stop'] .'&rate='. $rate .'&view='. $view . '&page=' . ($page - 1) . '"><< Previous</a> | ';
}

if ($rowCount < 500){
echo $rowCount . ' Results';
}else{
echo '<a href="index.php?start='. $_GET['start'] .'&stop='. $_GET['stop'] .'&rate='. $rate .'&view='. $view . '&page=' . ($page + 1) . '">Next 500>></a></center>';
}
