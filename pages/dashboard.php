<?php
// Proteksi akses langsung
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}

require_once ROOT_DIR . '/helpers/Database.php';
require_once ROOT_DIR . '/models/BookModel.php';

$db        = new Database();
$bookModel = new BookModel($db->getConnection());
$books     = $bookModel->getAllBooks();

require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-screen-md mx-auto px-4 py-8 flex-grow w-full">

    <!-- Judul -->
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-900">Master Data Buku (Admin)</h1>
        <div class="flex gap-2">
            <a href="?route=users"
                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-3 py-1.5 rounded transition-colors">
                Manajemen User
            </a>
            <a href="?route=orders"
                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-3 py-1.5 rounded transition-colors">
                Semua Transaksi
            </a>
        </div>
    </div>

    <!-- Flash Message -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="p-4 mb-6 text-sm rounded-lg font-medium
            <?= strpos($_SESSION['message'], 'berhasil') !== false
                ? 'bg-green-50 text-green-800'
                : 'bg-red-50 text-red-800' ?>">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Form Tambah Buku -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-8">
        <h2 class="text-md font-bold mb-4 text-gray-800">Tambah Koleksi Buku Toko</h2>

        <form action="?route=process_add_book" method="POST"
              enctype="multipart/form-data" class="space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="title"
                        class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Judul Buku
                    </label>
                    <input type="text" id="title" name="title" required
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label for="author"
                        class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Nama Penulis
                    </label>
                    <input type="text" id="author" name="author" required
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="price"
                        class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Harga Jual (Rp)
                    </label>
                    <input type="number" id="price" name="price" min="0" required
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label for="stock"
                        class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Jumlah Inventaris Stok
                    </label>
                    <input type="number" id="stock" name="stock" min="0" required
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label for="description"
                    class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                    Sinopsis Pendek
                </label>
                <textarea id="description" name="description" rows="3"
                    class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </textarea>
            </div>

            <div>
                <label for="cover_image"
                    class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                    File Sampul Buku
                </label>
                <input type="file" id="cover_image" name="cover_image" accept="image/*"
                    class="w-full text-xs text-gray-500 border border-gray-300 rounded bg-gray-50 p-1 cursor-pointer">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full md:w-auto text-white bg-blue-700 hover:bg-blue-800 font-bold rounded text-sm px-6 py-2.5 shadow text-center">
                    Simpan ke Database
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Buku -->
    <div class="border-b border-gray-200 pb-4 mb-4">
        <h2 class="text-lg font-black text-gray-900">Daftar Buku Tersimpan</h2>
        <p class="text-xs text-gray-500 mt-0.5">
            Total: <?= count($books) ?> judul buku
        </p>
    </div>

    <?php if (empty($books)): ?>
        <p class="text-sm text-gray-400 text-center py-8">
            Belum ada buku di database.
        </p>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden mb-8">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Judul</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Harga</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Stok</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($books as $book): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900 text-xs">
                                    <?= htmlspecialchars($book['title']) ?>
                                </p>
                                <p class="text-[11px] text-gray-400">
                                    <?= htmlspecialchars($book['author'] ?? '-') ?>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-700">
                                Rp <?= number_format($book['price'], 0, ',', '.') ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-bold
                                    <?= $book['stock'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $book['stock'] ?> unit
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="?route=edit_book&id=<?= (int)$book['id'] ?>"
                                        class="text-xs bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-3 py-1.5 rounded transition-colors">
                                        Edit
                                    </a>
                                    <form action="?route=delete_book" method="POST"
                                        onsubmit="return confirm('Hapus buku \'<?= htmlspecialchars(addslashes($book['title'])) ?>\'?')">
                                        <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
                                        <button type="submit"
                                            class="text-xs bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<?php require_once ROOT_DIR . '/components/footer.php'; ?>