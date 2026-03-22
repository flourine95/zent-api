<?php

namespace App\Domain\Product\DataTransferObjects;

final readonly class UpdateProductData
{
    public function __construct(
        public string $id,
        public string $categoryId,
        public string $name,
        public string $slug,
        public ?string $description,
        public ?string $thumbnail,
        public ?array $specs,
        public bool $isActive,
    ) {}

    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
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
