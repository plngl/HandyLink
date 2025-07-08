function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                console.log("Latitude: " + latitude);
                console.log("Longitude: " + longitude);

                // Step 1: Use OpenStreetMap Nominatim API to get address
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`)
                    .then(response => response.json())
                    .then(geoData => {
                        const address = geoData.address || {};
                        const city = address.city || address.town || address.village || "";
                        const province = address.state || "";
                        const barangay = address.suburb || address.neighbourhood || "";

                        console.log("City:", city);
                        console.log("Province:", province);
                        console.log("Barangay:", barangay);

                        // Step 2: Send all data to your PHP backend
                        fetch(BASE_URL + "controllers/routes.php?action=getLocation", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({
                                lat: latitude,
                                lng: longitude,
                                city: city,
                                province: province,
                                barangay: barangay
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Server response:", data);
                        })
                        .catch(error => {
                            console.error("Fetch error:", error);
                        });
                    })
                    .catch(error => {
                        console.error("Geocoding error:", error);
                    });
            },
            function (error) {
                console.error("Geolocation error: " + error.message);
            }
        );
    } else {
        console.error("Geolocation not supported.");
    }
}

window.addEventListener("DOMContentLoaded", () => {
    getLocation();
    setInterval(getLocation, 60000); // every 1 minute
});


