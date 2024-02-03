<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Penerjemah - BilikBecakap</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link href="assets/img/logo.svg" rel="icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- ======= Header ======= -->
  
    
    <section id="hero" class="d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 d-lg-flex flex-lg-column align-items-stretch hero-img" data-aos="fade-up">
                    <img src="penerjemah-logo.png" class="img-fluid" alt="" style="width: 450px; height: auto;">
                </div>
                <div class="col-lg-6 d-lg-flex flex-lg-column justify-content-center align-items-stretch" data-aos="fade-up">
                    <!-- Content -->
                    <?php
                      if ($_SERVER["REQUEST_METHOD"] == "POST") {
                          // Koneksi ke database
                        $conn = new mysqli("localhost", "#", "#", "u193767157_kata_belitung");

                          if ($conn->connect_error) {
                              die("Koneksi database gagal: " . $conn->connect_error);
                          }

                          // Ambil input dari pengguna
                          $senyubukText = $_POST["senyubukText"];

                          // Fungsi untuk memuat kamus dari database MySQL
                          function loadDictionary($conn) {
                              $dictionary = array();
                              $query = "SELECT kata_belitung, kata_indo FROM kata_belitung";
                              $result = $conn->query($query);

                              if ($result->num_rows > 0) {
                                  while ($row = $result->fetch_assoc()) {
                                      $dictionary[strtolower($row['kata_belitung'])] = strtolower($row['kata_indo']);
                                  }
                              }

                              return $dictionary;
                          }

                          // Fungsi untuk menerjemahkan kata
                          function translateWord($word, $dictionary) {
                              // Menghapus tanda titik jika ada
                              $word = str_replace(".", "", $word);
                              return $dictionary[strtolower($word)] ?? $word; // Menggunakan kata asli jika tidak ditemukan di kamus
                          }

                          // Memuat kamus dari database
                          $dictionary = loadDictionary($conn);

                          // Memisahkan kata-kata
                          $words = preg_split('/\s+/', $senyubukText);
                          $translatedWords = array_map(function($word) use ($dictionary) {
                              return translateWord($word, $dictionary);
                          }, $words);

                          // Menggabungkan kata-kata terjemahan
                          $indonesiaText = implode(' ', $translatedWords);

                          // Hasil terjemahan selalu dalam huruf kecil
                          $indonesiaText = strtolower($indonesiaText);
                      }
                      ?>

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <img src="penerjemah.png" alt="penerjemah" style="width: 200px; height: auto;">
                    </div>
                    <div class="form-container">
                      <form id="translation-form">
                          <div class="form-group">
                              <label for="senyubukText">Masukkan Teks Melayu Belitung:</label>
                              <textarea class="form-control" id="senyubukText" name="senyubukText" rows="3"><?php echo isset($senyubukText) ? $senyubukText : ""; ?></textarea>
                          </div>
                          <div class="switch-icon">
                              <button id="swap-button" class="btn btn-icon btn-primary">
                                  <i class="bi bi-arrow-left-right"></i>
                              </button>
                          </div>
                          <div class="form-group">
                              <label for="indonesiaText">Hasil Terjemahan Bahasa Indonesia:</label>
                              <textarea class="form-control" id="indonesiaText" name="indonesiaText" rows="3" readonly><?php echo isset($indonesiaText) ? $indonesiaText : ""; ?></textarea>
                          </div>
                          <br>
                          <button id="translate-all-button" type="button" class="btn btn-primary">Terjemahkan</button>
                      </form>
                  </div>
                </div>
            </div>
            <div class="ellipse-1"></div>
        </div>
    </section>


    <!-- Vendor JS Files -->
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
    
     <script>
    // Event listener untuk tombol terjemahkan
    document.getElementById('translate-all-button').addEventListener('click', translateText);
    
    function translateText() {
        const senyubukText = document.getElementById('senyubukText').value;

        // Kirim permintaan AJAX ke server untuk terjemahan
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "translator.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Handle respons dari server
                const response = JSON.parse(xhr.responseText);
                document.getElementById('indonesiaText').value = response.indonesiaText;
            }
        };

        // Mengirim data ke server
        const data = "senyubukText=" + senyubukText;
        xhr.send(data);
    }
  </script>

    <script>
    
    
        // Event listener untuk tombol terjemahkan
        document.getElementById('translate-all-button').addEventListener('click', function() {
            translateText(); // Panggil fungsi translateText
        });

        // Event listener untuk form terjemahan
        document.getElementById('translation-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah pengiriman form standar
            translateText(); // Panggil fungsi translateText
        });

        function translateText() {
            const senyubukText = document.getElementById('senyubukText').value;

            // Kirim permintaan AJAX ke server untuk terjemahan
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "translator.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Handle respons dari server
                    const response = JSON.parse(xhr.responseText);
                    document.getElementById('indonesiaText').value = response.indonesiaText;
                }
            };

            // Mengirim data ke server
            const data = "senyubukText=" + senyubukText;
            xhr.send(data);
        }
    </script>
    
    <script>
        let isTranslationToIndo = true; // Tambahkan variabel untuk melacak arah terjemahan

        document.getElementById('swap-button').addEventListener('click', function() {
            const senyubukText = document.getElementById('senyubukText');
            const indonesiaText = document.getElementById('indonesiaText');
            const senyubukLabel = document.querySelector('label[for="senyubukText"]');
            const indonesiaLabel = document.querySelector('label[for="indonesiaText"]');

            // Ganti teks pada label
            if (isTranslationToIndo) {
                senyubukLabel.textContent = 'Masukkan Teks Melayu Belitung:';
                indonesiaLabel.textContent = 'Hasil Terjemahan Bahasa Indonesia:';
            } else {
                senyubukLabel.textContent = 'Masukkan Teks Bahasa Indonesia:';
                indonesiaLabel.textContent = 'Hasil Terjemahan Melayu Belitung:';
            }

            // Ganti arah terjemahan
            isTranslationToIndo = !isTranslationToIndo;

            // Hapus teks hasil sebelumnya
            senyubukText.value = '';
            indonesiaText.value = '';
        });

        // Event listener untuk tombol terjemahkan
        document.getElementById('translate-all-button').addEventListener('click', function() {
            translateText();
        });

        function translateText() {
            const senyubukText = document.getElementById('senyubukText').value;

            // Kirim permintaan AJAX ke server untuk terjemahan
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "translator.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Handle respons dari server
                    const response = JSON.parse(xhr.responseText);
                    document.getElementById('indonesiaText').value = response.indonesiaText;
                }
            };

            // Mengirim data ke server
            const data = "senyubukText=" + senyubukText + "&isTranslationToIndo=" + isTranslationToIndo;
            xhr.send(data);
        }
     </script>


</body>
</html>
