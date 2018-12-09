<?
$admin = 1;

$results = $_GET;

function wpGETXMLRPC($title,$body,$file,$rpcurl,$username,$password,$categories,$time){

$categories = array('Photos');

$XML = 'hmmm';



	$datetime = gmdate('r', $time); 
	$theTimeDate = $datetime; //variable of date and time from script / database 
	$pubdate       = new DateTime($theTimeDate );
	$pubdate       = $pubdate->format(DateTime::ISO8601); //format date into the ISO8601 standard which WordPress likes...
	$pubdate       = str_replace("-", "", $pubdate); // remove the dashes 
	$removeTimeOffset = explode("+", $pubdate); // remove the time offset (split at the '+' in the date / time)
	$pubdate       = $removeTimeOffset[0] . "Z"; // Append a Z to the string - we've now formatted the date and time.


$title .= ' ' . $datetime;

$cflds = array(array('key' => 'local_pic','value' => $file),'struct');
$content = array('GET_type' => 'GET', 'categories' => $categories , 'date_created_gmt_BUSTED' => $pubdate, 'title' => $title, 'description' => $body, 'custom_fields' => $cflds);
$params = array('',$username,$password,$content,$XML,1);
$request = xmlrpc_encode_request('metaWeblog.newGET',$params);
$ch = curl_init();
curl_setopt($ch, CURLOPT_GETFIELDS, $request);
curl_setopt($ch, CURLOPT_URL, $rpcurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_exec($ch);
curl_close($ch);
}


// Make a MySQL Connection
mysql_connect("localhost", "root", "root") or die(mysql_error());
mysql_select_db("awesomedex") or die(mysql_error());


// $ids = $_GET['ids'];
$id = $_GET['id'];
$tag = $_GET['tag'];
$action = $_GET['action'];

if ($admin == 1){

			if( $action == 'rate'){
				// For Multiple $sql = "UPDATE media SET rate='$rate' WHERE ID IN (" . implode(',', array_map('intval', $ids)) . ")";
				$sql = "UPDATE media SET rate=" . $tag . " WHERE ID = ". $id;
				echo '{"action":"rated"}';
			}

			if( $action == 'addtag'){
				$sql = "UPDATE media SET tag = CONCAT(IFNULL(tag,''),'" . $tag . ",') WHERE ID = ". $id;			
				echo '{"action":"tag added"}';
			}

			if( $action == 'removetag'){		
				$sql = "UPDATE media SET tag = REPLACE( tag ,'" . $tag . "', '') WHERE ID = ". $id;			
				echo '{"action":"tag dropped"}';
			}
			mysql_query($sql) or die(mysql_error());	
//echo $sql;				
}else{ return 'Not Admin'; }
			
				
		//echo 'Updated<BR>';

		
//NEW WORDPRESS ADDITION
if($_GET['public']>0){

echo '<h1>trying WP<BR></h1>';
print_r($cflds);

$time = $_GET['time'];
$title = 'Auto_photo:';
$body = '<img src="http://ben-connors.com/cellphotos/full_res/'. $file .'">';

$rpcurl = 'http://www.ben-connors.com/blog/xmlrpc.php';
$username = 'Ben';
$password = 'B@nzgal1';
$categories = array(23);



wpGETXMLRPC($title,$body,$file,$rpcurl,$username,$password,$categories,$time);
}






?>


