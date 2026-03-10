<?php

use App\Domain\Category\Actions\CreateCategoryAction;
use App\Domain\Category\Actions\DeleteCategoryAction;
use App\Domain\Category\Actions\UpdateCategoryAction;
use App\Domain\Category\DataTransferObjects\CreateCategoryData;
use App\Domain\Category\DataTransferObjects\UpdateCategoryData;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Infrastructure\Models\Category;

beforeEach(function () {
    $this->repository = app(CategoryRepositoryInterface::class);
    $this->createAction = app(CreateCategoryAction::class);
    $this->updateAction = app(UpdateCategoryAction::class);
    $this->deleteAction = app(DeleteCategoryAction::class);
});

test('full category lifecycle works correctly', function () {
    // Create parent category
    $parentData = new CreateCategoryData(
        name: 'Electronics',
        slug: 'electronics',
        description: 'Electronic products',
        parentId: null,
        image: null,
        isVisible: true
    );

    $parent = $this->createAction->execute($parentData);
    expect($parent['name'])->toBe('Electronics');

    // Create child category
    $childData = new CreateCategoryData(
        name: 'Laptops',
        slug: 'laptops',
        description: 'Laptop computers',
        parentId: $parent['id'],
        image: null,
        isVisible: true
    );

    $child = $this->createAction->execute($childData);
    expect($child['parent_id'])->toBe($parent['id']);

    // Update child category
    $updateData = new UpdateCategoryData(
        id: $child['id'],
        name: 'Gaming Laptops',
        slug: 'gaming-laptops',
        description: 'High-performance gaming laptops',
        parentId: $parent['id'],
        image: null,
        isVisible: true
    );

    $updated = $this->updateAction->execute($updateData);
    expect($updated['name'])->toBe('Gaming Laptops');

    // Delete child category
    $deleted = $this->deleteAction->execute($child['id']);
    expect($deleted)->toBeTrue();

    // Verify deletion
    $found = $this->repository->findById($child['id']);
    expect($found)->toBeNull();
});

test('repository tree method returns hierarchical structure', function () {
    // Create parent
    $parent = Category::factory()->create(['name' => 'Parent', 'parent_id' => null]);

    // Create children
    Category::factory()->create(['name' => 'Child 1', 'parent_id' => $parent->id]);
    Category::factory()->create(['name' => 'Child 2', 'parent_id' => $parent->id]);

    $tree = $this->repository->getTree();

    expect($tree)->toBeArray()
        ->and(count($tree))->toBeGreaterThan(0);
});

test('slug uniqueness is enforced', function () {
    $data1 = new CreateCategoryData(
        name: 'Electronics',
        slug: 'electronics',
        description: null,
        parentId: null,
        image: null,
        isVisible: true
    );

    $this->createAction->execute($data1);

    // Try to create another with same slug
    $data2 = new CreateCategoryData(
        name: 'Electronics 2',
        slug: 'electronics',
        description: null,
        parentId: null,
        image: null,
        isVisible: true
    );

    $this->createAction->execute($data2);
})->throws(Exception::class);
