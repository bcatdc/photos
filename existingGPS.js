<script type="text/javascript">
var marker;
function initialize() {
    var latlng = new google.maps.LatLng(<? echo $row['lat'] .','. $row['lon']; ?>);
    var myOptions = {
        zoom: 14,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.HYBRID,
        streetViewControl: false,
        mapTypeControl: false
    };

    var map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

    var marker = new google.maps.Marker({
      position: latlng,
      map: map
  	});

      google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng);
        document.getElementById('latlon').value = event.latLng;
    });

    function placeMarker(location) {
        if (marker == undefined){
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    animation: google.maps.Animation.DROP
                });
            }else{
                marker.setPosition(location);
        }

        map.setCenter(location);

        google.maps.event.addListener(marker, "click", function (event) {
            document.getElementById('latlon').value = this.position;
        });
    }
}

</script>
