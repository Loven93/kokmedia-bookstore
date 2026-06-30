<?php
// Mencegah akses langsung tanpa melalui routing index.php
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}

// Memanggil Top Bar Navigasi Utama (Sesuai instruksi dosen)
require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-md mx-auto px-4 py-12 mt-10 flex-grow w-full">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h1 class="text-xl font-black text-gray-900 text-center mb-6 tracking-tight">LOGIN KOKMEDIA</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="p-3 mb-4 text-xs text-red-800 rounded bg-red-50 font-semibold text-center">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form action="?route=process_login" method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Username</label>
                <input type="text" name="username" required class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 outline-none" placeholder="Masukkan username Anda...">
            </div>
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Password</label>
                <input type="password" name="password" required class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 outline-none" placeholder="Masukkan password...">
            </div>
            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 font-bold rounded-lg text-sm py-2.5 shadow transition-colors">
                Masuk Sistem &rarr;
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-500">Belum memiliki akun? <a href="?route=register" class="text-blue-600 font-bold hover:underline">Daftar Akun Baru</a></p>
        </div>
    </div>
</div>

<?php require_once ROOT_DIR . '/components/footer.php'; ?>