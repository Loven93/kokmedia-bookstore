<?php
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}
require_once ROOT_DIR . '/helpers/Database.php';
require_once ROOT_DIR . '/models/UserModel.php';

$db        = new Database();
$userModel = new UserModel($db->getConnection());
$users     = $userModel->getAllUsers();

require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-screen-md mx-auto px-4 py-8 flex-grow w-full">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen User</h1>
        <a href="?route=dashboard"
            class="text-sm text-gray-500 hover:text-blue-600 font-medium">
            &larr; Kembali ke Dashboard
        </a>
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

    <?php if (empty($users)): ?>
        <p class="text-sm text-gray-400 text-center py-8">Belum ada user terdaftar.</p>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Username</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Email</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900 text-xs">
                                    <?= htmlspecialchars($user['username']) ?>
                                </p>
                                <!-- Tandai kalau ini akun yang sedang login -->
                                <?php if ((int)$user['id'] === (int)($_SESSION['user_id'] ?? 0)): ?>
                                    <span class="text-[10px] text-blue-500 font-bold">Akun Anda</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                <?= htmlspecialchars($user['email'] ?? '-') ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full
                                    <?= $user['role'] === 'admin'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-blue-100 text-blue-800' ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="?route=edit_user&id=<?= (int)$user['id'] ?>"
                                        class="text-xs bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-3 py-1.5 rounded transition-colors">
                                        Edit
                                    </a>

                                    <?php if ((int)$user['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                                        <form action="?route=delete_user" method="POST"
                                            onsubmit="return confirm('Hapus user \'<?= htmlspecialchars(addslashes($user['username'])) ?>\'?')">
                                            <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                                            <button type="submit"
                                                class="text-xs bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Tidak bisa hapus diri sendiri -->
                                        <button disabled
                                            class="text-xs bg-gray-100 text-gray-400 font-bold px-3 py-1.5 rounded cursor-not-allowed">
                                            Hapus
                                        </button>
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