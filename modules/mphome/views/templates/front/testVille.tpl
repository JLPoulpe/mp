<div id="ville-livrable" class="mpborder">
    <h3>Zone de livraison</h3>
    <div class="form-ville">
        Votre ville est-elle dans la zone de livraison ?&nbsp;&nbsp;<input type="text" name="cp" placeholder="Votre code postal" />&nbsp;<input type="submit" value="Rechercher" id="searchForCP" />
        <div id="resultFormOk">Votre ville fait partie de notre zone de livraison</div>
        <div id="resultFormKo"><span>Votre ville ne fait pas encore partie de notre zone de livraison.</span><br />Mais vous pouvez toujours venir récupérer les produits sur les marchés.<br />Vous habitez trop loin pour venir sur les marchés ? Contactez-nous car dans le cadre d'une commande groupée des solutions existent.</div>
    </div>
    <div id="map">
    </div>
    <script type="text/javascript">
    {literal}
    var map;
    function initMap() {
        var lat = '44.8350088'; //Set your latitude.
        var lon = '-0.587268999999992'; //Set your longitude.
        var myLatLng = new google.maps.LatLng(lat, lon);
        
        var myOptions = {
            scrollwheel: true,
            draggable: true,
            disableDefaultUI: false,
            center: myLatLng,
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById('map'), myOptions);
        
        var circleForGMap = {
          map: map,
          center: myLatLng,
          title : 'Zone de livraison mespaysans.com',
          radius: 26000,
          strokeColor:'#FAB300',
          strokeOpacity: 0.4,
          strokeWeight: 0,
          fillColor: '#FAB300',
          fillOpacity: 0.4,
        };
        
        cityCircle = new google.maps.Circle(circleForGMap);
    }
    {/literal}
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCQQgUvU2OLmJbJLY5EBBtcMfZ-AempBBQ&callback=initMap"></script>
    <div class="clearfix"></div>
</div>