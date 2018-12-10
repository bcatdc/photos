
<?php
//THIS IS ABOUT TO TAKE A LOT OF MEMORY
ini_set("memory_limit","10000M");
ini_set('max_execution_time', 3000); //300 seconds = 5 minutes

//WE WANT TO SEE ALL THE ERRORS
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

//SUPERDUPER LAME SECURITY EFFORT
if($_GET['pass'] != 'password') die('access denied');

//GET MYSQL CREDS
require "../../mysql_creds.php";

//ESTABLISH BOOLEAN FOR SKIPPING RANDOM FILE TYPES
$skip=0;


?>
<HTML>
<body>
<?


//CHECK FOR BATCH PROCESS TAGS OR RATINGS
if(isset($_GET['rate'])){
  $rate=$_GET['rate'];
  echo 'Using blanket rating '. $rate . '<br>';
}else{
  echo 'No Blanket rating in use. &rate=ZEROTOFIVE<BR>';
  $rate='';
}
if (isset($_GET['tag'])){
  $pre_tags .=  $_GET['tag'] +',' ;
  echo 'The tag "' . $_GET['tag'] . '" has been manually added for each file.<BR> ';
}else{
  echo 'There was no manual tag added &tag=YOURTAGHERE.<BR>';
  $pre_tags='';
}

?>
<HR>
FINISHED LOOKING AT GET VARIABLES, STARTING BATCH PROCESS
<HR>
<?



//ESTABSLISH COUNT OF FILES INSERTED INTO DB
$inserted ='0';


//GET GPS FROM EXIF DATA
function getGps($exifCoord, $hemi) {
	$degrees = count($exifCoord) > 0 ? gps2Num($exifCoord[0]) : 0;
	$minutes = count($exifCoord) > 1 ? gps2Num($exifCoord[1]) : 0;
	$seconds = count($exifCoord) > 2 ? gps2Num($exifCoord[2]) : 0;
	$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
	return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
}

//CONVERT GPS INTO NUMERIC DATA
function gps2Num($coordPart) {
	$parts = explode('/', $coordPart);
	if (count($parts) <= 0)
		return 0;
	if (count($parts) == 1)
		return $parts[0];
	return floatval($parts[0]) / floatval($parts[1]);
}

//CREATE A THUMBNAIL DIRECTOY IN CASE IT GETS MOVED
if(!is_dir('thumbs')) mkdir('thumbs') or die('can\' create thumbs directory');


//BUILD AN ARRAY OF ALL THE FILES IN "waiting_to_be_batch_processed/" THAT ARE NOT DUPLICATES
function listFiles( $from = 'waiting_to_be_batch_processed'){

  //Connect to DB
  global $link;

  //Crawl the directoy and sub directories
  if(! is_dir($from))
      return false;
  $files = array();
  $dirs = array( $from);
  while( NULL !== ($dir = array_pop( $dirs)))
  {
    if( $dh = opendir($dir))
    {
        while( false !== ($file = readdir($dh)))
        {
            if( $file == '.' || $file == '..')
                continue;
            $path = $dir . '/' . $file;
            if( is_dir($path))
                $dirs[] = $path;
            else;
            //Check each file to make sure it's not a DUP
  					$dupesql = "SELECT * FROM media where (file = '$path')";
  					$duperaw = mysqli_query($link, $dupesql);
  					$row_cnt = $duperaw->num_rows;

					//If it is a dup yell about it and ignore it
					if ($row_cnt > 0)
					{ echo "The file at ". $path . " is already in the DB and will be ignored.<BR>";

////////////////////////////////
////////
//Temporarilly Procecess dups with this line
$file_list[] = $path;
////////
///////////////////////////////

						}else{
					$file_list[] = $path;
					}
            }
            closedir($dh);
        }
    }
    //Return an array of all the files to be examined for processing
    return $file_list;
}

$file_list = listFiles();
$count = 0;
$total = count($file_list);
$file_list_r = array_reverse($file_list);
?>
<HR>
  Finished examining files in "waiting_to_be_batch_processed/" for dups. Beginning Processing
<HR>
<?

foreach($file_list_r as $file){

      //Break file structure apart
			$filename = explode('.',$file);
			$fileext = array_pop($filename);
      echo'<BR><BR><B>STARTING TO PROCESS:</B> ' . $file . ' <BR>';

      //If in a "Select" Directory give it a 4 rating
			if (strpos(strtolower($file['0']),'select') !== false) {
  			$pre_tags .=  'select,';
  			$rate = '4';
  			echo '<BR>select!<BR>';
			}


//////////////////////////
//      HANDLE ALL JPGS
/////////////////////////
			if (strtolower($fileext) == 'jpg'  )
			{

      //JPEG SUBTYPES
			if (strpos($file,'PANO') !== false)
			{
      //PANO
      echo 'PANO<BR>';
					$tags = ', pano, ' . $pre_tags;

			if (strpos($file,'TINYPLANET') !== false)
			{
        //TINY PLANET
        echo 'TINYPLANET<BR>';
				$tags = ', tinyplanet,' . $pre_tags;
			}

	    }else{
			$tags ='' . $pre_tags;
		  }

			if(filesize($file)>700000000){
//TOO BIG FOR THUMB
// PREVIOUS VERSION = if(filesize($file)>7000000){
				$type = 'large';
echo 'TOO LARGE FOR THUMB<BR>';
					}else{
//echo 'NEW<BR>';
//STANDARD JPG
					   $type = 'jpg';
//MAKE A THUMBNAIL

					   $filename_safe = str_replace("/","|", $file);
					   $save_path = getcwd().'/thumbs/';
					   $im = imagecreatefromjpeg($file);
					   $new_x = 495;
					   $factor = 495 / imagesx($im);
					   $new_y = imagesy($im) * $factor;
					   $small = imagecreatetruecolor($new_x,$new_y);
					   imagecopyresampled($small,$im,0,0,0,0,$new_x,$new_y,imagesx($im),imagesy($im));
					   imagejpeg($small,$save_path.$filename_safe,85);
					   //echo '<BR> <B>thumb path</B> '. $save_path.$filename_safe . '<BR>';
					   imagedestroy($im);
					   imagedestroy($small);
//					   usleep(100);
//					   set_time_limit(90);
					   $count++;
					}

			}



//////////////////////////
//      HANDLE ALL CR2s
/////////////////////////
			elseif(strtolower($fileext) == 'cr2'){
				//CR2 RAW FILE TYPE
									   $type = 'cr2';
				//MAKE A THUMBNAIL

									   $filename_safe = str_replace("/","|", $file);

									   $save_path = getcwd().'/thumbs/';
									   $im = imagecreatefromjpeg($file);
									   $new_x = 495;
									   $factor = 495 / imagesx($im);
									   $new_y = imagesy($im) * $factor;
									   $small = imagecreatetruecolor($new_x,$new_y);
									   imagecopyresampled($small,$im,0,0,0,0,$new_x,$new_y,imagesx($im),imagesy($im));
									   imagejpeg($small,$save_path.$filename_safe,85);
									   echo '<BR> <B>thumb path</B> '. $save_path.$filename_safe . '<BR>';
									   imagedestroy($im);
									   imagedestroy($small);
				//					   usleep(100);
				//					   set_time_limit(90);
									   $count++;

			}



//////////////////////////
//      HANDLE ALL NEFs
/////////////////////////
			elseif(strtolower($fileext) == 'nef'){
				//NEF RAW FILE TYPE
									   $type = 'nef';
				//MAKE A THUMBNAIL

									   $filename_safe = str_replace("/","|", $file);

									   $save_path = getcwd().'/thumbs/';
									   $im = imagecreatefromjpeg($file);
									   $new_x = 495;
									   $factor = 495 / imagesx($im);
									   $new_y = imagesy($im) * $factor;
									   $small = imagecreatetruecolor($new_x,$new_y);
									   imagecopyresampled($small,$im,0,0,0,0,$new_x,$new_y,imagesx($im),imagesy($im));
									   imagejpeg($small,$save_path.$filename_safe,85);
									   echo '<BR> <B>thumb path</B> '. $save_path.$filename_safe . '<BR>';
									   imagedestroy($im);
									   imagedestroy($small);
				//					   usleep(100);
				//					   set_time_limit(90);
									   $count++;

			}



//////////////////////////
//      HANDLE ALL MP4S
/////////////////////////
			elseif(strtolower($fileext) == 'mp4')
			{
					$tags='' . $pre_tags;
					echo 'MP4<BR>';
					$type = 'mp4';
          $filename_safe = str_replace("/","|", $file);
          //exec("ffmpeg -i /Users/bconnors/Desktop/Codes/bcatdc.us.to/photos/waiting_to_be_batch_processed/mp4s/input.mp4 -vcodec mjpeg -vframes 1 -an -f rawvideo -ss `ffmpeg -i /Users/bconnors/Desktop/Codes/bcatdc.us.to/photos/waiting_to_be_batch_processed/mp4s/input.mp4 2>&1 | grep Duration | awk '{print $2}' | tr -d , | awk -F ':' '{print ($3+$2*60+$1*3600)/2}'` /Users/bconnors/Desktop/Codes/bcatdc.us.to/photos/waiting_to_be_batch_processed/mp4s/output.jpg");




			}


//////////////////////////
//      HANDLE ALL 3GPS
/////////////////////////
      elseif(strtolower($fileext) == '3gp')
			{

					$tags='' . $pre_tags;
					echo '3GP<BR>';
					$type = '3gp';
					//OTHER FILE TYPES
					//WRITE TO DB
			}

//////////////////////////
//      HANDLE ALL JUST JUNK
/////////////////////////
          else{
					echo 'OTHER ' . $fileext . '<br>';
					$skip=1;
			}




			if($skip == 0){

			if($type == 'mp4'){
				$time = strtotime(str_replace("VID ","",str_replace("_"," ",str_replace(".mp4"," ",$file))));
				$lon = '';
				$lat = '';
			}else if($type == '3gp' || $type == '3gp' ){
				$time = strtotime(str_replace("VID ","",str_replace("_"," ",str_replace(".3gp"," ",$file))));
				$lon = '';
				$lat = '';
			}else{

//STRIP DATA FOR DB
						$exif = exif_read_data($file);
						if(isset($exif["GPSLongitude"])){
							$lon = getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
							$lat = getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
						}else{
							$lon = '';
							$lat = '';
						}

						if(isset($exif["DateTimeDigitized"])){
							$time = strtotime($exif["DateTimeDigitized"]);
						}else{
							//Android Camera Format
							//$time = strtotime(str_replace("IMG ","",str_replace("_"," ",str_replace(".jpg"," ",$file))));

							//Any JPEG
							$time = strtotime("1983-8-23");
						}

//WRITE TO DB
			}
			echo 'Tags: ' . $tags .'<BR>';

			mysqli_query($link , "INSERT INTO media (lat, lon, time, file, type, tag, rate) VALUES(  '$lat', '$lon', '$time', '$file', '$type', '$tags' ,'$rate') ") or die(mysql_error());
			//move_uploaded_file($file, 'full_res/'. $file);
			//rename($file, 'full_res/'. $file);
			echo '<img src="thumbs/'. $filename_safe . '">';
$inserted++;
		$pre_tags = '';
		$rate = '';
			}

		$skip=0;

		}

echo $total . ' Files Checked  | ' . $inserted . ' Inserted!' ;
		?>

</div>
</body>
</html>
