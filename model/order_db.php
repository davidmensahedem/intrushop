<?php
class OrderDB {
    // This function calculates a shipping charge of $5 per item
    // but it only charges shipping for the first 5 items
    public static function getShippingCost($item_count) {
        $item_shipping = 5;   // $5 per item
        if ($item_count > 5) {
            $shipping_cost = $item_shipping * 5;
        } else {
            $shipping_cost = $item_shipping * $item_count;
        }
        return $shipping_cost;
    }

    // This function calcualtes the sales tax,
    // but only for orders in California (CA)
    public static function getTaxAmount(float $subtotal, Address $shipping_address) {
        $state = $shipping_address->getStateUpper();
        switch ($state) {
            case 'CA': $tax_rate = 0.09; break;
            default: $tax_rate = 0; break;
        }
        return round($subtotal * $tax_rate, 2);
    }

    public static function addOrder(Customer $customer, Cart $cart,  
                                    Address $shipping_address, Card $card) {
        $db = Database::getDB();
        $shipping_cost = self::getShippingCost($cart->getItemCount());
        $tax = self::getTaxAmount($cart->getSubtotal(), $shipping_address);
        $order_date = date("Y-m-d H:i:s");

        $query = 'INSERT INTO orders (customerID, orderDate, shipAmount, taxAmount,
                                shipAddressID, cardType, cardNumber,
                                cardExpires, billingAddressID)
                  VALUES (:customer_id, :order_date, :ship_amount, :tax_amount,
                                :shipping_id, :card_type, :card_number,
                                :card_expires, :billing_id)';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':customer_id', $customer->getID());
            $statement->bindValue(':order_date', $order_date);
            $statement->bindValue(':ship_amount', $shipping_cost);
            $statement->bindValue(':tax_amount', $tax);
            $statement->bindValue(':shipping_id', $customer->getShippingAddressID());
            $statement->bindValue(':card_type', $card->getType());
            $statement->bindValue(':card_number', $card->getNumber());
            $statement->bindValue(':card_expires', $card->getExpires());
            $statement->bindValue(':billing_id', $customer->getBillingAddressID());
            $statement->execute();
            
            $order_id = $db->lastInsertId();
            $statement->closeCursor();
            return $order_id;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function addOrderItem(OrderItem $item) {
        $db = Database::getDB();
        $query = 'INSERT INTO OrderItems (orderID, productID, itemPrice,
                                discountAmount, quantity)
                  VALUES (:order_id, :product_id, :item_price, 
                                :discount, :quantity)';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':order_id', $item->getOrderID());
            $statement->bindValue(':product_id', $item->getProductID());
            $statement->bindValue(':item_price', $item->getPrice());
            $statement->bindValue(':discount', $item->getDiscountAmount());
            $statement->bindValue(':quantity', $item->getQuantity());
            $statement->execute();
            
            $order_item_id = $db->lastInsertId();
            $statement->closeCursor();
            return $order_item_id;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function getOrder(int $order_id) {
        $db = Database::getDB();
        $query = 'SELECT orderID, customerID, billingAddressID, shipAddressID, 
                      cardNumber, cardType, cardExpires, orderDate, shipDate, 
                      shipAmount, taxAmount
                  FROM orders 
                  WHERE orderID = :order_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':order_id', $order_id);
            $statement->execute();
            
            $row = $statement->fetch();
            $statement->closeCursor();
            
            $order = self::loadOrder($row);
            return $order;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
    
    private static function loadOrder($row) {
        // use named parameters so can skip cvv parameter
        $card = new Card(number:$row['cardNumber'], 
                         type:$row['cardType'], expires:$row['cardExpires']);
        $order = new Order($row['orderID'], $row['customerID'], 
                           $row['billingAddressID'], $row['shipAddressID'], 
                           $card, $row['orderDate'], $row['shipDate'], 
                           $row['shipAmount'], $row['taxAmount'], );
        if (array_key_exists('firstName', $row) && array_key_exists('lastName', $row)) {
            $order->setCustomerName($row['firstName'], $row['lastName']);
        }
        return $order;
    }

    public static function getOrderItems(int $order_id) {
        $db = Database::getDB();
        $query = 'SELECT itemID, orderID, productID, quantity, 
                      itemPrice, discountAmount
                  FROM OrderItems 
                  WHERE orderID = :order_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':order_id', $order_id);
            $statement->execute();
            
            $rows = $statement->fetchAll();
            $statement->closeCursor();
            
            $order_items = [];
            foreach ($rows as $row) {
                $item = new OrderItem($row['itemID'], $row['orderID'], 
                                      $row['productID'], $row['quantity'], 
                                      $row['itemPrice'], $row['discountAmount'], );
                $order_items[] = $item;
            }
            return $order_items;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function getOrdersByCustomerId(int $customer_id) {
        $db = Database::getDB();
        $query = 'SELECT orderID, customerID, cardType, cardNumber, cardExpires,
                      billingAddressID, shipAddressID, orderDate, shipDate, 
                      shipAmount, taxAmount
                  FROM orders 
                  WHERE customerID = :customer_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':customer_id', $customer_id);
            $statement->execute();
            
            $rows = $statement->fetchAll();
            $statement->closeCursor();
            
            $orders = [];
            foreach ($rows as $row) {
                $orders[] = self::loadOrder($row);
            }
            return $orders;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function getUnfilledOrders() {
        $db = Database::getDB();
        $query = 'SELECT orderID, o.customerID, c.firstName, c.lastName, 
                      cardType, cardNumber, cardExpires, o.billingAddressID, 
                      o.shipAddressID, orderDate, shipDate, shipAmount, taxAmount
                  FROM orders AS o
                  INNER JOIN customers AS c
                  ON c.customerID = o.customerID
                  WHERE shipDate IS NULL 
                  ORDER BY orderDate';
        try {
            $statement = $db->prepare($query);
            $statement->execute();
            
            $rows = $statement->fetchAll();
            $statement->closeCursor();
            
            $orders = [];
            foreach ($rows as $row) {
                $orders[] = self::loadOrder($row);
            }
            return $orders;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function getFilledOrders() {
        $db = Database::getDB();
        $query = 'SELECT orderID, o.customerID, c.firstName, c.lastName, 
                      cardType, cardNumber, cardExpires, o.billingAddressID, 
                      o.shipAddressID, orderDate, shipDate, shipAmount, taxAmount
                  FROM orders AS o
                  INNER JOIN customers AS c
                  ON c.customerID = o.customerID
                  WHERE shipDate IS NOT NULL 
                  ORDER BY orderDate';
        try {
            $statement = $db->prepare($query);
            $statement->execute();
            
            $rows = $statement->fetchAll();
            $statement->closeCursor();
            
            $orders = [];
            foreach ($rows as $row) {
                $orders[] = self::loadOrder($row);
            }
            return $orders;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function setShipDate(int $order_id) {
        $db = Database::getDB();
        $ship_date = date("Y-m-d H:i:s");
        $query = 'UPDATE orders
                  SET shipDate = :ship_date
                  WHERE orderID = :order_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':ship_date', $ship_date);
            $statement->bindValue(':order_id', $order_id);
            $statement->execute();
            
            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    public static function deleteOrder(int $order_id) {
        $db = Database::getDB();
        $query = 'DELETE FROM orders 
                  WHERE orderID = :order_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':order_id', $order_id);
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