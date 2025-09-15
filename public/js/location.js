if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition(
        function (position) {
            let lat = position.coords.latitude;
            let lng = position.coords.longitude;

            // send to Laravel via AJAX/fetch
            fetch("/update-location", {
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
                    console.log("Location updated:", data);
                })
                .catch(err => console.error(err));
        },
        function (error) {
            console.error("Error getting location:", error);
        }
    );
} else {
    alert("Geolocation is not supported in this browser.");
}

