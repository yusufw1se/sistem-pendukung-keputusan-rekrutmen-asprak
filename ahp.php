<?php
$page_title = 'Bobot AHP';
$active_page = 'ahp';
include 'koneksi.php';

$last_ahp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM ahp_log ORDER BY id DESC LIMIT 1"));
$status_msg = $_GET['status'] ?? '';

include 'header.php';

// Helper function untuk generate radio buttons HTML
function generateRadioGroup($name, $label1, $label2) {
    $html = '<div class="flex flex-col items-center p-6 bg-slate-50 rounded-2xl border border-slate-200">';
    $html .= '<h4 class="text-sm font-semibold text-slate-500 mb-6 tracking-wide">Pilih mana yang lebih penting</h4>';
    
    $html .= '<div class="flex flex-col md:flex-row items-center w-full gap-4 md:gap-2 justify-between max-w-3xl">';
    
    // Label Kiri
    $html .= '<div class="font-bold text-slate-800 text-center md:text-right w-32 shrink-0">'.$label1.'</div>';
    
    // Radio Group
    $html .= '<div class="flex items-center justify-center gap-1 sm:gap-2 flex-wrap flex-1">';
    
    // Skala AHP custom map: Kita kirim value 17,15,13.. untuk kiri dan 1,3,5.. untuk kanan
    // 9 (17), 7 (15), 5 (13), 3 (11), 1 (9), 3 (7), 5 (5), 7 (3), 9 (1)
    $options = [
        ['val'=>17, 'text'=>'9'], ['val'=>15, 'text'=>'7'], ['val'=>13, 'text'=>'5'], ['val'=>11, 'text'=>'3'],
        ['val'=>9, 'text'=>'1', 'class'=>'bg-slate-200 font-bold'], 
        ['val'=>7, 'text'=>'3'], ['val'=>5, 'text'=>'5'], ['val'=>3, 'text'=>'7'], ['val'=>1, 'text'=>'9']
    ];
    
    foreach($options as $opt) {
        $val = $opt['val'];
        $text = $opt['text'];
        $extra = $opt['class'] ?? 'bg-white font-semibold shadow-sm border border-slate-200';
        $html .= '
        <label class="cursor-pointer relative group">
            <input type="radio" class="peer hidden ahp-radio" name="'.$name.'" value="'.$val.'" '.($val==9 ? 'checked' : '').'>
            <div class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-full text-slate-600 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 hover:bg-slate-100 transition-all text-xs sm:text-sm '.$extra.'">
                '.$text.'
            </div>
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-max px-2 py-1 bg-slate-800 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                '.($text == '1' ? 'Sama Penting' : 'Skala '.$text).'
            </div>
        </label>';
    }
    
    $html .= '</div>';
    
    // Label Kanan
    $html .= '<div class="font-bold text-slate-800 text-center md:text-left w-32 shrink-0">'.$label2.'</div>';
    
    $html .= '</div></div>';
    return $html;
}
?>

<?php if($status_msg === 'sukses'): ?>
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3 shadow-sm animate-fade-in">
    <i class="fas fa-check-circle text-xl"></i>
    <span>Bobot AHP berhasil dihitung dan disimpan! Matriks perbandingan <b>Konsisten</b>.</span>
</div>
<?php elseif($status_msg === 'inconsistent'): ?>
<div class="mb-6 p-4 rounded-xl bg-orange-50 border border-orange-200 text-orange-700 flex items-center gap-3 shadow-sm animate-fade-in">
    <i class="fas fa-exclamation-triangle text-xl"></i>
    <span>Bobot tersimpan, namun rasio konsistensi (CR) > 0.1. Pertimbangkan untuk menyesuaikan kembali perbandingan Anda.</span>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8 mb-8">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Konfigurasi Bobot AHP</h2>
            <p class="text-sm text-gray-500">Gunakan perbandingan di bawah untuk menentukan tingkat kepentingan antar kriteria.</p>
        </div>
        <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fas fa-lightbulb"></i> Tips: Nilai 9 berarti sangat mutlak lebih penting.
        </div>
    </div>

    <form action="proses_ahp.php" method="POST" id="ahpForm">
        <div class="space-y-6 mb-8">
            <?= generateRadioGroup('c12', 'IPK', 'Nilai KHS') ?>
            <?= generateRadioGroup('c13', 'IPK', 'Nilai Praktikum') ?>
            <?= generateRadioGroup('c23', 'Nilai KHS', 'Nilai Praktikum') ?>
        </div>

        <!-- Matriks Preview & Action -->
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            <div class="flex-1 bg-slate-50 border border-slate-200 rounded-xl p-6 w-full">
                <h3 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wider">Preview Matriks</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-center">
                        <thead class="text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="py-2 text-left">Kriteria</th>
                                <th class="py-2">IPK</th>
                                <th class="py-2">Nilai KHS</th>
                                <th class="py-2">Nilai Praktikum</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-700 font-medium divide-y divide-slate-100">
                            <tr><td class="py-3 text-left">IPK</td><td class="py-3 bg-white">1</td><td class="py-3" id="m12">1</td><td class="py-3" id="m13">1</td></tr>
                            <tr><td class="py-3 text-left">Nilai KHS</td><td class="py-3" id="m21">1</td><td class="py-3 bg-white">1</td><td class="py-3" id="m23">1</td></tr>
                            <tr><td class="py-3 text-left">Nilai Praktikum</td><td class="py-3" id="m31">1</td><td class="py-3" id="m32">1</td><td class="py-3 bg-white">1</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="w-full lg:w-64 shrink-0 flex flex-col justify-center gap-3">
                <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/30 transition-all hover:-translate-y-0.5 text-sm">
                    Simpan Bobot
                </button>
                <button type="button" onclick="document.getElementById('ahpForm').reset(); updateAHP();" class="w-full py-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors text-sm">
                    Reset Pilihan
                </button>
            </div>
        </div>
    </form>
</div>

<?php if($last_ahp): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">Riwayat Tersimpan</h3>
        <?php if($last_ahp['is_consistent']): ?>
            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Konsisten</span>
        <?php else: ?>
            <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Tidak Konsisten</span>
        <?php endif; ?>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <?php
        $bobot_json = json_decode($last_ahp['bobot_prioritas'], true);
        if($bobot_json):
            $labels = ['IPK', 'Nilai KHS', 'Nilai Praktikum'];
            foreach($bobot_json as $i => $b):
        ?>
        <div class="flex flex-col bg-slate-50 p-4 rounded-xl border border-slate-100 items-center justify-center">
            <span class="text-sm font-semibold text-slate-500 mb-1"><?= $labels[$i] ?></span>
            <span class="text-3xl font-black text-blue-600"><?= number_format($b * 100, 1) ?>%</span>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div>
<?php endif; ?>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.4s ease forwards; }
</style>
<script>
    function sliderToAHP(val) {
        val = parseInt(val);
        const map = [null, 1/9, 1/8, 1/7, 1/6, 1/5, 1/4, 1/3, 1/2, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        return map[val] || 1;
    }
    function formatAHP(val) {
        if(val >= 1) return val.toFixed(0);
        return '1/' + (1/val).toFixed(0);
    }
    function updateAHP() {
        const c12 = sliderToAHP(document.querySelector('input[name="c12"]:checked').value);
        const c13 = sliderToAHP(document.querySelector('input[name="c13"]:checked').value);
        const c23 = sliderToAHP(document.querySelector('input[name="c23"]:checked').value);

        document.getElementById('m12').textContent = formatAHP(c12);
        document.getElementById('m13').textContent = formatAHP(c13);
        document.getElementById('m21').textContent = formatAHP(1/c12);
        document.getElementById('m23').textContent = formatAHP(c23);
        document.getElementById('m31').textContent = formatAHP(1/c13);
        document.getElementById('m32').textContent = formatAHP(1/c23);
    }
    
    // Attach listener
    document.querySelectorAll('.ahp-radio').forEach(r => {
        r.addEventListener('change', updateAHP);
    });
    // Init
    updateAHP();
</script>

<?php include 'footer.php'; ?>
