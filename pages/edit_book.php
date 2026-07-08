<?php
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}
// $book sudah disiapkan oleh route edit_book di index.php
require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-screen-md mx-auto px-4 py-8 flex-grow w-full">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-900">Edit Data Buku</h1>
        <a href="?route=dashboard" class="text-sm text-gray-500 hover:text-blue-600 font-medium">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="p-4 mb-6 text-sm rounded-lg <?= strpos($_SESSION['message'], 'berhasil') !== false ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' ?> font-medium">
            <?= htmlspecialchars($_SESSION['message']); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <form action="?route=process_edit_book" method="POST" 
              enctype="multipart/form-data" class="space-y-4">
            
            <!-- ID buku disimpan hidden -->
            <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Judul Buku
                    </label>
                    <input type="text" id="title" name="title" required
                        value="<?= htmlspecialchars($book['title']) ?>"
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label for="author" class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Nama Penulis
                    </label>
                    <input type="text" id="author" name="author" required
                        value="<?= htmlspecialchars($book['author']) ?>"
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Harga Jual (Rp)
                    </label>
                    <input type="number" id="price" name="price" min="0" required
                        value="<?= (float)$book['price'] ?>"
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label for="stock" class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Jumlah Stok
                    </label>
                    <input type="number" id="stock" name="stock" min="0" required
                        value="<?= (int)$book['stock'] ?>"
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label for="description" class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                    Sinopsis
                </label>
                <textarea id="description" name="description" rows="3"
                    class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none"
                ><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
            </div>

            <!-- Preview cover lama -->
            <?php if (!empty($book['cover_image'])): ?>
                <div>
                    <p class="text-xs font-bold text-gray-700 uppercase mb-1">Cover Saat Ini</p>
                    <img src="<?= htmlspecialchars($book['cover_image']) ?>"
                         alt="Cover saat ini"
                         class="h-32 object-cover rounded border border-gray-200">
                </div>
            <?php endif; ?>

            <div>
                <label for="cover_image" class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                    Ganti Cover (Opsional)
                </label>
                <input type="file" id="cover_image" name="cover_image" accept="image/*"
                    class="w-full text-xs text-gray-500 border border-gray-300 rounded bg-gray-50 p-1 cursor-pointer">
                <p class="text-[10px] text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti cover.</p>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full md:w-auto text-white bg-blue-700 hover:bg-blue-800 font-bold rounded text-sm px-6 py-2.5 shadow">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once ROOT_DIR . '/components/footer.php'; ?>