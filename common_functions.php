<?php

function epochDur($seconds) {
	// Turn Epoch Time into Days Hrs Mins & Seconds.
	// Used for displaying the time between images
  $t = round($seconds);
  return sprintf('%02d days %02d hrs %02d mins %02d secs', ($t/86400),($t/3600%24),($t/60%60), $t%60);
}

?>
