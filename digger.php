<html>
<body>
<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

function listFiles( $from = '../2005 Photos')
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
                    $files[] = $path;
                    echo $path . '<BR>';
            }
            closedir($dh);
        }
    }
}

listFiles();
?>

</body>
</html>