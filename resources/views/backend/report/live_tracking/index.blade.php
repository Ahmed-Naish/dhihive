@extends('backend.layouts.app')
@section('title', @$data['title'])
@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$data['title'],
    ]) !!}
    <div class="table-content table-basic">
        <section class="card">
            <div class="card-body">
                @php
                    $company_configs = DB::table('company_configs')->where('key', 'google')->first();
                    $company_configs = $company_configs ? $company_configs->value : '';
                @endphp
                @if ($company_configs != '')
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 text-center " style="height: 100px">
                                <h2 class="text-center justify-center mt-5">Live Location Tracking </h2>
                                <img src="{{ url('frontend/assets/ajax-loader.gif') }}" alt=""
                                    class="img img-responsive text-center justify-center" id="loader"
                                    style="width: 65px; margin:0 auto;  z-index:99999">
                            </div>
                            <div class="col-lg-2 d-none ">
                                <div class="table-responsive" style="height: 90vh; ">
                                    <table class="table table-bordered table-striped" style="display: none" id="table">
                                        <thead>
                                            <th>Employees</th>
                                        </thead>
                                        <tbody id="tbody">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12">

                                <div id="map" style="height: 90vh; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row p-5 mt-3 mb-3 ">
                        <div class="col-lg-12">
                            <h3 class="danger text-danger"> {{ _trans('error.Please set google map API Key') }}</h3>
                            <a href="{{ url('admin/company-setup/configuration') }}"
                                class="btn btn-primary">{{ _trans('common.Update Configuration') }}</a>
                        </div>
                    </div>
                    <div class="row mt-3 mb-3  p-5">
                        <h3>Create a Google Cloud Platform (GCP) Project:</h3>
                        <div class="col-lg-12">
                            <ol>
                                <li>Go to the <a href="https://console.cloud.google.com/" target="_blank"
                                        class="text-info">Google Cloud Console</a>.</li>
                                <li>If you're not already signed in, sign in with your Google account.</li>
                                <li>Click the project drop-down at the top left of the page and then click "New
                                    Project".
                                </li>
                                <li>Enter a name for your project, select an organization (if applicable), and choose a
                                    billing account. Click "Create" to create the project.</li>
                            </ol>

                            <h3>Enable the Google Maps JavaScript API:</h3>
                            <ol>
                                <li>In the GCP Console, navigate to the "APIs &amp; Services" &gt; "Library" section.
                                </li>
                                <li>Search for "Google Maps JavaScript API" and click on it.</li>
                                <li>Click the "Enable" button.</li>
                            </ol>

                            <h3>Create an API Key:</h3>
                            <ol>
                                <li>In the "APIs &amp; Services" &gt; "Credentials" section, click on "Create
                                    Credentials".
                                </li>
                                <li>Select "API key" from the drop-down menu.</li>
                                <li>Your API key will be displayed on the next screen. Make sure to restrict the usage
                                    of
                                    your API key for security purposes (optional but recommended).</li>
                            </ol>
                        </div>

                    </div>
                @endif
            </div>
        </section>
    </div>

    <input type="text" hidden id="data_url"
        value="{{ route('live_trackingReportEmployee.index', 'date=' . @$data['date']) }}">
    <input type="text" hidden id="token" value="{{ csrf_token() }}">
@endsection
@section('script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/8.2.2/firebase-app.min.js" referrerpolicy="no-referrer">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/8.2.2/firebase-database.min.js"
        referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/8.2.2/firebase-firestore.min.js"
        referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" referrerpolicy="no-referrer">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.36/moment-timezone-with-data.min.js"
        referrerpolicy="no-referrer"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ settings('google') }}" async defer
        referrerpolicy="no-referrer"></script>
    <script src="{{ url('backend/data/firebase-location.js') }}"></script>

    <script type="module">
        const config = {
            apiKey: "{{ env('FIREBASE_API_KEY') }}",
            authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
            projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
            storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
            appId: "{{ env('FIREBASE_APP_ID') }}",
            measurementId: "{{ env('FIREBASE_MEASUREMENT_ID') }}"
        };
        firebase.initializeApp(config);


        const db = firebase.firestore();


        // Example usage
        const collectionName = "hrm_employee_track"; // Replace with your Firestore collection name

        // Fetch user data once and store it in a JavaScript object
        $.ajax({
            url: "{{ url('get-all-employee-list-api') }}",
            method: "GET",
            success: function(userData) {
                const userMap = {};
                userData.forEach(user => {
                    userMap[user.company_user_id] = user;
                });

                function formatTimestamp(timestamp) {
                    return moment(timestamp).format('YYYY-MM-DD HH:mm:ss.SSS');
                }

                // Function to get the current timestamp in GMT+6 time zone and format it
                function getCurrentTimestampInGMT6() {
                    const currentTimestamp = moment().tz('Etc/GMT-6'); // GMT+6 time zone
                    return formatTimestamp(currentTimestamp);
                }

                function getTimestamp30MinutesAgoInGMT6() {
                    const thirtyMinutesAgo = moment().subtract(50, 'minutes').tz('Etc/GMT-6');
                    return thirtyMinutesAgo.format('YYYY-MM-DD HH:mm:ss');
                }

                function generateDateWithMicrosecondsAndSubtract30Minutes() {
                    const now = new Date();

                    // Subtract 30 minutes
                    now.setMinutes(now.getMinutes() - 60);

                    // Get the main date parts (YYYY-MM-DD HH:mm:ss)
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');

                    // Get the milliseconds and generate microseconds
                    const milliseconds = now.getMilliseconds();
                    const microseconds = Math.floor(Math.random() * 1000); // Random microseconds

                    // Format the final timestamp: YYYY-MM-DD HH:mm:ss.milliseconds microseconds
                    const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}.${String(milliseconds).padStart(3, '0')}${String(microseconds).padStart(3, '0')}`;

                    return formattedDate;
                }

                // Example usage
                const timestamp30MinutesAgo = generateDateWithMicrosecondsAndSubtract30Minutes();

                console.log(timestamp30MinutesAgo);

                function readDataFromFirestore(collectionName) {
                    console.log("Timestamp 30 Minutes Ago:", timestamp30MinutesAgo);

                    const collectionRef = db.collection(collectionName);

                    return collectionRef
                        .where("datetime", ">=",
                            timestamp30MinutesAgo) // Ensure Firestore datetime is a timestamp field
                        .orderBy("datetime", "desc")
                        .get()
                        .then((querySnapshot) => {
                            const data = [];
                            querySnapshot.forEach((doc) => {
                                const docData = doc.data();

                                data.push({
                                    id: doc.id,
                                    ...docData
                                });
                            });

                            console.log("Retrieved Data: ", data);
                            console.log("Successfully fetched data from Firestore.");
                            return data;
                        })
                        .catch((error) => {
                            console.error("Error reading data from Firestore:", error);
                            throw error;
                        });
                }







                // Now fetch data from Firestore
                readDataFromFirestore(collectionName)
                    .then((data) => {
                        var htmls = '';
                        $.each(data, function(key, value) {
                            // Find the corresponding user data using the employee ID
                            const user = userMap[value.id] || {};
                            htmls += `
                            <tr>

                                <td>
                                ${user.name || "ID#" + value.id} | ${user.email || ""}  <br />
                                Address: ${value.address || ""}, ${value.city || ""} , ${value.country || ""} <br />
                                ${formatDateTime(value.datetime)} | ${formatTimeDifference(value.datetime)} <br>
                                    ${value.latitude} | ${value.longitude}
                                </td>
                            </tr>
                            `;
                        });

                        $('#tbody').empty();
                        $('#tbody').html(htmls);
                        document.getElementById("table").style.display = "block";
                        drawMap(data, userMap);
                    })
                    .catch((error) => {
                        console.error("Failed to retrieve data from Firestore:", error);
                    });
            },
            error: function(error) {
                console.error("Failed to fetch user data:", error);
            },
        });

        function drawMap(data, userMap) {
            const locations = data;

            const map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 0,
                    lng: 0
                }, // Set the initial map center
                zoom: 3, // Adjust the initial zoom level as needed
            });
            const customMarkerIcon = {
                url: "{{ url('assets/live.gif') }}",
                scaledSize: new google.maps.Size(40, 40),
            };
            console.log(customMarkerIcon);

            // Hide the loading image once the map is loaded
            google.maps.event.addListenerOnce(map, "tilesloaded", function() {
                document.getElementById("loader").style.display = "none";
            });

            locations.forEach((location) => {
                const user = userMap[location.id] || {};
                const marker = new google.maps.Marker({
                    position: {
                        lat: location.latitude,
                        lng: location.longitude
                    },
                    map: map,
                    title: location.address,
                    icon: customMarkerIcon,
                });

                // Add an info window to show additional details
                const infoWindow = new google.maps.InfoWindow({
                    content: `<p>${user.name || "ID#" + location.id}</p><strong>${
                    location.city
                    }</strong><br>${location.address}`,
                });

                marker.addListener("click", () => {
                    infoWindow.open(map, marker);

                    map.setCenter({
                        lat: location.latitude,
                        lng: location.longitude
                    });
                    map.setZoom(15);
                });
            });

            if (locations.length > 0) {
                const lastLocation = locations[locations.length - 1]; // Get the last location
                map.setCenter({
                    lat: lastLocation.latitude,
                    lng: lastLocation.longitude
                });
                map.setZoom(12); // Adjust the zoom level as needed
            }
        }

        // Show the loader initially
        document.getElementById("loader").style.display = "block";

        // Function to reload the page
        function reloadPage() {
            location.reload();
        }

        // Set a timeout to reload the page every 2 minutes (120,000 milliseconds)
        setTimeout(reloadPage, 120000);
    </script>
@endsection
