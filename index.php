<? session_start(); ?>
<html>
<head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="lib/bootstrap/js/bootstrap.min.js"></script>
<script src="star_rating_system.js"></script>

<script>

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


<body style="padding:0px; margin:0px; background-color:#000; color:#eee;">
<div class="container-fluid">


<div class="row">
<div style="width:100%; background-color:#eee; padding:1%; margin:0px; margin-bottom:1%;">
<a href="map3.php">Map</a> &nbsp; <a href="single.php">Random</a> &nbsp; <a data-toggle="collapse" data-target="#SearchPane">Search </a>
<span style="display:block;float:right;"><a href="../x/">Admin</a>

<? if ($_SESSION['auth'] == '+yRjDNoj8GDPy+wcqDUB+MT56lQmKCxMT3vnJZs1kdU='){
echo 'logged in';
} ?>
</span>
</div>
</div>
<?

include('querybar.php');

while($row = mysqli_fetch_array( $result )) 
{
error_reporting(0);
$text = date( "F j, Y, g:i a" ,$row['time']);
error_reporting(1);
$i++;

if ($i==1){
$prev_time = $row['time'];
$text = date( "F j, Y" ,$row['time']);
?>


<h2><? echo $text;  ?></h2>
<div class="row">
<?
}
?>


<?
if (($prev_time - $row['time']) > 2592000 ){
$prev_time = $row['time'];
$text = date( "F j, Y" ,$row['time']);
?>
<BR><BR>
</div>
<div style="display:block; width:100%; margin:30px; border:5px solid #300;">
<? echo $text;  ?><BR>
<?
}
?>


<div id="<? echo $row['ID'] ; ?>" class="col-sm-4">
	<?
	if($row['type'] == 'mp4' || $row['type'] == '3gp' ){
?>
<BR><BR><div>
<?
require 'icon_bar.php';
 ?>
 </div>
 <div class="fourthree-container" style="text-align:center; width:100%; background-color:#222;"><BR><BR>
<a href="javascript:loadVideo('<? echo $row['ID'] . '\',\'' . $row['file'] . '\',\'' . $row['type'] ; ?>');">
<i class="fa fa-film fa-5x" aria-hidden="true"></i></a>
<BR><BR>
<BR><a href="/full_res/<? echo $row['file']; ?>">Open Video</a>
	</div>
</a>

<?
}else{
?>
<div>
<?
require 'icon_bar.php';
 ?>
</div>

<?
if (strpos($row['tag'],'DSLR_ARCHIVE') !== false) {
$thumb = '/'. str_replace("/","|", $row['file']);
}else{
$thumb = $row['file'];
}

?>
<div style="style="text-align:center; width:100%;">
<a target="_blank" href="single.php?i=<? echo $row['ID'] ; ?>">
	<img style="max-height:300px;" src='thumbs/<? echo $thumb ; ?>'></a>
	</div>
</a>


<?
}
?>



<?
?>
</div>
<?
}
?>
</div>
</div>
<?
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
