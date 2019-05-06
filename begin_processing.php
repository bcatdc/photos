
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

//CONNECT TO DB
global $link;

//CREATE A THUMBNAIL DIRECTOY IN CASE IT GETS MOVED
if(!is_dir('../thumbs')) mkdir('../thumbs') or die('can\' create thumbs directory');
?>

<HTML>
    <body>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
       <style>
           body{ background-color: #333; color:#ccc; font-family: 'Roboto', sans-serif;}
           b{color:#fff; font-size:1.2em;}
       </style>

        <script
			  src="https://code.jquery.com/jquery-3.3.1.min.js"
			  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
			  crossorigin="anonymous"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function(event) {
                console.log("DOM fully loaded and parsed");
                loadMoreData(0);
            });
        </script>

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
  $batch_tags =  $_GET['tag']  ;
  echo 'The tag "' . $batch_tags . '" has been manually added for each file.<BR> ';
}else{
  echo 'There was no manual tag added &tag=YOURTAGHERE.<BR>';
  $batch_tags='';
}
?>

<HR>
FINISHED LOOKING AT GET VARIABLES, STARTING BATCH PROCESS
<HR>
<?

//BUILD AN ARRAY OF ALL THE FILES IN "waiting_to_be_batch_processed/" THAT ARE NOT DUPLICATES
function listFiles( $from = '../waiting_to_be_batch_processed'){
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

            // OLD DUP CHECK METHO
  			//		$dupesql = "SELECT * FROM media where (file = '$path')";
  			//		$duperaw = mysqli_query($link, $dupesql);
  			//		$row_cnt = $duperaw->num_rows;
            //
					//If it is a dup yell about it and ignore it
			//		if ($row_cnt > 0)
			//		{ echo "The file at ". $path . " is already in the DB and will be ignored.<BR>";
            //      }else{
					    $file_list[] = $path;
			//		}
            }
            closedir($dh);
        }
    }
    //Return an array of all the files to be examined for processing
    return $file_list;

}
/*
//Get current Serial Number from DB for naming
$serialsql = "SELECT ID FROM media  ORDER BY ID DESC limit 1";
$serialraw = mysqli_query($link, $serialsql );
if (mysqli_num_rows($serialraw) > 0) {
    $row = mysqli_fetch_assoc($serialraw);
    $serial = $row["ID"]+1;
} else {
    $serial = 1;
}
*/
//Prepare List and total count of files
$file_list = listFiles();
$count = 0;
$total = count($file_list);
$file_list_r = array_reverse($file_list);
?>
<script>
   var files = <?php echo json_encode($file_list_r)?>;
</script>

<HR>
  Finished examining files in "waiting_to_be_batch_processed/" for dups. Beginning Processing of <? echo $total; ?> files
<HR>

<div id="results">
    FILL THIS SPACE
</div>


<script type="text/javascript">
console.log(files);
    var x=0;

    //SIZE & QUALITY VARIABLES
    var size=495;
    var quality=85;


    var preTags = "<?php echo $batch_tags; ?>";

    function loadMoreData(x){
        if (x < files.length){
            console.log(x);
            var filename = files[x];
            console.log(filename);
            var url = 'process_next.php?file_name=' + filename+'&pass=password&size='+size+'&quality='+ quality +'&pre_tags='+preTags;
            console.log(url);
              $.ajax(
                    {
                        url: url,
                        type: "get",
                        beforeSend: function(){ $('.ajax-load').show(); }
                    })

                    .done(function(data)

                    {
                        $('.ajax-load').hide();
                        $("#results").append(data);
                        x++;
                        loadMoreData(x);
                    })

                    .fail(function(jqXHR, ajaxOptions, thrownError)
                    {
                          alert('server not responding at ' + url);
                    });
            }else{
            alert('complete');
        }
}
</script>
</body>
</html>
