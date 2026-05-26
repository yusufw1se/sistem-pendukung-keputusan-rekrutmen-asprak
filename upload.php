<?php
$page_title = 'Upload Data CSV';
$active_page = 'upload';
include 'koneksi.php';

$total_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alternatif"))['total'] ?? 0;
$status_msg = $_GET['status'] ?? '';

include 'header.php';
?>

<?php if($status_msg === 'sukses'): ?>
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3 shadow-sm animate-fade-in">
    <i class="fas fa-check-circle text-xl"></i>
    <span>Data berhasil diimport! Total <b><?= $total_data ?></b> data pendaftar dalam database.</span>
</div>
<?php elseif($status_msg === 'error'): ?>
<div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3 shadow-sm animate-fade-in">
    <i class="fas fa-times-circle text-xl"></i>
    <span>Gagal mengimport data. Pastikan format CSV benar.</span>
</div>
<?php elseif($status_msg === 'cleared'): ?>
<div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center gap-3 shadow-sm animate-fade-in">
    <i class="fas fa-info-circle text-xl"></i>
    <span>Semua data pendaftar dan hasil telah dihapus.</span>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8 mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-1">Import Data CSV</h2>
    <p class="text-sm text-gray-500 mb-8">Unggah file CSV yang berisi data pendaftar asisten laboratorium.</p>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">

    <!-- Form Upload -->
    <div class="flex">
        <form action="proses_import.php" 
              method="POST" 
              enctype="multipart/form-data" 
              id="uploadForm" 
              class="flex flex-col w-full">

            <!-- Upload Card -->
            <div id="dropZone"
                 class="border-2 border-dashed border-gray-300 
                        hover:border-blue-500 hover:bg-blue-50 
                        rounded-2xl p-6 sm:p-8 lg:p-10 
                        text-center transition-colors 
                        flex-1 flex flex-col justify-center items-center 
                        relative cursor-pointer min-h-[320px]">

                <i class="fas fa-cloud-upload-alt text-4xl sm:text-5xl text-gray-400 mb-4 dropZoneIcon"></i>

                <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-1">
                    Drag & Drop file di sini
                </h3>

                <p class="text-sm text-gray-500 mb-4 px-2">
                    atau klik untuk menelusuri file komputer Anda (.csv)
                </p>

                <input type="file" 
                       name="file_csv" 
                       accept=".csv" 
                       required 
                       id="fileInput"
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                <div id="fileInfo"
                     class="hidden mt-4 px-4 py-2 
                            bg-blue-100 text-blue-800 
                            rounded-lg text-sm font-medium 
                            items-center gap-2 break-all">

                    <i class="fas fa-file-csv"></i>
                    <span id="fileName"></span>

                </div>
            </div>

            <!-- Button -->
            <div class="mt-5 flex flex-col sm:flex-row sm:justify-end">
                <button type="submit"
                        name="submit"
                        id="uploadBtn"
                        disabled
                        class="w-full sm:w-auto 
                               px-6 py-3 
                               bg-blue-500 hover:bg-blue-600 
                               disabled:bg-gray-300 disabled:cursor-not-allowed 
                               text-white rounded-xl 
                               text-sm font-semibold 
                               transition-all shadow-sm">

                    <i class="fas fa-upload mr-2"></i>
                    Proses Import

                </button>
            </div>

        </form>
    </div>

    <!-- Format Info -->
    <div class="bg-slate-50 rounded-2xl p-5 sm:p-6 border border-slate-200 h-full">

        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-500"></i>
            Format yang Diharapkan
        </h3>

        <p class="text-sm text-gray-600 mb-4">
            Pastikan baris pertama adalah <i>header</i> dan urutan kolom persis seperti berikut:
        </p>

        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">

            <table class="w-full text-sm text-left min-w-[500px]">

                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 font-semibold border-b">Kolom</th>
                        <th class="px-4 py-2 font-semibold border-b">Contoh Isi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="px-4 py-2 font-medium">NIM</td>
                        <td class="px-4 py-2 text-slate-500">2451507001...</td>
                    </tr>

                    <tr>
                        <td class="px-4 py-2 font-medium">Mata Kuliah Praktikum</td>
                        <td class="px-4 py-2 text-slate-500">PEMROGRAMAN LANJUT-SI</td>
                    </tr>

                    <tr>
                        <td class="px-4 py-2 font-medium">NAMA</td>
                        <td class="px-4 py-2 text-slate-500">John Doe</td>
                    </tr>

                    <tr>
                        <td class="px-4 py-2 font-medium">IPK</td>
                        <td class="px-4 py-2 text-slate-500">3.85</td>
                    </tr>

                    <tr>
                        <td class="px-4 py-2 font-medium">Nilai KHS Mata Kuliah Syarat</td>
                        <td class="px-4 py-2 text-slate-500">4.00</td>
                    </tr>

                    <tr>
                        <td class="px-4 py-2 font-medium">Nilai Praktikum Mata Kuliah Syarat</td>
                        <td class="px-4 py-2 text-slate-500">92.19</td>
                    </tr>
                </tbody>

            </table>

        </div>

    </div>

</div>
</div>

<?php if($total_data > 0): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800">Preview Data Tersimpan <span class="text-sm font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded-md ml-2"><?= number_format($total_data) ?> total</span></h3>
        <div class="flex gap-2">
            <a href="clear_data.php" onclick="return confirm('Hapus SEMUA data pendaftar?')" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-trash-alt mr-1"></i> Kosongkan
            </a>
            <a href="ahp.php" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                Lanjut AHP <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-4 font-semibold">NIM</th>
                    <th class="px-6 py-4 font-semibold">Nama</th>
                    <th class="px-6 py-4 font-semibold">Mata Kuliah Praktikum</th>
                    <th class="px-6 py-4 font-semibold">IPK</th>
                    <th class="px-6 py-4 font-semibold">Nilai KHS Mata Kuliah Syarat</th>
                    <th class="px-6 py-4 font-semibold">Nilai Praktikum Mata Kuliah Syarat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                $preview = mysqli_query($conn, "SELECT * FROM alternatif ORDER BY id_alternatif LIMIT 10");
                while($row = mysqli_fetch_assoc($preview)):
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-gray-700"><?= htmlspecialchars($row['NIM']) ?></td>
                    <td class="px-6 py-3 font-medium text-gray-900"><?= htmlspecialchars($row['NAMA']) ?></td>
                    <td class="px-6 py-3 text-gray-500 truncate max-w-xs" title="<?= htmlspecialchars($row['Mata Kuliah Praktikum']) ?>"><?= htmlspecialchars($row['Mata Kuliah Praktikum']) ?></td>
                    <td class="px-6 py-3 text-gray-700"><?= $row['IPK'] ?></td>
                    <td class="px-6 py-3 text-gray-700"><?= $row['Nilai KHS Mata Kuliah Syarat'] ?></td>
                    <td class="px-6 py-3 text-gray-700"><?= $row['Nilai Praktikum Mata Kuliah Syarat'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php if($total_data > 10): ?>
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-center text-sm text-gray-500 font-medium">
        Menampilkan 10 baris pertama dari <?= number_format($total_data) ?> data.
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.4s ease forwards; }
.dragover { background-color: #eff6ff; border-color: #3b82f6; }
.dragover .dropZoneIcon { color: #3b82f6; }
</style>
<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const uploadBtn = document.getElementById('uploadBtn');

    ['dragenter','dragover'].forEach(e => {
        dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('dragover'); });
    });
    ['dragleave','drop'].forEach(e => {
        dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.remove('dragover'); });
    });
    dropZone.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if(files.length) { fileInput.files = files; showFileInfo(files[0]); }
    });
    fileInput.addEventListener('change', e => {
        if(e.target.files.length) showFileInfo(e.target.files[0]);
    });
    function showFileInfo(file) {
        fileInfo.classList.remove('hidden');
        fileInfo.classList.add('flex');
        fileName.textContent = `${file.name} (${(file.size/1024).toFixed(1)} KB)`;
        uploadBtn.disabled = false;
    }
</script>

<?php include 'footer.php'; ?>
