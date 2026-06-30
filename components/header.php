<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kokmedia - Toko Buku Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="?route=home" class="flex items-center space-x-2">
                <span class="text-2xl font-black text-blue-700 tracking-wider">KOKMEDIA</span>
            </a>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                <a href="?route=home"
                    class="text-gray-700 hover:text-blue-700 font-medium text-sm flex items-center gap-1 shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span>Etalase</span>
                </a>

                <?php
                $cartCount = 0;
                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $cartCount += $item['quantity'];
                    }
                }
                ?>
                <a href="?route=cart"
                    class="relative text-gray-700 hover:text-blue-700 font-medium text-sm flex items-center gap-1 shrink-0 mr-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z">
                        </path>
                    </svg>
                    <span>Keranjang</span>
                    <?php if ($cartCount > 0): ?>
                        <span
                            class="absolute -top-2.5 -right-3.5 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center shadow-sm"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>

                <?php if (isset($_SESSION['username'])): ?>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <a href="?route=dashboard"
                            class="bg-yellow-400 text-gray-900 font-bold px-3 py-1.5 rounded-lg text-xs hover:bg-yellow-500 flex items-center gap-1 shadow-sm shrink-0 whitespace-nowrap transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Tambah Buku</span>
                        </a>
                    <?php endif; ?>

                    <div class="flex items-center gap-2 text-sm shrink-0 pl-1 border-l border-gray-200">
                        <span class="text-gray-400 font-normal">Hi,</span>
                        <span class="text-gray-700 font-bold"><?= htmlspecialchars($_SESSION['username']) ?></span>
                        <a href="?route=logout"
                            class="text-xs text-red-500 hover:text-red-700 font-medium ml-1 bg-red-50 px-2 py-1 rounded hover:bg-red-100 transition-colors">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="?route=login"
                        class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2 shrink-0 transition-colors shadow-sm">Login</a>
                <?php endif; ?>
            </div>

        </div>
    </nav>