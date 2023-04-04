<?php
class Customer extends User {

    public function __construct(
        // pass to superclass: no access modifiers
        int $id = 0, 
        string $first = '', 
        string $last = '', 
        string $email = '', 
        string $password = '',
        // promoted subclass properties
        private int $billingId = 0,  
        private int $shippingId = 0,) 
    {
        // Call User constructor to finish initialization
        parent::__construct($id, $first, $last, $email, $password);
    }
    
    public function getBillingAddressID() {
        return $this->billingId;
    }

    public function setBillingAddressID(int $value) {
        $this->billingId = $value;
    }
    
    public function getShippingAddressID() {
        return $this->shippingId;
    }

    public function setShippingAddressId(int $value) {
        $this->shippingId = $value;
    }

}
?>