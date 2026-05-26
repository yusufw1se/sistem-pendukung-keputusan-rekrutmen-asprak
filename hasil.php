<?php
$page_title = 'Hasil Perangkingan';
$active_page = 'hasil';
include 'koneksi.php';

// Ambil list Mata Kuliah untuk filter
$mk_list = [];
$q_mk = mysqli_query($conn, "SELECT DISTINCT `Mata Kuliah Praktikum` FROM alternatif ORDER BY `Mata Kuliah Praktikum`");
while($r = mysqli_fetch_assoc($q_mk)) {
    $mk_list[] = $r['Mata Kuliah Praktikum'];
}

$filter_mk = $_GET['mk'] ?? '';

// Build Query
$sql = "SELECT h.*, a.NIM, a.NAMA, a.`Mata Kuliah Praktikum`, a.IPK, a.`Nilai KHS Mata Kuliah Syarat`, a.`Nilai Praktikum Mata Kuliah Syarat` 
        FROM hasil h 
        JOIN alternatif a ON h.id_alternatif = a.id_alternatif";
if(!empty($filter_mk)) {
    $sql .= " WHERE a.`Mata Kuliah Praktikum` = '" . mysqli_real_escape_string($conn, $filter_mk) . "'";
}
$sql .= " ORDER BY h.skor_akhir DESC";
$query = mysqli_query($conn, $sql);

$has_results = mysqli_num_rows($query) > 0;

include 'header.php';
?>

<?php if(!$has_results && empty($filter_mk)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center max-w-2xl mx-auto mt-10">
        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-4xl text-gray-300 mx-auto mb-6">
            <i class="fas fa-inbox"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Hasil</h3>
        <p class="text-gray-500 mb-8">Silakan proses data terlebih dahulu pada halaman dashboard atau pastikan data alternatif sudah ada.</p>
        <a href="proses_saw.php" class="inline-flex px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-md transition-all" onclick="return confirm('Mulai proses perhitungan?')">
            <i class="fas fa-play mr-2 mt-1"></i> Mulai Proses Ranking
        </a>
    </div>
<?php else: ?>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Tabel Hasil Keputusan</h2>
            <p class="text-sm text-gray-500">Menampilkan <?= mysqli_num_rows($query) ?> kandidat teratas.</p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Filter Button -->
            <button type="button" onclick="document.getElementById('filterModal').classList.remove('hidden')" class="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center justify-between gap-3 shadow-sm text-sm font-medium transition-colors">
                <span class="truncate max-w-[150px] sm:max-w-[200px]">
                    <?= empty($filter_mk) ? 'Semua Mata Kuliah' : htmlspecialchars($filter_mk) ?>
                </span>
                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </button>
            <a href="export_excel.php?mk=<?= urlencode($filter_mk) ?>" class="shrink-0 flex items-center justify-center w-10 h-10 bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white rounded-lg transition-colors border border-emerald-100 shadow-sm" title="Export to Excel">
                <i class="fas fa-file-excel text-lg"></i>
            </a>
        </div>
    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-xl overflow-hidden flex flex-col max-h-[80vh]">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-gray-800"><i class="fas fa-filter text-blue-500 mr-2"></i>Filter Mata Kuliah</h3>
                <button type="button" onclick="document.getElementById('filterModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition-colors"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-2 overflow-y-auto flex-1 space-y-1">
                <a href="hasil.php" class="block p-3 rounded-xl text-sm transition-colors <?= empty($filter_mk) ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50' ?>">
                    <i class="fas fa-list mr-2 opacity-50"></i> Semua Mata Kuliah
                </a>
                <?php foreach($mk_list as $mk): ?>
                    <a href="hasil.php?mk=<?= urlencode($mk) ?>" class="block p-3 rounded-xl text-sm transition-colors <?= ($filter_mk === $mk) ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <?= htmlspecialchars($mk) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Rank</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Kandidat</th>
                        <?php if(empty($filter_mk)): ?><th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Mata Kuliah Praktikum</th><?php endif; ?>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Skor Total (SAW)</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Prediksi Kelayakan (AI)</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Rekomendasi Keputusan Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php 
                    $rank = 1;
                    while($row = mysqli_fetch_assoc($query)): 
                        $is_layak = ($row['prediksi_ai'] === 'Layak');
                        $confidence = $row['confidence'] * 100;
                        $is_top_rejected = (!$is_layak && $rank <= 3);
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors <?= $is_top_rejected ? 'bg-amber-50/30' : '' ?>">
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                <?= $rank == 1 ? 'bg-amber-100 text-amber-600' : ($rank == 2 ? 'bg-slate-200 text-slate-600' : ($rank == 3 ? 'bg-orange-100 text-orange-600' : 'bg-transparent text-slate-500')) ?>">
                                <?= $rank ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800"><?= htmlspecialchars($row['NAMA']) ?></div>
                            <div class="text-xs text-slate-500"><?= htmlspecialchars($row['NIM']) ?></div>
                        </td>
                        <?php if(empty($filter_mk)): ?>
                        <td class="px-6 py-4">
                            <div class="text-slate-600 truncate max-w-[200px]" title="<?= htmlspecialchars($row['Mata Kuliah Praktikum']) ?>">
                                <?= htmlspecialchars($row['Mata Kuliah Praktikum']) ?>
                            </div>
                        </td>
                        <?php endif; ?>
                        <td class="px-6 py-4">
                            <span class="font-mono text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded">
                                <?= number_format($row['skor_akhir'], 4) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="font-bold <?= $is_layak ? 'text-emerald-600' : 'text-rose-500' ?> text-xs uppercase tracking-wide">
                                    <?= $row['prediksi_ai'] ?>
                                </span>
                                <?php if($confidence > 0): ?>
                                <div class="w-16 h-1.5 bg-slate-200 rounded-full mt-1.5 overflow-hidden" title="Confidence: <?= number_format($confidence,1) ?>%">
                                    <div class="h-full <?= $is_layak ? 'bg-emerald-500' : 'bg-rose-500' ?>" style="width: <?= $confidence ?>%"></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if($is_layak): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    Diterima
                                </span>
                            <?php elseif($is_top_rejected): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200" title="Skor tinggi tapi pola ditolak AI">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Tinjau
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-600">
                                    Gugur
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $rank++; endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transparansi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-slate-900 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-10 text-8xl text-blue-500 group-hover:scale-110 transition-transform"><i class="fas fa-calculator"></i></div>
            <h4 class="font-bold text-white flex items-center gap-2 mb-3">
                <span class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center"><i class="fas fa-sort-numeric-up"></i></span>
                Penghitungan SAW
            </h4>
            <p class="text-sm text-gray-400 leading-relaxed mb-4">
                Simple Additive Weighting (SAW) mencari nilai proporsional setiap kandidat berdasarkan bobot AHP. Ini menjamin urutan ranking terbaik berdasar akumulasi nilai akademis murni (IPK, KHS, Prak).
            </p>
            <div class="bg-black/30 rounded-xl p-4 border border-white/5 font-mono text-xs text-blue-300">
                <div class="mb-2"><span class="text-gray-500"># 1. Normalisasi (Semua Benefit)</span><br>R_ij = X_ij / Max(X_j)</div>
                <div><span class="text-gray-500"># 2. Skor Akhir (Ranking)</span><br>V_i = Σ (W_j * R_ij)</div>
            </div>
        </div>
        <div class="bg-slate-900 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-10 text-8xl text-emerald-500 group-hover:scale-110 transition-transform"><i class="fas fa-brain"></i></div>
            <h4 class="font-bold text-white flex items-center gap-2 mb-3">
                <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center"><i class="fas fa-network-wired"></i></span>
                Klasifikasi ML
            </h4>
            <p class="text-sm text-gray-400 leading-relaxed mb-4">
                Random Forest ML memeriksa pola dari dataset history asisten. Bertujuan untuk menolak kandidat dengan nilai jelek namun tak sengaja ber-ranking tinggi karena sedikitnya pendaftar.
            </p>
            <div class="bg-black/30 rounded-xl p-4 border border-white/5 font-mono text-xs text-emerald-300">
                <div class="mb-2"><span class="text-gray-500"># Ensemble Learning</span><br>N_Estimators = 100 Trees</div>
                <div><span class="text-gray-500"># Kelas Prediksi</span><br>1 = Layak (Confidence > 0.5)<br>0 = Tidak Layak</div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php include 'footer.php'; ?>
