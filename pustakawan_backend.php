<?php
header('Content-Type: application/json');  // Pastikan tipe konten adalah JSON

// Koneksi Database
$host = "localhost";
$user = "root";
$password = "";
$database = "library_system";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi untuk mendapatkan daftar buku
if ($_GET['action'] == 'getBooks') {
    $sql = "SELECT * FROM books";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        echo json_encode($books);  // Mengembalikan data buku dalam format JSON
    } else {
        echo json_encode([]);  // Jika tidak ada buku, kirimkan array kosong
    }
    exit;
}

// Fungsi untuk mendapatkan daftar buku yang dipinjam
if ($_GET['action'] == 'getBorrowedBooks') {
    // Ganti dengan user_id yang sesuai, jika Anda menggunakan sistem login
    $user_id = 1;  // Misalnya user_id adalah 1 (admin atau pustakawan)

    $sql = "SELECT * FROM books INNER JOIN loans ON books.book_id = loans.book_id WHERE loans.user_id = $user_id AND loans.status = 'borrowed'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $borrowedBooks = [];
        while ($row = $result->fetch_assoc()) {
            $borrowedBooks[] = $row;
        }
        echo json_encode($borrowedBooks);
    } else {
        echo json_encode([]);  // Tidak ada buku yang dipinjam
    }
    exit;
}

// Fungsi untuk meminjam buku
if ($_GET['action'] == 'borrowBook') {
    $book_id = $_GET['book_id'];

    if (empty($book_id)) {
        echo json_encode(['success' => false, 'message' => 'Book ID is required.']);
        exit;
    }

    // Cek apakah buku sudah dipinjam
    $sql = "SELECT * FROM loans WHERE book_id = $book_id AND status = 'borrowed'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        // Jika buku belum dipinjam, tambahkan ke tabel loans
        $user_id = 1;  // Misalnya user_id adalah 1 (admin atau pustakawan)

        $sql = "INSERT INTO loans (user_id, book_id, status) VALUES ($user_id, $book_id, 'borrowed')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to borrow book.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Book already borrowed.']);
    }
    exit;
}

// Fungsi untuk mengembalikan buku
if ($_GET['action'] == 'returnBook') {
    $book_id = $_GET['book_id'];

    if (empty($book_id)) {
        echo json_encode(['success' => false, 'message' => 'Book ID is required.']);
        exit;
    }

    $sql = "UPDATE loans SET status = 'returned' WHERE book_id = $book_id AND status = 'borrowed'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to return book.']);
    }
    exit;
}

$conn->close();
?>
