<?php
/**
 * SYSTEM DIAGNOSTIC TOOL
 * 
 * Gunakan file ini untuk mengecek konfigurasi sistem
 * dan debug masalah path/Python/Model
 * 
 * Akses: http://localhost/spk-asisten-praktikum/diagnostic.php
 */

require_once 'PATH_SYNC_CONFIG.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>SPK System Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        h1 { color: #333; border-bottom: 3px solid #0066cc; padding-bottom: 10px; }
        .section { background: white; margin: 20px 0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .status { display: inline-block; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .status.success { background: #4CAF50; color: white; }
        .status.warning { background: #ff9800; color: white; }
        .status.error { background: #f44336; color: white; }
        .info-row { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #eee; }
        .info-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #666; }
        .value { color: #333; word-break: break-all; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New'; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .solution { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔧 SPK System Diagnostic</h1>
    
    <?php
    $diagnostics = SystemDiagnostics::runDiagnostics();
    
    // ============ PHP INFO ============
    echo '<div class="section">';
    echo '<h2>📊 Informasi PHP</h2>';
    echo '<div class="info-row"><div class="label">PHP Version:</div><div class="value">' . $diagnostics['php_info']['version'] . '</div></div>';
    echo '<div class="info-row"><div class="label">Operating System:</div><div class="value">' . $diagnostics['php_info']['os'] . '</div></div>';
    echo '<div class="info-row"><div class="label">Base Directory:</div><div class="value"><code>' . $diagnostics['php_info']['base_dir'] . '</code></div></div>';
    echo '</div>';
    
    // ============ PATHS ============
    echo '<div class="section">';
    echo '<h2>📁 Path Configuration</h2>';
    foreach($diagnostics['paths'] as $key => $value) {
        $exists = isset($diagnostics['filesystem']['model_dir_exists']) || isset($diagnostics['filesystem']['scripts_dir_exists']);
        $status = '';
        if(strpos($key, '_dir') !== false) {
            $status = is_dir($value) ? '<span class="status success">✓ EXISTS</span>' : '<span class="status error">✗ NOT FOUND</span>';
        } elseif(strpos($key, '_path') !== false) {
            $status = file_exists($value) ? '<span class="status success">✓ EXISTS</span>' : '<span class="status error">✗ NOT FOUND</span>';
        }
        echo '<div class="info-row"><div class="label">' . ucwords(str_replace('_', ' ', $key)) . ':</div><div class="value"><code>' . $value . '</code> ' . $status . '</div></div>';
    }
    echo '</div>';
    
    // ============ FILESYSTEM CHECK ============
    echo '<div class="section">';
    echo '<h2>🗂️ Filesystem Check</h2>';
    foreach($diagnostics['filesystem'] as $key => $value) {
        $status = $value ? '<span class="status success">✓</span>' : '<span class="status error">✗</span>';
        echo '<div class="info-row"><div class="label">' . ucwords(str_replace('_', ' ', $key)) . ':</div><div class="value">' . $status . '</div></div>';
    }
    echo '</div>';
    
    // ============ PYTHON CHECK ============
    echo '<div class="section">';
    echo '<h2>🐍 Python Configuration</h2>';
    $python = $diagnostics['python'];
    $pythonStatus = $python['success'] ? 'success' : 'error';
    echo '<div class="info-row"><div class="label">Status:</div><div class="value"><span class="status ' . $pythonStatus . '">' . ($python['success'] ? '✓ Valid' : '✗ Invalid') . '</span></div></div>';
    echo '<div class="info-row"><div class="label">Message:</div><div class="value">' . htmlspecialchars($python['message']) . '</div></div>';
    if(isset($python['python_path']) && $python['python_path']) {
        echo '<div class="info-row"><div class="label">Python Path:</div><div class="value"><code>' . htmlspecialchars($python['python_path']) . '</code></div></div>';
    }
    if(isset($python['python_version']) && $python['python_version']) {
        echo '<div class="info-row"><div class="label">Python Version:</div><div class="value"><code>' . htmlspecialchars($python['python_version']) . '</code></div></div>';
    }
    
    // Show recommendation if not successful
    if(!$python['success']) {
        if(isset($python['recommendation'])) {
            echo '<div class="solution"><strong>Recommendation:</strong> ' . htmlspecialchars($python['recommendation']) . '</div>';
        }
        
        // Show quick fix commands
        echo '<div class="solution"><strong>Solusi Cepat:</strong><br>';
        echo '1. Download Python 3.7+ dari <a href="https://www.python.org/downloads/" target="_blank">https://www.python.org/downloads/</a><br>';
        echo '2. Saat install, CENTANG "Add Python to PATH" ✓<br>';
        echo '3. Restart Apache (XAMPP Control Panel)<br>';
        echo '4. Refresh halaman ini<br>';
        echo '<br><strong>Atau jika sudah install:</strong><br>';
        echo 'Buka Command Prompt & jalankan:<br>';
        echo '<code>python --version</code><br>';
        echo 'Jika berhasil, restart Apache dan refresh halaman ini.';
        echo '</div>';
    }
    
    if(isset($python['error']) && !empty($python['error'])) {
        echo '<div class="solution"><strong>Error Details:</strong><br><pre>' . htmlspecialchars(substr($python['error'], 0, 500)) . '</pre></div>';
    }
    
    if(isset($python['fix_command'])) {
        echo '<div class="solution"><strong>Install Dependencies:</strong><br>';
        echo 'Buka Command Prompt & jalankan:<br>';
        echo '<code>' . htmlspecialchars($python['fix_command']) . '</code>';
        echo '</div>';
    }
    
    echo '</div>';
    
    // ============ MODEL CHECK ============
    echo '<div class="section">';
    echo '<h2>🤖 Model Status</h2>';
    $model = $diagnostics['model'];
    $modelStatus = isset($model['valid']) && $model['valid'] ? 'success' : (isset($model['exists']) && $model['exists'] ? 'warning' : 'error');
    echo '<div class="info-row"><div class="label">Status:</div><div class="value"><span class="status ' . $modelStatus . '">' . ($model['valid'] ? '✓ Valid' : ($model['exists'] ? '⚠ Exists but Invalid' : '✗ Not Found')) . '</span></div></div>';
    echo '<div class="info-row"><div class="label">Message:</div><div class="value">' . $model['message'] . '</div></div>';
    echo '<div class="info-row"><div class="label">Path:</div><div class="value"><code>' . $model['path'] . '</code></div></div>';
    if(isset($model['size'])) {
        echo '<div class="info-row"><div class="label">Size:</div><div class="value">' . $model['size_kb'] . ' KB (' . $model['size'] . ' bytes)</div></div>';
    }
    if(isset($model['solution'])) {
        echo '<div class="solution"><strong>Solution:</strong> ' . $model['solution'] . '</div>';
    }
    echo '</div>';
    
    // ============ SUMMARY ============
    echo '<div class="section">';
    echo '<h2>✅ Summary</h2>';
    $allOk = $diagnostics['python']['success'] && ($diagnostics['model']['valid'] ?? false);
    if($allOk) {
        echo '<p style="color: #4CAF50; font-size: 18px;"><strong>✓ Sistem siap digunakan!</strong></p>';
        echo '<p>Semua komponen telah dikonfigurasi dengan benar. Anda dapat mulai menggunakan fitur prediksi AI.</p>';
    } else {
        echo '<p style="color: #f44336; font-size: 18px;"><strong>✗ Ada masalah yang perlu diperbaiki</strong></p>';
        if(!$diagnostics['python']['success']) {
            echo '<p>• Python atau dependencies tidak siap. Lihat section "Python Configuration" di atas.</p>';
        }
        if(!($diagnostics['model']['valid'] ?? false)) {
            echo '<p>• Model belum dilatih atau rusak. Lihat section "Model Status" di atas.</p>';
        }
    }
    echo '</div>';
    
    // ============ QUICK TEST ============
    if($allOk) {
        echo '<div class="section">';
        echo '<h2>🧪 Quick Test</h2>';
        echo '<p>Testing model prediction dengan sample data...</p>';
        
        // Create test data
        $testData = [
            ['id' => 'test_1', 'ipk' => 3.5, 'khs' => 85, 'prak' => 80],
            ['id' => 'test_2', 'ipk' => 3.0, 'khs' => 75, 'prak' => 70]
        ];
        
        $pythonPath = PythonDetector::getPythonPath();
        $scriptPath = PathConfig::getPredictScriptPath();
        $modelDir = PathConfig::getModelDir();
        
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        
        $cmd = escapeshellarg($pythonPath) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($modelDir);
        $process = proc_open($cmd, $descriptors, $pipes);
        
        if(is_resource($process)) {
            fwrite($pipes[0], json_encode($testData));
            fclose($pipes[0]);
            
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            
            proc_close($process);
            
            if(!empty($output)) {
                echo '<p><strong>✓ Test Successful!</strong></p>';
                echo '<pre>' . htmlspecialchars($output) . '</pre>';
            } elseif(!empty($errors)) {
                echo '<p><strong>✗ Test Failed!</strong></p>';
                echo '<pre>' . htmlspecialchars($errors) . '</pre>';
            } else {
                echo '<p><strong>⚠ No output</strong></p>';
            }
        } else {
            echo '<p><strong>✗ Could not execute Python script</strong></p>';
        }
        
        echo '</div>';
    }
    ?>
    
</body>
</html>
