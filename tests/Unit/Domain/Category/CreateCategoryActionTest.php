<?php

use App\Domain\Category\Actions\CreateCategoryAction;
use App\Domain\Category\DataTransferObjects\CreateCategoryData;
use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

beforeEach(function () {
    $this->repository = Mockery::mock(CategoryRepositoryInterface::class);
    $this->action = new CreateCategoryAction($this->repository);
});

afterEach(function () {
    Mockery::close();
});

test('creates category successfully', function () {
    $data = new CreateCategoryData(
        name: 'Electronics',
        slug: 'electronics',
        description: 'Electronic products',
        parentId: null,
        image: null,
        isVisible: true
    );

    $expectedResult = [
        'id' => 1,
        'name' => 'Electronics',
        'slug' => 'electronics',
        'description' => 'Electronic products',
        'parent_id' => null,
        'image' => null,
        'is_visible' => true,
    ];

    $this->repository
        ->shouldReceive('create')
        ->once()
        ->with($data->toArray())
        ->andReturn($expectedResult);

    $result = $this->action->execute($data);

    expect($result)->toBe($expectedResult);
});

test('throws exception when parent category does not exist', function () {
    $data = new CreateCategoryData(
        name: 'Laptops',
        slug: 'laptops',
        description: 'Laptop computers',
        parentId: 999,
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

test('creates category with valid parent', function () {
    $data = new CreateCategoryData(
        name: 'Laptops',
        slug: 'laptops',
        description: 'Laptop computers',
        parentId: 1,
        image: null,
        isVisible: true
    );

    $expectedResult = [
        'id' => 2,
        'name' => 'Laptops',
        'slug' => 'laptops',
        'description' => 'Laptop computers',
        'parent_id' => 1,
        'image' => null,
        'is_visible' => true,
    ];

    $this->repository
        ->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(true);

    $this->repository
        ->shouldReceive('create')
        ->once()
        ->with($data->toArray())
        ->andReturn($expectedResult);

    $result = $this->action->execute($data);

    expect($result)->toBe($expectedResult);
});
