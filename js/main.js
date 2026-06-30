// Fungsi global untuk toggle Modal Tailwind
function toggleModal(modalID) {
    const modal = document.getElementById(modalID);
    if (modal) {
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }
}

// Pastikan semua elemen DOM sudah dimuat sebelum menjalankan event listener
document.addEventListener("DOMContentLoaded", function() {
    const authForm = document.getElementById('authForm');
    const authMessage = document.getElementById('authMessage');

    if (authForm) {
        authForm.addEventListener('submit', function(e) {
            // Tahan form agar halaman tidak reload
            e.preventDefault();

            // Ambil data input
            const usernameInput = document.getElementById('username').value;
            const passwordInput = document.getElementById('password').value;

            // Kembalikan style pesan notifikasi ke kondisi tersembunyi
            authMessage.classList.add('hidden');
            authMessage.classList.remove('bg-red-50', 'text-red-800', 'bg-green-50', 'text-green-800');

            // Kirim request ke route API di index.php
            fetch('?route=api_login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: usernameInput,
                    password: passwordInput
                })
            })
            .then(response => response.json())
            .then(data => {
                // Munculkan kembali div notifikasi
                authMessage.classList.remove('hidden');

                if (data.status === 'success') {
                    // Tampilan sukses
                    authMessage.classList.add('bg-green-50', 'text-green-800');
                    authMessage.textContent = data.message;
                    
                    // Alihkan user ke halaman dashboard setelah delay 1.5 detik
                    setTimeout(() => {
                        window.location.href = '?route=dashboard';
                    }, 1500);
                } else {
                    // Tampilan gagal
                    authMessage.classList.add('bg-red-50', 'text-red-800');
                    authMessage.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                authMessage.classList.remove('hidden');
                authMessage.classList.add('bg-red-50', 'text-red-800');
                authMessage.textContent = "Terjadi kesalahan koneksi ke server.";
            });
        });
    }
});