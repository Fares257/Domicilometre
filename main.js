document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('map').setView([46.603354, 1.888334], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © OpenStreetMap contributors'
    }).addTo(map);

    fetch('get_domiciles.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(d => {
                L.marker([d.lat, d.lon]).addTo(map).bindPopup(d.ville);
            });
        });
});