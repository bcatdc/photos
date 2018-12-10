
<!-- Google Maps API was here  -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="lib/bootstrap/js/bootstrap.min.js"></script>
<script src="star_rating_system.js"></script>
<script>

function show_map(){
console.log('clicked');
	document.getElementById("geo_overlay").style.display = "block";
    initialize();
}
</script>
<style>

</style>
<?

if ($_GET['geo_show'] == 1){
$geo_show = 'block';
}else{
$geo_show = 'none';
}

//Post to WP
function wpPostXMLRPC($title,$body,$file,$rpcurl,$username,$password,$categories,$time){

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
$content = array('post_type' => 'post', 'categories' => $categories , 'date_created_gmt_BUSTED' => $pubdate, 'title' => $title, 'description' => $body, 'custom_fields' => $cflds);
$params = array('',$username,$password,$content,$XML,1);
$request = xmlrpc_encode_request('metaWeblog.newPost',$params);
$ch = curl_init();
curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
curl_setopt($ch, CURLOPT_URL, $rpcurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_exec($ch);
curl_close($ch);
}

function epochDur($seconds) {
  $t = round($seconds);
  return sprintf('%02d days %02d hrs %02d mins %02d secs', ($t/86400),($t/3600%24),($t/60%60), $t%60);
}
?>

<script>
var fadedout = 1;

function showOverlay(){
	if ( fadedout == 0){
		console.log('wait');
	}else{

$('#overlay').addClass('showverlay');
$('#overlay').removeClass('hideverlay');
fadedout = 0;

setTimeout(function(){
  $('#overlay').addClass('hideverlay');
  $('#overlay').removeClass('showverlay');
fadedout = 1;
}, 4000);

}}

function smartToggle(el,update){
		if( el.hasClass("off")){
			el.removeClass('off');
			el.addClass('on');

				if(update == 'update'){
				var action = 'removetag';
    			console.log( el.data('id') + action +  el.data('tag') );
				updatedb(el.data('id'), action, el.data('tag'));
				}
    	}else{
			el.removeClass('on');
			el.addClass('off');
    		if(update == 'update'){
				var action = 'addtag';
    			console.log( el.data('id') + action +  el.data('tag') );
				updatedb(el.data('id'), action, el.data('tag'));
			}
    	}
    	}

$(document).ready(function(){
    $("span").tooltip();

    $("span").hover(function(){
		smartToggle($(this),'');
    });

    $("span").click(function(){
 		console.log( $(this).data('id') + ' - ' + $(this).data('tag')  );
		smartToggle($(this),'update');

});

});

function updatedb(id,action,tag){
console.log( action +'-'+ tag +'-'+ id);
$.ajax({
        method: "get",
        //url: 'jsdemo.php',
        url: 'db_update.php',
        data: {action:action, tag: tag, 'id': id},
        success: function(data){
            var result = JSON.parse(data);
            console.log(result);
            //Check the dev console in your browser
            //Do something with returned data
        }
});
}
</script>

<style>


.on{
opacity:1;
}

.off{
opacity:.33;
}


.pano{
color:#1abc9c;
}

.aes{
color:#2ecc71;
}

.nost{
color:#3498DB;
}

.remind{
color:#EA4c88;
}

.resource{
color:#EA4c88;
}

.share{
color:#e74c3c;
}

.thumb{
color:#FF926B;
}

.showverlay{
opacity:1;
   transition: opacity .25s ease-in-out;
}

.hideverlay{
opacity:0;
   transition: opacity 1s ease-in-out;
}

.tooltip.top .tooltip-inner {
    background-color:#222;
  }
.tooltip.top .tooltip-arrow {
          border-top-color: #222;
       }

.fourthree-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; }
</style>

<script>
function loadVideo(id,file,type){
document.getElementById(id).innerHTML = "<div class='grid' style='background-size: contain; background-repeat: no-repeat; '> <video style='width:100%;' controls> <source src='full_res/" + file +"' type='video/" + type + "'></video></div><BR><span style='font-size:10px;'></span>"
}
</script>


<body style="padding:0px; margin:0px; background-color:#000; color:#eee;" onload="" >



<?
// Make a MySQL Connection
require "../../mysql_creds.php";
?>

<?

if(strlen($_POST['i'])>0){


$id = $_POST['i'];
$blurb = $_POST['desc'];
$rate = $_POST['photo_rating'];
$file = $_POST['file'];




$latlon = str_replace(array( '(', ')' ), '', $_POST['latlon']);

$latlon = explode(',' , $latlon);
$lat = $latlon[0];
$lon = $latlon[1];

$manual_gps = 'Yes';

$tags = '';

if($_POST['aesthetic']>0){ $tags .= 'aesthetic,';}
if($_POST['nostalgic']>0){ $tags .= 'nostalgic,';}
if($_POST['cellphoto']>0){ $tags .= 'cellphoto,';}
if($_POST['best']>0){ $tags .= 'best,';}
if($_POST['pano']>0){ $tags .= 'pano,';}
if($_POST['resource']>0){ $tags .= 'resource,';}
if($_POST['public']>0){ $tags .= 'public,';}


if ($admin == 1){

		if(strlen($_POST['latlon'])>2){

			if(strlen($rate)>0){
				mysql_query("UPDATE media SET lat='$lat', lon='$lon', manual_gps ='$manual_gps' , rate='$rate', blurb='$blurb', tag='$tags' WHERE ID='$id' ") or die(mysql_error());
			}else{
				mysql_query("UPDATE media SET lat='$lat', lon='$lon', manual_gps ='$manual_gps' , blurb='$blurb', tag='$tags' WHERE ID='$id' ") or die(mysql_error());
			}


		}else{
			if(strlen($rate)>0){
				mysql_query("UPDATE media SET rate='$rate', blurb='$blurb', tag='$tags' WHERE ID='$id' ") or die(mysql_error());
			}else{
				mysql_query("UPDATE media SET blurb='$blurb', tag='$tags' WHERE ID='$id' ") or die(mysql_error());
			}
		}

		echo 'Updated<BR>';

//NEW WORDPRESS ADDITION
if($_POST['public']>0){

echo '<h1>trying WP<BR></h1>';
print_r($cflds);

$time = $_POST['time'];
$title = 'Auto_photo:';
$body = '<img src="http://ben-connors.com/cellphotos/full_res/'. $file .'">';

$rpcurl = 'http://www.ben-connors.com/blog/xmlrpc.php';
$username = 'Ben';
$password = 'B@nzgal1';
$categories = array(23);



wpPostXMLRPC($title,$body,$file,$rpcurl,$username,$password,$categories,$time);
}






		}
}


?>


<?
$i=0;


if (strlen($_GET['go'])>0){

$seq = $_GET['time'] ;

if ($_GET['go'] == 'prev'){
	$result = mysql_query("SELECT * FROM media WHERE time < $seq ORDER by time DESC LIMIT 1");
}

if ($_GET['go'] == 'next'){
	$result = mysql_query("SELECT * FROM media WHERE time > $seq ORDER by time ASC LIMIT 1");
}

}else{

if (strlen($_GET['i'])>0){

$id= $_GET['i'];

$result = mysql_query("SELECT * FROM media WHERE ID = $id LIMIT 1");
}else{

$result = mysql_query("SELECT * FROM media ORDER BY RAND() LIMIT 1");
}
}
?>
<?

while($row = mysql_fetch_array( $result )) {
?>

<div id='loading' style="color:#41c8f4; font-weight:400; margin:5px; padding:10px; display:inline-block; background-color:rgba(0,0,0,0.5); position:fixed; z-index:200; "><img src="http://www.ben-connors.com/img/spinner2.gif" style="height:20px;">HD LOADING</div>

<div id="overlay" class="hideverlay" onmousemove="showOverlay()" style="width:100%; height:100%; z-index:100; position:fixed;">

<div style="text-align:center; position:absolute; bottom:50%; left:10px; background-color:rgba(0,0,0,0.5); padding:20px; font-size:50px; width:90px; height:90px; border-radius: 50px; margin:0px; ">
	<a href="single.php?go=prev&time=<? echo $row['time']; ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
</div>

<div style="text-align:center; position:absolute; bottom:50%; right:10px; background-color:rgba(0,0,0,0.5); padding:20px; font-size:50px; width:90px; height:90px; border-radius: 50px; margin:0px; ">

<a href="single.php?go=next&time=<? echo $row['time']; ?>"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
</div>


<div style="position:absolute; bottom:0px; background-color:rgba(0,0,0,0.5); padding:20px; font-size:20px; width:100%; margin:0px; color:#aaa; ">
<a href="index.php"><i class="fa fa-th" aria-hidden="true"></i></a> &nbsp;
<a href="map3.php"><i class="fa fa-map-marker" aria-hidden="true"></i></a>&nbsp;
<a href="single.php"><i class="fa fa-random" aria-hidden="true"></i></a> &nbsp; &nbsp;

<? if ($_SESSION['auth'] == '+yRjDNoj8GDPy+wcqDUB+MT56lQmKCxMT3vnJZs1kdU='){
echo 'logged in';
$admin = 1;
} ?>

<?


$rate = $row['rate'];

if ($_GET['go'] == 'prev'){
echo  epochDur($seq - $row['time']) . ' before previous ';
}

if ($_GET['go'] == 'next'){
echo  epochDur($row['time'] - $seq) . '  after previous ';
}
?>
&nbsp; &nbsp;


<?
echo date( "g:iA" ,$row['time']) ;
echo '<!-- ' . $row['ID'] . '-->';

require 'icon_bar.php';


if (strpos($row['tag'],'DSLR_ARCHIVE') !== false) {
$file = $row['file'];
$thumb = 'thumbs/'. str_replace("/","|", $row['file']);
}else{
$file = "http://ben-connors.com/cellphotos/full_res/" . $row['file'];
}

 ?>



 <a href="<? echo $file; ?>"><i class="fa fa-download" aria-hidden="true"></i></a>
</div>
</div>

<? if(strpos($row['tag'],'pano') !== false){
?>
    <iframe title="pannellum panorama viewer" width="495" height="247" webkitAllowFullScreen mozallowfullscreen allowFullScreen style="border-style:none;" src="../lib/pannellum-master/src/pannellum.htm?panorama=../../../cellphotos/full_res/<? echo $file; ?>&amp;preview=../../../cellphotos/thumbs/<? echo $file; ?>"></iframe><BR>
    <a href="full_res/<? echo $file; ?>">fullres</a> | <a href='http://www.ben-connors.com/cellphotos/single_thumb.php?pass=password&file=<? echo $file; ?>'>rebuild thumb</a>
<?
}elseif(strpos($row['type'],'mp4') !== false){
?>
<div class='grid' style='background-size: contain; background-repeat: no-repeat; '> <video width='600' height='300' controls> <source src='<? echo $file; ?>' type='video/<? echo $row['type']; ?>'></video></div><BR><span style='font-size:10px;'>vid</span>
<?
}
else
{

list($width, $height) = getimagesize($file );
$image=$file;
if ($height / $width < 1) {
$orient = 'EXTREME HORIZONTAL';
echo "<div style=\"   filter: blur(5px); opacity: 0.75; -webkit-filter: blur(5px); z-index:-1; width:100%; height:100%; margin:0px; padding:0px; color:#fff;  text-shadow: 2px 2px 5px #000000; postion:absolute;     background-size:   cover;    background-position: center;                     /* <------ */
     background-image:url('". $thumb . "');\"></div>";

echo "<div id='photo_res' style=\" position:absolute; z-index:1; width:100%; height:100%; top:0px; padding:0px;     background-size:   contain;         background-repeat: no-repeat;   background-position: center;               /* <------ */
     background-image:url('". $thumb . "');\"></div>";

} else

{
$orient =  'VERTICAL';
echo "<div style=\"   filter: blur(5px); opacity: 0.75; -webkit-filter: blur(5px); z-index:-1; width:100%; height:100%; margin:0px; padding:0px; color:#fff;  text-shadow: 2px 2px 5px #000000; postion:absolute;     background-size:   cover;       background-position: center;                  /* <------ */
     background-image:url('". $thumb . "');\"></div>";

echo "<div id='photo_res' style=\" position:absolute; z-index:1; width:100%; height:100%; top:0px; padding:0px;     background-size:   contain;         background-repeat: no-repeat; background-position: center;                  /* <------ */
     background-image:url('". $thumb . "');\"></div>";
}


} ?>

<script>
console.log('running');
var image = document.getElementById('photo_res');
var downloadingImage = new Image();
downloadingImage.onload = function(){
    image.style.backgroundImage = "url('"+ this.src+"')";
    console.log('downloaded');
    document.getElementById('loading').style.opacity = "0";
};
downloadingImage.src = '<? echo $file; ?>' ;
</script>

<form method="POST">



<? if ($admin == 1){
?>

<?
} ?>



<?
if(strlen($row['lat'])>3)
{
?>

<script type="text/javascript">
var marker;

function initialize() {
    var latlng = new google.maps.LatLng(<? echo $row['lat'] .','. $row['lon']; ?>);

    var myOptions = {
        zoom: 14,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.HYBRID,
        streetViewControl: false,
        mapTypeControl: false
    };


    var map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);

      var marker = new google.maps.Marker({
      position: latlng,
      map: map
  });


      google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng);
        document.getElementById('latlon').value = event.latLng;

    });

    function placeMarker(location) {
        if (marker == undefined){
            marker = new google.maps.Marker({
                position: location,
                map: map,
                animation: google.maps.Animation.DROP
            });
        }
        else{
            marker.setPosition(location);
        }
        map.setCenter(location);


        google.maps.event.addListener(marker, "click", function (event) {
               document.getElementById('latlon').value = this.position;
        });
       }

       }

     </script>


<div id="geo_overlay" style="display:<? echo $geo_show;?>; color:#333; position:fixed; top:0px; left:0px; z-index:300; background-color:#fff;">
       <div id="map_canvas" style="width:400px; height:400px;"></div>
         &nbsp; Lat Lon: <input type="text" name="latlon" id="latlon" class="input"/> <input type="submit">
</div>

<? }else{
?>


<script type="text/javascript">
var marker;

function initialize() {
    var latlng = new google.maps.LatLng(0, 0);

    var myOptions = {
        zoom: 1,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.HYBRID,
        streetViewControl: false,
        mapTypeControl: false
    };

    var map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);


      google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng);
        document.getElementById('latlon').value = event.latLng;

    });

    function placeMarker(location) {
        if (marker == undefined){
            marker = new google.maps.Marker({
                position: location,
                map: map,
                animation: google.maps.Animation.DROP
            });
        }
        else{
            marker.setPosition(location);
        }
        map.setCenter(location);

        google.maps.event.addListener(marker, "click", function (event) {
               document.getElementById('latlon').value = this.position;
        });
       }

       }

     </script>


<div id="geo_overlay" style="display:<? echo $geo_show;?>; color:#333; position:fixed; top:0px; left:0px; z-index:300; background-color:#fff;">
       <div id="map_canvas" style="width:400px; height:400px;"></div>
         &nbsp; Lat Lon: <input type="text" name="latlon" id="latlon" class="input"/> <input type="submit">
</div>


<?
}
?>
<!-- </td><td>
<textarea name='desc' style="width:300px; height:300px;">
<? echo $row['blurb']; ?>
</textarea>
</td></tr></table>

-->
<input type="hidden" name="i" value="<? echo $row['ID']; ?>" >
</form>

<?
}
?>

</body>
</html>
