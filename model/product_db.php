<?php
class ProductDB {   
    public static function getProductsByCategory($category_id) {
        $db = Database::getDB();
        $query = 'SELECT c.categoryID, categoryName, productID, productCode, 
                     productName, description, listPrice, discountPercent 
                  FROM products AS p
                  JOIN categories AS c
                  ON p.categoryID = c.categoryID
                  WHERE p.categoryID = :category_id
                  ORDER BY productID';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $category_id);
            $statement->execute();
            
            $rows = $statement->fetchAll();
            $statement->closeCursor();
            
            $products = [];
            foreach ($rows as $row) {
                $category = new Category($row['categoryID'], $row['categoryName']);
                $products[] = self::loadProduct($row, $category);
            }
            return $products;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function getProduct($product_id) {
        $db = Database::getDB();
        $query = 'SELECT c.categoryID, categoryName, productID, productCode, 
                     productName, description, listPrice, discountPercent 
                  FROM products aS p
                  JOIN categories AS c
                  ON p.categoryID = c.categoryID
                  WHERE productID = :product_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':product_id', $product_id);
            $statement->execute();
            
            $row = $statement->fetch();
            $statement->closeCursor();
            
            if ($row) {
                $category = new Category($row['categoryID'], $row['categoryName']);
                return self::loadProduct($row, $category);
            } else {
                return NULL;
            }
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
    
    private static function loadProduct($row, $category) {
        $product = new Product($category,
                               $row['productCode'],
                               $row['productName'],
                               $row['description'],
                               $row['listPrice'],
                               $row['discountPercent'],);
        $product->setID($row['productID']);
        return $product;
    }

    public static function getProductOrderCount($product_id) {
        $db = Database::getDB();
        $query = 'SELECT COUNT(*) AS orderCount
                  FROM orderitems
                  WHERE productID = :product_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':product_id', $product_id);
            $statement->execute();
            
            $row = $statement->fetch();
            $order_count = $row['orderCount'];
            $statement->closeCursor();
            return $order_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function addProduct($product) {
        $db = Database::getDB();
        $query = 'INSERT INTO products
                     (categoryID, productCode, productName, description, 
                      listPrice, discountPercent, dateAdded)
                  VALUES
                     (:category_id, :code, :name, :description, :price,
                      :discount_percent, NOW())';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $product->getCategory()->getID());
            $statement->bindValue(':code', $product->getCode());
            $statement->bindValue(':name', $product->getName());
            $statement->bindValue(':description', $product->getDescription());
            $statement->bindValue(':price', $product->getPrice());
            $statement->bindValue(':discount_percent', $product->getDiscountPercent());
            $statement->execute();

            // Get the last product ID that was automatically generated
            $product_id = $db->lastInsertId();
            $statement->closeCursor();
            return $product_id;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function updateProduct($product) {
        $db = Database::getDB();
        $query = 'UPDATE Products
                  SET productName = :name, 
                      productCode = :code,
                      description = :description, 
                      listPrice = :price,
                      discountPercent = :discount_percent,
                      categoryID = :category_id
                  WHERE productID = :product_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $product->getCategory()->getID());
            $statement->bindValue(':code', $product->getCode());
            $statement->bindValue(':name', $product->getName());
            $statement->bindValue(':description', $product->getDescription());
            $statement->bindValue(':price', $product->getPrice());
            $statement->bindValue(':discount_percent', $product->getDiscountPercent());
            $statement->bindValue(':product_id', $product->getID());
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function deleteProduct($product_id) {
        $db = Database::getDB();
        $query = 'DELETE FROM products
                  WHERE productID = :product_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':product_id', $product_id);
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