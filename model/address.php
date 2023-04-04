<?php
class Address {
    public function __construct(
        private int $id = 0,
        private string $line1 = '',
        private string $line2 = '',
        private string $city = '',
        private string $state = '',
        private string $zipCode = '',
        private string $phone = '',
    ) { }

    public function getID() {
        return $this->id;
    }

    public function setID(int $value) {
        $this->id = $value;
    }
    
    public function getLine1() {
        return $this->line1;
    }

    public function setLine1(string $value) {
        $this->line1 = $value;
    }
    
    public function hasLine2() {
        return strlen($this->line2) > 0;
    }
    
    public function getLine2() {
        return $this->line2;
    }

    public function setLine2(string $value) {
        $this->line2 = $value;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity(string $value) {
        $this->city = $value;
    }
    
    public function getState() {
        return $this->state;
    }
    
    public function getStateUpper() {
        return strtoupper($this->state);
    }

    public function setState(string $value) {
        $this->state = $value;
    }
    
    public function getZipCode() {
        return $this->zipCode;
    }

    public function setZipCode(string $value) {
        $this->zipCode = $value;
    }
    
    public function getFullAddress() {
        return $this->city . ', ' . $this->state . ' ' . $this->zipCode;
    }
    
    public function getPhone() {
        return $this->phone;
    }

    public function setPhone(string $value) {
        $this->phone = $value;
    }
}
?>