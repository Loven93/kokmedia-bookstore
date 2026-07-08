<?php
if (!defined('ROOT_DIR')) {
    header("Location: index.php");
    exit;
}
// $user sudah disiapkan oleh route edit_user di index.php
require_once ROOT_DIR . '/components/header.php';
?>

<div class="max-w-screen-md mx-auto px-4 py-8 flex-grow w-full">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
        <a href="?route=users"
            class="text-sm text-gray-500 hover:text-blue-600 font-medium">
            &larr; Kembali ke Daftar User
        </a>
    </div>

    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <form action="?route=process_edit_user" method="POST" class="space-y-4">
            <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="username"
                        class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Username
                    </label>
                    <input type="text" id="username" name="username" required
                        value="<?= htmlspecialchars($user['username']) ?>"
                        autocomplete="username"
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label for="email"
                        class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Email
                    </label>
                    <input type="email" id="email" name="email"
                        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                        autocomplete="email"
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="role"
                        class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Role
                    </label>
                    <select id="role" name="role"
                        class="w-full p-2 border border-gray-300 rounded text-sm bg-white focus:ring-1 focus:ring-blue-500 outline-none">
                        <option value="pembeli" <?= $user['role'] === 'pembeli' ? 'selected' : '' ?>>
                            Pembeli
                        </option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                            Administrator
                        </option>
                    </select>
                </div>
                <div>
                    <label for="new_password"
                        class="block mb-1 text-xs font-bold text-gray-700 uppercase">
                        Password Baru (Opsional)
                    </label>
                    <input type="password" id="new_password" name="new_password"
                        minlength="8"
                        placeholder="Kosongkan jika tidak diganti"
                        autocomplete="new-password"
                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
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