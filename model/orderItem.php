<?php
class OrderItem {
    public function __construct(
        private int $id,
        private int $orderId, 
        private int $productId,
        private int $quantity,
        private float $price,
        private float $discountAmount,
    ) { }

    public function getID() {
        return $this->id;
    }

    public function setID(int $value) {
        $this->id = $value;
    }
    
    public function getOrderID() {
        return $this->orderId;
    }
    
    public function setOrderID(int $value) {
        $this->orderId = $value;
    }

    public function getProductID() {
        return $this->productId;
    }
    
    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity(int $value) {
        $this->quantity = $value;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getDiscountAmount() {
        return $this->discountAmount;
    }
    
    public function getCost() {
        return $this->price - $this->discountAmount;
    }
    
    public function getLineTotal() {
        return $this->getCost() * $this->quantity;
    }
}
?>