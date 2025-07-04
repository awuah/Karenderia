<?php
/**
 * Home Controller
 */

class HomeController {
    
    /**
     * Display home page
     */
    public function index() {
        global $db;
        
        // Get featured restaurants
        $stmt = $db->prepare("SELECT * FROM restaurants WHERE status = 'active' AND featured = 1 ORDER BY created_at DESC LIMIT 8");
        $stmt->execute();
        $featured_restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all restaurants
        $stmt = $db->prepare("SELECT * FROM restaurants WHERE status = 'active' ORDER BY created_at DESC");
        $stmt->execute();
        $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get categories
        $stmt = $db->prepare("SELECT DISTINCT category FROM restaurants WHERE status = 'active'");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = array(
            'featured_restaurants' => $featured_restaurants,
            'restaurants' => $restaurants,
            'categories' => $categories,
            'page_title' => 'Home - ' . SITE_NAME
        );
        
        $this->loadView('home', $data);
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