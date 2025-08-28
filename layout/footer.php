</main>
    
<footer class="bg-white text-gray-600 p-4 shadow-md">
    <div class="flex flex-col md:flex-row justify-between items-center mx-auto">
        <p class="text-sm order-2 md:order-1 mt-2 md:mt-0" id="dynamic-footer-text">
            Copyright &copy; 2024 ApexTrack. All rights reserved.
        </p>
        <p class="text-xs text-gray-400 order-1 md:order-2" id="app-version">Loading...</p>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const websiteTitle = document.title;
        const currentYear = new Date().getFullYear();
        const footerTextElement = document.getElementById('dynamic-footer-text');
        const versionElement = document.getElementById('app-version');

        if (websiteTitle && footerTextElement) {
            footerTextElement.textContent = `Copyright © ${currentYear} ${websiteTitle}. All rights reserved.`;
        }

        fetch('version.txt')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat file version.txt');
                }
                return response.text();
            })
            .then(version => {
                if (versionElement) {
                    versionElement.textContent = `Version ${version.trim()}`;
                }
            })
            .catch(error => {
                console.error('Error fetching version:', error);
                if (versionElement) {
                    versionElement.textContent = 'Version N/A';
                }
            });

        lucide.createIcons();
    });
</script>
