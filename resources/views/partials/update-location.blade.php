<script>
    document.addEventListener("DOMContentLoaded", function () {
        if ("geolocation" in navigator) {
            // Start watching location
            navigator.geolocation.watchPosition(
                function (position) {
                    let lat = position.coords.latitude;
                    let lng = position.coords.longitude;

                    // Get last saved coords from localStorage
                    let lastLat = localStorage.getItem("user_lat");
                    let lastLng = localStorage.getItem("user_lng");

                    // Only update if new coords are different (to avoid spam)
                    if (lastLat !== lat.toString() || lastLng !== lng.toString()) {
                        // Save in localStorage
                        localStorage.setItem("user_lat", lat);
                        localStorage.setItem("user_lng", lng);

                        // Send to Laravel via fetch
                        fetch("{{ route('profile.updateLocation') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                lat: lat,
                                lng: lng
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            console.log("✅ Location updated:", data);
                        })
                        .catch(err => console.error("❌ Error updating location:", err));
                    }
                },
                function (error) {
                    console.error("Geolocation error:", error);
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 10000,   // reuse recent position for 10s
                    timeout: 20000       // wait up to 20s
                }
            );
        } else {
            console.warn("Geolocation is not supported in this browser.");
        }
    });
</script>
