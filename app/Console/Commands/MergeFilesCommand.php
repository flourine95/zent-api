<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class MergeFilesCommand extends Command
{
    // Thêm dấu ? để biến tham số thành KHÔNG BẮT BUỘC
    protected $signature = 'file:merge
                            {folder? : Thư mục cần quét (Mặc định là toàn bộ project)}
                            {output? : File kết quả (Mặc định: all_code.txt)}';

    protected $description = 'Gộp toàn bộ code trong dự án (bao gồm thư mục con) thành 1 file';

    // Danh sách các thư mục BỎ QUA (để tránh file quá nặng)
    protected $ignoredFolders = [
        'vendor',
        'node_modules',
        '.git',
        '.idea',
        'storage',
        'public', // Thường chứa ảnh/assets nặng, bỏ qua nếu chỉ cần code
        'bootstrap/cache'
    ];

    // Danh sách đuôi file cần lấy (chỉ lấy code, bỏ qua ảnh/exe)
    protected $allowedExtensions = [
        'php', 'js', 'ts', 'vue', 'blade.php', 'html', 'css', 'scss', 'json', 'sql', 'env'
    ];

    public function handle()
    {
        // 1. Xử lý tham số mặc định
        // Nếu không nhập folder -> Lấy base_path() (Thư mục gốc dự án)
        $folderInput = $this->argument('folder') ?? '';
        $targetPath = base_path($folderInput);

        $outputFileName = $this->argument('output') ?? 'all_code.txt';
        $outputFilePath = base_path($outputFileName);

        if (!File::isDirectory($targetPath)) {
            $this->error("❌ Thư mục không tồn tại: $targetPath");
            return 1;
        }

        $this->info("📂 Đang quét: " . $targetPath);
        $this->info("🚫 Đang bỏ qua: " . implode(', ', $this->ignoredFolders));

        // 2. Lấy TẤT CẢ file (bao gồm thư mục con - Recursive)
        // Dùng allFiles thay vì files
        $allFiles = File::allFiles($targetPath);

        // 3. Lọc file (Bỏ vendor, node_modules và file không phải code)
        $filesToMerge = array_filter($allFiles, function (SplFileInfo $file) use ($outputFileName) {
            // A. Bỏ qua chính file output
            if ($file->getFilename() === $outputFileName) return false;

            // B. Kiểm tra xem file có nằm trong thư mục bị cấm không
            $relativePath = $file->getRelativePath();
            foreach ($this->ignoredFolders as $ignored) {
                // Nếu đường dẫn file bắt đầu bằng tên thư mục cấm (VD: vendor/...)
                if (str_starts_with($relativePath, $ignored) || str_starts_with($file->getPathname(), base_path($ignored))) {
                    return false;
                }
            }

            // C. Chỉ lấy các đuôi file cho phép (Code)
            if (!in_array($file->getExtension(), $this->allowedExtensions)) {
                return false;
            }

            return true;
        });

        if (empty($filesToMerge)) {
            $this->warn("⚠️ Không tìm thấy file code nào phù hợp.");
            return 0;
        }

        $totalFiles = count($filesToMerge);
        $this->info("✨ Tìm thấy $totalFiles file code. Đang gộp...");

        // 4. Ghi file (Dùng Stream)
        $handle = fopen($outputFilePath, 'w');

        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        foreach ($filesToMerge as $file) {
            // Header đẹp để AI hoặc người đọc dễ phân biệt
            $header  = "\n" . str_repeat('=', 50) . "\n";
            $header .= "FILE PATH: " . $file->getRelativePathname() . "\n";
            $header .= str_repeat('=', 50) . "\n";

            fwrite($handle, $header);

            // Đọc và ghi nội dung
            $fileHandle = fopen($file->getRealPath(), 'r');
            while (!feof($fileHandle)) {
                fwrite($handle, fread($fileHandle, 8192));
            }
            fclose($fileHandle);

            $bar->advance();
        }

        fclose($handle);
        $bar->finish();

        $this->newLine();
        $this->info("✅ XONG! File nằm tại: $outputFilePath");
    }
}
