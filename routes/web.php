<?php

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route để test Category queries - xem kết quả trong Telescope
Route::get('/test-category', function () {
    echo "<style>body { font-family: system-ui; padding: 20px; } .box { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }</style>";
    echo "<h2>Testing Category Queries với Telescope</h2>";
    echo "<p><a href='/telescope/queries' target='_blank' style='color: blue;'>📊 Mở Telescope Queries →</a></p>";
    echo "<hr>";
    
    // Clear để đếm queries chính xác
    echo "<p><em>Tip: Mở Telescope trước, sau đó click vào từng test bên dưới để xem queries riêng biệt</em></p>";
    
    echo "<div class='box'>";
    echo "<h3>🔴 Test 1: WITHOUT chaperone (N+1 problem)</h3>";
    echo "<p><a href='/test-without-chaperone' target='_blank' style='color: red;'>Click để chạy test này →</a></p>";
    echo "<p>Khi loop qua children và access parent, mỗi child sẽ trigger 1 query riêng!</p>";
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h3>✅ Test 2: WITH chaperone (No N+1)</h3>";
    echo "<p><a href='/test-with-chaperone' target='_blank' style='color: green;'>Click để chạy test này →</a></p>";
    echo "<p>Nhờ chaperone(), parent đã được hydrate sẵn, không cần query thêm!</p>";
    echo "</div>";
    
    echo "<hr>";
    echo "<h3>📖 Giải thích:</h3>";
    echo "<ul>";
    echo "<li><strong>N+1 Problem:</strong> 1 query lấy parent + N queries lấy parent của từng child</li>";
    echo "<li><strong>Chaperone:</strong> Tự động hydrate parent lên children khi eager load</li>";
    echo "<li><strong>Kết quả:</strong> Giảm từ N+1 queries xuống còn 2 queries</li>";
    echo "</ul>";
    
    return '';
});

// Test WITHOUT chaperone
Route::get('/test-without-chaperone', function () {
    echo "<h2>🔴 Test WITHOUT Chaperone</h2>";
    echo "<p><a href='/telescope/queries' target='_blank'>Xem queries trong Telescope →</a></p>";
    echo "<hr>";
    
    // Tìm category có children
    $category = Category::whereNull('parent_id')
        ->whereHas('children')
        ->first();
    
    if (!$category) {
        echo "<p style='color: red;'>Không tìm thấy category có children. Hãy tạo thêm categories!</p>";
        return '';
    }
    
    $children = $category->children()->withoutChaperone()->get();
    echo "<strong>Parent:</strong> {$category->name}<br><br>";
    echo "<strong>Children:</strong><br>";
    
    $count = 0;
    foreach ($children as $child) {
        $count++;
        // Mỗi lần access $child->parent sẽ trigger 1 query mới!
        echo "{$count}. {$child->name} → parent: {$child->parent->name}<br>";
    }
    
    echo "<hr>";
    echo "<p style='color: orange;'>⚠️ <strong>Queries:</strong> 1 (get parent) + 1 (get children) + {$count} (get parent của mỗi child) = <strong>" . (2 + $count) . " queries</strong></p>";
    echo "<p><em>Mỗi lần access \$child->parent đã trigger thêm 1 query!</em></p>";
    
    echo "<p><a href='/test-category'>← Quay lại</a> | <a href='/telescope/queries'>Xem Telescope</a></p>";
    return '';
});

// Test WITH chaperone
Route::get('/test-with-chaperone', function () {
    echo "<h2>✅ Test WITH Chaperone</h2>";
    echo "<p><a href='/telescope/queries' target='_blank'>Xem queries trong Telescope →</a></p>";
    echo "<hr>";
    
    // Tìm category có children
    $category = Category::with('children')
        ->whereNull('parent_id')
        ->whereHas('children')
        ->first();
    
    if (!$category) {
        echo "<p style='color: red;'>Không tìm thấy category có children. Hãy tạo thêm categories!</p>";
        return '';
    }
    
    echo "<strong>Parent:</strong> {$category->name}<br><br>";
    echo "<strong>Children:</strong><br>";
    
    $count = 0;
    foreach ($category->children as $child) {
        $count++;
        // Access $child->parent KHÔNG trigger query mới nhờ chaperone!
        echo "{$count}. {$child->name} → parent: {$child->parent->name}<br>";
    }
    
    echo "<hr>";
    echo "<p style='color: green;'>✅ <strong>Queries:</strong> 1 (get parent) + 1 (get children) = <strong>2 queries only!</strong></p>";
    echo "<p>Dù có {$count} children, vẫn chỉ cần 2 queries nhờ chaperone()</p>";
    echo "<p><em>Parent đã được hydrate sẵn lên children, không cần query thêm!</em></p>";
    
    echo "<p><a href='/test-category'>← Quay lại</a> | <a href='/telescope/queries'>Xem Telescope</a></p>";
    return '';
});
