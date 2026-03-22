<?php

namespace App\Domain\Category\DataTransferObjects;

final readonly class UpdateCategoryData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public ?string $parentId,
        public ?string $image,
        public bool $isVisible,
    ) {}

    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            name: $data['name'],
            slug: $data['slug'],
            description: $data['description'] ?? null,
            parentId: $data['parent_id'] ?? null,
            image: $data['image'] ?? null,
            isVisible: $data['is_visible'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'image' => $this->image,
            'is_visible' => $this->isVisible,
        ];
    }
}
