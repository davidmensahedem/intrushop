<?php
class User {
    public function __construct(
        private int $id = 0,
        private string $firstName = '',
        private string $lastName = '',
        private string $email = '',
        private string $password = '', 
    ) { }

    public function getID() {
        return $this->id;
    }

    public function setID(int $value) {
        $this->id = $value;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName(string $value) {
        $this->firstName = $value;
    }
    
    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName(string $value) {
        $this->lastName = $value;
    }
    
    public function getName() {
        return "$this->firstName $this->lastName";
    }
    
    public function getEmail() {
        return $this->email;
    }

    public function setEmail(string $value) {
        $this->email = $value;
    }
    
    public function getPassword() {
        return $this->password;
    }

    public function setPassword(string $value) {
        $this->password = $value;
    }
    
    public function hasPassword() {
        return !empty($this->password);
    }

}
?>