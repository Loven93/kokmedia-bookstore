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
        <h1 class="text-xl font-black text-gray-900 text-center mb-6 tracking-tight">DAFTAR AKUN KOKMEDIA</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="p-3 mb-4 text-xs text-red-800 rounded bg-red-50 font-semibold text-center">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form action="?route=process_register" method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Username</label>
                <input type="text" name="username" required class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Email</label>
                <input type="email" name="email" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 outline-none" placeholder="nama@email.com">
            </div>
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Password</label>
                <input type="password" name="password" required class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Daftar Sebagai (Role)</label>
                <select name="role" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:ring-1 focus:ring-blue-500 outline-none cursor-pointer">
                    <option value="pembeli">Pembeli (Buyer)</option>
                    <option value="admin">Administrator (Admin Master Data)</option>
                </select>
            </div>
            
            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 font-bold rounded-lg text-sm py-2.5 shadow transition-colors">
                Mendaftar Akun Baru &rarr;
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-500">Sudah punya akun? <a href="?route=login" class="text-blue-600 font-bold hover:underline">Login di sini</a></p>
        </div>
    </div>
</div>

<?php require_once ROOT_DIR . '/components/footer.php'; ?>