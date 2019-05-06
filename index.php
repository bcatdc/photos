<? session_start(); ?>
<html>
<head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
<link rel="stylesheet" href="photos.css">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script>
<script src="star_rating_system.js"></script>
<script src="photos.js"></script>


<body style="padding:0px; margin:0px; background-color:#000; color:#eee;">
	<div class="container-fluid">
		<div class="row">
			<div style="width:100%; background-color:#eee; padding:1%; margin:0px; margin-bottom:1%;">
				<a href="map3.php">Map</a> &nbsp; <a href="single.php">Random</a> &nbsp;
					<a class="force_blue" data-toggle="collapse" data-target="#SearchPane">Search </a>

					<span style="display:block;float:right;"><a href="../x/">Admin</a>

						<? if ($_SESSION['auth'] == '+yRjDNoj8GDPy+wcqDUB+MT56lQmKCxMT3vnJZs1kdU='){
						echo 'logged in';
						} ?>

					</span>
				</div>
			</div>

<? include('querybar.php');

			while($row = mysqli_fetch_array( $result )){
				error_reporting(0);
				$text = date( "F j, Y, g:i a" ,$row['time']);
				error_reporting(1);
				$i++;

//If beginning of results head with DATE
				if ($i==1){
					$prev_time = $row['time'];
					$text = date( "F j, Y" ,$row['time']);
					$month_break ="<div>RESULTS BEGIN ON ". $text . "</div><div class='row month'>";
//If a new month close preview month DIV
				}elseif(($prev_time - $row['time']) > 2592000 ){
					$prev_time = $row['time'];
					$text = date( "F j, Y" ,$row['time']);
					$month_break ="</div><div>". $text. "</div><div class='row month'>";
				}else{
					$month_break = "";
				}

//Start the little box for thumb and details
				echo $month_break . "<div id=". $row['ID'] . " class='col-sm-4'>";
				require 'icon_bar.php';
				$thumbwidth = 495;

				if($row['type']  == "mov" || $row['type']  == "mp4" || $row['type']  == "3gp" || $row['type']  == "m4v"   ){

					$thumb = $row['ID'] . "_thumb_". $thumbwidth .".gif";
				}else{
					$thumb = $row['ID'] . "_thumb_". $thumbwidth .".jpg";
				}
				?>

					 <div style="text-align:center; width:100%;">
					 	<a target="_blank" href="single.php?i=<? echo $row['ID'] ; ?>">
					 		<img style="max-height:300px;" src='../thumbs/<? echo $thumb ; ?>'>
						</a>
					 </div>
				 </div>

<?
}

if ($page > 1){
echo '<a href="index.php?start='. $_GET['start'] .'&stop='. $_GET['stop'] .'&rate='. $rate .'&view='. $view . '&page=' . ($page - 1) . '"><< Previous</a> | ';
}

if ($rowCount < 500){
echo $rowCount . ' Results';
}else{
echo '<a href="index.php?start='. $_GET['start'] .'&stop='. $_GET['stop'] .'&rate='. $rate .'&view='. $view . '&page=' . ($page + 1) . '">Next 500>></a>';
}

?>

</div>
</body>
</html>
