<?php
class CategoryDB {
    public static function getCategories() {
        $db = Database::getDB();
        $query = 'SELECT categoryID, categoryName,
                    (SELECT COUNT(*)
                     FROM products
                     WHERE Products.categoryID = Categories.categoryID)
                     AS productCount
                  FROM categories
                  ORDER BY categoryID';
        try {
            $statement = $db->prepare($query);
            $statement->execute();
            
            $rows = $statement->fetchAll();
            $statement->closeCursor();
            
            $categories = [];
            foreach ($rows as $row) {
                $categories[] = new Category($row['categoryID'],
                                             $row['categoryName'],
                                             $row['productCount'],);
            }
            return $categories;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function getCategory($category_id) {
        $db = Database::getDB();
        $query = 'SELECT categoryID, categoryName 
                  FROM categories
                  WHERE categoryID = :category_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $category_id);
            $statement->execute();
            
            $row = $statement->fetch();
            $statement->closeCursor();
            
            return new Category($row['categoryID'],
                                $row['categoryName']);
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function addCategory($name) {
        $db = Database::getDB();
        $query = 'INSERT INTO categories
                     (categoryName)
                  VALUES
                     (:name)';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':name', $name);
            $statement->execute();

            // Get the last product ID that was automatically generated
            $category_id = $db->lastInsertId();
            $statement->closeCursor();
            return $category_id;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function updateCategory($category) {
        $db = Database::getDB();
        $query = 'UPDATE categories
                  SET categoryName = :name
                  WHERE categoryID = :category_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':name', $category->getName());
            $statement->bindValue(':category_id', $category->getID());
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function deleteCategory($category_id) {
        $db = Database::getDB();
        $query = 'DELETE FROM categories 
                  WHERE categoryID = :category_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $category_id);
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