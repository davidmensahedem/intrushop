<?php
class Admin extends User {
    public function __construct(
        // pass to superclass: no access modifiers, so aren't promoted subclass properties
        int $id = 0, 
        string $first = '', 
        string $last = '', 
        string $email = '', 
        string $password = '',) 
    {
        // Call User constructor to finish initialization
        parent::__construct($id, $first, $last, $email, $password);
    }
}

?>