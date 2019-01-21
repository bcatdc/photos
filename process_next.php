<?
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
///TO DO :

// Clean up could by adding SWITCH / CASE for file types

//See if you can fix thumbnail brightness for .dng raws

//Make sure moving raws without .XMLs isn't creating permanent breaks

//Handle .pngs

// Think through and implement how to migrate META data from current DB for Cell Photos

//Move Colors into function

//Think through and implement HEX colors to HLS or HLV for better Search

//Consider Preview Option sizes

//Deal with "invalid" .JPG files

/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////



//THIS IS ABOUT TO TAKE A LOT OF MEMORY
ini_set("memory_limit","10000M");
ini_set('max_execution_time', 3000); //300 seconds = 5 minutes

//WE WANT TO SEE ALL THE ERRORS
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

//GET MYSQL CREDS
require "../../mysql_creds.php";

//SECURITY
require_once("security.php");

//COLOR EXTRACTION CLASS
include_once("colors.inc.php");

//RECIEVE SINGLE FILE FROM BEGIN_PROCESSING.PHP
$file = $_GET['file_name'];
$size = $_GET['size'];
$quality = $_GET['quality'];
$pre_tags = $_GET['pre_tags'];
$serial = $_GET['serial'];
if(isset($_GET['rate'])){
$rate = $_GET['rate'];
}else{
    $rate= "";
}

//SET A SKIP VARIABLE
$skip = 0;


//JPEG THUMBNAIL FUNCTION
function makeAThumbFromJpeg($file,$serial,$size,$quality){
    global $status;
    //$filename_safe = str_replace("/","|", $file);
    $thumbname=$serial."_thumb_".$size.".jpg";
    $status .= "<B>CREATING THUMBNAIL: </B> " . $thumbname."<BR>";
    $save_path = getcwd().'/thumbs/';
    $im = imagecreatefromjpeg($file);
    $new_x = $size;
    $factor = $size / imagesx($im);
    $new_y = imagesy($im) * $factor;
    $small = imagecreatetruecolor($new_x,$new_y);
    imagecopyresampled($small,$im,0,0,0,0,$new_x,$new_y,imagesx($im),imagesy($im));
    imagejpeg($small,$save_path.$thumbname,$quality);
    imagedestroy($im);
    imagedestroy($small);
}

//RAW THUMBNAIL FUNCTION
function makeAThumbFromRaw($file,$serial,$size,$quality){
global $status;
    $ufraw_path = "/usr/local/Cellar/ufraw/0.22_2/bin/ufraw-batch";
    $raw_file_path ="/Users/benconnors/Bens\ Things/Code/Repos/Photos/".str_replace(" ","\ ",$file);
    $output_filename="thumbs/".$serial."_thumb_".$size.".jpg";
    $maxsize=$size;
    $quality=$quality;

    $command= $ufraw_path . " ".  $raw_file_path ." --out-type=jpeg --compression=". $quality ." --exposure=auto --size=". $maxsize .",". $maxsize ."  --output=" . $output_filename ;
    system($command);
    $status .= "<B>command:</B>" . $command . "<br>";

}

//VIDEO THUMBNAIL FUNCTION
function makeAThumbFromVideo($file,$serial,$size,$quality){

    $ffmpeg_path = "/usr/local/bin/ffmpeg";
    $loop_legnth_seconds="2.5";
    $start_at_seconds="2";
    $video_file_path ="/Users/benconnors/Bens\ Things/Code/Repos/Photos/".str_replace(" ","\ ",$file);
    $output_file_path="/Users/benconnors/Bens\ Things/Code/Repos/Photos/thumbs/".$serial."_thumb_".$size.".gif";
    $width=$size;
    $fps="12";

    $gif_command= $ffmpeg_path . " -ss ". $start_at_seconds ." -t " . $loop_legnth_seconds ." -i ". $video_file_path ." -filter_complex '[0:v] fps=".$fps.",scale=w=".$width.":h=-1,split [a][b];[a] palettegen=stats_mode=single [p];[b][p] paletteuse=new=1' ". $output_file_path ;
    //system($gif_command);
    exec($gif_command);

    $jpg_output_filename="thumbs/".$serial."_thumb_".$size.".jpg";
    $jpg_command= $ffmpeg_path . " -i ". $video_file_path ." -vf  'thumbnail,scale=".$width.":-1' -qscale:v 4 -frames:v 1 ".$jpg_output_filename;
    exec($jpg_command);


}

//CREATE TAGS BASED ON NAME OF FILE
function getTagsFromDirectory($file){
    global $tags, $batch_tags,$status,$rate;
    //If in a "Select" Directory give it a 4 rating
	if (strpos(strtolower($file),'select') !== false) {
    	$tags .=  'select,aesthetic';
    	$rate = '4';
    	$status .=  '<B>IMPORTED AS A SELECT!</B><BR>';
	}
    //GET ADDITIONAL INFO FROM FILE STRUCTURE AND SAVE IT TO TAGS
      $name_tags = str_replace("/",",", $file);
      $tags .= strtolower($name_tags);
}

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

//GET LAT LON & TIME FROM EXIF
function getEXIFdata($file){

    global $status,$lat,$lon,$time;

    //READ EXIF IF POSSIBLE & IGNORE ERRORS IF NOT
    set_error_handler(function() { /* ignore errors */ });
	$exif = exif_read_data($file);
    restore_error_handler();

    //GET LAT LON IF EXISTS OR FALLBACK
	if(isset($exif["GPSLongitude"])){
		$lon = getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
		$lat = getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
	}else{
		$lon = '';
		$lat = '';
	}

    //GET DATE TIME DIGITIZED  IF EXISTS OR FALL BACK
	if(isset($exif["DateTimeDigitized"])){
		$time = strtotime($exif["DateTimeDigitized"]);
	}else{
        //OR FALLBACK TO USE DOB AS DEFAULT TIME
        $time = strtotime("1983-8-23");
    }
    //return array($lat,$lon,$time);
}


//GET LAT LON EXIF, GET TIME FROM EXIF OR FALLBACK OPTIONS
function getMovieMetadata($file){

    global $status,$lat,$lon,$time;

        $ffmpeg_path = "/usr/local/bin/ffprobe";
        $movie_meta_command= $ffmpeg_path . " -show_streams -show_format  ".$file;
        $movie_meta_output = shell_exec($movie_meta_command);

        //Parse movie meta for created
        preg_match_all('/creation_time=(.*?)Z/', $movie_meta_output, $matches);

        if(!empty($matches[1])) {
            $time = strtotime( $matches[1][0]);
        }else{
        //OR FALLBACK TO USE DOB AS DEFAULT TIME
		     $time = strtotime("1983-8-23");
        }

}








        //CLEAR VARIABLES FOR LOOP REPEATS
        $status='<B>URL: </B>'. "$_SERVER[REQUEST_URI]" .'<BR>';
        $tags="";

        //Break filename structure apart
		$filename = explode('.',$file);
		$type = array_pop($filename);

        //Commit file & process details to Status
        $status .= "<B>SERIAL:</B> ". $serial . "<BR>";
        $status .= '<B>PROCESSING:</B> ' . $file . ' <BR>';

        getTagsFromDirectory($file);


        //READ EXIF DATA OR FALL BACK TO NAMING CONVENTION FOR TIME & LAT LON
        //list($lat,$lon,$time) = getLatLonTime($file);

        ///////////////////////////////
        //FIGURE OUT FILE TYPE AND START PERFORMING OPERATIONS
        ///////////////////////////////

        if (strtolower($type) == 'jpg'  )
        {
            $preview_ext_type =".jpg";
            makeAThumbFromJpeg($file,$serial,$size,$quality);
            getEXIFdata($file);

            }elseif(strtolower($type) == 'cr2'){
                $preview_ext_type =".jpg";
                makeAThumbFromRaw($file,$serial,$size,$quality);
                getEXIFdata($file);

            }elseif(strtolower($type) == 'dng'){
                $preview_ext_type =".jpg";
                makeAThumbFromRaw($file,$serial,$size,$quality);
                getEXIFdata($file);

		    }elseif(strtolower($type) == 'nef'){
                $preview_ext_type =".jpg";
                makeAThumbFromRaw($file,$serial,$size,$quality);
                getEXIFdata($file);

			}elseif(strtolower($type) == 'mp4'){
                 $preview_ext_type =".gif";
                makeAThumbFromVideo($file,$serial,$size,$quality);
                getMovieMetadata($file);

            }elseif(strtolower($type) == 'mov'){
                 $preview_ext_type =".gif";
                makeAThumbFromVideo($file,$serial,$size,$quality);
                 getMovieMetadata($file);

            }elseif(strtolower($type) == '3gp'){
                $preview_ext_type =".gif";
                makeAThumbFromVideo($file,$serial,$size,$quality);
                getMovieMetadata($file);
			}else{
                //////////////////////////
                //      HANDLE ALL OTHER RANDOM FILES
                ///////////////////////
        		$skip=1;
                $status = "<B>SKIPPED: </B>." . $file. "<BR>";
            }

    //WRITE TYPE TO STATUS
    $status .= "<B>TYPE:</B> " . $type . "<BR>";

    //COLLECT ALL THE TAGS
    $tags .= ','.$pre_tags;
    $status .=  '<B>TAGS</B>: ' . $tags .'<BR>';


    // ANYTHING THAT'S AN IDENTIFIABLE FILE TYPE MOVE & ADD TO DB ALO PRINT STATUS
    if ($skip == 0) {

        //COLORS
        $num_results = 8;
        $delta =  24;
        $reduce_brightness = 1;
        $reduce_gradients = 1;
        $ex=new GetMostCommonColors();
        $colors=$ex->Get_Color('thumbs/'. $serial. '_thumb_'.$size.'.jpg', $num_results, $reduce_brightness, $reduce_gradients, $delta);
        $color_array = json_encode($colors);
        $status .= "<B>COLORS :</B>  ";
        foreach ( $colors as $hex => $count )
        {
        		$status .= "<span style='font-size:15px; font-family:Tahoma; padding-left:15px; border:1px solid white; overflow:hidden; background-color:#".$hex.";'></span> ";
        }
        $status .="<BR>";

       // BUILD PREVIEW IMAGE TAG BASED ON TYPE
        $preview_img =  '<img src="thumbs/'. $serial. '_thumb_'.$size. $preview_ext_type. '" style="float:left; margin-right:12px;">';
        $status.= "<B>LAT:</B> ". $lat ."<BR><B> LON: </B>". $lon . "<BR><B>TIME: </B>" . date("Y-m-d h:i:sa", $time). "<BR>"  ;

        //PRINT STATUS W PIC
        echo "<div style='border:1px solid #ccc; margin-bottom:20px;'>" . $preview_img . $status . "<div style='clear:both;'></div></div>";


            //MOVE AND RENAME FILE
            rename($file, 'image_archive/'. $serial.'.'. $type);
            $status .= '<B>MOVED TO</B> image_archive/'.$serial.'.'.$type.'<br>';

            //INSERT INTO DB
			mysqli_query($link , "INSERT INTO media (lat, lon, time, type, tag, rate, colors) VALUES(  '$lat', '$lon', '$time', '$type', '$tags' ,'$rate', '$color_array') ") or die(mysql_error());
            $serial++;
	 }else{
     //VISIBILY NOTE SKIPPED Files

            //PRINT STATUS W PIC
            echo "<div style='border:1px solid #ccc; margin-bottom:20px;'><B>SKIPPED:" . $file. "<div style='clear:both;'></div></div>";

     }

	$skip=0;
    ?>
