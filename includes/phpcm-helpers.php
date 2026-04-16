<?php
/**
 * Shared helpers for PHP Constants Manager.
 *
 * Single source of truth for value casting, validation, and formatting.
 * Used by the main plugin, the generated MU plugin, the CSV import/export
 * service, and the WP-CLI command class.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('phpcm_allowed_types')) {
    function phpcm_allowed_types() {
        return array('string', 'integer', 'float', 'boolean', 'null');
    }
}

if (!function_exists('phpcm_validate_constant_name')) {
    /**
     * PHP constant names must start with an uppercase letter and contain
     * only uppercase letters, digits, and underscores.
     */
    function phpcm_validate_constant_name($name) {
        return (bool) preg_match('/^[A-Z][A-Z0-9_]*$/', (string) $name);
    }
}

if (!function_exists('phpcm_cast_value')) {
    /**
     * Convert a stored string value into the typed PHP value that should
     * be passed to define(). Mirrors the casting contract documented in
     * the admin UI; must stay identical to the MU-plugin fallback.
     */
    function phpcm_cast_value($value, $type) {
        switch ($type) {
            case 'boolean':
                if (is_string($value)) {
                    $lower_value = strtolower(trim($value));
                    if (in_array($lower_value, array('true', '1', 'yes', 'on'), true)) {
                        return true;
                    }
                    if (in_array($lower_value, array('false', '0', 'no', 'off', ''), true)) {
                        return false;
                    }
                    $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    return $filtered === null ? false : $filtered;
                }
                if (is_numeric($value)) {
                    return (bool) intval($value);
                }
                return (bool) $value;

            case 'integer':
                return is_numeric($value) ? intval($value) : 0;

            case 'float':
                return is_numeric($value) ? floatval($value) : 0.0;

            case 'null':
                return null;

            case 'string':
            default:
                return (string) $value;
        }
    }
}

if (!function_exists('phpcm_validate_constant_value')) {
    /**
     * Validate and normalize a user-supplied value for a given type.
     *
     * @return array{error:bool, message:string, value:string}
     */
    function phpcm_validate_constant_value($value, $type) {
        $result = array(
            'error'   => false,
            'message' => '',
            'value'   => $value,
        );

        switch ($type) {
            case 'string':
                break;

            case 'integer':
                if (!is_numeric($value) || (string) (int) $value !== (string) $value) {
                    $result['error']   = true;
                    $result['message'] = sprintf(
                        /* translators: %s: the invalid value entered by user */
                        __('Invalid integer value "%s". Please enter a whole number (e.g., 42, -10, 0).', 'php-constants-manager'),
                        esc_html($value)
                    );
                } else {
                    $result['value'] = (string) (int) $value;
                }
                break;

            case 'float':
                if (!is_numeric($value)) {
                    $result['error']   = true;
                    $result['message'] = sprintf(
                        /* translators: %s: the invalid value entered by user */
                        __('Invalid float value "%s". Please enter a number (e.g., 3.14, -2.5, 10).', 'php-constants-manager'),
                        esc_html($value)
                    );
                } else {
                    $result['value'] = (string) (float) $value;
                }
                break;

            case 'boolean':
                $lower_value = strtolower(trim((string) $value));
                $valid_true  = array('true', '1', 'yes', 'on');
                $valid_false = array('false', '0', 'no', 'off', '');
                if (!in_array($lower_value, array_merge($valid_true, $valid_false), true)) {
                    $result['error']   = true;
                    $result['message'] = sprintf(
                        /* translators: %s: the invalid value entered by user */
                        __('Invalid boolean value "%s". Please enter one of: true, false, 1, 0, yes, no, on, off (or leave empty for false).', 'php-constants-manager'),
                        esc_html($value)
                    );
                } else {
                    $result['value'] = in_array($lower_value, $valid_true, true) ? 'true' : 'false';
                }
                break;

            case 'null':
                $result['value'] = '';
                break;

            default:
                $result['error']   = true;
                $result['message'] = sprintf(
                    /* translators: %s: the invalid constant type */
                    __('Invalid constant type "%s".', 'php-constants-manager'),
                    esc_html($type)
                );
        }

        return $result;
    }
}

if (!function_exists('phpcm_format_constant_value')) {
    /**
     * Production-safe display format for a PHP value.
     */
    function phpcm_format_constant_value($value) {
        if (is_null($value)) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_string($value)) {
            return '"' . esc_html($value) . '"';
        }
        if (is_numeric($value)) {
            return esc_html((string) $value);
        }
        if (is_array($value)) {
            return 'Array(' . count($value) . ')';
        }
        if (is_object($value)) {
            return 'Object(' . get_class($value) . ')';
        }
        return esc_html((string) $value);
    }
}
