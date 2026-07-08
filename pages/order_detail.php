<?php
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}
// $order sudah disiapkan oleh route order_detail di index.php
require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-screen-md mx-auto px-4 py-8 flex-grow w-full">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-900">
            Detail Order #<?= (int)$order['id'] ?>
        </h1>
        <a href="?route=orders"
            class="text-sm text-gray-500 hover:text-blue-600 font-medium">
            &larr; Kembali
        </a>
    </div>

    <!-- Info order -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-6 text-sm">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-400 uppercase font-bold">Pembeli</p>
                <p class="font-semibold text-gray-800">
                    <?= htmlspecialchars($order['username']) ?>
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase font-bold">Status</p>
                <p class="font-semibold text-gray-800">
                    <?= htmlspecialchars($order['status']) ?>
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase font-bold">Tanggal</p>
                <p class="font-semibold text-gray-800">
                    <?= htmlspecialchars($order['created_at'] ?? '-') ?>
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase font-bold">Total</p>
                <p class="font-black text-blue-700">
                    Rp <?= number_format($order['total_amount'], 0, ',', '.') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Item-item dalam order -->
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-700">Item Dibeli</h2>
        </div>
        <div class="divide-y divide-gray-100">
            <?php foreach ($order['items'] as $item): ?>
                <div class="p-4 flex items-center gap-4">
                    <img src="<?= htmlspecialchars($item['cover_image'] ?? '') ?>"
                         alt="Cover"
                         class="w-10 h-14 object-cover rounded border border-gray-100"
                         onerror="this.classList.add('hidden')">
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 text-sm">
                            <?= htmlspecialchars($item['title']) ?>
                        </p>
                        <p class="text-xs text-gray-400">
                            <?= htmlspecialchars($item['author'] ?? '-') ?>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            <?= (int)$item['quantity'] ?> unit
                        </p>
                    </div>
                    <p class="font-extrabold text-gray-900 text-sm shrink-0">
                        Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once ROOT_DIR . '/components/footer.php'; ?>