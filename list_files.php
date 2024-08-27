<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar File</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: calc(100% - 110px);
            box-sizing: border-box;
            margin-right: 10px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            border: 1px solid #007bff;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
            border-color: #004494;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            color: #007bff;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }

        .button:hover {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .button.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
            font-weight: bold;
        }

        .button.disabled {
            color: #ddd;
            pointer-events: none;
            cursor: default;
            border-color: #ddd;
            background-color: #f9f9f9;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
        }

        /* CSS Transitions */
        tr {
            transition: all 0.3s ease;
        }

        tr.hidden {
            display: none;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            input[type="text"] {
                width: calc(100% - 110px);
                margin-bottom: 10px;
            }

            input[type="submit"] {
                width: 100%;
                margin: 0;
            }

            table {
                font-size: 14px;
                border: 0;
            }

            table thead {
                display: none;
            }

            table, tbody, tr, td {
                display: block;
                width: 100%;
                border-bottom: 1px solid #ddd;
            }

            td {
                position: relative;
                padding-left: 50%;
                text-align: right;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 10px;
                font-weight: bold;
                white-space: nowrap;
                background: #f8f9fa;
            }

            .pagination {
                display: flex;
                flex-direction: column;
            }

            .pagination a, .pagination span {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar File yang Telah Diunggah</h2>

        <!-- Formulir pencarian file -->
        <form id="searchForm" action="list_files.php" method="get">
            <input type="text" name="searchQuery" id="searchQuery" placeholder="Cari file..." value="<?php echo isset($_GET['searchQuery']) ? htmlspecialchars($_GET['searchQuery']) : ''; ?>">
            <input type="submit" value="Cari">
        </form>

        <!-- Tabel Daftar File -->
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama File</th>
                    <th>Link</th>
                    <th>Tanggal Unggah</th>
                    <th>Unduh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="fileTableBody">
                <?php
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

                // Ambil query pencarian jika ada
                $searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';
                $limit = 10; // Jumlah item per halaman
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $limit;

                // Buat query SQL dengan pencarian dan paginasi, urutkan berdasarkan nama file
                $sql = "SELECT * FROM files WHERE file_name LIKE ? ORDER BY file_name ASC LIMIT ? OFFSET ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sii", $searchTerm, $limit, $offset);
                $searchTerm = "%{$searchQuery}%";
                $stmt->execute();
                $result = $stmt->get_result();

                // Query untuk menghitung total jumlah file
                $count_sql = "SELECT COUNT(*) AS total FROM files WHERE file_name LIKE ?";
                $count_stmt = $conn->prepare($count_sql);
                $count_stmt->bind_param("s", $searchTerm);
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $total_files = $count_result->fetch_assoc()['total'];
                $total_pages = ceil($total_files / $limit);

                // Close the statements
                $count_stmt->close();
                ?>

                <?php
                if ($result->num_rows > 0) {
                    $count = $offset + 1; // Nomor urut
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $count . "</td>";
                        echo "<td data-label='Nama File'>" . htmlspecialchars($row["file_name"]) . "</td>";
                        echo "<td data-label='Link'><a href='" . htmlspecialchars($row["file_path"]) . "' target='_blank'>Lihat File</a></td>";
                        echo "<td data-label='Tanggal Unggah'>" . htmlspecialchars($row["uploaded_at"]) . "</td>";
                        echo "<td data-label='Unduh'><a href='" . htmlspecialchars($row["file_path"]) . "' download class='button'>Unduh</a></td>";
                        echo "<td data-label='Aksi'><a href='delete.php?id=" . htmlspecialchars($row["id"]) . "' class='button'>Hapus</a></td>";
                        echo "</tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada file yang ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Navigasi Paginasi -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="list_files.php?page=<?php echo $page - 1; ?>&searchQuery=<?php echo urlencode($searchQuery); ?>" class="button">Sebelumnya</a>
            <?php else: ?>
                <span class="button disabled">Sebelumnya</span>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="list_files.php?page=<?php echo $i; ?>&searchQuery=<?php echo urlencode($searchQuery); ?>" class="<?php echo ($i == $page) ? 'button active' : 'button'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="list_files.php?page=<?php echo $page + 1; ?>&searchQuery=<?php echo urlencode($searchQuery); ?>" class="button">Selanjutnya</a>
            <?php else: ?>
                <span class="button disabled">Selanjutnya</span>
            <?php endif; ?>
        </div>

        <?php
        $stmt->close();
        $conn->close();
        ?>

        <a href="index.html" class="button">Kembali ke Beranda</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('searchForm');
            const searchQueryInput = document.getElementById('searchQuery');
            const fileTableBody = document.getElementById('fileTableBody');
            const pagination = document.querySelector('.pagination');
            let currentPage = <?php echo $page; ?>;

            function fetchFiles(query, page) {
                fetch(`list_files.php?searchQuery=${encodeURIComponent(query)}&page=${page}`)
                    .then(response => response.text())
                    .then(data => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data, 'text/html');
                        fileTableBody.innerHTML = doc.querySelector('#fileTableBody').innerHTML;
                        pagination.innerHTML = doc.querySelector('.pagination').innerHTML;
                    })
                    .catch(error => console.error('Error:', error));
            }

            searchQueryInput.addEventListener('input', function() {
                const searchQuery = searchQueryInput.value;
                fetchFiles(searchQuery, currentPage);
            });

            // Handle pagination links
            document.addEventListener('click', function(event) {
                if (event.target.matches('.pagination a')) {
                    event.preventDefault();
                    currentPage = new URL(event.target.href).searchParams.get('page');
                    const searchQuery = searchQueryInput.value;
                    fetchFiles(searchQuery, currentPage);
                }
            });
        });
    </script>
</body>
</html>
