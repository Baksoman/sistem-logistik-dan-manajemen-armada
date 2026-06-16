const shipmentId = window.location.pathname.split('/').pop();                                                                    
                                                                                                                                     
    const domElement = document.querySelector('[x-data]');                                                                           
    const tracker = domElement ? domElement._x_dataStack[0] : null;                                                                  
                                                                                                                                     
    if (!tracker || !tracker.routeJson) {                                                                                            
        console.error("❌ Gagal membaca Rute GeoJSON! Pastikan garis biru muncul di peta.");                                         
    } else {                                                                                                                         
        // 1. Ekstrak data Rute                                                                                                      
        let geoData = tracker.routeJson;                                                                                             
        if (typeof geoData === 'string') geoData = JSON.parse(geoData);                                                              
                                                                                                                                     
        // Cari daftar Koordinat (Format GeoJSON selalu [Longitude, Latitude])                                                       
        let coordinates = [];                                                                                                        
        if (geoData.type === 'Feature' && geoData.geometry) coordinates = geoData.geometry.coordinates;                              
        else if (geoData.coordinates) coordinates = geoData.coordinates;                                                             
        else if (geoData.features && geoData.features.length > 0) coordinates = geoData.features[0].geometry.coordinates;            
                                                                                                                                     
        if (!coordinates || coordinates.length === 0) {                                                                              
            console.error("❌ Rute kosong / tidak valid.");                                                                          
        } else {                                                                                                                     
            console.log(`🚀 Simulasi Autopilot Dinyalakan! Menyusuri ${coordinates.length} belokan.`);                               
                                                                                                                                     
            let currentIndex = 0;                                                                                                    
                                                                                                                                     
            const driverEngine = setInterval(() => {                                                                                 
                // Cek apakah sudah sampai tujuan                                                                                    
                if (currentIndex >= coordinates.length) {                                                                            
                    console.log("🏁 TRUK TELAH SAMPAI DI TUJUAN AKHIR!");                                                            
                    clearInterval(driverEngine);                                                                                     
                    return;                                                                                                          
                }                                                                                                                    
                                                                                                                                     
                // Ekstrak (GeoJSON = Lng, Lat)                                                                                      
                const lng = coordinates[currentIndex][0];                                                                            
                const lat = coordinates[currentIndex][1];                                                                            
                                                                                                                                     
                // Update ikon truk secara visual di HP Anda                                                                         
                if(tracker.marker) {                                                                                                 
                    tracker.marker.setLatLng([lat, lng]);                                                                            
                }                                                                                                                    
                                                                                                                                     
                // Broadcast *Live Tracking* ke Komputer Dosen (Admin)                                                               
                fetch('/api/driver/location/ping', {                                                                                 
                    method: 'POST',                                                                                                  
                    credentials: 'include',                                                                                          
                    headers: {                                                                                                       
                        'Content-Type': 'application/json',                                                                          
                        'Accept': 'application/json',                                                                                
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''             
                    },                                                                                                               
                    body: JSON.stringify({ shipment_id: shipmentId, lat: lat, lng: lng })                                            
                }).then(r=>r.json()).then(d => {                                                                                     
                    console.log(`📡 Bergeser ke titik currentIndex + 1/{coordinates.length}`);                                       
                });                                                                                                                  
                                                                                                                                     
                // KECEPATAN TRUK:                                                                                                   
                // Kita skip 2 titik setiap melompat, agar jalan di petanya terasa ngebut                                            
                // tapi tetap halus menempel di garis biru.                                                                          
                currentIndex += 2;                                                                                                   
                                                                                                                                     
            }, 1500); // Bergerak setiap 1.5 detik                                                                                   
        }                                                                                                                            
    }                  