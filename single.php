<? session_start(); ?>
<html>
	<head>
		<!-- Google Maps API was here  -->
		<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyC71PIQ_QM6fvAGhDzhnBCBA_hrogBdjhI&sensor=false"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script>
		<script src="star_rating_system.js"></script>
		<link rel="stylesheet" href="photos.css">
		<script src="photos.js"></script>
		<body style="    margin: 0 !important; padding: 0 !important; background-color:#000; color:#eee;" onload="" >

<?
// Make a MySQL Connection
require "../../mysql_creds.php";
require "update_functions.php";
require "common_functions.php";


/////////////////////////////////////////////////////
//
// SINGLE NAVIGATION
//
/////////////////////////////////////////////////////

//HANDLE GO TO NEXT OR GO TO LAST
if (strlen($_GET['go'])>0){
		$seq = $_GET['time'] ;
//LAST
		if ($_GET['go'] == 'prev'){
			$result = mysqli_query($link, "SELECT * FROM media WHERE time < $seq ORDER by time DESC LIMIT 1");
		}
//NEXT
		if ($_GET['go'] == 'next'){
			$result = mysqli_query($link, "SELECT * FROM media WHERE time > $seq ORDER by time ASC LIMIT 1");
		}
	}else{
//IF A SPECIFIC ID HAS BEEN PASSED GO GET THAT ENTRY
	if (strlen($_GET['i'])>0){
		$id= $_GET['i'];
		$result = mysqli_query( $link, "SELECT * FROM media WHERE ID = $id LIMIT 1");
	}else{
//ELSE PICK ONE AT RANDOM
		$result = mysqli_query($link, "SELECT * FROM media ORDER BY RAND() LIMIT 1");
	}
}

while($row = mysqli_fetch_array( $result )) {

/////////////////////////////////////////////////
///
/// PULL METADATA
///
/////////////////////////////////////////////////
	$rate = $row['rate'];

	$timeTaken ="&nbsp; &nbsp" . date( "g:iA" ,$row['time']) ;

	if ($_GET['go'] == 'prev'){
			$timeDifferencial=  'Taken ' . epochDur($seq - $row['time']) . ' before next photo ';
		}elseif ($_GET['go'] == 'next'){
			$timeDifferencial =  'Taken ' . epochDur($row['time'] - $seq) . '  after previous photo ';
		}else{
			$timeDifferencial = '';
		}


/////////////////////////////////////////////////
///
/// HANDLE DIFFERENT FILE TYPES
///
/////////////////////////////////////////////////

// HANDLE JPEGS
$file = "../image_archive/" .$row["ID"] .'.'. $row["type"] ;
$thumb = '../thumbs/'. $row["ID"] .'_thumb_495.'. $row["type"] ;


		if (strpos($row['type'],'jpg') !== false) {
			$file = "../image_archive/" .$row["ID"] .'.'. $row["type"] ;
			$thumb = '../thumbs/'. $row["ID"] .'_thumb_495.'. $row["type"] ;
			?>

			<!--  LOADING SPINNER FOR JPGS   -->
			<div id='loading' ><img src="http://www.ben-connors.com/img/spinner2.gif" style="height:20px;">
				HD LOADING
			</div>
			<?
		}


//FOR RAWS DO THE SAME AS JPG BUT POINT TO LARGE THUMB NOT THE RAW
		if ( (strpos($row['type'],'nef') !== false) || (strpos($row['type'],'cr2') !== false) || (strpos($row['type'],'dng') !== false) ){
			$file = "../thumbs/" .$row["ID"] .'_thumb_2880.jpg';
			$thumb = '../thumbs/'. $row["ID"] .'_thumb_495.jpg' ;
			?>

			<!--  LOADING SPINNER FOR JPGS   -->
			<div id='loading' ><img src="http://www.ben-connors.com/img/spinner2.gif" style="height:20px;">
				HD LOADING
			</div>
			<?
		}





/////////////////////////////////////////////////////
//
// COMMON  LAYOUT FOR SINGLE PAGE
//
/////////////////////////////////////////////////////

?>



<div id="overlay" class="hideverlay" onmousemove="showOverlay()" style="width:100%; height:100%; z-index:100; position:fixed;">
	<div class="chevron left_chev">
		<a href="single.php?go=prev&time=<? echo $row['time']; ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
	</div>
	<div class="chevron right_chev">
		<a href="single.php?go=next&time=<? echo $row['time']; ?>"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
	</div>

	<!--  NAVIGATION TO CONTACT PAGE, MAP, AND SHUFFLE + RELATIVE TIME  -->
	<div class="overlay_bar">
		<a href="index.php"><i class="fa fa-th" aria-hidden="true"></i></a> &nbsp;
		<a href="map3.php"><i class="fa fa-map-marker" aria-hidden="true"></i></a>&nbsp;
		<a href="single.php"><i class="fa fa-random" aria-hidden="true"></i></a> &nbsp; &nbsp;

		<?
		echo $timeDifferencial;
		echo $timeTaken;
		require 'icon_bar.php';
 		?>

 		<a href="<? echo $file; ?>"><i class="fa fa-download" aria-hidden="true"></i></a>
	</div>
</div>


<?

/////////////////////////////////////////////////////
//
// LAYOUT FOR SPECFIC MEDIA TYPES
//
/////////////////////////////////////////////////////




// HANDLE PANOS
if(strpos($row['tag'],'pano') !== false){
		?>
		<script>
			console.log('pano');
		</script>
	    <iframe title="pannellum panorama viewer" width="495" height="247" webkitAllowFullScreen mozallowfullscreen allowFullScreen style="border-style:none;" src="../lib/pannellum-master/src/pannellum.htm?panorama=../../../cellphotos/full_res/<? echo $file; ?>&amp;preview=../../../cellphotos/thumbs/<? echo $file; ?>"></iframe><BR>
	    <a href="full_res/<? echo $file; ?>">fullres</a> | <a href='http://www.ben-connors.com/cellphotos/single_thumb.php?pass=password&file=<? echo $file; ?>'>rebuild thumb</a>
		<?

// HANDLE MP4s
}elseif(strpos($row['type'],'mp4') !== false || strpos($row['type'],'mov') !== false  || strpos($row['type'],'3gp') !== false || strpos($row['type'],'m4v') !== false    ){

		?>
		<script>
			console.log('<? echo $type; ?>');
		</script>
		<div style="z-index:901; width:80%; left:10%; position:absolute; height:100%;" >
			<div class="outer">
			  <div class="middle">
			    <div class="inner">
					<video style="width:100%;" controls src='<? echo $file; ?>'
					</video>
			    </div>
			  </div>
			</div>
		</div>
		<?

// HANDLE JPGS
	}else{
		list($width, $height) = getimagesize($file );
		$image=$file;

	//WIDER THAN THEY ARE TALL
		if ($height / $width < 1) {
				$orient = 'EXTREME HORIZONTAL';
				echo "<div style=\"   filter: blur(5px); opacity: 0.75; -webkit-filter: blur(5px); z-index:-1; width:100%; height:100%; margin:0px; padding:0px; color:#fff;  text-shadow: 2px 2px 5px #000000; postion:absolute;     background-size:   cover;    background-position: center;                     /* <------ */
				     background-image:url('". $thumb . "');\"></div>";
				echo "<div id='photo_res' style=\" position:absolute; z-index:1; width:100%; height:100%; top:0px; padding:0px;     background-size:   contain;         background-repeat: no-repeat;   background-position: center;               /* <------ */
				     background-image:url('". $thumb . "');\"></div>";

			} else {
	// TALLER THAN THEY ARE WIDE
				$orient =  'VERTICAL';
				echo "<div style=\"   filter: blur(5px); opacity: 0.75; -webkit-filter: blur(5px); z-index:-1; width:100%; height:100%; margin:0px; padding:0px; color:#fff;  text-shadow: 2px 2px 5px #000000; postion:absolute;     background-size:   cover;       background-position: center;                  /* <------ */
				     background-image:url('". $thumb . "');\"></div>";
				echo "<div id='photo_res' style=\" position:absolute; z-index:1; width:100%; height:100%; top:0px; padding:0px;     background-size:   contain;         background-repeat: no-repeat; background-position: center;                  /* <------ */
				     background-image:url('". $thumb . "');\"></div>";
		 }
	}
 ?>


<script>
// SCRIPT TO  TURN OFF "LOADING" SPINNER ON HD JPG LOAD

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
	<script type="text/javascript">

	<?
	// IF GPS VALUES ARE PRESENT GRAB SCRIPT FOR THAT TYPE OF GOOGLE MAP
	if(strlen($row['lat'])>3){
		echo "var zoomjs = 14;";
		echo "var latjs = ". $row['lat']. ";";
		echo "var lonjs = ". $row['lon']. ";";
	}else{
		echo "var zoomjs = 1;";
		echo "var latjs = 0;";
		echo "var lonjs = 0;";
	}
	?>
		var marker;
		function initialize() {
		    var latlng = new google.maps.LatLng(latjs,lonjs);
		    var myOptions = {
		        zoom: zoomjs,
		        center: latlng,
		        mapTypeId: google.maps.MapTypeId.HYBRID,
		        streetViewControl: false,
		        mapTypeControl: false
		    };

		    var map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

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
			        }else{
			            marker.setPosition(location);
		        }

		        map.setCenter(location);
		        google.maps.event.addListener(marker, "click", function (event) {
		        	document.getElementById('latlon').value = this.position;
	        	});
		 	}
		}
</script>

	<div id="geo_overlay" style="display:none; color:#333; position:fixed; top:0px; left:0px; z-index:300; background-color:#fff;">
	       <div id="map_canvas" style="width:400px; height:400px;"></div>
	         &nbsp; Lat Lon: <input type="text" name="latlon" id="latlon" class="input"/> <input type="submit">
	</div>

<? echo $row['blurb']; ?>

<input type="hidden" name="i" value="<? echo $row['ID']; ?>" >
</form>

<?
//END WHILE imagetruecolortopalette
}
?>

</body>
</html>
