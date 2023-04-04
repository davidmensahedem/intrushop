<?php
class Card {
    public function __construct(
        private string $type = '',
        private string $number = '',
        private string $cvv = '',
        private string $expires = '',
    ) { }
    
    public function getType() {
        return $this->type;
    }

    public function setType(string $value) {
        $this->type = $value;
    }
    
    public function getNumber() {
        return $this->number;
    }

    public function setNumber(string $value) {
        $this->number = $value;
    }
    
    public function getCvv() {
        return $this->cvv;
    }

    public function setCvv(string $value) {
        $this->cvv = $value;
    }
    
    public function getExpires() {
        return $this->expires;
    }

    public function setExpires(string $value) {
        $this->expires = $value;
    }
    
    public function getName() {
        $cards = Card::list();
        if (array_key_exists($this->type, $cards)) {
            return $cards[$this->type];
        } else {
            return '';
        }
    }
    
    // static functions: belong to class, not instance of class
    public static function list() {
        return [
            'm' => 'MasterCard',
            'v' => 'Visa',
            'd' => 'Discover',
            'a' => 'American Express',
        ];
        
        /*
        // could also use keep this data in database...
         * 
         */
    }
    
    public static function codes() {
        $codes = [];
        foreach (self::list() as $code => $name) {
            $codes[] = $code;
        }
        return $codes;
        
        /*
        // could also use the array_keys() function (not presented in book)
        return array_keys(self::list());
         * 
         */
    }

}
?>