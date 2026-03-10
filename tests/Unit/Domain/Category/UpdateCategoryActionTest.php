<?php

use App\Domain\Category\Actions\UpdateCategoryAction;
use App\Domain\Category\DataTransferObjects\UpdateCategoryData;
use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Exceptions\InvalidCategoryHierarchyException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

beforeEach(function () {
    $this->repository = Mockery::mock(CategoryRepositoryInterface::class);
    $this->action = new UpdateCategoryAction($this->repository);
});

afterEach(function () {
    Mockery::close();
});

test('updates category successfully', function () {
    $data = new UpdateCategoryData(
        id: 1,
        name: 'Electronics Updated',
        slug: 'electronics-updated',
        description: 'Updated description',
        parentId: null,
        image: null,
        isVisible: true
    );

    $expectedResult = [
        'id' => 1,
        'name' => 'Electronics Updated',
        'slug' => 'electronics-updated',
        'description' => 'Updated description',
        'parent_id' => null,
        'image' => null,
        'is_visible' => true,
    ];

    $this->repository
        ->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(true);

    $this->repository
        ->shouldReceive('update')
        ->once()
        ->with(1, $data->toArray())
        ->andReturn($expectedResult);

    $result = $this->action->execute($data);

    expect($result)->toBe($expectedResult);
});

test('throws exception when category does not exist', function () {
    $data = new UpdateCategoryData(
        id: 999,
        name: 'Non-existent',
        slug: 'non-existent',
        description: null,
        parentId: null,
        image: null,
        isVisible: true
    );

    $this->repository
        ->shouldReceive('exists')
        ->once()
        ->with(999)
        ->andReturn(false);

    $this->action->execute($data);
})->throws(CategoryNotFoundException::class);

test('throws exception when trying to set self as parent', function () {
    $data = new UpdateCategoryData(
        id: 1,
        name: 'Electronics',
        slug: 'electronics',
        description: null,
        parentId: 1,
        image: null,
        isVisible: true
    );

    $this->repository
        ->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(true);

    $this->action->execute($data);
})->throws(InvalidCategoryHierarchyException::class);

test('throws exception when parent does not exist', function () {
    $data = new UpdateCategoryData(
        id: 1,
        name: 'Electronics',
        slug: 'electronics',
        description: null,
        parentId: 999,
        image: null,
        isVisible: true
    );

    $this->repository
        ->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(true);

    $this->repository
        ->shouldReceive('exists')
        ->once()
        ->with(999)
        ->andReturn(false);

    $this->action->execute($data);
})->throws(CategoryNotFoundException::class);

test('throws exception when creating circular reference', function () {
    $data = new UpdateCategoryData(
        id: 1,
        name: 'Electronics',
        slug: 'electronics',
        description: null,
        parentId: 2,
        image: null,
        isVisible: true
    );

    $this->repository
        ->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(true);

    $this->repository
        ->shouldReceive('exists')
        ->once()
        ->with(2)
        ->andReturn(true);

    $this->repository
        ->shouldReceive('isDescendantOf')
        ->once()
        ->with(2, 1)
        ->andReturn(true);

    $this->action->execute($data);
})->throws(InvalidCategoryHierarchyException::class);
