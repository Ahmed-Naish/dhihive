"use strict";
$(document).ready(function () {
  $("#location-pattern-select").change(function () {
    const selectedPattern = $(this).val();

    $('[id^="map-pattern-"]').hide();

    if (selectedPattern) {
      $(`#map-pattern-${selectedPattern}`).show();
    }
  });

  const $dataUrl = $("#data_url");
  const $mapElement = $("#map");
  let map;
  let markers = [];
  let directionsService;

  function createMarker(position, iconUrl, labelText) {
    return new google.maps.Marker({
      position,
      map,
      title: labelText,
      optimized: false,
      label: {
        text: labelText,
        color: "black",
        fontWeight: "bold",
        className: "marker-label",
        labelOrigin: new google.maps.Point(0, 100),
      },
      icon: {
        url: iconUrl,
        scaledSize: new google.maps.Size(40, 40),
      },
    });
  }

  function initializeMap(mapData) {
    const initialLocation = {
      lat: mapData[0]?.latitude ?? 23.7947653,
      lng: mapData[0]?.longitude ?? 90.4013282,
    };

    map = new google.maps.Map($mapElement[0], {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      scrollwheel: true,
      center: initialLocation,
      zoom: 12,
    });

    const bounds = new google.maps.LatLngBounds();
    const pathCoordinates = [];

    // Add markers for each record in mapData and collect path coordinates
    mapData.forEach((data, index) => {
      const position = { lat: parseFloat(data.latitude), lng: parseFloat(data.longitude) };
      const marker = createMarker(
          position,
          index === 0
              ? "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
              : index === mapData.length - 1
              ? "https://maps.google.com/mapfiles/ms/icons/green-dot.png"
              : "https://maps.google.com/mapfiles/ms/icons/blue-dot.png",
          `${data.start_location ? data.start_location : ''}, ${data.created_at}`
      );

      bounds.extend(marker.getPosition());
      markers.push(marker);
      pathCoordinates.push(position);
    });

    directionsService = new google.maps.DirectionsService();

    // Calculate and display routes between each pair of points
    let pathIndex = 0;
    function calculateNextRoute() {
      if (pathIndex < pathCoordinates.length - 1) {
        const startLocation = pathCoordinates[pathIndex];
        const endLocation = pathCoordinates[pathIndex + 1];

        directionsService.route(
            {
              origin: startLocation,
              destination: endLocation,
              travelMode: google.maps.TravelMode.WALKING,
            },
            function (response, status) {
              if (status === "OK") {
                // Display the route on the map
                const directionsRenderer = new google.maps.DirectionsRenderer({
                  map,
                  suppressMarkers: true,
                  polylineOptions: {
                    strokeColor: "red",
                    strokeOpacity: 1.0,
                    strokeWeight: 4,
                  },
                });

                directionsRenderer.setDirections(response);

                // Move to the next segment
                pathIndex++;
                calculateNextRoute();
              } else {
                window.alert("Directions request failed due to " + status);
              }
            }
        );
      } else {
        map.fitBounds(bounds);
      }
    }

    calculateNextRoute();
  }

  function initMap() {
    const initialLocation = { lat: 23.7947653, lng: 90.4013282 };

    map = new google.maps.Map($mapElement[0], {
      zoom: 12,
      center: initialLocation,
    });
  }

  if ($dataUrl.val()) {
    $.getJSON($dataUrl.val())
        .done(function (mapData) {
          console.log(mapData)
          if (mapData[0]) {
            initializeMap(mapData);
          } else {
            initMap();
          }
        })
        .fail(function () {
          window.alert("Failed to load map data.");
          initMap();
        });
  } else {
    initMap();
  }
});
