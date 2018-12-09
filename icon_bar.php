
<? echo date( "m/j/y" ,$row['time']); ?>

&nbsp;
&nbsp;

<span class="<? if(strpos($row['tag'], 'pano') !== false){ echo 'on'; }else{ echo 'off'; } ?>" data-id="<? echo $row['ID']; ?>" data-tag="pano" data-toggle="tooltip" data-placement="top" title="Panorama"><i class="fa fa-arrows-alt pano" aria-hidden="true" ></i></span>

<!-- <a href="#pano" class="img-cat" data-id="<? echo $row['ID']; ?>"  data-tag="pano" data-toggle="tooltip" data-placement="top" title="Panorama"><i class="fa fa-arrows-alt" aria-hidden="true" ></i></a> --!>

&nbsp;
&nbsp;
<span class="<? if(strpos($row['tag'], 'aesthetic') !== false){ echo 'on'; }else{ echo 'off'; } ?>" data-id="<? echo $row['ID']; ?>" data-tag="aesthetic" data-toggle="tooltip" data-placement="top" title="Aesthetic"><i class="fa fa-camera-retro aes" aria-hidden="true" ></i></span>

<span class="<? if(strpos($row['tag'], 'nostalgic') !== false){ echo 'on'; }else{ echo 'off'; } ?>" data-id="<? echo $row['ID']; ?>" data-tag="nostalgic" data-toggle="tooltip" data-placement="top" title="Nostalgic"><i class="fa fa-group nost" aria-hidden="true" ></i></span>

<span class="<? if(strpos($row['tag'], 'reminder') !== false){ echo 'on'; }else{ echo 'off'; } ?>" data-id="<? echo $row['ID']; ?>" data-tag="reminder" data-toggle="tooltip" data-placement="top" title="Reminder"><i class="fa fa-pencil-square-o remind" aria-hidden="true" ></i></span>

<span class="<? if(strpos($row['tag'], 'resource') !== false){ echo 'on'; }else{ echo 'off'; } ?>" data-id="<? echo $row['ID']; ?>" data-tag="resource" data-toggle="tooltip" data-placement="top" title="Resource"><i class="fa fa-file-image-o resource" aria-hidden="true" ></i></span>

&nbsp;
&nbsp;

<div id="stars-existing" class="starrr" data-itemnum='<? echo $row['ID'];?>' data-rating='<? echo $row['rate']; ?>' style='color:#cc9f00; display:inline-block;' ></div>

&nbsp;
&nbsp;
<span class="<? if(strpos($row['tag'], 'public') !== false){ echo 'on'; }else{ echo 'off'; } ?>" data-id="<? echo $row['ID']; ?>" data-tag="public" data-toggle="tooltip" data-placement="top" title="Share"><i class="fa fa-send" aria-hidden="true" ></i></span>

 
<a  href="single_thumb.php?pass=password&file=<? echo $row['file']; ?>" data-toggle="tooltip" data-placement="top" title="Process Thumbnail"><i class="fa fa-file-image-o" aria-hidden="true"></i></a>

<a href='javascript:show_map();consol.log("trig");'>
<span class="<? if(strlen($row['lat']) >3){ echo 'on'; }else{ echo 'off'; } ?>" data-id="<? echo $row['ID']; ?>" data-tag="location" data-toggle="tooltip" data-placement="top" title="Location"><i class="fa fa-globe" aria-hidden="true" ></i></span>
</a>
<span class="<? if(strlen($row['blurb']) >1){ echo 'on'; }else{ echo 'off'; } ?>" data-id="<? echo $row['ID']; ?>" data-tag="location" data-toggle="tooltip" data-placement="top" title="Location"><i class="fa fa-commenting-o" aria-hidden="true"></i></span>

