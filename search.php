<!DOCTYPE html>
<html>
<head>
    <title>Hasil Pencarian</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <?php
        $searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';
        $files = scandir('uploads/');
        $resultFiles = [];

        if ($searchQuery) {
            foreach ($files as $file) {
                if (stripos($file, $searchQuery) !== false) {
                    $resultFiles[] = $file;
                }
            }
        }

        if (!empty($resultFiles)) {
            echo "<h2>Hasil Pencarian:</h2>";
            echo "<ul>";
            foreach ($resultFiles as $file) {
                echo "<li><a href='uploads/$file' target='_blank'>$file</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<h2>Tidak ada file yang ditemukan.</h2>";
        }
        ?>
        <a href='index.html' class='button'>Kembali ke Beranda</a>
    </div>
</body>
</html>
