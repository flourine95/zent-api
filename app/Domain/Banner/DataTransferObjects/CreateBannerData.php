<?php

namespace App\Domain\Banner\DataTransferObjects;

use DateTimeInterface;

final readonly class CreateBannerData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public string $image,
        public ?string $link,
        public ?string $buttonText,
        public string $position,
        public int $order,
        public bool $isActive,
        public ?DateTimeInterface $startDate,
        public ?DateTimeInterface $endDate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            image: $data['image'],
            link: $data['link'] ?? null,
            buttonText: $data['button_text'] ?? null,
            position: $data['position'],
            order: $data['order'] ?? 0,
            isActive: $data['is_active'] ?? true,
            startDate: isset($data['start_date']) ? new \DateTime($data['start_date']) : null,
            endDate: isset($data['end_date']) ? new \DateTime($data['end_date']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'link' => $this->link,
            'button_text' => $this->buttonText,
            'position' => $this->position,
            'order' => $this->order,
            'is_active' => $this->isActive,
            'start_date' => $this->startDate?->format('Y-m-d H:i:s'),
            'end_date' => $this->endDate?->format('Y-m-d H:i:s'),
        ];
    }
}
