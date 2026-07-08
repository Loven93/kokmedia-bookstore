<?php
// Mencegah akses langsung tanpa melalui routing index.php
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}

// Inisialisasi koneksi basis data & model jika belum disiapkan oleh pengendali utama
require_once ROOT_DIR . '/helpers/Database.php';
require_once ROOT_DIR . '/models/BookModel.php';

$database = new Database();
$dbConnection = $database->getConnection();

// 1. Mengambil data statistik toko secara dinamis menggunakan fungsi agregat SQL
try {
    $statsQuery = "SELECT COUNT(*) as total_judul, SUM(stock) as total_stok FROM books";
    $statsStmt = $dbConnection->prepare($statsQuery);
    $statsStmt->execute();
    $storeStats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    $totalJudul = $storeStats['total_judul'] ?? 0;
    $totalStok = $storeStats['total_stok'] ?? 0;
} catch (Exception $e) {
    $totalJudul = 0;
    $totalStok = 0;
}

// 2. Mengambil seluruh katalog buku dari database PostgreSQL
try {
    $bookModel = new BookModel($dbConnection);
    $books = $bookModel->getAllBooks();
} catch (Exception $e) {
    $books = [];
}

// Memanggil template header navigasi utama
require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">

    <!-- HERO SECTION & REAL-TIME STATS -->
    <div
        class="bg-gradient-to-r from-blue-900 to-indigo-950 rounded-2xl p-6 md:p-8 text-white shadow-sm mb-10 relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span
                class="bg-blue-500 text-white font-bold text-[10px] px-2.5 py-1 rounded-full uppercase tracking-wider">UAS
                Project</span>
            <h1 class="text-2xl md:text-4xl font-black tracking-tight mt-3 mb-2">Selamat Datang di Kokmedia</h1>
            <p class="text-blue-200 text-xs md:text-sm leading-relaxed mb-6">
                Platform e-commerce buku online ilmiah yang dibangun menggunakan arsitektur Native PHP MVC terstruktur
                dan didukung oleh efisiensi basis data relasional PostgreSQL.
            </p>

            <!-- Grid Statistik Dinamis -->
            <div class="grid grid-cols-2 gap-4 max-w-md">
                <div class="bg-white/10 backdrop-blur-sm p-3.5 rounded-xl border border-white/10">
                    <span class="block text-[10px] uppercase font-bold text-blue-300 tracking-wider">Koleksi
                        Judul</span>
                    <span class="text-2xl font-black text-white"><?= number_format($totalJudul) ?></span>
                    <span class="text-[10px] text-blue-200 block mt-0.5">Judul Buku Terdaftar</span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm p-3.5 rounded-xl border border-white/10">
                    <span class="block text-[10px] uppercase font-bold text-blue-300 tracking-wider">Total
                        Logistik</span>
                    <span class="text-2xl font-black text-white"><?= number_format($totalStok) ?></span>
                    <span class="text-[10px] text-blue-200 block mt-0.5">Unit Buku Siap Jual</span>
                </div>
            </div>
        </div>
        <!-- Ornamen Dekoratif Latar Belakang -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl pointer-events-none">
        </div>
    </div>

    <!-- SECTION TITLE -->
    <div class="border-b border-gray-200 pb-4 mb-6">
        <h2 class="text-xl font-black text-gray-900 tracking-tight">Katalog Buku Terbaru</h2>
        <p class="text-xs text-gray-500 mt-0.5">Jelajahi pustaka ilmu pengetahuan terlengkap kami</p>
    </div>

    <!-- GRID KATALOG BUKU DYNAMIS -->
    <?php if (empty($books)): ?>
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center my-6">
            <p class="text-sm text-gray-500 font-medium">Belum ada koleksi buku di dalam database PostgreSQL saat ini.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($books as $book): ?>
                <div
                    class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col justify-between shadow-sm transition-all duration-300 hover:shadow-md hover:border-blue-300 group">
                    <div>
                        <!-- BAGIAN COVER BUKU DENGAN PROTEKSI RUNTIME GANDA -->
                        <div
                            class="w-full h-48 bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden mb-4 relative border border-gray-100 select-none">
                            <?php
                            $coverPath = $book['cover_image'];
                            $isOnlineUrl = filter_var($coverPath, FILTER_VALIDATE_URL);
                            $isFileExists = !empty($coverPath) && file_exists(ROOT_DIR . '/' . $coverPath);

                            // Membuat ID unik untuk manipulasi DOM JavaScript
                            $uniqueId = 'book-cover-' . $book['id'];
                            $hasImage = ($isOnlineUrl || $isFileExists);
                            ?>

                            <!-- Elemen Gambar Utama -->
                            <img id="<?= $uniqueId ?>-img" src="<?= !empty($coverPath) ? htmlspecialchars($coverPath) : '' ?>"
                                alt="Cover <?= htmlspecialchars($book['title']) ?>"
                                class="<?= $hasImage ? 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-105' : 'hidden' ?>"
                                onerror="this.classList.add('hidden'); document.getElementById('<?= $uniqueId ?>-placeholder').classList.remove('hidden');">

                            <!-- Elemen Placeholder (Otomatis muncul jika gambar di atas GAGAL dimuat oleh browser) -->
                            <div id="<?= $uniqueId ?>-placeholder"
                                class="<?= $hasImage ? 'hidden' : '' ?> flex flex-col items-center justify-center p-4 text-center">
                                <svg class="w-8 h-8 text-blue-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                                <span class="text-blue-600 font-black text-xs tracking-widest block">KOKMEDIA</span>
                                <span class="text-gray-400 text-[9px] font-bold uppercase tracking-wider">Book Identity</span>
                            </div>
                        </div>

                        <!-- KONTEN TEKS INFORMASI BUKU -->
                        <h3
                            class="text-sm font-bold text-gray-900 line-clamp-2 tracking-tight min-h-[40px] mb-1 group-hover:text-blue-700 transition-colors">
                            <?= htmlspecialchars($book['title']) ?>
                        </h3>
                        <p class="text-[11px] text-gray-400 font-semibold mb-3 truncate">
                            <?= htmlspecialchars($book['author'] ?? 'Penulis Anonym') ?>
                        </p>
                    </div>

                    <div>
                        <!-- HARGA & STATUS LOGISTIK STOK -->
                        <div class="mb-3 flex items-baseline justify-between">
                            <span class="text-sm font-black text-blue-700">
                                Rp <?= number_format($book['price'], 0, ',', '.') ?>
                            </span>

                            <?php if ($book['stock'] > 0): ?>
                                <span class="text-[10px] text-gray-600 font-bold">
                                    Stok Tersedia: <span class="text-gray-900"><?= $book['stock'] ?></span>
                                </span>
                            <?php else: ?>
                                <span class="text-[10px] text-red-600 font-extrabold bg-red-50 px-1.5 py-0.5 rounded">
                                    Stok Habis
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- TOMBOL KENDALI BELANJA BERBASIS VALIDASI STOK -->
                        <?php if ($book['stock'] > 0): ?>
                            <form action="?route=add_to_cart" method="POST">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit"
                                    class="w-full text-center text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-lg text-xs py-2 shadow-sm transition-colors cursor-pointer block">
                                    + Keranjang
                                </button>
                            </form>
                        <?php else: ?>
                            <button disabled
                                class="w-full text-center text-gray-400 bg-gray-100 font-bold rounded-lg text-xs py-2 border border-gray-200 cursor-not-allowed block select-none">
                                Stok Habis
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php
// Memanggil template footer
require_once ROOT_DIR . '/components/footer.php';
?>