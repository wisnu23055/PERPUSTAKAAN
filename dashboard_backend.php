<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$database = "library_system";

// Membuat koneksi
$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tangani permintaan berdasarkan parameter 'action'
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Ambil semua data untuk dashboard
    if ($action === 'getData') {
        $response = [];

        // Ambil data buku
        $booksQuery = "SELECT * FROM books";
        $booksResult = $conn->query($booksQuery);
        $response['books'] = $booksResult ? $booksResult->fetch_all(MYSQLI_ASSOC) : [];

        // Ambil data pustakawan
        $usersQuery = "SELECT user_id, name, email, phone FROM users WHERE role = 'pustakawan'";
        $usersResult = $conn->query($usersQuery);
        $response['pustakawan'] = $usersResult ? $usersResult->fetch_all(MYSQLI_ASSOC) : [];

        // Ambil data buku yang dipinjam
        $loansQuery = "SELECT l.loan_id, u.name AS peminjam, b.name AS book_name, 
                              b.number AS book_number, b.category
                       FROM loans l
                       JOIN users u ON l.user_id = u.user_id
                       JOIN books b ON l.book_id = b.book_id
                       WHERE l.status = 'borrowed'";
        $loansResult = $conn->query($loansQuery);
        $response['borrowedBooks'] = $loansResult ? $loansResult->fetch_all(MYSQLI_ASSOC) : [];

        // Kirim response dalam format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Tambah buku baru
    if ($action === 'addBook' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari form
        $name = $conn->real_escape_string($_POST['name']);
        $number = $conn->real_escape_string($_POST['number']);
        $category = $conn->real_escape_string($_POST['category']);

        // Query untuk menambah buku
        $query = "INSERT INTO books (name, number, category) VALUES ('$name', '$number', '$category')";
        
        // Eksekusi query
        if ($conn->query($query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false, 
                'error' => $conn->error
            ]);
        }
        exit;
    }

    // Hapus buku
    if ($action === 'deleteBook' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari body request
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $conn->real_escape_string($data['id']);

        // Query untuk menghapus buku
        $query = "DELETE FROM books WHERE book_id = '$id'";
        
        // Eksekusi query
        if ($conn->query($query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false, 
                'error' => $conn->error
            ]);
        }
        exit;
    }

    // Hapus pustakawan
    if ($action === 'deleteUser' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari body request
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $conn->real_escape_string($data['id']);

        // Query untuk menghapus pustakawan
        $query = "DELETE FROM users WHERE user_id = '$id'";
        
        // Eksekusi query
        if ($conn->query($query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false, 
                'error' => $conn->error
            ]);
        }
        exit;
    }
}

// Tutup koneksi database
$conn->close();
?>