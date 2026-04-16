<?php
/**
 * CSV import/export service for PHP Constants Manager.
 *
 * Pure data layer — no HTTP headers, no redirects, no $_FILES. Both the
 * admin UI and the WP-CLI command consume it.
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPCM_Import_Export {

    /** @var PHPCM_DB */
    private $db;

    public function __construct(PHPCM_DB $db = null) {
        $this->db = $db ?: new PHPCM_DB();
    }

    /**
     * Import constants from a CSV string.
     *
     * @param string $csv_content Raw CSV (UTF-8 BOM tolerated).
     * @param array  $options {
     *     @type bool $overwrite Update rows whose name already exists.
     * }
     * @return array {
     *     @type bool  $ok             False only when the input is unparseable.
     *     @type string $error         Machine-readable code when $ok is false.
     *     @type int   $imported       New rows inserted.
     *     @type int   $updated        Existing rows overwritten.
     *     @type int   $skipped        Existing rows left untouched.
     *     @type int   $errors         Row-level validation or DB failures.
     *     @type array $error_details  Human-readable error messages per row.
     * }
     */
    public function import_from_string($csv_content, array $options = array()) {
        $overwrite = !empty($options['overwrite']);

        $stats = array(
            'ok'            => true,
            'error'         => '',
            'imported'      => 0,
            'updated'       => 0,
            'skipped'       => 0,
            'errors'        => 0,
            'error_details' => array(),
        );

        // Strip UTF-8 BOM if present.
        $bom = pack('H*', 'EFBBBF');
        if (substr($csv_content, 0, 3) === $bom) {
            $csv_content = substr($csv_content, 3);
        }

        $csv_data = $this->parse_csv($csv_content);

        if (empty($csv_data) || !isset($csv_data[0][0])) {
            $stats['ok']    = false;
            $stats['error'] = 'empty_file';
            return $stats;
        }

        $first_row  = $csv_data[0];
        $first_cell = strtolower(trim($first_row[0]));

        if ($first_cell !== 'name' && $first_cell !== 'constant name') {
            $stats['ok']    = false;
            $stats['error'] = 'missing_header';
            return $stats;
        }

        if (count($first_row) < 3) {
            $stats['ok']    = false;
            $stats['error'] = 'invalid_header';
            return $stats;
        }

        $total_rows = count($csv_data);
        for ($i = 1; $i < $total_rows; $i++) {
            $data            = $csv_data[$i];
            $csv_line_number = $i + 1;

            if (empty(array_filter($data))) {
                continue;
            }

            if (count($data) < 3) {
                $stats['errors']++;
                $stats['error_details'][] = sprintf(
                    /* translators: %d: line number in CSV file */
                    __('Line %d: Missing required columns (need at least Name, Value, Type)', 'php-constants-manager'),
                    $csv_line_number
                );
                continue;
            }

            $name        = trim($data[0]);
            $value       = isset($data[1]) ? trim($data[1]) : '';
            $type        = isset($data[2]) ? trim($data[2]) : 'string';
            $is_active   = isset($data[3]) ? (bool) $data[3] : true;
            $description = isset($data[4]) ? trim($data[4]) : '';

            if (!phpcm_validate_constant_name($name)) {
                $stats['errors']++;
                $stats['error_details'][] = sprintf(
                    /* translators: 1: line number in CSV file, 2: invalid constant name */
                    __('Line %1$d: Invalid constant name "%2$s" (must be uppercase letters, numbers, and underscores only)', 'php-constants-manager'),
                    $csv_line_number,
                    esc_html($name)
                );
                continue;
            }

            if (!in_array($type, phpcm_allowed_types(), true)) {
                $type = 'string';
            }

            $validation = phpcm_validate_constant_value($value, $type);
            if ($validation['error']) {
                $stats['errors']++;
                $stats['error_details'][] = sprintf(
                    /* translators: 1: line number in CSV file, 2: validation error message, 3: constant name */
                    __('Line %1$d: %2$s (Constant: %3$s)', 'php-constants-manager'),
                    $csv_line_number,
                    $validation['message'],
                    esc_html($name)
                );
                continue;
            }
            $value = $validation['value'];

            $existing = $this->db->get_constant_by_name($name);
            if ($existing) {
                if ($overwrite) {
                    $result = $this->db->update_constant($existing->id, array(
                        'value'       => $value,
                        'type'        => $type,
                        'is_active'   => $is_active,
                        'description' => $description,
                    ));
                    if ($result !== false) {
                        $stats['updated']++;
                    } else {
                        $stats['errors']++;
                        $stats['error_details'][] = sprintf(
                            /* translators: 1: line number in CSV file, 2: constant name */
                            __('Line %1$d: Database error updating constant "%2$s"', 'php-constants-manager'),
                            $csv_line_number,
                            esc_html($name)
                        );
                    }
                } else {
                    $stats['skipped']++;
                }
                continue;
            }

            $result = $this->db->insert_constant(array(
                'name'        => $name,
                'value'       => $value,
                'type'        => $type,
                'is_active'   => $is_active,
                'description' => $description,
            ));

            if ($result) {
                $stats['imported']++;
            } else {
                $stats['errors']++;
                $stats['error_details'][] = sprintf(
                    /* translators: 1: line number in CSV file, 2: constant name */
                    __('Line %1$d: Database error saving constant "%2$s"', 'php-constants-manager'),
                    $csv_line_number,
                    esc_html($name)
                );
            }
        }

        return $stats;
    }

    /**
     * Build a CSV string from current constants.
     *
     * @param array $filters PHPCM_DB::get_constants() arguments (is_active, type, search).
     * @return string CSV body. Does not include a UTF-8 BOM — add one at the HTTP layer if desired.
     */
    public function export_to_string(array $filters = array()) {
        $constants = $this->db->get_constants($filters);

        $output = "Name,Value,Type,Active,Description\r\n";
        foreach ($constants as $constant) {
            $row = array(
                $constant->name,
                $constant->value,
                $constant->type,
                $constant->is_active ? '1' : '0',
                $constant->description,
            );
            $output .= $this->csv_row($row) . "\r\n";
        }
        return $output;
    }

    private function csv_row(array $fields) {
        $escaped = array();
        foreach ($fields as $field) {
            if ($field === null) {
                $escaped[] = '';
                continue;
            }
            $field = (string) $field;
            if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false || strpos($field, "\r") !== false) {
                $escaped[] = '"' . str_replace('"', '""', $field) . '"';
            } else {
                $escaped[] = $field;
            }
        }
        return implode(',', $escaped);
    }

    /**
     * Parse a CSV string into rows. Handles quoted fields containing commas,
     * embedded CR/LF newlines, and escaped double-quotes (`""`). Rows that are
     * entirely empty are dropped. Supports LF, CRLF, and CR line endings.
     *
     * @param string $content CSV body (no UTF-8 BOM — strip it before calling).
     * @return array<int, array<int, string>>
     */
    private function parse_csv($content) {
        $rows      = array();
        $row       = array();
        $field     = '';
        $in_quotes = false;
        $len       = strlen($content);

        $finish_row = function () use (&$rows, &$row, &$field) {
            $row[] = $field;
            $field = '';
            $empty = true;
            foreach ($row as $cell) {
                if (trim($cell) !== '') {
                    $empty = false;
                    break;
                }
            }
            if (!$empty) {
                $rows[] = $row;
            }
            $row = array();
        };

        for ($i = 0; $i < $len; $i++) {
            $char = $content[$i];

            if ($in_quotes) {
                if ($char === '"') {
                    if ($i + 1 < $len && $content[$i + 1] === '"') {
                        $field .= '"';
                        $i++;
                    } else {
                        $in_quotes = false;
                    }
                } else {
                    $field .= $char;
                }
                continue;
            }

            if ($char === '"') {
                $in_quotes = true;
            } elseif ($char === ',') {
                $row[] = $field;
                $field = '';
            } elseif ($char === "\n" || $char === "\r") {
                if ($char === "\r" && $i + 1 < $len && $content[$i + 1] === "\n") {
                    $i++;
                }
                $finish_row();
            } else {
                $field .= $char;
            }
        }

        // Flush trailing field / row (file without terminating newline).
        if ($field !== '' || !empty($row)) {
            $finish_row();
        }

        return $rows;
    }
}
