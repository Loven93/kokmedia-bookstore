<?php
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart       = $_SESSION['cart'] ?? [];
$grandTotal = 0;

require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-screen-md mx-auto px-4 py-8 flex-grow w-full">
    <h1 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-3">
        Keranjang Belanja Anda
    </h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="p-4 mb-4 text-sm rounded-lg font-medium
            <?= strpos($_SESSION['message'], 'berhasil') !== false || strpos($_SESSION['message'], 'ditambahkan') !== false
                ? 'bg-green-50 text-green-800'
                : 'bg-red-50 text-red-800' ?>"
            role="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <!-- Empty state -->
        <div class="p-8 text-center bg-gray-50 rounded-lg border border-dashed border-gray-300">
            <p class="text-gray-500 mb-4">Keranjang Anda masih kosong.</p>
            <a href="?route=home"
                class="text-white bg-blue-600 px-4 py-2 rounded text-sm font-medium hover:bg-blue-700">
                Kembali ke Etalase
            </a>
        </div>

    <?php else: ?>
        <!-- Tabel item cart -->
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">

                <?php foreach ($cart as $id => $item):
                    $subtotal    = (float)($item['price']    ?? 0) * (int)($item['quantity'] ?? 0);
                    $grandTotal += $subtotal;
                ?>
                    <div class="p-4 flex items-center justify-between gap-4">

                        <!-- Cover + Info Buku -->
                        <div class="flex items-center space-x-4 flex-1">
                            <img src="<?= htmlspecialchars($item['cover_image'] ?? '') ?>"
                                alt="Cover <?= htmlspecialchars($item['title'] ?? '') ?>"
                                class="w-12 h-16 object-cover rounded border border-gray-100"
                                onerror="this.classList.add('hidden')">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">
                                    <?= htmlspecialchars($item['title'] ?? '-') ?>
                                </h3>
                                <p class="text-xs text-gray-500">
                                    Rp <?= number_format($item['price'] ?? 0, 0, ',', '.') ?> / unit
                                </p>
                            </div>
                        </div>

                        <!-- Update Quantity -->
                        <form action="?route=update_cart" method="POST"
                              class="flex items-center gap-2">
                            <input type="hidden" name="book_id" value="<?= (int)$id ?>">
                            <input type="number" name="quantity"
                                value="<?= (int)($item['quantity'] ?? 1) ?>"
                                min="1" max="99"
                                class="w-16 text-center border border-gray-300 rounded text-sm p-1 focus:ring-1 focus:ring-blue-500 outline-none"
                                onchange="this.form.submit()">
                        </form>

                        <!-- Subtotal -->
                        <div class="text-right shrink-0">
                            <p class="font-extrabold text-gray-900 text-sm">
                                Rp <?= number_format($subtotal, 0, ',', '.') ?>
                            </p>
                        </div>

                        <!-- Tombol Hapus -->
                        <form action="?route=remove_from_cart" method="POST">
                            <input type="hidden" name="book_id" value="<?= (int)$id ?>">
                            <button type="submit"
                                class="text-red-400 hover:text-red-600 transition-colors"
                                title="Hapus dari keranjang"
                                onclick="return confirm('Hapus item ini dari keranjang?')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>

            </div>

            <!-- Total & Checkout -->
            <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">
                        Total Pembayaran
                    </p>
                    <p class="text-xl font-black text-blue-700">
                        Rp <?= number_format($grandTotal, 0, ',', '.') ?>
                    </p>
                </div>
                <form action="?route=checkout" method="POST">
                    <button type="submit"
                        class="text-white bg-green-600 hover:bg-green-700 font-bold rounded-lg text-sm px-5 py-2.5 shadow-md"
                        onclick="return confirm('Konfirmasi pembelian sebesar Rp <?= number_format($grandTotal, 0, ',', '.') ?>?')">
                        Konfirmasi &amp; Bayar &rarr;
                    </button>
                </form>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php require_once ROOT_DIR . '/components/footer.php'; ?>