<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Lokasi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .main-container {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .attendance-container {
            max-width: 90%;
            width: 100%;
            margin: 0 auto;
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .attendance-card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 20px;
            min-height: 80vh;
            text-align: center;
            background: white;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header-text {
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.1rem;
        }

        .time-display {
            font-size: 3.2rem;
            font-weight: bold;
            margin: 8px 0;
            color: #333;
        }

        .date-display {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .btn-attendance {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            margin: 8px 0;
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-history {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        #map {
            height: 300px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            flex-shrink: 0;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Camera Modal Styles */
        .camera-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 100%;
            overflow: hidden;
            border-radius: 8px;
            background: #000;
            margin-bottom: 15px;
        }

        .camera-view {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .camera-actions {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .camera-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-capture {
            background: #fff;
            color: #333;
        }

        .btn-retake {
            background: #dc3545;
            color: white;
        }

        .btn-confirm {
            background: #28a745;
            color: white;
        }

        .confirmation-photo {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
            border: 1px solid #e0e0e0;
        }

        .location-info {
            text-align: left;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .location-info p {
            margin: 5px 0;
            font-size: 0.9rem;
        }

        .location-info label {
            font-weight: bold;
            color: #333;
        }

        .logout-container {
            width: 100%;
            display: flex;
            justify-content: end;
        }


        @media (max-width: 576px) {
            .attendance-container {
                padding: 10px;
                max-width: 100%;
            }

            .attendance-card {
                padding: 15px;
                min-height: 90vh;
            }

            .time-display {
                font-size: 3rem;
            }

            .btn-attendance {
                padding: 10px;
                font-size: 0.9rem;
            }

            #map {
                height: 250px;
            }
        }

        @media (max-height: 600px) {
            .time-display {
                font-size: 2.5rem;
            }

            #map {
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="container attendance-container">
            <div class="attendance-card">
                <div class="logout-container">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger" type="submit">
                            <i class="fas fa-sign-out-alt fa-sm me-2 "></i> Logout
                        </button>
                    </form>
                </div>

                <div class="header-text">ABSEN</div>

                <!-- Map -->
                <div id="map"></div>

                <!-- Location Info -->
                <div class="location-info">
                    <p><label>Status Lokasi:</label> <span id="status">Mendeteksi lokasi...</span></p>
                    <p><label>Nama Lokasi:</label> <span id="nama_lokasi">-</span></p>
                    <div class="row">
                        <div class="col-6">
                            <p><label>Latitude:</label> <span id="latitude">-</span></p>
                        </div>
                        <div class="col-6">
                            <p><label>Longitude:</label> <span id="longitude">-</span></p>
                        </div>
                    </div>
                </div>

                <div class="content-wrapper">
                    <div class="time-display" id="currentTime">07:21:34</div>
                    <div class="date-display" id="currentDate">1 Januari 2025</div>

                    <button class="btn btn-success btn-attendance" id="clockInBtn">
                        <i class="fas fa-sign-in-alt me-2"></i>ABSEN MASUK
                    </button>

                    <button class="btn btn-danger btn-attendance" id="clockOutBtn">
                        <i class="fas fa-sign-out-alt me-2"></i>ABSEN PULANG
                    </button>

                    <button class="btn btn-outline-secondary btn-history" id="historyBtn">
                        <i class="fas fa-history me-2"></i>LIHAT RIWAYAT
                    </button>
                </div>
            </div>

            <!-- Camera Modal -->
            <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cameraModalLabel">Ambil Foto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="camera-container">
                                <video id="video" class="camera-view" autoplay playsinline></video>
                                <canvas id="canvas" style="display:none;"></canvas>
                                <img id="photoPreview" class="camera-preview">
                            </div>
                            <div class="camera-actions">
                                <button type="button" class="btn btn-capture camera-btn" id="captureBtn">
                                    <i class="fas fa-camera"></i>
                                </button>
                                <button type="button" class="btn btn-retake camera-btn" id="retakeBtn"
                                    style="display: none;">
                                    <i class="fas fa-redo"></i>
                                </button>
                                <button type="button" class="btn btn-confirm camera-btn" id="usePhotoBtn"
                                    style="display: none;">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Absen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin melakukan absen <span id="modalStatusAbsen"></span>?</p>
                            <img id="confirmPhotoPreview" class="confirmation-photo">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="confirmSubmit">Ya, Absen</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Variables
            let stream = null;
            let capturedPhoto = null;
            let currentAttendanceType = '';
            const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

            // Update time
            function updateTime() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');

                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ];

                $('#currentTime').text(`${hours}:${minutes}:${seconds}`);
                $('#currentDate').text(
                    `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`);
            }

            // Update time every second
            updateTime();
            setInterval(updateTime, 1000);

            // Initialize Map
            let map = L.map('map').setView([-6.200000, 106.816666], 13);
            let marker;
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://carto.com/">CARTO</a>'
            }).addTo(map);

            // Get current location
            function updateLocation(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                $('#latitude').text(lat);
                $('#longitude').text(lon);
                $('#status').text('Lokasi Ditemukan');

                if (marker) {
                    marker.setLatLng([lat, lon]);
                } else {
                    marker = L.marker([lat, lon]).addTo(map)
                        .bindPopup("Lokasi Anda")
                        .openPopup();
                }

                map.setView([lat, lon], 15);

                // Get location name from OpenStreetMap
                $.getJSON(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`, function(
                    data) {
                    $('#nama_lokasi').text(data.display_name || 'Tidak Diketahui');
                }).fail(function() {
                    $('#nama_lokasi').text('Gagal mendapatkan nama lokasi');
                });
            }

            function errorHandler(error) {
                $('#status').text('Gagal mendapatkan lokasi: ' + error.message);
            }

            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(
                    updateLocation,
                    errorHandler, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                $('#status').text('Geolocation tidak didukung di browser ini');
            }

            // Handle clock in/out buttons
            $('#clockInBtn').click(function() {
                currentAttendanceType = 'masuk';
                $('#modalStatusAbsen').text(currentAttendanceType);
                cameraModal.show();
                startCamera();
            });

            $('#clockOutBtn').click(function() {
                currentAttendanceType = 'pulang';
                $('#modalStatusAbsen').text(currentAttendanceType);
                cameraModal.show();
                startCamera();
            });

            // Handle history button
            $('#historyBtn').click(function() {
                window.location.href = '/history';
            });


            // Camera functions
            function startCamera() {
                navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "environment",
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 720
                            }
                        },
                        audio: false
                    })
                    .then(function(cameraStream) {
                        stream = cameraStream;
                        const video = document.getElementById('video');
                        video.srcObject = stream;
                    })
                    .catch(function(error) {
                        console.error("Camera error:", error);
                        alert("Tidak dapat mengakses kamera: " + error.message);
                    });
            }

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            }

            // Capture photo
            $('#captureBtn').click(function() {
                const video = document.getElementById('video');
                const canvas = document.getElementById('canvas');
                const context = canvas.getContext('2d');
                const preview = document.getElementById('photoPreview');

                // Set canvas size to match video
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Draw video frame to canvas
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Get image data and show preview
                capturedPhoto = canvas.toDataURL('image/jpeg');
                preview.src = capturedPhoto;

                // Switch UI elements
                $('#video').hide();
                $('#photoPreview').show();
                $('#captureBtn').hide();
                $('#retakeBtn').show();
                $('#usePhotoBtn').show();

                stopCamera();
            });

            // Retake photo
            $('#retakeBtn').click(function() {
                $('#photoPreview').hide();
                $('#video').show();
                $('#captureBtn').show();
                $('#retakeBtn').hide();
                $('#usePhotoBtn').hide();
                startCamera();
            });

            // Use photo
            $('#usePhotoBtn').click(function() {
                cameraModal.hide();
                $('#confirmPhotoPreview').attr('src', capturedPhoto).show();
                confirmationModal.show();
            });

            // When camera modal is hidden
            $('#cameraModal').on('hidden.bs.modal', function() {
                stopCamera();
                $('#video').show();
                $('#photoPreview').hide();
                $('#captureBtn').show();
                $('#retakeBtn').hide();
                $('#usePhotoBtn').hide();
            });

            // Confirm submission
            $('#confirmSubmit').click(function() {
                // Prepare data to send
                const formData = {
                    status_absen: currentAttendanceType,
                    photo: capturedPhoto,
                    nama_lokasi: $('#nama_lokasi').text(),
                    latitude: $('#latitude').text(),
                    longitude: $('#longitude').text(),
                    waktu: $('#currentTime').text() + ' ' + $('#currentDate').text()
                };

                // Send data to server
                $.ajax({
                    url: "{{ route('absensi.store') }}", // Ganti dengan endpoint Anda
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
                    },
                    data: JSON.stringify(formData),
                    contentType: "application/json",
                    success: function(response) {
                        alert('Absen ' + currentAttendanceType + ' berhasil dicatat!');
                        console.log(response);
                    },
                    error: function(error) {
                        console.error("Error:", error);
                        alert('Gagal mengirim absen!');
                    }
                });

                confirmationModal.hide();
            });
        });
    </script>
</body>

</html>
