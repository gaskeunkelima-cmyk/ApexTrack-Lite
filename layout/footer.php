</main>
    
<footer class="bg-white text-gray-600 p-4 shadow-md">
    <div class="flex flex-col md:flex-row justify-between items-center mx-auto">
        <p class="text-sm order-2 md:order-1 mt-2 md:mt-0" id="dynamic-footer-text">
            Copyright &copy; 2024 ApexTrack. All rights reserved.
        </p>
        <p class="text-xs text-gray-400 order-1 md:order-2">Version 1.0.0 Beta</p>
    </div>
</footer>

<script>
    // Pastikan kode ini berjalan setelah elemen-elemen HTML dimuat
    document.addEventListener('DOMContentLoaded', (event) => {
        const websiteTitle = document.title;
        const currentYear = new Date().getFullYear();
        const footerTextElement = document.getElementById('dynamic-footer-text');

        // Memeriksa apakah judul website dan elemen footer ditemukan
        if (websiteTitle && footerTextElement) {
            footerTextElement.textContent = `Copyright © ${currentYear} ${websiteTitle}. All rights reserved.`;
        }
    });

    lucide.createIcons();
</script>
</body>
</html>
