<?php

namespace App\Services;

class GhtkOrderBuilder
{
    protected array $order = [];

    protected array $products = [];

    /**
     * Create order builder instance
     *
     * @param  array  $defaultPickup  Default pickup information from provider config
     */
    public function __construct(protected array $defaultPickup = [])
    {
        $this->setDefaultPickupInfo();
    }

    public static function make(array $defaultPickup = []): self
    {
        return new self($defaultPickup);
    }

    protected function setDefaultPickupInfo(): self
    {
        if (! empty($this->defaultPickup)) {
            $this->order['pick_name'] = $this->defaultPickup['name'] ?? '';
            $this->order['pick_address'] = $this->defaultPickup['address'] ?? '';
            $this->order['pick_province'] = $this->defaultPickup['province'] ?? '';
            $this->order['pick_district'] = $this->defaultPickup['district'] ?? '';
            $this->order['pick_ward'] = $this->defaultPickup['ward'] ?? '';
            $this->order['pick_street'] = $this->defaultPickup['street'] ?? '';
            $this->order['pick_tel'] = $this->defaultPickup['tel'] ?? '';
            $this->order['pick_email'] = $this->defaultPickup['email'] ?? '';
        }

        return $this;
    }

    public function setOrderId(string $id): self
    {
        $this->order['id'] = $id;

        return $this;
    }

    public function setPickupInfo(array $pickup): self
    {
        $this->order['pick_name'] = $pickup['name'];
        $this->order['pick_address'] = $pickup['address'];
        $this->order['pick_province'] = $pickup['province'];
        $this->order['pick_district'] = $pickup['district'];
        $this->order['pick_ward'] = $pickup['ward'] ?? null;
        $this->order['pick_street'] = $pickup['street'] ?? null;
        $this->order['pick_tel'] = $pickup['tel'];
        $this->order['pick_email'] = $pickup['email'] ?? null;

        return $this;
    }

    public function setDeliveryInfo(array $delivery): self
    {
        $this->order['name'] = $delivery['name'];
        $this->order['tel'] = $delivery['tel'];
        $this->order['address'] = $delivery['address'];
        $this->order['province'] = $delivery['province'];
        $this->order['district'] = $delivery['district'];
        $this->order['ward'] = $delivery['ward'] ?? null;
        $this->order['street'] = $delivery['street'] ?? null;
        $this->order['hamlet'] = $delivery['hamlet'] ?? 'Khác';
        $this->order['email'] = $delivery['email'] ?? null;

        return $this;
    }

    public function setPickMoney(int $amount): self
    {
        $this->order['pick_money'] = $amount;

        return $this;
    }

    public function setValue(int $value): self
    {
        $this->order['value'] = $value;

        return $this;
    }

    public function setNote(?string $note): self
    {
        if ($note) {
            $this->order['note'] = substr($note, 0, 120);
        }

        return $this;
    }

    public function setFreeShip(bool $isFreeShip = true): self
    {
        $this->order['is_freeship'] = $isFreeShip ? '1' : '0';

        return $this;
    }

    public function setTransport(string $transport): self
    {
        $this->order['transport'] = $transport; // 'fly' or 'road'

        return $this;
    }

    public function setPickOption(string $option): self
    {
        $this->order['pick_option'] = $option; // 'cod' or 'post'

        return $this;
    }

    public function setPickDate(string $date): self
    {
        $this->order['pick_date'] = $date; // YYYY-MM-DD

        return $this;
    }

    public function setDeliverDate(string $date): self
    {
        $this->order['deliver_date'] = $date; // YYYY-MM-DD

        return $this;
    }

    public function setTags(array $tags): self
    {
        $this->order['tags'] = $tags;

        return $this;
    }

    public function addProduct(array $product): self
    {
        $this->products[] = [
            'name' => $product['name'],
            'weight' => $product['weight'],
            'quantity' => $product['quantity'] ?? 1,
            'product_code' => $product['product_code'] ?? null,
            'barcode' => $product['barcode'] ?? null,
            'price' => $product['price'] ?? null,
            'cod_money' => $product['cod_money'] ?? null,
        ];

        return $this;
    }

    public function build(): array
    {
        return [
            'order' => $this->order,
            'products' => $this->products,
        ];
    }

    public function validate(): bool
    {
        $required = ['id', 'pick_name', 'pick_address', 'pick_province', 'pick_district',
            'pick_tel', 'name', 'tel', 'address', 'province', 'district',
            'hamlet', 'pick_money', 'value'];

        foreach ($required as $field) {
            if (empty($this->order[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (empty($this->products)) {
            throw new \InvalidArgumentException('At least one product is required');
        }

        return true;
    }
}
