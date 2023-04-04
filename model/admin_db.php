<?php
class AdminDB {
    public static function isValidAdminLogin($email, $password) {
        $db = Database::getDB();
        $query = 'SELECT password 
                  FROM administrators
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
    
    public static function isValidAdminEmail($email) {
        $db = Database::getDB();
        $query = 'SELECT adminID  
                  FROM administrators
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
    
    public static function getAdminCount() {
        $db = Database::getDB();
        $query = 'SELECT count(adminID) AS adminCount 
                  FROM administrators';
        try {
            $statement = $db->prepare($query);
            $statement->execute();

            $row = $statement->fetch();
            $statement->closeCursor();

            if ($row) {
                return $row['adminCount'];
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function getAllAdmins() {
        $db = Database::getDB();
        $query = 'SELECT adminID, firstName, lastName,
                      emailAddress, password 
                  FROM administrators 
                  ORDER BY lastName, firstName';
        try {
            $statement = $db->prepare($query);
            $statement->execute();
            
            $rows = $statement->fetchAll();
            $statement->closeCursor();
            
            $admins = [];
            foreach ($rows as $row) {
                $admins[] = self::loadAdmin($row);
            }
            return $admins;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
    
    private static function loadAdmin($row) {
        return new Admin($row['adminID'], $row['firstName'], 
                         $row['lastName'], $row['emailAddress'], 
                         $row['password'], );
    }

    public static function getAdmin ($admin_id) {
        $db = Database::getDB();
        $query = 'SELECT adminID, firstName, lastName,
                      emailAddress, password 
                  FROM administrators 
                  WHERE adminID = :admin_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':admin_id', $admin_id);
            $statement->execute();
            
            $row = $statement->fetch();
            $statement->closeCursor();
            
            if ($row) {
                return self::loadAdmin($row);
            } else {
                return NULL;
            }
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function getAdminByEmail ($email) {
        $db = Database::getDB();
        $query = 'SELECT adminID, firstName, lastName,
                      emailAddress, password  
                  FROM administrators 
                  WHERE emailAddress = :email';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':email', $email);
            $statement->execute();
            
            $row = $statement->fetch();
            $statement->closeCursor();
            
            if ($row) {
                return self::loadAdmin($row);
            } else {
                return NULL;
            }
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function addAdmin($admin) {
        $db = Database::getDB();
        $hash = password_hash($admin->getPassword(), PASSWORD_DEFAULT);
        $query = 'INSERT INTO administrators 
                      (emailAddress, password, firstName, lastName)
                  VALUES 
                      (:email, :password, :first_name, :last_name)';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':email', $admin->getEmail());
            $statement->bindValue(':password', $hash);
            $statement->bindValue(':first_name', $admin->getFirstName());
            $statement->bindValue(':last_name', $admin->getLastName());
            $statement->execute();
            
            $admin_id = $db->lastInsertId();
            $statement->closeCursor();
            return $admin_id;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function updateAdmin($admin) {
        $db = Database::getDB();
        $query = 'UPDATE administrators
                  SET firstName = :first_name,
                      lastName = :last_name
                  WHERE adminID = :admin_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':first_name', $admin->getFirstName());
            $statement->bindValue(':last_name', $admin->getLastName());
            $statement->bindValue(':admin_id', $admin->getID());
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
    
    public static function changePassword($admin) {
        $db = Database::getDB();
        $hash = password_hash($admin->getPassword(), PASSWORD_DEFAULT);
        $query = 'UPDATE administrators
                  SET password = :hash 
                  WHERE adminID = :admin_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':hash', $hash);
            $statement->bindValue(':admin_id', $admin->getID());
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function deleteAdmin($admin_id) {
        $db = Database::getDB();
        $query = 'DELETE FROM administrators 
                  WHERE adminID = :admin_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':admin_id', $admin_id);
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