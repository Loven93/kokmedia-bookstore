<?php
require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-screen-md mx-auto px-4 py-8 flex-grow w-full">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-900">Master Data Buku (Admin)</h1>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="p-4 mb-6 text-sm rounded-lg <?= strpos($_SESSION['message'], 'berhasil') !== false ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' ?> font-medium">
            <?= $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h2 class="text-md font-bold mb-4 text-gray-800">Tambah Koleksi Buku Toko</h2>
        
        <form action="?route=process_add_book" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Judul Buku</label>
                    <input type="text" name="title" required class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Nama Penulis</label>
                    <input type="text" name="author" required class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Harga Jual (Rp)</label>
                    <input type="number" name="price" min="0" required class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Jumlah Inventaris Stok</label>
                    <input type="number" name="stock" min="0" required class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Sinopsis Pendek</label>
                <textarea name="description" rows="3" class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none"></textarea>
            </div>

            <div>
                <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">File File Sampul Buku</label>
                <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-gray-500 border border-gray-300 rounded bg-gray-50 p-1 cursor-pointer">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full md:w-auto text-white bg-blue-700 hover:bg-blue-800 font-bold rounded text-sm px-6 py-2.5 shadow text-center">
                    Simpan ke Database
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once ROOT_DIR . '/components/footer.php'; ?>