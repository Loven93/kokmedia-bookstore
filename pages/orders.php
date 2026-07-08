<?php
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}

require_once ROOT_DIR . '/helpers/Database.php';
require_once ROOT_DIR . '/models/OrderModel.php';

$db         = new Database();
$orderModel = new OrderModel($db->getConnection());

// Admin lihat semua, pembeli lihat punya sendiri
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

if ($isAdmin) {
    $orders = $orderModel->getAllOrders();
} elseif (isset($_SESSION['user_id'])) {
    $orders = $orderModel->getOrdersByUser($_SESSION['user_id']);
} else {
    $_SESSION['message'] = "Silakan login untuk melihat riwayat transaksi.";
    header("Location: ?route=login");
    exit;
}

require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-screen-md mx-auto px-4 py-8 flex-grow w-full">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-900">
            <?= $isAdmin ? 'Semua Transaksi' : 'Riwayat Pembelian Saya' ?>
        </h1>
        <?php if ($isAdmin): ?>
            <a href="?route=dashboard"
                class="text-sm text-gray-500 hover:text-blue-600 font-medium">
                &larr; Kembali ke Dashboard
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="p-4 mb-6 text-sm rounded-lg font-medium
            <?= strpos($_SESSION['message'], 'berhasil') !== false
                ? 'bg-green-50 text-green-800'
                : 'bg-red-50 text-red-800' ?>">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="p-8 text-center bg-gray-50 rounded-lg border border-dashed border-gray-300">
            <p class="text-gray-500 mb-4">Belum ada transaksi.</p>
            <a href="?route=home"
                class="text-white bg-blue-600 px-4 py-2 rounded text-sm font-medium hover:bg-blue-700">
                Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">#ID</th>
                        <?php if ($isAdmin): ?>
                            <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">User</th>
                        <?php endif; ?>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Total</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-xs font-bold text-gray-500">
                                #<?= (int)$order['id'] ?>
                            </td>
                            <?php if ($isAdmin): ?>
                                <td class="px-4 py-3 text-xs text-gray-700 font-semibold">
                                    <?= htmlspecialchars($order['username']) ?>
                                </td>
                            <?php endif; ?>
                            <td class="px-4 py-3 text-xs font-bold text-blue-700">
                                Rp <?= number_format($order['total_amount'], 0, ',', '.') ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php
                                $statusColor = match($order['status']) {
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default     => 'bg-yellow-100 text-yellow-800'
                                };
                                ?>
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full <?= $statusColor ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Detail order -->
                                    <a href="?route=order_detail&id=<?= (int)$order['id'] ?>"
                                        class="text-xs bg-blue-500 hover:bg-blue-600 text-white font-bold px-3 py-1.5 rounded transition-colors">
                                        Detail
                                    </a>

                                    <?php if ($isAdmin): ?>
                                        <!-- Update status -->
                                        <form action="?route=update_order_status" method="POST"
                                              class="flex items-center gap-1">
                                            <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
                                            <select name="status"
                                                class="text-xs border border-gray-300 rounded p-1 bg-white outline-none">
                                                <option value="pending"   <?= $order['status'] === 'pending'   ? 'selected' : '' ?>>Pending</option>
                                                <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                            <button type="submit"
                                                class="text-xs bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-2 py-1 rounded transition-colors">
                                                Ubah
                                            </button>
                                        </form>

                                        <!-- Hapus order -->
                                        <form action="?route=delete_order" method="POST"
                                              onsubmit="return confirm('Hapus order #<?= (int)$order['id'] ?>? Stok buku akan dikembalikan.')">
                                            <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
                                            <button type="submit"
                                                class="text-xs bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
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