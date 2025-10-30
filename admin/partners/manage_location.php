<?php
session_start();
require_once '../../db_connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// รับ ID ของ partner
$partner_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ดึงข้อมูล partner
$partner = null;
if ($partner_id > 0) {
    $query = "SELECT * FROM partners WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $partner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $partner = $result->fetch_assoc();
    
    if (!$partner) {
        $_SESSION['error'] = "ไม่พบข้อมูลพันธมิตร";
        header('Location: index.php');
        exit;
    }
}

// ดึง Google Maps API Key จากฐานข้อมูล
$google_maps_api_key = '';
$api_query = "SELECT api_key FROM api_keys WHERE api_name = 'google_maps' AND is_active = 1 LIMIT 1";
$api_result = $conn->query($api_query);
if ($api_result && $api_result->num_rows > 0) {
    $api_data = $api_result->fetch_assoc();
    $google_maps_api_key = $api_data['api_key'];
}

// จัดการการอัพเดทพิกัด
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_location'])) {
    $latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
    $longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
    $address = trim($_POST['address']);
    $map_zoom_level = intval($_POST['map_zoom_level']);
    
    // Validate zoom level
    if ($map_zoom_level < 1) $map_zoom_level = 1;
    if ($map_zoom_level > 20) $map_zoom_level = 20;
    
    $update_query = "UPDATE partners SET 
                    latitude = ?, 
                    longitude = ?, 
                    address = ?, 
                    map_zoom_level = ?
                    WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ddsii', $latitude, $longitude, $address, $map_zoom_level, $partner_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "อัพเดทตำแหน่งบนแผนที่เรียบร้อยแล้ว";
        header("Location: manage_location.php?id=$partner_id");
        exit;
    } else {
        $error = "เกิดข้อผิดพลาดในการอัพเดท: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการตำแหน่งบนแผนที่ - <?php echo htmlspecialchars($partner['name']); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <style>
        .map-container {
            height: 500px;
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #dee2e6;
            margin-bottom: 20px;
        }
        
        .location-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .search-box {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
            width: 300px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            font-size: 14px;
        }
        
        .coordinates-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .marker-instructions {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-map-marked-alt"></i> จัดการตำแหน่งบนแผนที่</h2>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
        
        <!-- Partner Info -->
        <div class="alert alert-info mb-4">
            <h5 class="mb-1">
                <i class="fas fa-building"></i> <?php echo htmlspecialchars($partner['name']); ?>
            </h5>
            <?php if (!empty($partner['project_name'])): ?>
            <small>โครงการ: <?php echo htmlspecialchars($partner['project_name']); ?></small>
            <?php endif; ?>
        </div>
        
        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Instructions -->
        <div class="marker-instructions">
            <h6><i class="fas fa-info-circle"></i> วิธีการใช้งาน:</h6>
            <ol class="mb-0">
                <li>คลิกบนแผนที่เพื่อกำหนดตำแหน่งใหม่</li>
                <li>ลากหมุดเพื่อปรับตำแหน่ง</li>
                <li>ใช้ช่องค้นหาเพื่อหาสถานที่</li>
                <li>ปรับระดับ zoom ตามต้องการ</li>
                <li>กดบันทึกเมื่อพอใจกับตำแหน่ง</li>
            </ol>
        </div>
        
        <!-- Map -->
        <div class="position-relative">
            <div class="search-box">
                <input id="searchInput" 
                       type="text" 
                       placeholder="ค้นหาสถานที่..." 
                       onkeydown="if (event.keyCode === 13) { event.preventDefault(); return false; }">
            </div>
            <div id="map" class="map-container"></div>
        </div>
        
        <!-- Location Form -->
        <div class="location-form">
            <form method="POST" action="">
                <div class="coordinates-display">
                    <h5 class="mb-3">พิกัดปัจจุบัน</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="latitude">Latitude:</label>
                                <input type="number" 
                                       step="0.00000001" 
                                       class="form-control" 
                                       id="latitude" 
                                       name="latitude" 
                                       value="<?php echo $partner['latitude'] ?? ''; ?>"
                                       placeholder="เช่น 6.6238">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="longitude">Longitude:</label>
                                <input type="number" 
                                       step="0.00000001" 
                                       class="form-control" 
                                       id="longitude" 
                                       name="longitude" 
                                       value="<?php echo $partner['longitude'] ?? ''; ?>"
                                       placeholder="เช่น 100.0676">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="map_zoom_level">ระดับ Zoom (1-20):</label>
                                <input type="number" 
                                       min="1" 
                                       max="20" 
                                       class="form-control" 
                                       id="map_zoom_level" 
                                       name="map_zoom_level" 
                                       value="<?php echo $partner['map_zoom_level'] ?? 15; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">ที่อยู่</label>
                    <textarea class="form-control" 
                              id="address" 
                              name="address" 
                              rows="2"
                              placeholder="เช่น อำเภอเมืองสตูล จังหวัดสตูล"><?php echo htmlspecialchars($partner['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-warning" onclick="getCurrentLocation()">
                        <i class="fas fa-crosshairs"></i> ใช้ตำแหน่งปัจจุบันของฉัน
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary" onclick="clearLocation()">
                            <i class="fas fa-times"></i> ล้างตำแหน่ง
                        </button>
                        <button type="submit" name="update_location" class="btn btn-primary">
                            <i class="fas fa-save"></i> บันทึกตำแหน่ง
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Google Maps Script -->
    <script>
    let map;
    let marker;
    let searchBox;
    
    function initMap() {
        // Default center (Satun)
        let defaultLat = <?php echo $partner['latitude'] ?? 6.6238; ?>;
        let defaultLng = <?php echo $partner['longitude'] ?? 100.0676; ?>;
        let defaultZoom = <?php echo $partner['map_zoom_level'] ?? 15; ?>;
        
        // Create map
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: defaultLat, lng: defaultLng },
            zoom: defaultZoom,
            mapTypeId: 'roadmap',
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true
        });
        
        // Create marker if location exists
        if (<?php echo ($partner['latitude'] && $partner['longitude']) ? 'true' : 'false'; ?>) {
            marker = new google.maps.Marker({
                position: { lat: defaultLat, lng: defaultLng },
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                title: 'ลากเพื่อย้ายตำแหน่ง'
            });
            
            // Update coordinates when marker is dragged
            marker.addListener('dragend', function() {
                updateCoordinates(marker.getPosition());
            });
        }
        
        // Click on map to add/move marker
        map.addListener('click', function(event) {
            placeMarker(event.latLng);
        });
        
        // Zoom changed event
        map.addListener('zoom_changed', function() {
            document.getElementById('map_zoom_level').value = map.getZoom();
        });
        
        // Search box
        const input = document.getElementById('searchInput');
        
        // Check if Places library is loaded
        if (typeof google.maps.places !== 'undefined') {
            searchBox = new google.maps.places.SearchBox(input);
            
            // Bias search results to current map bounds
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });
            
            // Listen for search selection
            searchBox.addListener('places_changed', function() {
                const places = searchBox.getPlaces();
                
                if (places.length === 0) {
                    console.log('ไม่พบสถานที่ที่ค้นหา');
                    return;
                }
                
                const place = places[0];
                
                if (!place.geometry || !place.geometry.location) {
                    console.log('ไม่พบตำแหน่งของสถานที่');
                    return;
                }
                
                // Move map to selected place
                map.setCenter(place.geometry.location);
                map.setZoom(17);
                
                // Place marker
                placeMarker(place.geometry.location);
                
                // Update address
                if (place.formatted_address) {
                    document.getElementById('address').value = place.formatted_address;
                }
            });
            
            // Handle Enter key in search box
            input.addEventListener('keydown', function(e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            });
        } else {
            console.error('Google Places library not loaded. Please check API Key permissions.');
            input.placeholder = 'การค้นหาไม่พร้อมใช้งาน (ตรวจสอบ API Key)';
            input.disabled = true;
        }
    }
    
    function placeMarker(location) {
        // Remove existing marker
        if (marker) {
            marker.setMap(null);
        }
        
        // Create new marker
        marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP
        });
        
        // Update coordinates
        updateCoordinates(location);
        
        // Update coordinates when marker is dragged
        marker.addListener('dragend', function() {
            updateCoordinates(marker.getPosition());
        });
    }
    
    function updateCoordinates(location) {
        document.getElementById('latitude').value = location.lat().toFixed(8);
        document.getElementById('longitude').value = location.lng().toFixed(8);
    }
    
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                map.setCenter(pos);
                map.setZoom(17);
                placeMarker(new google.maps.LatLng(pos.lat, pos.lng));
                
                // Try to get address using reverse geocoding
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ location: pos }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        document.getElementById('address').value = results[0].formatted_address;
                    }
                });
            }, function() {
                alert('ไม่สามารถดึงตำแหน่งปัจจุบันได้');
            });
        } else {
            alert('Browser ของคุณไม่รองรับ Geolocation');
        }
    }
    
    function clearLocation() {
        if (marker) {
            marker.setMap(null);
            marker = null;
        }
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        document.getElementById('address').value = '';
    }
    </script>
    
    <!-- Load Google Maps API from Database -->
    <?php
    // ดึง Google Maps API Key จากฐานข้อมูล (ใช้ที่ดึงจากด้านบนแล้ว)
    // $google_maps_api_key ได้ถูกกำหนดค่าแล้วในบรรทัด 31-38
    ?>
    
    <?php if ($google_maps_api_key): ?>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key; ?>&callback=initMap&libraries=places&language=th">
    </script>
    <?php else: ?>
    <script>
        alert('ไม่พบ Google Maps API Key! กรุณาเพิ่ม API Key ในระบบจัดการ API Keys');
        window.location.href = '../api_keys/index.php';
    </script>
    <?php endif; ?>
</body>
</html>
