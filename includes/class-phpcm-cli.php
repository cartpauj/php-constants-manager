<?php
/**
 * WP-CLI commands for PHP Constants Manager.
 *
 * Registered only when WP_CLI is defined. All commands share the plugin's
 * existing PHPCM_DB layer and helper functions so behavior matches the
 * admin UI exactly.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Manage PHP constants stored by the PHP Constants Manager plugin.
 */
class PHPCM_CLI_Command extends WP_CLI_Command {

    /** @var PHPCM_DB */
    private $db;

    public function __construct() {
        $this->db = new PHPCM_DB();
    }

    /**
     * List managed constants.
     *
     * ## OPTIONS
     *
     * [--active]
     * : Only active constants.
     *
     * [--inactive]
     * : Only inactive constants.
     *
     * [--type=<type>]
     * : Filter by type.
     * ---
     * options:
     *   - string
     *   - integer
     *   - float
     *   - boolean
     *   - 'null'
     * ---
     *
     * [--search=<term>]
     * : Match against name, value, or description.
     *
     * [--fields=<fields>]
     * : Comma-separated list of columns to show. Defaults to name,value,type,active.
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     *   - count
     *   - ids
     * ---
     *
     * ## EXAMPLES
     *
     *     wp phpcm list
     *     wp phpcm list --active --type=boolean
     *     wp phpcm list --fields=name,value --format=json
     *
     * @subcommand list
     */
    public function list_($args, $assoc_args) {
        $query = array();
        if (!empty($assoc_args['active']) && !empty($assoc_args['inactive'])) {
            WP_CLI::error('Use either --active or --inactive, not both.');
        }
        if (!empty($assoc_args['active'])) {
            $query['is_active'] = true;
        } elseif (!empty($assoc_args['inactive'])) {
            $query['is_active'] = false;
        }
        if (!empty($assoc_args['type'])) {
            $query['type'] = $assoc_args['type'];
        }
        if (!empty($assoc_args['search'])) {
            $query['search'] = $assoc_args['search'];
        }

        $rows = array();
        foreach ($this->db->get_constants($query) as $c) {
            $rows[] = $this->row_to_array($c);
        }

        $format = isset($assoc_args['format']) ? $assoc_args['format'] : 'table';
        if ($format === 'ids') {
            WP_CLI::line(implode(' ', wp_list_pluck($rows, 'name')));
            return;
        }

        $fields = isset($assoc_args['fields'])
            ? array_map('trim', explode(',', $assoc_args['fields']))
            : array('name', 'value', 'type', 'active');

        WP_CLI\Utils\format_items($format, $rows, $fields);
    }

    /**
     * Show a single managed constant.
     *
     * ## OPTIONS
     *
     * <name>
     * : Constant name.
     *
     * [--field=<field>]
     * : Print just one field's value.
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - yaml
     *   - csv
     * ---
     */
    public function get($args, $assoc_args) {
        list($name) = $args;
        $row = $this->db->get_constant_by_name($name);
        if (!$row) {
            WP_CLI::error(sprintf('Constant "%s" is not managed by this plugin.', $name));
        }

        $data = $this->row_to_array($row);
        $formatter = new WP_CLI\Formatter($assoc_args, array_keys($data));
        $formatter->display_item($data);
    }

    /**
     * Add a new constant.
     *
     * ## OPTIONS
     *
     * <name>
     * : Constant name (uppercase, starts with a letter).
     *
     * [<value>]
     * : Constant value. Omit for null type. Use - to read from stdin.
     *
     * [--type=<type>]
     * : Value type.
     * ---
     * default: string
     * options:
     *   - string
     *   - integer
     *   - float
     *   - boolean
     *   - 'null'
     * ---
     *
     * [--description=<text>]
     * : Optional description.
     *
     * [--inactive]
     * : Create in inactive state (default is active).
     *
     * [--porcelain]
     * : Print only the new row's ID on success.
     *
     * ## EXAMPLES
     *
     *     wp phpcm add WP_DEBUG true --type=boolean
     *     wp phpcm add MY_API_KEY "abc123" --description="External API key"
     *     wp phpcm add MY_NULL --type=null
     */
    public function add($args, $assoc_args) {
        $name  = $args[0];
        $value = isset($args[1]) ? $args[1] : '';
        if ($value === '-') {
            $value = trim(file_get_contents('php://stdin'));
        }
        $type        = isset($assoc_args['type']) ? $assoc_args['type'] : 'string';
        $description = isset($assoc_args['description']) ? $assoc_args['description'] : '';
        $is_active   = empty($assoc_args['inactive']);

        if (!phpcm_validate_constant_name($name)) {
            WP_CLI::error('Invalid constant name. Must start with a letter and contain only uppercase letters, digits, and underscores.');
        }
        if (!in_array($type, phpcm_allowed_types(), true)) {
            WP_CLI::error(sprintf('Invalid type "%s".', $type));
        }
        if ($this->db->get_constant_by_name($name)) {
            WP_CLI::error(sprintf('A constant named "%s" already exists. Use `wp phpcm update` to change it.', $name));
        }

        $validation = phpcm_validate_constant_value($value, $type);
        if ($validation['error']) {
            WP_CLI::error(wp_strip_all_tags($validation['message']));
        }

        $result = $this->db->insert_constant(array(
            'name'        => $name,
            'value'       => $validation['value'],
            'type'        => $type,
            'is_active'   => $is_active,
            'description' => $description,
        ));
        if ($result === false) {
            WP_CLI::error('Failed to save constant.');
        }

        if (!empty($assoc_args['porcelain'])) {
            global $wpdb;
            WP_CLI::line((int) $wpdb->insert_id);
            return;
        }
        WP_CLI::success(sprintf('Added %s.', $name));
    }

    /**
     * Update an existing constant.
     *
     * ## OPTIONS
     *
     * <name>
     * : Constant name.
     *
     * [--value=<value>]
     * : New value. Use - to read from stdin.
     *
     * [--type=<type>]
     * : New type.
     * ---
     * options:
     *   - string
     *   - integer
     *   - float
     *   - boolean
     *   - 'null'
     * ---
     *
     * [--description=<text>]
     * : New description.
     *
     * [--active]
     * : Mark active.
     *
     * [--inactive]
     * : Mark inactive.
     */
    public function update($args, $assoc_args) {
        list($name) = $args;
        $row = $this->db->get_constant_by_name($name);
        if (!$row) {
            WP_CLI::error(sprintf('Constant "%s" is not managed by this plugin.', $name));
        }

        $update = array();

        if (isset($assoc_args['type'])) {
            if (!in_array($assoc_args['type'], phpcm_allowed_types(), true)) {
                WP_CLI::error(sprintf('Invalid type "%s".', $assoc_args['type']));
            }
            $update['type'] = $assoc_args['type'];
        }

        $effective_type = isset($update['type']) ? $update['type'] : $row->type;

        if (array_key_exists('value', $assoc_args)) {
            $value = $assoc_args['value'];
            if ($value === '-') {
                $value = trim(file_get_contents('php://stdin'));
            }
            $validation = phpcm_validate_constant_value($value, $effective_type);
            if ($validation['error']) {
                WP_CLI::error(wp_strip_all_tags($validation['message']));
            }
            $update['value'] = $validation['value'];
        } elseif (isset($update['type'])) {
            // Type changed but no new value supplied — re-validate the existing
            // stored value against the new type so we don't leave a mismatched row.
            $validation = phpcm_validate_constant_value($row->value, $effective_type);
            if ($validation['error']) {
                WP_CLI::error(sprintf(
                    'Current value "%s" is not valid for type "%s". Pass --value=... to supply a compatible value.',
                    $row->value,
                    $effective_type
                ));
            }
            if ($validation['value'] !== $row->value) {
                $update['value'] = $validation['value'];
            }
        }

        if (isset($assoc_args['description'])) {
            $update['description'] = $assoc_args['description'];
        }

        if (!empty($assoc_args['active']) && !empty($assoc_args['inactive'])) {
            WP_CLI::error('Use either --active or --inactive, not both.');
        }
        if (!empty($assoc_args['active'])) {
            $update['is_active'] = true;
        } elseif (!empty($assoc_args['inactive'])) {
            $update['is_active'] = false;
        }

        if (empty($update)) {
            WP_CLI::warning('Nothing to update.');
            return;
        }

        $result = $this->db->update_constant($row->id, $update);
        if ($result === false) {
            WP_CLI::error('Failed to update constant.');
        }
        WP_CLI::success(sprintf('Updated %s.', $name));
    }

    /**
     * Delete one or more constants.
     *
     * ## OPTIONS
     *
     * <name>...
     * : Constant names to delete.
     *
     * [--yes]
     * : Skip confirmation prompt.
     */
    public function delete($args, $assoc_args) {
        WP_CLI::confirm(sprintf('Delete %d constant(s)?', count($args)), $assoc_args);

        $deleted = 0;
        foreach ($args as $name) {
            $row = $this->db->get_constant_by_name($name);
            if (!$row) {
                WP_CLI::warning(sprintf('Not found: %s', $name));
                continue;
            }
            if ($this->db->delete_constant($row->id) !== false) {
                $deleted++;
            } else {
                WP_CLI::warning(sprintf('Failed to delete: %s', $name));
            }
        }
        WP_CLI::success(sprintf('Deleted %d constant(s).', $deleted));
    }

    /**
     * Mark one or more constants active.
     *
     * ## OPTIONS
     *
     * <name>...
     * : Constant names.
     */
    public function activate($args) {
        $this->set_active($args, true);
    }

    /**
     * Mark one or more constants inactive.
     *
     * ## OPTIONS
     *
     * <name>...
     * : Constant names.
     */
    public function deactivate($args) {
        $this->set_active($args, false);
    }

    /**
     * Flip the active state of one or more constants.
     *
     * ## OPTIONS
     *
     * <name>...
     * : Constant names.
     */
    public function toggle($args) {
        foreach ($args as $name) {
            $row = $this->db->get_constant_by_name($name);
            if (!$row) {
                WP_CLI::warning(sprintf('Not found: %s', $name));
                continue;
            }
            $this->db->toggle_constant($row->id);
            WP_CLI::log(sprintf('%s -> %s', $name, $row->is_active ? 'inactive' : 'active'));
        }
        WP_CLI::success('Done.');
    }

    private function set_active(array $names, $active) {
        foreach ($names as $name) {
            $row = $this->db->get_constant_by_name($name);
            if (!$row) {
                WP_CLI::warning(sprintf('Not found: %s', $name));
                continue;
            }
            $this->db->update_constant($row->id, array('is_active' => $active));
        }
        WP_CLI::success($active ? 'Activated.' : 'Deactivated.');
    }

    /**
     * Report whether a constant is currently defined, and by whom.
     *
     * ## OPTIONS
     *
     * <name>
     * : Constant name.
     *
     * [--field=<field>]
     * : Print just one field's value.
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - yaml
     *   - csv
     * ---
     *
     * ## EXAMPLES
     *
     *     wp phpcm defined WP_DEBUG
     *     wp phpcm defined MY_FLAG --format=json
     */
    public function defined($args, $assoc_args) {
        list($name) = $args;

        $row     = $this->db->get_constant_by_name($name);
        $is_defined = defined($name);
        $plugin  = PHP_Constants_Manager::get_instance();
        $source_code = $plugin->get_constant_source($name);

        if ($row) {
            $managed = $row->is_active ? 'yes (active)' : 'yes (inactive)';
        } else {
            $managed = 'no';
        }

        switch ($source_code) {
            case 'early-load':
                $source = 'this plugin (early-load)';
                break;
            case 'plugin':
                $source = 'this plugin';
                break;
            case 'elsewhere':
                $source = 'elsewhere (wp-config.php or similar)';
                break;
            default:
                $source = 'not defined';
        }

        $data = array(
            'name'    => $name,
            'managed' => $managed,
            'defined' => $is_defined ? 'yes' : 'no',
            'source'  => $source,
            'value'   => $is_defined ? phpcm_format_constant_value(constant($name)) : '',
            'type'    => $row ? $row->type : '',
        );

        $formatter = new WP_CLI\Formatter($assoc_args, array_keys($data));
        $formatter->display_item($data);
    }

    /**
     * List every PHP constant currently defined in this process.
     *
     * ## OPTIONS
     *
     * [--user-defined]
     * : Only user-defined constants (skip PHP/extension built-ins).
     *
     * [--search=<term>]
     * : Substring match against constant name.
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     *   - count
     * ---
     *
     * @subcommand all-defines
     */
    public function all_defines($args, $assoc_args) {
        $grouped = get_defined_constants(true);
        $user_only = !empty($assoc_args['user-defined']);
        $search    = isset($assoc_args['search']) ? strtolower($assoc_args['search']) : '';

        $rows = array();
        foreach ($grouped as $category => $constants) {
            if ($user_only && $category !== 'user') {
                continue;
            }
            foreach ($constants as $const_name => $value) {
                if ($search !== '' && strpos(strtolower($const_name), $search) === false) {
                    continue;
                }
                $rows[] = array(
                    'name'     => $const_name,
                    'value'    => phpcm_format_constant_value($value),
                    'category' => $category,
                );
            }
        }

        usort($rows, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        $format = isset($assoc_args['format']) ? $assoc_args['format'] : 'table';
        WP_CLI\Utils\format_items($format, $rows, array('name', 'value', 'category'));
    }

    /**
     * Show plugin health: table state, counts, early-loading status.
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - yaml
     * ---
     */
    public function status($args, $assoc_args) {
        $table_exists = $this->db->table_exists();
        $total = $table_exists ? (int) $this->db->count_constants() : 0;
        $active = $table_exists ? (int) $this->db->count_constants(array('is_active' => true)) : 0;

        $mu_path = WPMU_PLUGIN_DIR . '/0001-php-constants-manager-early.php';
        $mu_enabled = (bool) get_option('phpcm_early_loading_enabled', false);
        $mu_present = file_exists($mu_path);

        $data = array(
            'version'              => defined('PHPCM_VERSION') ? PHPCM_VERSION : 'unknown',
            'table'                => $table_exists ? 'present' : 'missing',
            'constants_total'      => $total,
            'constants_active'     => $active,
            'constants_inactive'   => $total - $active,
            'early_loading_option' => $mu_enabled ? 'on' : 'off',
            'early_loader_file'    => $mu_present ? 'present' : 'missing',
        );

        $formatter = new WP_CLI\Formatter($assoc_args, array_keys($data));
        $formatter->display_item($data);
    }

    /**
     * Import constants from a CSV file.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to CSV file. Use - to read from stdin.
     *
     * [--overwrite]
     * : Update rows whose name already exists instead of skipping them.
     *
     * ## EXAMPLES
     *
     *     wp phpcm import constants.csv
     *     cat backup.csv | wp phpcm import - --overwrite
     */
    public function import($args, $assoc_args) {
        list($file) = $args;
        if ($file === '-') {
            $contents = file_get_contents('php://stdin');
        } else {
            if (!file_exists($file)) {
                WP_CLI::error(sprintf('File not found: %s', $file));
            }
            $contents = file_get_contents($file);
            if ($contents === false) {
                WP_CLI::error(sprintf('Could not read file: %s', $file));
            }
        }

        $service = new PHPCM_Import_Export($this->db);
        $stats   = $service->import_from_string($contents, array('overwrite' => !empty($assoc_args['overwrite'])));

        if (!$stats['ok']) {
            WP_CLI::error(sprintf('CSV could not be parsed (%s).', $stats['error']));
        }

        foreach ($stats['error_details'] as $msg) {
            WP_CLI::warning(wp_strip_all_tags($msg));
        }

        WP_CLI::success(sprintf(
            '%d imported, %d updated, %d skipped, %d errors.',
            $stats['imported'],
            $stats['updated'],
            $stats['skipped'],
            $stats['errors']
        ));
    }

    /**
     * Export constants to CSV.
     *
     * ## OPTIONS
     *
     * [<file>]
     * : Destination path. Omit to write to stdout.
     *
     * [--active]
     * : Only active constants.
     *
     * [--inactive]
     * : Only inactive constants.
     *
     * [--type=<type>]
     * : Only constants of the given type.
     * ---
     * options:
     *   - string
     *   - integer
     *   - float
     *   - boolean
     *   - 'null'
     * ---
     *
     * ## EXAMPLES
     *
     *     wp phpcm export backup.csv
     *     wp phpcm export --active > active.csv
     */
    public function export($args, $assoc_args) {
        $filters = array();
        if (!empty($assoc_args['active']) && !empty($assoc_args['inactive'])) {
            WP_CLI::error('Use either --active or --inactive, not both.');
        }
        if (!empty($assoc_args['active'])) {
            $filters['is_active'] = true;
        } elseif (!empty($assoc_args['inactive'])) {
            $filters['is_active'] = false;
        }
        if (!empty($assoc_args['type'])) {
            $filters['type'] = $assoc_args['type'];
        }

        $service = new PHPCM_Import_Export($this->db);
        $csv = $service->export_to_string($filters);

        if (empty($args)) {
            WP_CLI::line(rtrim($csv, "\r\n"));
            return;
        }

        $file = $args[0];
        if (file_put_contents($file, $csv) === false) {
            WP_CLI::error(sprintf('Could not write file: %s', $file));
        }
        WP_CLI::success(sprintf('Wrote %s.', $file));
    }

    /**
     * Manage the early-loading MU plugin.
     *
     * ## OPTIONS
     *
     * <action>
     * : enable, disable, or status.
     * ---
     * options:
     *   - enable
     *   - disable
     *   - status
     * ---
     *
     * @subcommand early-loading
     */
    public function early_loading($args) {
        list($action) = $args;
        $mu_path = WPMU_PLUGIN_DIR . '/0001-php-constants-manager-early.php';

        switch ($action) {
            case 'status':
                WP_CLI::line(sprintf('Option:  %s', get_option('phpcm_early_loading_enabled', false) ? 'on' : 'off'));
                WP_CLI::line(sprintf('File:    %s', file_exists($mu_path) ? $mu_path : 'not present'));
                return;

            case 'enable':
                $result = PHP_Constants_Manager::get_instance()->set_early_loading(true);
                if (is_wp_error($result)) {
                    WP_CLI::error($result->get_error_message());
                }
                WP_CLI::success('Early loading enabled.');
                return;

            case 'disable':
                $result = PHP_Constants_Manager::get_instance()->set_early_loading(false);
                if (is_wp_error($result)) {
                    WP_CLI::error($result->get_error_message());
                }
                WP_CLI::success('Early loading disabled.');
                return;
        }
    }

    /**
     * Convert a DB row object into a formatter-friendly array.
     */
    private function row_to_array($row) {
        return array(
            'id'          => (int) $row->id,
            'name'        => $row->name,
            'value'       => $row->value,
            'type'        => $row->type,
            'active'      => $row->is_active ? 'yes' : 'no',
            'description' => $row->description,
            'created_at'  => $row->created_at,
            'updated_at'  => $row->updated_at,
        );
    }
}

WP_CLI::add_command('phpcm', 'PHPCM_CLI_Command');
