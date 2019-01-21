<?PHP


//$file="/Users/benconnors/Bens\ Things/Code/Repos/Photos/image_archive/1.mp4";
//$file="/Users/benconnors/Bens\ Things/Code/Repos/Photos/image_archive/2.3gp";
$file="/Users/benconnors/Bens\ Things/Code/Repos/Photos/image_archive/3.mov";
//$file="/Users/benconnors/Bens\ Things/Code/Repos/Photos/image_archive/15.jpg";

$ffmpeg_path = "/usr/local/bin/ffprobe";

//ffprobe -show_streams -show_format DV06xx.avi
$movie_meta= $ffmpeg_path . " -show_streams -show_format  ".$file;

$movie_meta_output = shell_exec($movie_meta);

//echo $movie_meta_output;

preg_match_all('/creation_time=(.*?)Z/', $movie_meta_output, $match);
echo $match[1][0];
//print_r($match);
/*

$str = 'before-str-after';
if (preg_match('/before-(.*?)-after/', $str, $match) == 1) {
    echo '<HR>'.$match[1];
}
*/
?>
