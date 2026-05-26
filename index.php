<?php
$page_title = 'Dashboard';
$active_page = 'dashboard';
include 'koneksi.php';

// Hitung status
$total_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alternatif"))['total'] ?? 0;
$total_hasil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil"))['total'] ?? 0;
$ahp_configured = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ahp_log"))['total'] ?? 0;
$bobot = [];
$q = mysqli_query($conn, "SELECT nama_kriteria, bobot FROM kriteria ORDER BY id_kriteria");
while($r = mysqli_fetch_assoc($q)) $bobot[$r['nama_kriteria']] = $r['bobot'];

$model_exists = file_exists(__DIR__ . '/model/model_rf.pkl');
$status_msg = $_GET['status'] ?? '';

include 'header.php';
?>

<?php if($status_msg === 'sukses'): ?>
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3 shadow-sm animate-fade-in">
    <i class="fas fa-check-circle text-xl"></i>
    <span>Perhitungan SAW + Prediksi AI berhasil! Lihat hasilnya di halaman <b>Hasil Ranking</b>.</span>
</div>
<?php endif; ?>
<?php if($status_msg === 'reset'): ?>
<div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center gap-3 shadow-sm animate-fade-in">
    <i class="fas fa-info-circle text-xl"></i>
    <span>Data hasil perhitungan berhasil direset.</span>
</div>
<?php endif; ?>

<!-- Welcome Panel -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8 mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Hai, Selamat Datang!</h2>
        <p class="text-gray-500 text-sm md:text-base">Sistem Pendukung Keputusan Rekrutmen Calon Asisten Praktikum.</p>
    </div>
    <div class="flex-shrink-0">
        <a href="upload.php" class="inline-flex items-center justify-center px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm font-semibold transition-all shadow-md shadow-blue-500/30 hover:-translate-y-0.5">
            <i class="fas fa-file-upload mr-2"></i> Import Data
        </a>
    </div>
</div>

<!-- 4 Stat Cards based on image -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1 (Green - Sales style) -->
    <div class="bg-gradient-to-br from-[#10b981] to-[#059669] rounded-2xl p-6 text-white shadow-lg shadow-emerald-500/30 flex items-center gap-4 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <div class="w-12 h-12 rounded-full bg-white text-emerald-600 flex items-center justify-center text-xl z-10 shadow-inner">
            <i class="fas fa-users"></i>
        </div>
        <div class="z-10">
            <div class="text-xs font-semibold text-white/80 mb-1 uppercase tracking-wider">Total Pendaftar</div>
            <div class="text-2xl font-bold"><?= number_format($total_data) ?> <span class="text-sm font-normal text-white/70">Orang</span></div>
        </div>
    </div>
    <!-- Card 2 (Blue - Purchases style) -->
    <div class="bg-gradient-to-br from-[#3b82f6] to-[#2563eb] rounded-2xl p-6 text-white shadow-lg shadow-blue-500/30 flex items-center gap-4 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <div class="w-12 h-12 rounded-full bg-white text-blue-600 flex items-center justify-center text-xl z-10 shadow-inner">
            <i class="fas fa-balance-scale"></i>
        </div>
        <div class="z-10">
            <div class="text-xs font-semibold text-white/80 mb-1 uppercase tracking-wider">Status AHP</div>
            <div class="text-xl font-bold mt-1"><?= $ahp_configured > 0 ? 'Terkonfigurasi' : 'Belum Diatur' ?></div>
        </div>
    </div>
    <!-- Card 3 (Red - Orders style) -->
    <div class="bg-gradient-to-br from-[#ef4444] to-[#dc2626] rounded-2xl p-6 text-white shadow-lg shadow-red-500/30 flex items-center gap-4 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <div class="w-12 h-12 rounded-full bg-white text-red-600 flex items-center justify-center text-xl z-10 shadow-inner">
            <i class="fas fa-robot"></i>
        </div>
        <div class="z-10">
            <div class="text-xs font-semibold text-white/80 mb-1 uppercase tracking-wider">Model AI</div>
            <div class="text-xl font-bold mt-1"><?= $model_exists ? 'Ready' : 'Not Found' ?></div>
        </div>
    </div>
    <!-- Card 4 (Orange - Growth style) -->
    <div class="bg-gradient-to-br from-[#f59e0b] to-[#d97706] rounded-2xl p-6 text-white shadow-lg shadow-amber-500/30 flex items-center gap-4 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <div class="w-12 h-12 rounded-full bg-white text-amber-600 flex items-center justify-center text-xl z-10 shadow-inner">
            <i class="fas fa-trophy"></i>
        </div>
        <div class="z-10">
            <div class="text-xs font-semibold text-white/80 mb-1 uppercase tracking-wider">Total Hasil</div>
            <div class="text-2xl font-bold"><?= number_format($total_hasil) ?> <span class="text-sm font-normal text-white/70">Tersedia</span></div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Bobot Saat Ini -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Bobot Kriteria AHP</h3>
            <a href="ahp.php" class="text-sm text-blue-500 hover:text-blue-700 font-semibold px-3 py-1.5 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">Ubah Bobot</a>
        </div>
        
        <?php if($ahp_configured > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach($bobot as $nama => $nilai): ?>
                <div class="text-center group">
                    <div class="relative inline-flex items-center justify-center mb-4">
                        <svg class="w-24 h-24 transform -rotate-90">
                            <circle cx="48" cy="48" r="36" stroke="currentColor" stroke-width="8" fill="transparent" class="text-gray-100" />
                            <circle cx="48" cy="48" r="36" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="<?= 2 * pi() * 36 ?>" stroke-dashoffset="<?= (2 * pi() * 36) * (1 - $nilai) ?>" class="text-blue-500 group-hover:text-blue-600 transition-all duration-1000 ease-out" />
                        </svg>
                        <span class="absolute text-xl font-bold text-gray-700"><?= number_format($nilai * 100, 1) ?>%</span>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-1"><?= $nama ?></h4>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h4 class="text-gray-700 font-semibold mb-1">Bobot AHP Kosong</h4>
                <p class="text-gray-500 text-sm">Silakan atur bobot terlebih dahulu untuk menggunakan sistem.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Quick Actions Vertical Responsive -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 lg:p-8">
    
    <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">
        Aksi Cepat
    </h3>

    <div class="relative space-y-5 
        before:absolute before:left-5 before:top-0 before:h-full 
        before:w-0.5 before:bg-gradient-to-b 
        before:from-transparent before:via-slate-300 before:to-transparent">

        <!-- Upload Data -->
        <div class="relative flex items-start gap-4 group">
            
            <div class="flex items-center justify-center 
                w-10 h-10 rounded-full border-4 border-white 
                bg-blue-500 text-white shadow shrink-0 z-10">
                <i class="fas fa-upload text-sm"></i>
            </div>

            <div class="flex-1">
                <a href="upload.php"
                   class="block p-4 rounded-xl border border-slate-200 
                          bg-white shadow-sm transition-all 
                          hover:shadow-md hover:border-blue-300">

                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-bold text-slate-900 text-sm sm:text-base">
                            Upload Data
                        </h4>
                    </div>

                    <p class="text-slate-500 text-xs sm:text-sm">
                        Import file CSV pendaftar
                    </p>
                </a>
            </div>

        </div>

        <!-- Proses Ranking -->
        <div class="relative flex items-start gap-4 group">
            
            <div class="flex items-center justify-center 
                w-10 h-10 rounded-full border-4 border-white 
                bg-emerald-500 text-white shadow shrink-0 z-10">
                <i class="fas fa-cogs text-sm"></i>
            </div>

            <div class="flex-1">
                <a href="proses_saw.php"
                   onclick="return confirm('Mulai proses perhitungan?')"
                   class="block p-4 rounded-xl border border-slate-200 
                          bg-white shadow-sm transition-all 
                          hover:shadow-md hover:border-emerald-300
                          <?= ($total_data == 0 || $ahp_configured == 0) ? 'opacity-50 pointer-events-none' : '' ?>">

                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-bold text-slate-900 text-sm sm:text-base">
                            Proses Ranking
                        </h4>
                    </div>

                    <p class="text-slate-500 text-xs sm:text-sm">
                        Jalankan SAW & ML
                    </p>
                </a>
            </div>

        </div>

        <!-- Lihat Hasil -->
        <div class="relative flex items-start gap-4 group">
            
            <div class="flex items-center justify-center 
                w-10 h-10 rounded-full border-4 border-white 
                bg-amber-500 text-white shadow shrink-0 z-10">
                <i class="fas fa-trophy text-sm"></i>
            </div>

            <div class="flex-1">
                <a href="hasil.php"
                   class="block p-4 rounded-xl border border-slate-200 
                          bg-white shadow-sm transition-all 
                          hover:shadow-md hover:border-amber-300
                          <?= ($total_hasil == 0) ? 'opacity-50 pointer-events-none' : '' ?>">

                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-bold text-slate-900 text-sm sm:text-base">
                            Lihat Hasil
                        </h4>
                    </div>

                    <p class="text-slate-500 text-xs sm:text-sm">
                        Tabel & rekomendasi
                    </p>
                </a>
            </div>

        </div>

    </div>

</div>
</div>

<style>
/* Animations used */
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.4s ease forwards; }
</style>

<?php include 'footer.php'; ?>