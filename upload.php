<!DOCTYPE html>
<html>
<head>
    <title>Hasil Unggah File</title>
    <link rel="stylesheet" type="text/css" href="upload.css">
</head>
<body>
    <div class="container">
        <?php
        $target_dir = "uploads/";
        
        // Database connection
        $servername = "localhost";
        $username = "root"; // Ganti dengan username database Anda
        $password = ""; // Ganti dengan password database Anda
        $dbname = "file_upload_db";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $uploadOk = 1;
        $uploaded_files = [];

        // Loop melalui setiap file yang diunggah
        foreach ($_FILES['filesToUpload']['name'] as $key => $filename) {
            $target_file = $target_dir . basename($filename);
            $file_tmp = $_FILES['filesToUpload']['tmp_name'][$key];
            $file_name = basename($filename);
            
            // Check if file already exists
            if (file_exists($target_file)) {
                echo "File " . htmlspecialchars($file_name) . " sudah ada.<br>";
                $uploadOk = 0;
                continue; // Skip this file
            }

            // Check file size
            if ($_FILES['filesToUpload']['size'][$key] > 5000000000) { // 500KB limit
                echo "File " . htmlspecialchars($file_name) . " terlalu besar.<br>";
                $uploadOk = 0;
                continue; // Skip this file
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "File " . htmlspecialchars($file_name) . " tidak diunggah.<br>";
            } else {
                if (move_uploaded_file($file_tmp, $target_file)) {
                    // Prepare SQL statement
                    $stmt = $conn->prepare("INSERT INTO files (file_name, file_path) VALUES (?, ?)");
                    $stmt->bind_param("ss", $file_name, $target_file);

                    if ($stmt->execute()) {
                        $uploaded_files[] = [
                            'name' => $file_name,
                            'path' => $target_file
                        ];
                    } else {
                        echo "Gagal menyimpan informasi file " . htmlspecialchars($file_name) . " ke database.<br>";
                    }
                    $stmt->close();
                } else {
                    echo "Terjadi kesalahan saat mengunggah file " . htmlspecialchars($file_name) . ".<br>";
                }
            }
        }

        $conn->close();

        if (count($uploaded_files) > 0) {
            echo "<h2>File telah berhasil diunggah!</h2>";
            foreach ($uploaded_files as $file) {
                echo "<p>Nama file: " . htmlspecialchars($file['name']) . "</p>";
                echo "<p>File telah diunggah ke: <a href='" . htmlspecialchars($file['path']) . "' target='_blank'>" . htmlspecialchars($file['path']) . "</a></p>";

                // Tampilkan pratinjau gambar jika file adalah gambar
                $fileType = mime_content_type($file['path']);
                if (strpos($fileType, 'image') !== false) {
                    echo "<p>Pratinjau Gambar:</p>";
                    echo "<img src='" . htmlspecialchars($file['path']) . "' alt='Pratinjau Gambar' style='max-width: 100%; height: auto;'>";
                }
            }
        } else {
            echo "<h2>Maaf, tidak ada file yang berhasil diunggah.</h2>";
        }
        ?>

        <!-- Tombol untuk kembali ke beranda -->
        <a href="index.html" class="button">Kembali ke Beranda</a>
    </div>
</body>
</html>
