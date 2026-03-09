<?php

namespace App\Services;

class GhnOrderBuilder
{
    protected array $order = [];

    protected array $items = [];

    /**
     * Create order builder instance
     *
     * @param  array  $defaultPickup  Default pickup information from provider config
     */
    public function __construct(protected array $defaultPickup = [])
    {
        $this->setDefaultShopInfo();
    }

    public static function make(array $defaultPickup = []): self
    {
        return new self($defaultPickup);
    }

    protected function setDefaultShopInfo(): self
    {
        if (! empty($this->defaultPickup)) {
            $this->order['from_district_id'] = $this->defaultPickup['district_id'] ?? null;
            $this->order['from_ward_code'] = $this->defaultPickup['ward_code'] ?? null;
        }

        return $this;
    }

    public function setPaymentTypeId(int $paymentTypeId): self
    {
        $this->order['payment_type_id'] = $paymentTypeId; // 1: Shop/Seller, 2: Buyer

        return $this;
    }

    public function setRequiredNote(string $note): self
    {
        $this->order['required_note'] = $note; // CHOTHUHANG, CHOXEMHANGKHONGTHU, KHONGCHOXEMHANG

        return $this;
    }

    public function setClientOrderCode(string $code): self
    {
        $this->order['client_order_code'] = $code;

        return $this;
    }

    public function setToInfo(array $to): self
    {
        $this->order['to_name'] = $to['name'];
        $this->order['to_phone'] = $to['phone'];
        $this->order['to_address'] = $to['address'];
        $this->order['to_ward_code'] = $to['ward_code'];
        $this->order['to_district_id'] = $to['district_id'];

        return $this;
    }

    public function setCodAmount(int $amount): self
    {
        $this->order['cod_amount'] = $amount;

        return $this;
    }

    public function setInsuranceValue(int $value): self
    {
        $this->order['insurance_value'] = $value;

        return $this;
    }

    public function setServiceId(int $serviceId): self
    {
        $this->order['service_id'] = $serviceId;

        return $this;
    }

    public function setServiceTypeId(int $serviceTypeId): self
    {
        $this->order['service_type_id'] = $serviceTypeId; // 2: Standard, 5: Express

        return $this;
    }

    public function setNote(?string $note): self
    {
        if ($note) {
            $this->order['note'] = $note;
        }

        return $this;
    }

    public function setWeight(int $weight): self
    {
        $this->order['weight'] = $weight; // grams

        return $this;
    }

    public function setLength(int $length): self
    {
        $this->order['length'] = $length; // cm

        return $this;
    }

    public function setWidth(int $width): self
    {
        $this->order['width'] = $width; // cm

        return $this;
    }

    public function setHeight(int $height): self
    {
        $this->order['height'] = $height; // cm

        return $this;
    }

    public function addItem(array $item): self
    {
        $this->items[] = [
            'name' => $item['name'],
            'quantity' => $item['quantity'] ?? 1,
            'weight' => $item['weight'] ?? 0,
            'length' => $item['length'] ?? 0,
            'width' => $item['width'] ?? 0,
            'height' => $item['height'] ?? 0,
        ];

        return $this;
    }

    public function build(): array
    {
        $this->order['items'] = $this->items;

        return $this->order;
    }

    public function validate(): bool
    {
        $required = [
            'to_name',
            'to_phone',
            'to_address',
            'to_ward_code',
            'to_district_id',
            'weight',
            'service_id',
            'service_type_id',
            'payment_type_id',
        ];

        foreach ($required as $field) {
            if (empty($this->order[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (empty($this->items)) {
            throw new \InvalidArgumentException('At least one item is required');
        }

        return true;
    }
}
