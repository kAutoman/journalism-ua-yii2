//--------- map js code -----------
$(".product_map").each(function () {
    initMap($(this).attr('id'));

});

$(document).on('mouseover', '.product_map.builder', function () {
    if (!$(this).hasClass('loaded')) {
        initMap($(this).attr('id'));
        $(this).addClass('loaded')
    }

});

function initMap(mapId) {
    if (mapId == undefined) {
        var lastMap = $('.product_map').last();
        var lastMapId = lastMap.attr('id');
    } else {
        var lastMapId = mapId;
    }

    var uniqueId = lastMapId.replace('product_map_', '');

    var nearestLatInput = $('#product_map_lat_' + uniqueId);
    var nearestLongInput = $('#product_map_lng_' + uniqueId);

    var coordsFromBaseLat = parseFloat(nearestLatInput.val());
    var coordsFromBaseLon = parseFloat(nearestLongInput.val());
    var coordsFromBase = {lat: coordsFromBaseLat, lng: coordsFromBaseLon};

    var map = new google.maps.Map(document.getElementById(lastMapId), {
        zoom: 7,
        elementId: lastMapId,
        center: coordsFromBase,
        scrollwheel: false,
        styles: [{
            stylers: [{
                saturation: -100
            }]
        }]
    });

    var markers = [
        new google.maps.Marker({
            position: coordsFromBase,
            map: map
        })
    ];

    //try to get coordinates with stackoverflow
    google.maps.event.addListener(map, "click", function (event) {

        var lat = event.latLng.lat();
        var lng = event.latLng.lng();
        // populate yor box/field with lat, lng

        $('#product_map_lat_' + uniqueId).val(lat);
        $('#product_map_lng_' + uniqueId).val(lng);

        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }

        mark = new google.maps.Marker({
            position: {lat: lat, lng: lng},
            map: map
        });
        markers.push(mark);
    });

    var input = document.getElementById('pac-input');

    var searchBox = new google.maps.places.SearchBox(input);

    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    google.maps.event.addListener(searchBox, 'places_changed', function () {
        var places = searchBox.getPlaces();

        for (var i = 0, marker; marker = markers[i]; i++) {
            marker.setMap(null);
        }

        // For each place, get the icon, place name, and location.
        markers = [];
        var bounds = new google.maps.LatLngBounds();
        var place = null;
        var viewport = null;
        for (var i = 0; place = places[i]; i++) {
            var image = {
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            var marker = new google.maps.Marker({
                map: map,
                icon: image,
                title: place.name,
                position: place.geometry.location
            });
            viewport = place.geometry.viewport;
            markers.push(marker);

            bounds.extend(place.geometry.location);
        }
        map.setCenter(bounds.getCenter());
    });

    // Bias the SearchBox results towards places that are within the bounds of the
    // current map's viewport.
    google.maps.event.addListener(map, 'bounds_changed', function () {
        var bounds = map.getBounds();
        searchBox.setBounds(bounds);
    });
}
