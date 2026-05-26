<?php
// header.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'SPK Asisten Lab' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { dark: '#1e1e2d', light: '#f3f4f7', card: '#ffffff', text: '#3f4254', muted: '#b5b5c3' },
                        primary: '#3699ff', success: '#1bc5bd', danger: '#f64e60', warning: '#ffa800', info: '#8950fc'
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f3f4f7; color: #3f4254; font-family: 'Inter', sans-serif; }
        .sidebar { transition: transform 0.3s ease; }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
        }
        
        /* Custom Scrollbar for a cleaner look */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="flex h-screen overflow-hidden bg-brand-light">

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-[#252839] text-white flex flex-col lg:relative lg:translate-x-0 shadow-xl">
        <div class="flex items-center justify-between h-16 px-6 bg-[#1f2230]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold">
                    <i class="fas fa-microscope"></i>
                </div>
                <span class="text-sm font-bold tracking-wider uppercase text-white">SPK Asisten</span>
            </div>
            <button id="closeSidebar" class="lg:hidden text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="px-6 py-4 text-xs font-semibold text-gray-500 tracking-wider uppercase">Menu Utama</div>
        
        <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= ($active_page ?? '') == 'dashboard' ? 'bg-blue-500/10 text-blue-400' : 'text-gray-400 hover:text-white hover:bg-white/5' ?> transition-colors">
                <i class="fas fa-home w-5 text-center"></i> <span class="font-medium text-sm">Dashboard</span>
            </a>
            <a href="upload.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= ($active_page ?? '') == 'upload' ? 'bg-blue-500/10 text-blue-400' : 'text-gray-400 hover:text-white hover:bg-white/5' ?> transition-colors">
                <i class="fas fa-file-upload w-5 text-center"></i> <span class="font-medium text-sm">Upload Data</span>
            </a>
            <a href="ahp.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= ($active_page ?? '') == 'ahp' ? 'bg-blue-500/10 text-blue-400' : 'text-gray-400 hover:text-white hover:bg-white/5' ?> transition-colors">
                <i class="fas fa-balance-scale w-5 text-center"></i> <span class="font-medium text-sm">Bobot AHP</span>
            </a>
            
            <div class="px-2 py-4 mt-4 text-xs font-semibold text-gray-500 tracking-wider uppercase">Laporan</div>
            
            <a href="hasil.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= ($active_page ?? '') == 'hasil' ? 'bg-blue-500/10 text-blue-400' : 'text-gray-400 hover:text-white hover:bg-white/5' ?> transition-colors">
                <i class="fas fa-chart-bar w-5 text-center"></i> <span class="font-medium text-sm">Hasil Ranking</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Topbar -->
        <header class="flex items-center justify-between h-16 px-6 bg-white shadow-sm z-40 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <button id="openSidebar" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-semibold text-gray-800"><?= $page_title ?? 'Dashboard' ?></h1>
            </div>
            <!-- <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <div class="text-sm font-semibold text-gray-700">Administrator</div>
                    <div class="text-xs text-gray-500">Sistem SPK</div>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold border border-blue-200">
                    A
                </div>
            </div> -->
        </header>

        <!-- Main Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-brand-light p-4 md:p-6 lg:p-8">
