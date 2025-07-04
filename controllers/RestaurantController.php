<?php
/**
 * Restaurant Controller
 */

class RestaurantController {
    
    /**
     * Show restaurant details
     */
    public function show($id) {
        global $db;
        
        // Get restaurant details
        $stmt = $db->prepare("SELECT * FROM restaurants WHERE id = ? AND status = 'active'");
        $stmt->execute([$id]);
        $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$restaurant) {
            http_response_code(404);
            include 'views/404.php';
            return;
        }
        
        // Get restaurant menu categories
        $stmt = $db->prepare("SELECT DISTINCT category FROM menu_items WHERE restaurant_id = ? AND status = 'active' ORDER BY category");
        $stmt->execute([$id]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get menu items
        $stmt = $db->prepare("SELECT * FROM menu_items WHERE restaurant_id = ? AND status = 'active' ORDER BY category, name");
        $stmt->execute([$id]);
        $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get restaurant reviews
        $stmt = $db->prepare("SELECT r.*, u.name as customer_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.restaurant_id = ? ORDER BY r.created_at DESC LIMIT 10");
        $stmt->execute([$id]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = array(
            'restaurant' => $restaurant,
            'categories' => $categories,
            'menu_items' => $menu_items,
            'reviews' => $reviews,
            'page_title' => $restaurant['name'] . ' - ' . SITE_NAME
        );
        
        $this->loadView('restaurant', $data);
    }
    
    /**
     * Load view
     */
    private function loadView($view, $data = array()) {
        extract($data);
        include 'views/' . $view . '.php';
    }
}
?>