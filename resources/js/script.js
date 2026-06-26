(function() {
    // Dapatkan shipment ID dari URL
    const shipmentId = window.location.pathname.split('/').filter(Boolean).pop();
    
    // Akses Alpine.js v3 data menggunakan Alpine.$data() dengan query selector spesifik
    const domElement = document.querySelector('[x-data="journeyTracker()"]');
    if (!domElement) {
        console.error("Tidak ada komponen Alpine.js 'journeyTracker()' ditemukan di halaman ini.");
        return;
    }

    // Alpine v3: gunakan Alpine.$data() atau _x_dataStack
    let tracker = null;
    if (window.Alpine && typeof Alpine.$data === 'function') {
        tracker = Alpine.$data(domElement);
    } else if (domElement._x_dataStack && domElement._x_dataStack.length > 0) {
        tracker = domElement._x_dataStack[0];
    }
    
    if (!tracker) {
        console.error("Gagal membaca data Alpine.js. Pastikan halaman sudah sepenuhnya dimuat.");
        return;
    }

    if (!tracker.routeJson) {
        console.error("Gagal membaca Rute GeoJSON! Pastikan shipment ini memiliki rute yang terpasang dan garis biru muncul di peta.");
        console.info("Cek: tracker.routeJson =", tracker.routeJson);
        return;
    }

    // 1. Ekstrak data Rute
    let geoData = tracker.routeJson;
    if (typeof geoData === 'string') {
        try { geoData = JSON.parse(geoData); } catch(e) {
            console.error("GeoJSON tidak valid:", e);
            return;
        }
    }

    // Cari daftar Koordinat (Format GeoJSON selalu [Longitude, Latitude])
    let coordinates = [];
    if (geoData.type === 'Feature' && geoData.geometry) coordinates = geoData.geometry.coordinates;
    else if (geoData.coordinates) coordinates = geoData.coordinates;
    else if (geoData.features && geoData.features.length > 0) coordinates = geoData.features[0].geometry.coordinates;

    if (!coordinates || coordinates.length === 0) {
        console.error("Rute kosong / tidak valid. Cek struktur GeoJSON:", geoData);
        return;
    }

    console.log(`Simulasi Autopilot Dinyalakan! Menyusuri ${coordinates.length} titik koordinat.`);
    console.log(`Shipment ID: ${tracker.shipmentId || shipmentId}`);

    let currentIndex = 0;
    const actualShipmentId = tracker.shipmentId || shipmentId;

    const driverEngine = setInterval(() => {
        // Cek apakah sudah sampai tujuan
        if (currentIndex >= coordinates.length) {
            console.log("TRUK TELAH SAMPAI DI TUJUAN AKHIR! Simulator berhenti.");
            clearInterval(driverEngine);
            return;
        }

        // Ekstrak (GeoJSON = Lng, Lat)
        const lng = coordinates[currentIndex][0];
        const lat = coordinates[currentIndex][1];

        // Update ikon truk secara visual di peta
        if (tracker.marker) {
            tracker.marker.setLatLng([lat, lng]);
            
            // Auto-pan peta kalau mode tracking aktif
            if (tracker.isTracking) {
                tracker.map.panTo([lat, lng]);
            }
        }

        // Broadcast ke server (simpan ke gps_history)
        fetch('/api/driver/location/ping', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ shipment_id: actualShipmentId, lat: lat, lng: lng })
        }).then(r => r.json()).then(() => {
            console.log(`Step ${currentIndex}/${coordinates.length} → [${lat.toFixed(5)}, ${lng.toFixed(5)}]`);
        }).catch(err => {
            console.warn("Ping gagal:", err);
        });

        // Skip 2 titik per interval agar simulasi terasa lebih cepat
        currentIndex += 2;

    }, 1500); // Bergerak setiap 1.5 detik

    console.log("Simulator berjalan. Untuk berhenti ketik: clearInterval(driverEngine)");
    window.driverEngine = driverEngine;
})();