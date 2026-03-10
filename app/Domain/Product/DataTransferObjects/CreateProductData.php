<?php

namespace App\Domain\Product\DataTransferObjects;

final readonly class CreateProductData
{
    public function __construct(
        public int $categoryId,
        public string $name,
        public string $slug,
        public ?string $description,
        public ?string $thumbnail,
        public ?array $specs,
        public bool $isActive,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            categoryId: $data['category_id'],
            name: $data['name'],
            slug: $data['slug'],
            description: $data['description'] ?? null,
            thumbnail: $data['thumbnail'] ?? null,
            specs: $data['specs'] ?? null,
            isActive: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'specs' => $this->specs,
            'is_active' => $this->isActive,
        ];
    }
}
