<?php
// Koneksi Database
$host = "localhost";
$user = "root";
$password = "";
$database = "library_system";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi Registrasi
if ($_POST['action'] == 'register') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $sql = "INSERT INTO users (name, email, password, phone, role) VALUES ('$name', '$email', '$password', '$phone', '$role')";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Berhasil mendaftar. Silakan login.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mendaftar.']);
    }
}

// Fungsi Login
if ($_POST['action'] == 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            echo json_encode(['success' => true, 'role' => $user['role']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Password salah.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email tidak ditemukan.']);
    }
}

$conn->close();
?>
