<?php
class Category {
    public function __construct(
        private int $id = 0,
        private string $name = '',
        private int $count = 0,  
    ) { }

    public function getID() {
        return $this->id;
    }

    public function setID(int $value) {
        $this->id = $value;
    }
    
    public function hasID() {
        return $this->id > 0;
    }

    public function getName() {
        return $this->name;
    }

    public function setName(string $value) {
        $this->name = $value;
    }
    
    public function getProductCount() {
        return $this->count;
    }

    public function setProductCount(int $value) {
        $this->count = $value;
    }
}
?>