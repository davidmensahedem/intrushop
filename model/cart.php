<?php
class Cart {
    private $items = [];
    
    public function __construct() {
        // Load cart items from session and database
        if (!isset($_SESSION['cart']) ) {
            $_SESSION['cart'] = [];  // Create an empty cart if it doesn't exist
        } else {
            foreach ($_SESSION['cart'] as $product_id => $quantity ) {
                // Get product data from db
                $product = ProductDB::getProduct($product_id);
                
                if ($product === NULL) {
                    // product no longer in db - remove from cart
                    unset($_SESSION['cart'][$product_id]);
                } else {
                    // Create order item based on quantity and product data
                    $order_item = $this->loadOrderItem($product, $quantity);

                    // Store data in items array
                    $this->items[$product_id]['product'] = $product;
                    $this->items[$product_id]['order_item'] = $order_item;
                }
            }
        }
    }
    
    // helper function to create OrderItem object 
    private function loadOrderItem(Product $product, int $quantity) {
        return new OrderItem(0,0, // placeholder values for item id and order id
                             $product->getID(), 
                             $quantity, 
                             $product->getPrice(), 
                             $product->getDiscountAmount());
    }

    // Add an item to the cart
    public function addItem($product_id, $quantity) {
        // Add to session
        $_SESSION['cart'][$product_id] = round($quantity, 0);
        
        // Get product data from db
        $product = ProductDB::getProduct($product_id);

        // Create order item based on quantity and product data
        $order_item = $this->loadOrderItem($product, $quantity);

        // Store data in items array
        $this->items[$product_id]['product'] = $product;
        $this->items[$product_id]['order_item'] = $order_item;

        // Set last category added to cart
        $this->setLastCategory($product->getCategory());
    }

    // Update an item in the cart
    public function updateItem($product_id, $quantity) {
        if (isset($_SESSION['cart'][$product_id])) {
            $new_quantity = round($quantity, 0);
            
            // Update session
            $_SESSION['cart'][$product_id] = $new_quantity;
            
            // Update items array
            $order_item = $this->items[$product_id]['order_item'];
            $order_item->setQuantity($new_quantity);
        }
    }

    // Remove an item from the cart
    public function removeItem($product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            
            // Remove from session
            unset($_SESSION['cart'][$product_id]);
            
            // Remove from items array
            unset($this->items[$product_id]); 
        }
    }

    // Get items in the cart
    public function getItems() {
        return $this->items;
    }

    // Get the number of products in the cart
    public function getProductCount() {
        return count($this->items);
    }

    // Get the number of items in the cart
    public function getItemCount () {
        $count = 0;
        foreach ($this->items as $item) {
            $order_item = $item['order_item'];
            $count += $order_item->getQuantity();
        }
        return $count;
    }

    // Get the subtotal for the cart
    public function getSubtotal () {
        $subtotal = 0;
        foreach ($this->items as $item) {
            $order_item = $item['order_item'];
            $subtotal += $order_item->getLineTotal();
        }
        return $subtotal;
    }

    // Remove all items from the cart
    public function clear() {
        $_SESSION['cart'] = [];
        $this->items = [];
    }
    
    public function hasLastCategory() {
        return isset($_SESSION['last_category_id']) && 
               isset($_SESSION['last_category_name']);
    }
    
    public function getLastCategory() {
        if ($this->hasLastCategory()) {
            return new Category($_SESSION['last_category_id'],
                                $_SESSION['last_category_name']);
        } else {
            return NULL;
        }
    }

    // Set the category for the last item added to the cart
    public function setLastCategory(Category $category) {
        $_SESSION['last_category_id'] = $category->getID();
        $_SESSION['last_category_name'] = $category->getName();
    }
    
    // static function - called when a category is deleted 
    public static function clearLastCategory($category_id) {
        if (isset($_SESSION['last_category_id']) &&
            $_SESSION['last_category_id'] === $category_id) 
        {
            unset($_SESSION['last_category_id']);
            unset($_SESSION['last_category_name']);
        }
    }

}

?>