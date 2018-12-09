
<?
ini_set("memory_limit","10000M");
ini_set('max_execution_time', 3000); //300 seconds = 5 minutes

 error_reporting(E_ALL); 
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
//error_reporting(-1);

$skip=0;

$rate=$_GET['rate'];

// ini_set('max_execution_time', 300); //300 seconds = 5 minutes

// Make a MySQL Connection
mysql_connect("localhost", "root", "root") or die(mysql_error());
mysql_select_db("awesomedex") or die(mysql_error());

$inserted ='0';
	
	function getGps($exifCoord, $hemi) {
	
		$degrees = count($exifCoord) > 0 ? gps2Num($exifCoord[0]) : 0;
		$minutes = count($exifCoord) > 1 ? gps2Num($exifCoord[1]) : 0;
		$seconds = count($exifCoord) > 2 ? gps2Num($exifCoord[2]) : 0;
		$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
		return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
	}
	
	
	function gps2Num($coordPart) {
	
		$parts = explode('/', $coordPart);
		if (count($parts) <= 0)
			return 0;
		if (count($parts) == 1)
			return $parts[0];
		return floatval($parts[0]) / floatval($parts[1]);
	}




if($_GET['pass'] != 'password') die('access denied');

// error_reporting(E_ALL);

if(!is_dir('thumbs')) mkdir('thumbs') or die('can\' create thumbs directory');
?>

<HTML>
<body>

<?


//BUILD A LIST

$file_list = array();


function listFiles( $from = '../test')
{
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
                else
                
					$dupesql = "SELECT * FROM media where (file = '$path')";
					$duperaw = mysql_query($dupesql);
					// But we've used this action before alert duplicate:
					if (mysql_num_rows($duperaw) > 0) 
					{ echo $path . " is a duplicate<BR>";}else{
						$file_list[] = $path;
					}
            }
            closedir($dh);
        }
    }
    return $file_list;
}

$file_list = listFiles();
/*
if ($handle = opendir('.')) {

   while (false !== ($file = readdir($handle))) 
   {

//CHECK DUPS
			$dupesql = "SELECT * FROM media where (file = '$file')";
			$duperaw = mysql_query($dupesql);
			// But we've used this action before alert duplicate:
			if (mysql_num_rows($duperaw) > 0) 
			{
//STOP DUPS 
echo memory_get_usage() ;
echo '*' . $file . '<BR>'  ; 
			}else{  
$file_list[] = $file;
echo $file . '<BR> ' ;
			}
	}
closedir($handle);
}
*/


$count = 0;
$total = count($file_list);
$file_list_r = array_reverse($file_list);

	
	foreach($file_list_r as $file) 
		{
$pre_tags='DSLR_ARCHIVE,';
if (strlen($_GET['tag'])>0){ $pre_tags .=  $_GET['tag'] +',' ; }

//echo '<BR><BR><hr>' . $file . ' ';	
	
			$filename = explode('.',$file); 
			$fileext = array_pop($filename);
			
			if (strpos(strtolower($filename),'select') !== false) {
			$pre_tags .=  'select,';
			$rate = '4';
			echo '<BR>select!<BR>';
			}
			
			if (strtolower($fileext) == 'jpg') 
			{
			
//JPEG	SUBTYPES

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
				    
				    
				    
				    
			if(filesize($file)>7000000000){ 
//TOO BIG FOR THUMB
				$type = 'large';
echo 'LARGE<BR>';
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
			elseif(strtolower($fileext) == 'cr2' || strtolower($fileext) == 'nef' ||)
			{

//STANDARD JPG
					   $type = 'jpg';
//MAKE A THUMBNAIL	
						
					   $filename_safe = str_replace("/","|", $file);
					   
					   $save_path = getcwd().'/thumbs/';
					   $im = new Imagick($file);
					   $im->setImageFormat( 'jpg' );
					   $new_x = 720;
					   $factor = 720 / imagesx($im);
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
					   
					$tags='' . $pre_tags;	
					echo 'CR2<BR>';
					$type = 'RAW';
					//OTHER FILE TYPES
					//WRITE TO DB
					}



			}elseif(strtolower($fileext) == 'mov')
			{

					$tags='' . $pre_tags;	
					echo 'mov<BR>';
					$type = 'mov';
					//OTHER FILE TYPES
					//WRITE TO DB
			}elseif(strtolower($fileext) == '3gp')
			{

					$tags='' . $pre_tags;	
					echo '3GP<BR>';
					$type = '3gp';
					//OTHER FILE TYPES
					//WRITE TO DB
			}else{
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
			}else if($type == 'mov' || $type == 'mov' ){
				$time = filectime($file);
				echo '<BR> time -'.$time;
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
							$time = strtotime(str_replace("IMG ","",str_replace("_"," ",str_replace(".jpg"," ",$file))));

						}
		
//WRITE TO DB
			}
			echo 'Tags: ' . $tags .'<BR>';
			mysql_query("INSERT INTO media (lat, lon, time, file, type, tag, rate) VALUES(  '$lat', '$lon', '$time', '$file', '$type', '$tags' ,'$rate') ") or die(mysql_error());  
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
