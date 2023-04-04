<?php
class Order {
    private Customer $customer;
    
    public function __construct(
        private int $id,
        private int $customerId,
        private int $billingAddressId,
        private int $shippingAddressId,
        private Card $card,
        private string $orderDate,
        private ?string $shipDate,
        private float $shipAmount,
        private float $taxAmount,
    ) 
    { 
        $this->customer = new Customer($this->customerId);
    }

    public function getID() {
        return $this->id;
    }

    public function setID(int $value) {
        $this->id = $value;
    }

    public function getCustomerID() {
        return $this->customerId;
    }
    
    public function getCustomer() {
        return $this->customer;
    }
    
    public function setCustomerName(string $first, string $last) {
        $this->customer->setFirstName($first);
        $this->customer->setLastName($last);
    }
    
    public function getBillingAddressID() {
        return $this->billingAddressId;
    }
    
    public function getShippingAddressID() {
        return $this->shippingAddressId;
    }
    
    public function getCard() {
        return $this->card;
    }
    
    public function getOrderDate() {
        return $this->orderDate;
    }
    
    public function getOrderDateFormatted() {
        $date = strtotime($this->orderDate);
        return date('M j, Y', $date);
    }

    public function setOrderDate(string $value) {
        $this->orderDate = $value;
    }
    
    public function hasShipDate() {
        return $this->shipDate !== NULL;
    }
    
    public function getShipDate() {
        return $this->shipDate;
    }
    
    public function getShipDateFormatted() {
        $date = strtotime($this->shipDate);
        return date('M j, Y', $date);
    }

    public function setShipDate(string $value) {
        $this->shipDate = $value;
    }
    
    public function getShipAmount() {
        return $this->shipAmount;
    }

    public function getShipAmountFormatted() {
        $formatted = number_format($this->shipAmount, 2);
        return $formatted;
    }

    public function setShipAmount(float $value) {
        $this->shipAmount = $value;
    }
    
    public function getTaxAmount() {
        return $this->taxAmount;
    }

    public function getTaxAmountFormatted() {
        $formatted = number_format($this->taxAmount, 2);
        return $formatted;
    }

    public function setTaxAmount(float $value) {
        $this->taxAmount = $value;
    }
    
    public function getTotal(float $subtotal) {
        return $subtotal + $this->taxAmount + $this->shipAmount;
    }
}
?>