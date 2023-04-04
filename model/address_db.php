<?php
class AddressDB {
    public static function getAddress(int $address_id) {
        $db = Database::getDB();
        $query = 'SELECT addressID, line1, line2, city, state,
                      zipCode, phone
                  FROM addresses 
                  WHERE addressID = :address_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':address_id', $address_id);
            $statement->execute();
            
            $row = $statement->fetch();
            $statement->closeCursor();
            
            if ($row) {
                return new Address($row['addressID'], $row['line1'], $row['line2'], 
                                   $row['city'], $row['state'],
                                   $row['zipCode'], $row['phone'],);
            } else {
                return NULL;
            }
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
    
    public static function addAddress(int $customer_id, Address $address) {
            $db = Database::getDB();
            $query = 'INSERT INTO addresses (customerID, line1, line2,
                                    city, state, zipCode, phone)
                      VALUES (:customer_id, :line1, :line2,
                                    :city, :state, :zip_code, :phone)';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':customer_id', $customer_id);
            $statement->bindValue(':line1', $address->getLine1());
            $statement->bindValue(':line2', $address->getLine2());
            $statement->bindValue(':city', $address->getCity());
            $statement->bindValue(':state', $address->getState());
            $statement->bindValue(':zip_code', $address->getZipCode());
            $statement->bindValue(':phone', $address->getPhone());
            $statement->execute();
            
            $address_id = $db->lastInsertId();
            $statement->closeCursor();
            return $address_id;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function updateAddress (Address $address) {
        $db = Database::getDB();
        $query = 'UPDATE addresses
                  SET line1 = :line1,
                      line2 = :line2,
                      city = :city,
                      state = :state,
                      zipCode = :zip_code,
                      phone = :phone
                  WHERE addressID = :address_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':address_id', $address->getID());
            $statement->bindValue(':line1', $address->getLine1());
            $statement->bindValue(':line2', $address->getLine2());
            $statement->bindValue(':city', $address->getCity());
            $statement->bindValue(':state', $address->getState());
            $statement->bindValue(':zip_code', $address->getZipCode());
            $statement->bindValue(':phone', $address->getPhone());
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function disableOrDeleteAddress(int $address_id) {
        $db = Database::getDB();
        if (self::isUsedAddressId($address_id)) {
            $query = 'UPDATE addresses SET disabled = 1 
                      WHERE addressID = :address_id';
            try {
                $statement = $db->prepare($query);
                $statement->bindValue(':address_id', $address_id);
                $statement->execute();
            
                $row_count = $statement->rowCount();
                $statement->closeCursor();
                return $row_count;
            } catch (PDOException $e) {
                Database::displayError($e->getMessage());
            }
        } else {
            $query = 'DELETE FROM addresses 
                      WHERE addressID = :address_id';
            try {
                $statement = $db->prepare($query);
                $statement->bindValue(':address_id', $address_id);
                $statement->execute();
            
                $row_count = $statement->rowCount();
                $statement->closeCursor();
                return $row_count;
            } catch (PDOException $e) {
                Database::displayError($e->getMessage());
            }
        }
    }

    public static function isUsedAddressId(int $address_id) {
        $db = Database::getDB();

        // Check if the address is used as a billing address
        $query1 = 'SELECT COUNT(*) FROM orders 
                   WHERE billingAddressID = :value';
        $statement1 = $db->prepare($query1);
        $statement1->bindValue(':value', $address_id);
        $statement1->execute();
        
        $result1 = $statement1->fetch();
        $billing_count = $result1[0];
        $statement1->closeCursor();
        if ($billing_count > 0) { return TRUE; }

        // Check if the address is used as a shipping address
        $query2 = 'SELECT COUNT(*) FROM orders WHERE shipAddressID = :value';
        $statement2 = $db->prepare($query2);
        $statement2->bindValue(':value', $address_id);
        $statement2->execute();
        
        $result2 = $statement2->fetch();
        $ship_count = $result2[0];
        $statement2->closeCursor();
        if ($ship_count > 0) { return TRUE; }

        return FALSE;
    }
}

?>