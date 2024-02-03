<?php

// Koneksi ke database
$conn = new mysqli("localhost", "#", "#", "#");

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ambil input dari pengguna dan konversi menjadi huruf kecil
    $senyubukText = strtolower($_POST["senyubukText"]);
    $isTranslationToIndo = $_POST["isTranslationToIndo"];

    // Fungsi untuk membersihkan kata dari tanda baca
    function cleanWord($word) {
        // Menghapus semua karakter tanda baca kecuali tanda "-"
        $word = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $word);
        return $word;
    }

    // Fungsi untuk memuat kamus dari database MySQL
    function loadDictionary($conn, $isTranslationToIndo) {
        $dictionary = array();
        $query = "SELECT ";
        if ($isTranslationToIndo) {
            $query .= "LOWER(kata_belitung) AS kata_belitung, LOWER(kata_indo) AS kata_indo";
        } else {
            $query .= "LOWER(kata_indo) AS kata_belitung, LOWER(kata_belitung) AS kata_indo";
        }
        $query .= " FROM kata_belitung";

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $dictionary[$row['kata_belitung']] = $row['kata_indo'];
            }
        }

        return $dictionary;
    }

    // Fungsi untuk menerjemahkan kata dengan mengabaikan tanda baca
    function translateWord($word, $dictionary) {
        $cleanedWord = cleanWord($word);
        return $dictionary[$cleanedWord] ?? $word; // Menggunakan kata asli jika tidak ditemukan di kamus
    }

    // Memuat kamus dari database sesuai arah terjemahan
    $dictionary = loadDictionary($conn, $isTranslationToIndo);

    // Memisahkan kata-kata
    $words = preg_split('/\s+/', $senyubukText);
    $translatedWords = array_map(function($word) use ($dictionary) {
        return translateWord($word, $dictionary);
    }, $words);

    // Menggabungkan kata-kata terjemahan
    $translatedText = implode(' ', $translatedWords);

    // Mengembalikan hasil terjemahan dalam huruf kecil
    echo json_encode(["indonesiaText" => strtolower($translatedText)]);
}
?>
