<?

/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
///TO DO :

// Make tag and type searching more robust.
// TAG should have &/OR
// TYPE should have bundles, like RAW  & VIDEO

/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////


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

Tag:
<select name="tags">
  <option value="all">All</option>
  <option value="best">Best</option>
  <option value="select">Select</option>
  <option value="pano">Panos</option>
  <option value="planet">Tiny Planet</option>
</select>

Type:
<select name="type">
  <option value="jpg">JPG</option>
  <option value="mp4">Best</option>
  <option value="select">Select</option>
  <option value="pano">Panos</option>
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

$i=0;

if(isset($_GET['rate']) && strlen($_GET['rate'])>0){
    $rate = "rate >=". $_GET['rate'] ." AND";
}else{
    $rate = "";
}

if( isset($_GET['start']) && strlen($_GET['start'])>1){
        $start = strtotime($_GET['start']);
        $start_date=$_GET['start'];
    }else{
        $start = strtotime('1983-8-22') ;
        $start_date="1983-8-22";
}

if(isset($_GET['stop']) && strlen($_GET['stop'])>1){
        $stop = strtotime($_GET['stop']);
        $stop_date = $_GET['stop'];
    }else{
        $stop = time() + (7 * 24 * 60 * 60);
        $stop_date="";
}

if(isset($_GET['$flip'])){
    $orderby = 'time ASC';
}else{
    $orderby = 'time DESC';
}

if(isset($_GET['$rand'])){
    $orderby = 'RAND()';
}

if(isset($_GET['$page'])){
        $p = ($_GET['page']) * 500;
        $page = $_GET['page'];
    } else {
        $p=0;
        $page=0;
}

if(isset($_GET['tags']) && $_GET['tags'] != "all" ){
        $tags = " AND tag LIKE '%" . $_GET['tags'] ."%'";
    }else{
        $tags='';
}

if(isset($_GET['type']) ){
        $type = " AND type =  '". $_GET['type'] ."'";
    }else{
        $type=' ';
}
$query = "SELECT * FROM media WHERE ".$rate." time >". $start . " AND time <". $stop . $tags. $type." ORDER BY ". $orderby. " LIMIT ". $p.", 500";
//echo $query;
$result = mysqli_query($link, $query);

$rowCount = mysqli_num_rows($result);

if ($page >= 1){
echo '<center><a href="index.php?start='. $start_date.'&stop='. $stop_date .'&rate='. $rate .'&tags='. $tags .'&type='. $type . '&page=' . ($page - 1) . '"><< Previous</a> | ';
}

if ($rowCount < 500){
echo $rowCount . ' Results';
}else{
echo '<a href="index.php?start='. $start_date.'&stop='. $stop_date .'&rate='. $rate .'&tags='. $tags . '&type='. $type .'&page=' . ($page + 1) . '">Next 500>></a></center>';
}
