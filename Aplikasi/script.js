
        let dataKeranjang = [];
        let totalHarga = 0;
        let timerToast;

        const daftarProduk = [
            { nama: 'Jersey Liverpool Vintage 95/96', harga: 250000, kategori: 'baju', gambar: 'https://media.karousell.com/media/photos/products/2023/8/1/liverpool_9596_home_retro_kit_1690896946_91e706cd_progressive' },
            { nama: 'Kemeja Flannel Kotak Original', harga: 120000, kategori: 'baju', gambar: 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' },
            { nama: 'Celana Jeans Levi\'s 501 Bekas', harga: 350000, kategori: 'celana', gambar: 'https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' },
            { nama: 'Celana Geared (Water-Repellent)', harga: 180000, kategori: 'celana', gambar: 'https://image.uniqlo.com/UQ/ST3/AsianCommon/imagesgoods/463981/sub/goods_463981_sub14_3x4.jpg?width=600' },
            { nama: 'Sepatu Boots Dr. Martens 1460', harga: 1200000, kategori: 'sepatu', gambar: 'https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' },
            { nama: 'Sepatu Sneakers Vans Old Skool', harga: 450000, kategori: 'sepatu', gambar: 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' },
            { nama: 'Topi Baseball NY Vintage', harga: 85000, kategori: 'aksesoris', gambar: 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' },
            { nama: 'Kacamata Hitam Retro 80s', harga: 65000, kategori: 'aksesoris', gambar: 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' }
        ];

        function filterProduk(kategoriDipilih, elemenTombol) {
            const wadah = document.getElementById('wadah-produk');
            wadah.innerHTML = ''; 

            // Ubah gaya tombol aktif
            if(elemenTombol) {
                document.querySelectorAll('.btn-kategori').forEach(btn => btn.classList.remove('aktif'));
                elemenTombol.classList.add('aktif');
            }

            // Saring array produk sesuai kategori
            const produkTerfilter = kategoriDipilih === 'semua' 
                ? daftarProduk 
                : daftarProduk.filter(item => item.kategori === kategoriDipilih);

            // Jika kosong (hanya untuk jaga-jaga)
            if(produkTerfilter.length === 0) {
                wadah.innerHTML = '<p style="grid-column: 1 / -1; text-align: center; color: var(--teks-sekunder);">Koleksi belum tersedia.</p>';
                return;
            }

            // Buat HTML produk baru
            produkTerfilter.forEach(item => {
                wadah.innerHTML += `
                    <div class="kartu-produk">
                        <img src="${item.gambar}" alt="${item.nama}">
                        <div class="info-produk">
                            <h3>${item.nama}</h3>
                            <div class="harga-produk">${formatRupiah(item.harga)}</div>
                            <button class="btn-beli" onclick="tambahKeKeranjang('${item.nama}', ${item.harga}, '${item.gambar}')">Beli Sekarang</button>
                        </div>
                    </div>
                `;
            });
        }

        // --- FUNGSI NAVIGASI SCROLL KE KOLEKSI ---
        function bukaKoleksi() {
            bukaHalaman('utama'); // Pastikan berada di halaman beranda
            
            // Beri jeda sangat singkat agar DOM halaman utama tampil sebelum discroll
            setTimeout(() => {
                const bagianProduk = document.getElementById('produk');
                // Menggulir dengan efek halus (smooth)
                bagianProduk.scrollIntoView({ behavior: 'smooth' });
            }, 50);
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        // --- FUNGSI NAVIGASI HALAMAN (SPA SIMULATOR) ---
        function bukaHalaman(namaHalaman) {
            document.getElementById('halaman-utama').style.display = 'none';
            document.getElementById('halaman-keranjang').style.display = 'none';
            document.getElementById('halaman-checkout').style.display = 'none';

            if (namaHalaman === 'utama') {
                document.getElementById('halaman-utama').style.display = 'block';
                window.scrollTo(0, 0);
            } else if (namaHalaman === 'keranjang') {
                renderKeranjang();
                document.getElementById('halaman-keranjang').style.display = 'block';
                window.scrollTo(0, 0);
            } else if (namaHalaman === 'checkout') {
                if (dataKeranjang.length === 0) {
                    alert("Keranjang masih kosong!");
                    return;
                }
                document.getElementById('tagihan-akhir').innerText = formatRupiah(totalHarga);
                document.getElementById('halaman-checkout').style.display = 'block';
                window.scrollTo(0, 0);
            }
        }

        // --- FUNGSI MENAMBAH BARANG KE KERANJANG ---
        function tambahKeKeranjang(nama, harga, gambar) {
            dataKeranjang.push({ nama: nama, harga: harga, gambar: gambar });
            document.getElementById('jumlah-keranjang').innerText = dataKeranjang.length;
            tampilkanNotifikasi("🛒 " + nama + " masuk keranjang!");
        }

        // --- FUNGSI MERENDER (MENAMPILKAN) ISI KERANJANG ---
        function renderKeranjang() {
            const wadahList = document.getElementById('daftar-item-keranjang');
            wadahList.innerHTML = ''; 
            totalHarga = 0; 

            if (dataKeranjang.length === 0) {
                wadahList.innerHTML = '<p style="text-align:center; padding: 30px; color: #64748b;">Keranjang Anda masih kosong. Yuk belanja!</p>';
                document.getElementById('total-belanja').innerText = 'Rp 0';
                document.getElementById('btn-lanjut-checkout').disabled = true;
                document.getElementById('btn-lanjut-checkout').style.backgroundColor = '#cbd5e1';
                return;
            }

            document.getElementById('btn-lanjut-checkout').disabled = false;
            document.getElementById('btn-lanjut-checkout').style.backgroundColor = 'var(--primer)';

            dataKeranjang.forEach(function(item, index) {
                totalHarga += item.harga;
                wadahList.innerHTML += `
                    <div class="item-keranjang">
                        <img src="${item.gambar}" alt="${item.nama}">
                        <div class="info-item">
                            <h4>${item.nama}</h4>
                            <div class="harga-item">${formatRupiah(item.harga)}</div>
                        </div>
                        <button class="btn-hapus" onclick="hapusDariKeranjang(${index})">Hapus</button>
                    </div>
                `;
            });

            document.getElementById('total-belanja').innerText = formatRupiah(totalHarga);
        }

        // --- FUNGSI MENGHAPUS BARANG DARI KERANJANG ---
        function hapusDariKeranjang(index) {
            dataKeranjang.splice(index, 1);
            document.getElementById('jumlah-keranjang').innerText = dataKeranjang.length;
            renderKeranjang();
        }

        // --- FUNGSI PROSES PEMESANAN (CHECKOUT SUBMIT) ---
        function prosesPesanan(event) {
            event.preventDefault(); 
            const nama = document.getElementById('nama-pembeli').value;
            const metode = document.getElementById('metode-pembayaran').value;

            alert(`🎉 Pesanan Berhasil Dibuat!\n\nTerima kasih, ${nama}.\nMetode Pembayaran: ${metode}\nTotal Tagihan: ${formatRupiah(totalHarga)}\n\nKami akan segera memproses pesanan Anda.`);
            
            dataKeranjang = [];
            document.getElementById('jumlah-keranjang').innerText = '0';
            event.target.reset();
            bukaHalaman('utama');
        }

        // --- FUNGSI TOAST NOTIFIKASI ---
        function tampilkanNotifikasi(pesan) {
            const elemenToast = document.getElementById('toast');
            elemenToast.innerText = pesan;
            elemenToast.classList.add('toast-tampil');
            
            clearTimeout(timerToast);
            timerToast = setTimeout(function() {
                elemenToast.classList.remove('toast-tampil');
            }, 3000);
        }

        // Inisialisasi: Render Semua Produk Saat Halaman Pertama Kali Dimuat
        window.onload = function() {
            filterProduk('semua', null);
        };

        // Inisialisasi: Render Keranjang Saat Halaman Pertama Kali Dimuat
        window.onload = function() {
            renderKeranjang();
        };