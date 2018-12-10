<?php

//WE WANT TO SEE ALL THE ERRORS
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);


$command="ffmpeg -i /Users/bconnors/Desktop/Codes/bcatdc.us.to/photos/waiting_to_be_batch_processed/mp4s/input.mp4 -vcodec mjpeg -vframes 1 -an -f rawvideo -ss `ffmpeg -i /Users/bconnors/Desktop/Codes/bcatdc.us.to/photos/waiting_to_be_batch_processed/mp4s/input.mp4 2>&1 | grep Duration | awk '{print $2}' | tr -d , | awk -F ':' '{print ($3+$2*60+$1*3600)/2}'` /Users/bconnors/Desktop/Codes/bcatdc.us.to/photos/waiting_to_be_batch_processed/mp4s/output.jpg 2>&1";

system($command);

?>
