<?php
include 'koneksi.php';
session_start();

$pesan_error = "";
$pesan_sukses = "";

// --- LOGIKA DAFTAR AKUN BARU ---
if (isset($_POST['daftar'])) {
    $username_baru = mysqli_real_escape_string($conn, $_POST['username_baru']);
    $password_baru = mysqli_real_escape_string($conn, $_POST['password_baru']);

    // Cek apakah username sudah ada di database
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username='$username_baru'");
    if (mysqli_num_rows($cek_user) > 0) {
        $pesan_error = "Username sudah terdaftar! Silakan gunakan username lain.";
    } else {
        // Simpan user baru ke tabel 'users'
        $query_daftar = "INSERT INTO users (username, password) VALUES ('$username_baru', '$password_baru')";
        if (mysqli_query($conn, $query_daftar)) {
            $pesan_sukses = "Pendaftaran berhasil! Silakan masuk.";
        } else {
            $pesan_error = "Gagal mendaftar: " . mysqli_error($conn);
        }
    }
}

// --- LOGIKA LOGIN AKUN ---
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cocokkan data dengan database
    $query_login = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($query_login) === 1) {
        $_SESSION['user'] = $username;
        header("Location: thriftify.php"); // Refresh halaman setelah login sukses
        exit;
    } else {
        $pesan_error = "Username atau Password salah!";
    }
}

// --- LOGIKA KELUAR (LOGOUT) ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: thriftify.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thriftify - Digital Thrift Experience</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <!-- NOTIFIKASI ERROR / SUKSES PHP -->
    <?php if(!empty($pesan_error)): ?>
        <script>alert('❌ <?php echo $pesan_error; ?>');</script>
    <?php endif; ?>
    <?php if(!empty($pesan_sukses)): ?>
        <script>alert('✅ <?php echo $pesan_sukses; ?>');</script>
    <?php endif; ?>

    <nav>
        <div class="nav-left">
            <div class="logo" onclick="bukaHalaman('utama')">Thriftify.</div>
        </div>

        <div class="menu-nav" id="menu">
            <a onclick="bukaHalaman('utama')">Beranda</a>
            <a onclick="bukaKoleksi()">Koleksi</a>
        </div>

        <div class="nav-right">
            <div class="keranjang" onclick="bukaHalaman('keranjang')">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="badge-keranjang" id="jumlah-keranjang">0</span>
            </div>

            <!-- Jika SUDAH Login -->
            <?php if (isset($_SESSION['user'])): ?>
                <div class="akun-user" onclick="alert('Halo, <?php echo $_SESSION['user']; ?>!')" style="cursor: pointer; font-weight: 600; color: var(--primer);">
                    <i class="fa-solid fa-user"></i> <?php echo $_SESSION['user']; ?>
                </div>
                <a href="thriftify.php?logout=1" style="text-decoration: none; color: #ef4444; font-weight: 600; font-size: 14px; margin-left: 10px;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
            
            <!-- Jika BELUM Login -->
            <?php else: ?>
                <div class="tombol-login-nav" onclick="bukaHalaman('login')" style="cursor: pointer; font-weight: 600; color: var(--primer);">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk
                </div>
            <?php endif; ?>

            <div class="hamburger" id="hamburger">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
    </nav>

    <div id="halaman-utama">
        <section class="hero">
            <div class="hero-konten">
                <h1>Gaya Keren, Harga Pantas, Bumi Bernapas.</h1>
                <p>Thriftify adalah destinasi utama untuk pakaian bekas terkurasi. Temukan koleksi fashion vintage langka dengan kualitas terbaik. Hemat uangmu, selamatkan bumi dari limbah tekstil!</p>
                <a onclick="bukaKoleksi()" class="btn-utama">Mulai Belanja</a>
            </div>
            <div class="hero-gambar">
                <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Pakaian Bekas Berkualitas">
            </div>
        </section>

        <section class="manfaat">
            <h2>Kenapa Memilih Thriftify?</h2>
            <div class="grid-manfaat">
                <div class="kartu-manfaat">
                    <div class="ikon-manfaat">✨</div>
                    <h3>Kualitas Terkurasi</h3>
                    <p>Setiap barang preloved telah melalui proses sortir, cuci, dan sterilisasi. Kami pastikan barang dalam kondisi mulus seperti baru.</p>
                </div>
                <div class="kartu-manfaat">
                    <div class="ikon-manfaat">🌍</div>
                    <h3>Ramah Lingkungan</h3>
                    <p>Dengan membeli pakaian bekas, Anda berkontribusi langsung dalam mengurangi limbah industri tekstil dan jejak karbon.</p>
                </div>
                <div class="kartu-manfaat">
                    <div class="ikon-manfaat">🏷️</div>
                    <h3>Harga Terjangkau</h3>
                    <p>Dapatkan pakaian dari merek-merek ternama dunia dengan harga miring, jauh di bawah harga retail pasar.</p>
                </div>
            </div>
        </section>

        <section id="produk" class="produk">
            <h2>Koleksi Terbaru Kami</h2>
            
            <div class="kategori-filter">
                <button class="btn-kategori aktif" onclick="filterProduk('semua', this)">Semua</button>
                <button class="btn-kategori" onclick="filterProduk('baju', this)">Baju</button>
                <button class="btn-kategori" onclick="filterProduk('celana', this)">Celana</button>
                <button class="btn-kategori" onclick="filterProduk('sepatu', this)">Sepatu</button>
                <button class="btn-kategori" onclick="filterProduk('aksesoris', this)">Aksesoris</button>
            </div>
            <div class="grid-produk" id="wadah-produk"></div>
        </section>
    </div>

    <!-- HALAMAN KERANJANG -->
    <div id="halaman-keranjang" class="halaman-dinamis" style="display: none;">
        <h2>Keranjang Belanja Anda</h2>
        <div id="daftar-item-keranjang" class="daftar-keranjang"></div>
        <div class="ringkasan">
            <p style="color: var(--teks-sekunder); margin-bottom: 5px;">Total Pembayaran:</p>
            <h3 id="total-belanja">Rp 0</h3>
            <button class="btn-utama" id="btn-lanjut-checkout" onclick="bukaHalaman('checkout')" style="width: 100%;">Lanjut ke Pembayaran</button>
            <button class="btn-beli" onclick="bukaHalaman('utama')" style="width: 100%; margin-top: 10px; background-color: var(--latar); color: var(--teks-utama); border: 1px solid var(--border);">Kembali Belanja</button>
        </div>
    </div>

    <!-- HALAMAN CHECKOUT -->
    <div id="halaman-checkout" class="halaman-dinamis" style="display: none;">
        <h2>Formulir Pengiriman & Pembayaran</h2>
        <form class="form-checkout" onsubmit="prosesPesanan(event)">
            <h3 style="margin-bottom: 15px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Informasi Pengiriman</h3>
            <div class="form-grup">
                <label>Nama Lengkap</label>
                <input type="text" id="nama-pembeli" required placeholder="Masukkan nama lengkap Anda">
            </div>
            <div class="form-grup">
                <label>Nomor Telepon (WhatsApp)</label>
                <input type="tel" required placeholder="Contoh: 081234567890">
            </div>
            <div class="form-grup">
                <label>Alamat Lengkap</label>
                <textarea rows="4" required placeholder="Sertakan nama jalan, RT/RW, kelurahan, dan kecamatan"></textarea>
            </div>
            <h3 style="margin: 30px 0 15px 0; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Metode Pembayaran</h3>
            <div class="form-grup">
                <label>Pilih Metode Pembayaran</label>
                <select id="metode-pembayaran" required>
                    <option value="">-- Silakan Pilih --</option>
                    <option value="Transfer Bank BCA">Transfer Bank (BCA)</option>
                    <option value="Transfer Bank Mandiri">Transfer Bank (Mandiri)</option>
                    <option value="GoPay / OVO / DANA">E-Wallet (GoPay / OVO / DANA)</option>
                    <option value="Bayar di Tempat (COD)">Bayar di Tempat (COD)</option>
                </select>
            </div>
            <div style="background-color: var(--latar); padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <p>Total yang harus dibayar:</p>
                <h3 id="tagihan-akhir" style="color: var(--primer); font-size: 24px;">Rp 0</h3>
            </div>
            <button type="submit" class="btn-utama" style="width: 100%; font-size: 18px;">Konfirmasi Pesanan</button>
            <button type="button" class="btn-beli" onclick="bukaHalaman('keranjang')" style="width: 100%; margin-top: 10px; background-color: transparent; color: var(--teks-sekunder);">Batal & Kembali ke Keranjang</button>
        </form>
    </div>

    <!-- HALAMAN LOGIN & DAFTAR -->
    <div id="halaman-login" class="halaman-dinamis" style="display: none; max-width: 400px; margin: 40px auto; padding: 20px; border: 1px solid var(--border); border-radius: 8px; background: #fff;">
        
        <!-- Form MASUK -->
        <div id="form-masuk-wrapper">
            <h2 style="text-align: center; margin-bottom: 20px;">Masuk ke Thriftify</h2>
            <form class="form-checkout" action="thriftify.php" method="POST">
                <div class="form-grup">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Masukkan username">
                </div>
                <div class="form-grup" style="margin-top: 15px;">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" required placeholder="Masukkan kata sandi">
                </div>
                <button type="submit" name="login" class="btn-utama" style="width: 100%; font-size: 16px; margin-top: 25px;">Masuk</button>
                <p style="text-align: center; margin-top: 15px; font-size: 14px; color: var(--teks-sekunder);">
                    Belum punya akun? <a href="#" onclick="tukarForm('daftar')" style="color: var(--primer); text-decoration: none; font-weight: bold;">Daftar Sekarang</a>
                </p>
                <button type="button" class="btn-beli" onclick="bukaHalaman('utama')" style="width: 100%; margin-top: 10px; background-color: transparent; color: var(--teks-sekunder); border: none;">Kembali ke Beranda</button>
            </form>
        </div>

        <!-- Form DAFTAR (Sembunyi secara default) -->
        <div id="form-daftar-wrapper" style="display: none;">
            <h2 style="text-align: center; margin-bottom: 20px;">Daftar Akun Baru</h2>
            <form class="form-checkout" action="thriftify.php" method="POST">
                <div class="form-grup">
                    <label>Buat Username</label>
                    <input type="text" name="username_baru" required placeholder="Masukkan username baru">
                </div>
                <div class="form-grup" style="margin-top: 15px;">
                    <label>Buat Kata Sandi</label>
                    <input type="password" name="password_baru" required placeholder="Masukkan kata sandi baru">
                </div>
                <button type="submit" name="daftar" class="btn-utama" style="width: 100%; font-size: 16px; margin-top: 25px; background-color: #22c55e;">Daftar Akun</button>
                <p style="text-align: center; margin-top: 15px; font-size: 14px; color: var(--teks-sekunder);">
                    Sudah punya akun? <a href="#" onclick="tukarForm('masuk')" style="color: var(--primer); text-decoration: none; font-weight: bold;">Masuk di sini</a>
                </p>
                <button type="button" class="btn-beli" onclick="bukaHalaman('utama')" style="width: 100%; margin-top: 10px; background-color: transparent; color: var(--teks-sekunder); border: none;">Kembali ke Beranda</button>
            </form>
        </div>

    </div>

    <footer>
        <h2>Thriftify.</h2>
        <p style="margin-top: 10px; color: #94a3b8;">© 2026 Thriftify. Gaya berkelanjutan untuk semua.</p>
        <p style="margin-top: 10px; color: #ffffff;">Kelompok 6</p>
        <div class="anggota-grid">
            <div class="anggota-item"><div class="nama">Muhammad Zanuar Abidin</div><div class="nim">13182420133</div></div>
            <div class="anggota-item"><div class="nama">Aswangga Oda</div><div class="nim">13182420179</div></div>
            <div class="anggota-item"><div class="nama">Windy Wulan Shyerina</div><div class="nim">13182420133</div></div>
            <div class="anggota-item"><div class="nama">Fatimatun Azzahra</div><div class="nim">13182420164</div></div>
        </div>
    </footer>

    <div id="toast" class="toast-notifikasi">Item berhasil ditambahkan!</div>

    <script>
        // Fungsi tukar form login dan daftar
        function tukarForm(target) {
            const formMasuk = document.getElementById('form-masuk-wrapper');
            const formDaftar = document.getElementById('form-daftar-wrapper');
            if(target === 'daftar') {
                formMasuk.style.display = 'none';
                formDaftar.style.display = 'block';
            } else {
                formMasuk.style.display = 'block';
                formDaftar.style.display = 'none';
            }
        }
    </script>
    <script src="script.js"></script>
</body>
</html>