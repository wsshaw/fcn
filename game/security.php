<?php
/**
 * security.php: Security utility functions for Fantasy Collecting
 * 
 * @author William Shaw <william.shaw@duke.edu> (modernized)
 * @date 2025
 */

/**
 * Sanitize and validate integer input
 */
function sanitize_int($input, $default = 0) {
    return isset($input) ? (int)$input : $default;
}

/**
 * Sanitize string input for display
 */
function sanitize_string($input, $default = '') {
    return isset($input) ? htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8') : $default;
}

/**
 * Sanitize string input for database (removes HTML but preserves some formatting)
 */
function sanitize_db_string($input, $default = '') {
    if (!isset($input)) return $default;
    return trim(strip_tags($input, '<br><p><b><i><em><strong>'));
}

/**
 * Validate email address
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if user is logged in and session is valid
 */
function require_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['uuid']) || !isset($_SESSION['uname'])) {
        header('Location: ../login.php');
        exit();
    }
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * Validate CSRF token
 */
function validate_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate work ID ownership (prevents users from manipulating others' works)
 */
function validate_work_ownership($work_id, $user_id, $dbh) {
    $stmt = $dbh->prepare("SELECT owner FROM works WHERE id = ?");
    $stmt->execute([$work_id]);
    $result = $stmt->fetch();
    
    return $result && (int)$result['owner'] === (int)$user_id;
}

/**
 * Rate limiting - simple implementation
 */
function check_rate_limit($action, $limit_per_minute = 10) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $key = 'rate_limit_' . $action;
    $now = time();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [];
    }
    
    // Remove timestamps older than 1 minute
    $_SESSION[$key] = array_filter($_SESSION[$key], function($timestamp) use ($now) {
        return ($now - $timestamp) < 60;
    });
    
    if (count($_SESSION[$key]) >= $limit_per_minute) {
        return false;
    }
    
    $_SESSION[$key][] = $now;
    return true;
}

?>