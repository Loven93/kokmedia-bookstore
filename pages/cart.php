<?php
require_once ROOT_DIR . '/components/header.php';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$grandTotal = 0;
?>

<div class="max-w-screen-md mx-auto px-4 py-8 flex-grow w-full">
    <h1 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-3">Keranjang Belanja Anda</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
            <?= $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if(empty($cart)): ?>
        <div class="p-8 text-center bg-gray-50 rounded-lg border border-dashed border-gray-300">
            <p class="text-gray-500 mb-4">Keranjang Anda masih kosong.</p>
            <a href="?route=home" class="text-white bg-blue-600 px-4 py-2 rounded text-sm font-medium hover:bg-blue-700">Kembali ke Etalase</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
                <?php foreach($cart as $id => $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $grandTotal += $subtotal;
                ?>
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <img src="<?= htmlspecialchars($item['cover_image'] ?? 'https://via.placeholder.com/100x150') ?>" class="w-12 h-16 object-cover rounded">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($item['title']) ?></h3>
                                <p class="text-xs text-gray-500">Rp <?= number_format($item['price'], 0, ',', '.') ?> x <?= $item['quantity'] ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-extrabold text-gray-900 text-sm">Rp <?= number_format($subtotal, 0, ',', '.') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Total Pembayaran</p>
                    <p class="text-xl font-black text-blue-700">Rp <?= number_format($grandTotal, 0, ',', '.') ?></p>
                </div>
                <form action="?route=checkout" method="POST">
                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-bold rounded-lg text-sm px-5 py-2.5 shadow-md">
                        Konfirmasi & Bayar &rarr;
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_DIR . '/components/footer.php'; ?>