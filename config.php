<?php
/**
 * Environment-specific configuration for Fantasy Collecting.
 *
 * This file contains environment-specific settings that can be customized
 * for different deployment environments (development, staging, production).
 *
 * @author     William Shaw <william.shaw@duke.edu>
 *
 * @version    0.2 (modernized)
 *
 * @since      2025-09-10
 *
 * @license    MIT
 */

// Environment detection - can be set via environment variable or default to development
$environment = $_ENV['FCN_ENV'] ?? $_SERVER['FCN_ENV'] ?? 'development';

// Environment-specific configuration
$config = [
    'development' => [
        'debug' => true,
        'error_reporting' => E_ALL,
        'display_errors' => true,
        'log_errors' => true,
        'database' => [
            'host' => 'localhost',
            'port' => 3306,
            'charset' => 'utf8mb4'
        ],
        'security' => [
            'session_cookie_secure' => false, // Allow HTTP in development
            'csrf_protection' => true
        ],
        'external_resources' => [
            'd3js_url' => 'https://d3js.org/d3.v2.js'
        ]
    ],
    'production' => [
        'debug' => false,
        'error_reporting' => E_ERROR | E_WARNING,
        'display_errors' => false,
        'log_errors' => true,
        'database' => [
            'host' => 'localhost',
            'port' => 3306,
            'charset' => 'utf8mb4'
        ],
        'security' => [
            'session_cookie_secure' => true, // Require HTTPS in production
            'csrf_protection' => true
        ],
        'external_resources' => [
            'd3js_url' => 'https://d3js.org/d3.v2.js'
        ]
    ],
    'testing' => [
        'debug' => true,
        'error_reporting' => E_ALL,
        'display_errors' => true,
        'log_errors' => false,
        'database' => [
            'host' => 'localhost',
            'port' => 3306,
            'charset' => 'utf8mb4'
        ],
        'security' => [
            'session_cookie_secure' => false,
            'csrf_protection' => false // Disable for easier testing
        ],
        'external_resources' => [
            'd3js_url' => 'https://d3js.org/d3.v2.js'
        ]
    ]
];

// Get current environment config or fallback to development
$currentConfig = $config[$environment] ?? $config['development'];

// Apply PHP settings based on environment
if (isset($currentConfig['error_reporting'])) {
    error_reporting($currentConfig['error_reporting']);
}
if (isset($currentConfig['display_errors'])) {
    ini_set('display_errors', $currentConfig['display_errors'] ? '1' : '0');
}
if (isset($currentConfig['log_errors'])) {
    ini_set('log_errors', $currentConfig['log_errors'] ? '1' : '0');
}

// Load version from authoritative source
$FCN_VERSION = trim(file_get_contents(__DIR__ . '/VERSION'));

// Make config globally accessible
$GLOBALS['FCN_CONFIG'] = $currentConfig;
$GLOBALS['FCN_ENVIRONMENT'] = $environment;
$GLOBALS['FCN_VERSION'] = $FCN_VERSION;

/**
 * Get a configuration value with optional default.
 *
 * @param string $key     Configuration key using dot notation (e.g., 'database.host')
 * @param mixed  $default Default value if key not found
 *
 * @return mixed Configuration value or default
 */
function getConfig($key, $default = null)
{
    $config = $GLOBALS['FCN_CONFIG'];
    $keys = explode('.', $key);

    foreach ($keys as $segment) {
        if (!is_array($config) || !array_key_exists($segment, $config)) {
            return $default;
        }
        $config = $config[$segment];
    }

    return $config;
}

/**
 * Check if the application is running in debug mode.
 *
 * @return bool True if debug mode is enabled
 */
function isDebugMode()
{
    return getConfig('debug', false);
}

/**
 * Get the current environment name.
 *
 * @return string Current environment (development, production, testing)
 */
function getCurrentEnvironment()
{
    return $GLOBALS['FCN_ENVIRONMENT'];
}

/**
 * Get the current application version.
 *
 * @return string Current version from VERSION file
 */
function getVersion()
{
    return $GLOBALS['FCN_VERSION'];
}

/**
 * Get version information including environment.
 *
 * @param bool $includeEnvironment Whether to include environment in output
 *
 * @return string Formatted version string
 */
function getVersionInfo($includeEnvironment = true)
{
    $version = getVersion();
    if ($includeEnvironment) {
        $env = getCurrentEnvironment();
        return "Fantasy Collecting v{$version} ({$env})";
    }

    return "Fantasy Collecting v{$version}";
}

/**
 * Compare current version with a given version.
 *
 * @param string $compareVersion Version to compare against (e.g., '0.1.0')
 *
 * @return int -1 if current < compare, 0 if equal, 1 if current > compare
 */
function compareVersion($compareVersion)
{
    return version_compare(getVersion(), $compareVersion);
}