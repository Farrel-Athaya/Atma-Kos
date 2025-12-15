/**
 * Home Page JavaScript
 * Script untuk halaman utama
 */

document.addEventListener('DOMContentLoaded', function() {
    // Load kos terbaru
    loadKosTerbaru();
    
    // Setup search form
    setupSearchForm();
});

/**
 * Load daftar kos terbaru
 */
async function loadKosTerbaru() {
    const kosGrid = document.getElementById('kosGrid');
    
    if (!kosGrid) return;
    
    showLoading(kosGrid);
    
    try {
        const response = await API.kos.getList({ limit: 6 });
        
        // Handle response - data bisa berupa array langsung atau object dengan property data
        let kosList = [];
        if (response.success) {
            kosList = Array.isArray(response.data) ? response.data : (response.data?.kos || []);
        }
        
        if (kosList.length > 0) {
            renderKosList(kosGrid, kosList);
        } else {
            showEmpty(kosGrid, 'Belum ada data kos tersedia.');
        }
    } catch (error) {
        console.error('Error loading kos:', error);
        showError(kosGrid, 'Gagal memuat data kos. Silakan refresh halaman.');
    }
}

/**
 * Render daftar kos ke grid
 */
function renderKosList(container, kosList) {
    container.innerHTML = kosList.map(kos => {
        const nama = kos.nama_kos || kos.nama;
        const tipe = kos.tipe_kos || kos.tipe;
        const foto = kos.foto_utama || kos.foto;
        const hargaMin = kos.harga_min || 0;
        const hargaMax = kos.harga_max || hargaMin;
        
        return `
        <div class="kos-card">
            <div class="kos-image">
                <img src="${foto ? UPLOAD_BASE_URL + '/kos/' + foto : 'assets/images/no-image.svg'}" 
                     alt="${nama}"
                     onerror="this.src='assets/images/no-image.svg'">
                <span class="kos-badge badge-${tipe}">${tipe}</span>
            </div>
            <div class="kos-info">
                <h3>${nama}</h3>
                <p class="kos-address">${kos.alamat || 'Lokasi tidak tersedia'}</p>
                <p class="kos-price">
                    <strong>${formatRupiah(hargaMin)}</strong> ${hargaMax > hargaMin ? '- ' + formatRupiah(hargaMax) : ''} /bulan
                </p>
                <p class="kos-rooms">${kos.kamar_tersedia || 0} kamar tersedia</p>
                <a href="pages/detail-kos.html?id=${kos.id}" class="btn btn-primary btn-block">Lihat Detail</a>
            </div>
        </div>
    `}).join('');
}

/**
 * Setup search form
 */
function setupSearchForm() {
    const searchForm = document.getElementById('searchForm');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const query = document.getElementById('searchQuery').value;
            const tipe = document.getElementById('searchTipe').value;
            
            // Redirect ke halaman cari kos dengan parameter
            const params = [];
            
            if (query) params.push('q=' + encodeURIComponent(query));
            if (tipe) params.push('tipe=' + encodeURIComponent(tipe));
            
            window.location.href = 'pages/cari-kos.html?' + params.join('&');
        });
    }
}
