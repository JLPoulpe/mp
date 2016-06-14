<div class="mpborder" id="marche-proximite">
    <h3>Localisez vos marchés</h3>
    <div id="map">
    </div>
    <script type="text/javascript">
    {literal}
    var map;
    function initMap() {
        var lat = '44.8350088'; //Set your latitude.
        var lon = '-0.587268999999992'; //Set your longitude.
        
        /* Déclaration de l'icône personnalisée */
        var monIconPerso = new google.maps.MarkerImage("/themes/mespaysans/img/gmap.png",
            /* dimensions de l'image */
            new google.maps.Size(32,37),
            /* Origine de l'image 0,0. */
            new google.maps.Point(0,0),
            /* l'ancre (point d'accrochage sur la map) du picto
            (varie en fonction de ces dimensions) */
            new google.maps.Point(16,34)
        );

        var myOptions = {
            scrollwheel: true,
            draggable: true,
            disableDefaultUI: false,
            center: new google.maps.LatLng(lat, lon),
            zoom: 11,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById('map'), myOptions);
        {/literal}
        {$listMarketGMapContent}
        {literal}
    }
    {/literal}
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCQQgUvU2OLmJbJLY5EBBtcMfZ-AempBBQ&callback=initMap"></script>
</div>