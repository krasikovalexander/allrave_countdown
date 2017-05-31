@if ($model->lat) 
<div id="map" style="height: 400px"></div>
<script>
      function initMap() {
        var place = {lat: {{$model->lat}}, lng: {{$model->lng}}};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 16,
          center: place
        });
        var marker = new google.maps.Marker({
          position: place,
          map: map
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{config("services.google.maps.api_key")}}&callback=initMap">
    </script>
@endif