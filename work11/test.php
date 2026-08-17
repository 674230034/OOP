<?php declare(strict_types=1);

final class Product
{
    private float $price;

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        float $price,
    ) {
        $this->price = $this->validatePrice($price);
    }

    public function applyDiscount(float $percent): void
    {
        if ($percent < 0.0 || $percent > 100.0) {
            throw new InvalidArgumentException('Discount must be between 0 and 100.');
        }

        $this->price = round($this->price * (100.0 - $percent) / 100.0, 2);
    }

    public function displayPrice(): string
    {
        return sprintf('%s (#%d) ราคา: %.2f บาท', $this->name, $this->id, $this->price);
    }

    private function validatePrice(float $price): float
    {
        if ($price < 0.0) {
            throw new InvalidArgumentException('Price cannot be negative.');
        }

        return round($price, 2);
    }
}

$product = new Product(1, 'สมุดโน้ต', 45.00);

echo $product->displayPrice();

$product->applyDiscount(10);

echo "<br>";
echo $product->displayPrice();
?>