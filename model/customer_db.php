<?php
class CustomerDB {
    public static function hasCustomerEmail(string $email) {
        $db = Database::getDB();
        $query = 'SELECT customerID 
                  FROM customers
                  WHERE emailAddress = :email';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':email', $email);
            $statement->execute();
            
            $valid = ($statement->rowCount() == 1);
            $statement->closeCursor();
            return $valid;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function isValidCustomerLogin(string $email, string $password) {
        $db = Database::getDB();
        $query = 'SELECT password 
                  FROM customers
                  WHERE emailAddress = :email';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':email', $email);
            $statement->execute();
            
            $row = $statement->fetch();
            $statement->closeCursor();
            
            if ($row === FALSE) { return FALSE; } 
            else {
                $hash = $row['password'];
                return password_verify($password, $hash);
            }
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function getCustomer(int $customer_id) {
        $db = Database::getDB();
        $query = 'SELECT customerID, firstName, lastName, emailAddress,
                      billingAddressID, shipAddressID, password
                  FROM customers 
                  WHERE customerID = :customer_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':customer_id', $customer_id);
            $statement->execute();
            
            $row = $statement->fetch();
            $statement->closeCursor();

            $customer = self::loadCustomer($row);
            return $customer;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
    
    private static function loadCustomer($row) {
        if ($row) {
            return new Customer($row['customerID'], $row['firstName'], 
                                $row['lastName'], $row['emailAddress'], 
                                $row['password'], $row['billingAddressID'], 
                                $row['shipAddressID'], );
        } else {
            return NULL;
        }
    }

    public static function getCustomerByEmail(string $email) {
        $db = Database::getDB();
        $query = 'SELECT customerID, firstName, lastName, emailAddress,
                      billingAddressID, shipAddressID, password
                  FROM customers 
                  WHERE emailAddress = :email';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':email', $email);
            $statement->execute();
            
            $row = $statement->fetch();
            $statement->closeCursor();

            $customer = self::loadCustomer($row);
            return $customer;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function customerChangeShippingID(int $customer_id, int $address_id) {
        $db = Database::getDB();
        $query = 'UPDATE customers 
                  SET shipAddressID = :address_id 
                  WHERE customerID = :customer_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':address_id', $address_id);
            $statement->bindValue(':customer_id', $customer_id);
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function customerChangeBillingID(int $customer_id, int $address_id) {
        $db = Database::getDB();
        $query = 'UPDATE customers 
                  SET billingAddressID = :address_id
                  WHERE customerID = :customer_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':address_id', $address_id);
            $statement->bindValue(':customer_id', $customer_id);
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
    
    public static function customerChangePassword($customer) {
        $db = Database::getDB();
        $hash = password_hash($customer->getPassword(), PASSWORD_DEFAULT);
        $query = 'UPDATE customers
                  SET password = :hash 
                  WHERE customerID = :customer_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':hash', $hash);
            $statement->bindValue(':customer_id', $customer->getID());
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function addCustomer(Customer $customer) {
        $db = Database::getDB();
        $hash = password_hash($customer->getPassword(), PASSWORD_DEFAULT);
        
        $query = 'INSERT INTO customers 
                      (emailAddress, password, firstName, lastName)
                  VALUES 
                      (:email, :password, :first_name, :last_name)';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':email', $customer->getEmail());
            $statement->bindValue(':password', $hash);
            $statement->bindValue(':first_name', $customer->getFirstName());
            $statement->bindValue(':last_name', $customer->getLastName());
            $statement->execute();
            
            $customer_id = $db->lastInsertId();
            $statement->closeCursor();
            return $customer_id;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function updateCustomer(Customer $customer) {
        $db = Database::getDB();
        $query = 'UPDATE customers
                  SET firstName = :first_name,
                      lastName = :last_name
                  WHERE customerID = :customer_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':first_name', $customer->getFirstName());
            $statement->bindValue(':last_name', $customer->getLastName());
            $statement->bindValue(':customer_id', $customer->getID());
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
}
?>