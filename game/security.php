<?php
/**
 * Security utility functions for Fantasy Collecting
 * 
 * This file provides essential security functions including input sanitization,
 * authentication checks, CSRF protection, and rate limiting to protect the
 * Fantasy Collecting application from common web vulnerabilities.
 * 
 * @package    FantasyCollecting
 * @author     William Shaw <william.shaw@duke.edu> (modernized)
 * @version    0.2
 * @since      2025-09-10
 * @license    MIT
 */

/**
 * Sanitize and validate integer input
 * 
 * Converts input to integer and provides default value if input is not set.
 * Prevents type juggling vulnerabilities and ensures integer values.
 *
 * @param mixed $input   The input value to sanitize
 * @param int   $default Default value if input is not set or invalid
 * @return int           Sanitized integer value
 * @since 0.2
 */
function sanitize_int($input, $default = 0) {
    return isset($input) ? (int)$input : $default;
}

/**
 * Sanitize string input for safe HTML display
 * 
 * Escapes HTML entities and trims whitespace to prevent XSS attacks.
 * Safe for displaying user input in HTML context.
 *
 * @param mixed  $input   The input string to sanitize
 * @param string $default Default value if input is not set
 * @return string         HTML-safe sanitized string
 * @since 0.2
 */
function sanitize_string($input, $default = '') {
    return isset($input) ? htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8') : $default;
}

/**
 * Sanitize string input for database storage
 * 
 * Removes potentially dangerous HTML tags while preserving basic formatting.
 * Allows safe HTML tags like <br>, <p>, <b>, <i>, <em>, <strong>.
 *
 * @param mixed  $input   The input string to sanitize
 * @param string $default Default value if input is not set
 * @return string         Database-safe sanitized string
 * @since 0.2
 */
function sanitize_db_string($input, $default = '') {
    if (!isset($input)) return $default;
    return trim(strip_tags($input, '<br><p><b><i><em><strong>'));
}

/**
 * Validate email address format
 * 
 * Uses PHP's built-in email validation filter to check if the provided
 * email address has a valid format.
 *
 * @param string $email The email address to validate
 * @return bool         True if email is valid, false otherwise
 * @since 0.2
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Require user authentication and validate session
 * 
 * Checks if user is logged in with valid session data. Redirects to login
 * page if not authenticated. Also implements session regeneration for security.
 * 
 * @return void
 * @since 0.2
 * @throws void Redirects to login page if not authenticated
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
 * Validate CSRF (Cross-Site Request Forgery) token
 * 
 * Compares provided token with session token using timing-safe comparison
 * to prevent CSRF attacks on form submissions.
 *
 * @param string $token The CSRF token to validate
 * @return bool         True if token is valid, false otherwise
 * @since 0.2
 */
function validate_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF (Cross-Site Request Forgery) token
 * 
 * Creates a cryptographically secure random token for CSRF protection.
 * Token is stored in session and should be included in forms.
 *
 * @return string 64-character hexadecimal CSRF token
 * @since 0.2
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
 * Validate artwork ownership authorization
 * 
 * Prevents users from manipulating artworks they don't own by verifying
 * ownership in the database before allowing operations.
 *
 * @param int $work_id The artwork ID to check
 * @param int $user_id The user ID claiming ownership
 * @param PDO $dbh     Database connection handle
 * @return bool        True if user owns the work, false otherwise
 * @since 0.2
 */
function validate_work_ownership($work_id, $user_id, $dbh) {
    $stmt = $dbh->prepare("SELECT owner FROM works WHERE id = ?");
    $stmt->execute([$work_id]);
    $result = $stmt->fetch();
    
    return $result && (int)$result['owner'] === (int)$user_id;
}

/**
 * Rate limiting protection against abuse
 * 
 * Implements simple session-based rate limiting to prevent abuse of
 * sensitive operations like login attempts or form submissions.
 *
 * @param string $action           The action to rate limit (e.g., 'login', 'trade')
 * @param int    $limit_per_minute Maximum number of attempts per minute
 * @return bool                    True if within rate limit, false if exceeded
 * @since 0.2
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