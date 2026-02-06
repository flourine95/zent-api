<?php

use App\Models\Category;
use App\Models\Product;

describe('Product Rich Content', function () {
    beforeEach(function () {
        $this->artisan('migrate:fresh --seed');
    });

    it('can format rich content description', function () {
        $category = Category::first() ?? Category::factory()->create([
            'name' => ['vi' => 'Điện tử', 'en' => 'Electronics'],
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => ['vi' => 'iPhone 15', 'en' => 'iPhone 15'],
            'description' => [
                'vi' => '<p>Sản phẩm <strong>iPhone 15</strong> với nhiều tính năng mới.</p>',
                'en' => '<p>Product <strong>iPhone 15</strong> with many new features.</p>',
            ],
            'slug' => 'iphone-15-test',
            'is_active' => true,
        ]);

        $formattedVi = $product->getFormattedDescriptionAttribute('vi');
        $formattedEn = $product->getFormattedDescriptionAttribute('en');

        expect($formattedVi)
            ->toContain('iPhone 15')
            ->toContain('<strong>')
            ->toContain('<p>');

        expect($formattedEn)
            ->toContain('iPhone 15')
            ->toContain('<strong>')
            ->toContain('<p>');
    });

    it('handles empty description gracefully', function () {
        $product = Product::create([
            'name' => ['vi' => 'Test Product'],
            'description' => ['vi' => '', 'en' => ''],
            'slug' => 'test-empty-desc',
            'is_active' => true,
        ]);

        expect($product->getFormattedDescriptionAttribute('vi'))->toBe('');
        expect($product->getFormattedDescriptionAttribute('en'))->toBe('');
    });

    it('sanitizes HTML content for security', function () {
        $product = Product::create([
            'name' => ['vi' => 'Test Product'],
            'description' => [
                'vi' => '<p>Safe content</p><script>alert("xss")</script>',
            ],
            'slug' => 'test-html-content',
            'is_active' => true,
        ]);

        $formatted = $product->getFormattedDescriptionAttribute('vi');

        expect($formatted)
            ->toContain('Safe content')
            ->not->toContain('<script>')
            ->not->toContain('alert');
    });
});
