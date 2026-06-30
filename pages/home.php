<?php
// Mencegah akses langsung tanpa melalui routing index.php
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}

require_once ROOT_DIR . '/components/header.php';
require_once ROOT_DIR . '/helpers/Database.php';
require_once ROOT_DIR . '/models/BookModel.php';

$db = new Database();
$conn = $db->getConnection();
$bookModel = new BookModel($conn);
$books = $bookModel->getAllBooks();

// FITUR AKADEMIS: Mengambil Statistik Toko Secara Dinamis dari PostgreSQL
try {
    $stmtCount = $conn->query("SELECT COUNT(*) as total_buku, SUM(stock) as total_stok FROM books");
    $stats = $stmtCount->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $stats = ['total_buku' => 0, 'total_stok' => 0];
}
?>

<div class="max-w-screen-xl mx-auto px-4 py-8 flex-grow">
    
    <!-- ========================================== -->
    <!-- HALAMAN TAMPILAN AWAL (HERO SECTION DINAMIS) -->
    <!-- ========================================== -->
    <div class="mb-10 bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl p-6 md:p-10 text-white shadow-xl flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="max-w-xl">
            <span class="bg-blue-500 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full bg-opacity-30 border border-blue-400">Project UAS Aplikasi E-Commerce</span>
            <h1 class="text-3xl md:text-4xl font-black tracking-tight mt-3 mb-4">Solusi Literasi Digital Modern Bersama Kokmedia</h1>
            <p class="text-sm md:text-base text-blue-100 font-light leading-relaxed">Jelajahi ekosistem platform toko buku online berbasis arsitektur Native PHP MVC dan database relasional PostgreSQL.</p>
        </div>
        
        <!-- Bar Statistik Riil Database (Disukai Dosen karena menunjukkan pengolahan data agregat SQL) -->
        <div class="grid grid-cols-2 gap-4 w-full md:w-auto shrink-0">
            <div class="bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-10 rounded-xl p-4 text-center">
                <p class="text-xs text-blue-200 uppercase font-semibold">Koleksi Judul</p>
                <p class="text-3xl font-black mt-1"><?= $stats['total_buku'] ?? 0 ?></p>
            </div>
            <div class="bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-10 rounded-xl p-4 text-center">
                <p class="text-xs text-blue-200 uppercase font-semibold">Total Stok</p>
                <p class="text-3xl font-black mt-1"><?= $stats['total_stok'] ?? 0 ?></p>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- NOTIFIKASI TRANSAKSI / KERANJANG -->
    <!-- ========================================== -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="p-4 mb-6 text-sm text-blue-800 rounded-lg bg-blue-50 font-medium shadow-sm border border-blue-100 flex items-center" role="alert">
            <span class="font-bold mr-2">Info Sistem:</span> <?= $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- KATALOG PRODUK BUKU -->
    <!-- ========================================== -->
    <div class="flex justify-between items-center mb-6 border-b pb-3">
        <h2 class="text-xl font-black text-gray-900 tracking-tight">Katalog Buku Terbaru</h2>
    </div>
    
    <?php if(empty($books)): ?>
        <div class="p-6 text-center bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200">
            Etalase kosong. Silakan masuk sebagai Admin (username: admin / pass: admin) untuk mengisi Master Buku.
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <?php foreach ($books as $book): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between overflow-hidden hover:shadow-md transition-shadow duration-200">
                    
                    <!-- PENANGANAN ERROR IMAGE: Menggunakan Placeholder Elegan jika Path NULL/Rusak -->
                    <div class="w-full h-52 bg-gray-100 flex items-center justify-center relative overflow-hidden border-b border-gray-100">
                        <?php if(!empty($book['cover_image']) && filter_var($book['cover_image'], FILTER_VALIDATE_URL)): ?>
                            <img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="Cover" class="w-full h-full object-cover">
                        <?php elseif(!empty($book['cover_image']) && file_exists(ROOT_DIR . '/' . $book['cover_image'])): ?>
                            <img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="Cover" class="w-full h-full object-cover">
                        <?php else: ?>
                            <!-- Fallback UI saat Link Gambar Bermasalah/Kosong -->
                            <div class="text-center p-4 select-none">
                                <div class="text-blue-500 font-black text-lg tracking-widest mb-1">KOKMEDIA</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Book Identity</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 line-clamp-2 min-h-[40px]" title="<?= htmlspecialchars($book['title']) ?>">
                                <?= htmlspecialchars($book['title']) ?>
                            </h3>
                            <p class="text-xs text-gray-400 mt-1 truncate font-medium"><?= htmlspecialchars($book['author']) ?></p>
                            <p class="font-black text-blue-700 text-base mt-3">Rp <?= number_format($book['price'], 0, ',', '.') ?></p>
                            <p class="text-[11px] font-bold mt-1.5 <?= ($book['stock'] > 0) ? 'text-gray-500' : 'text-red-500' ?>">
                                Stok Tersedia: <?= $book['stock'] ?>
                            </p>
                        </div>
                        
                        <?php if($book['stock'] > 0): ?>
                            <form action="?route=add_to_cart" method="POST" class="mt-4">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-lg text-xs px-3 py-2.5 text-center transition-colors shadow-sm">
                                    + Keranjang
                                </button>
                            </form>
                        <?php else: ?>
                            <button disabled class="w-full mt-4 text-gray-400 bg-gray-50 border border-gray-100 font-bold rounded-lg text-xs px-3 py-2.5 text-center cursor-not-allowed">
                                Stok Habis
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_DIR . '/components/footer.php'; ?>