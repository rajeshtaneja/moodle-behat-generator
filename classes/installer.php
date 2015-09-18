<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace moodlehq\behat_generator;
use Symfony\Component\Console\Output\OutputInterface;

global $CFG;

require_once($CFG->libdir . "/testing/classes/util.php");
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/installlib.php');
require_once($CFG->libdir . '/upgradelib.php');

/**
 * Utils for performance-related stuff
 *
 * @package    moodlehq_performancetoolkit_sitegenerator
 * @copyright  2015 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    die(); // No access from web!.
}

/**
 * Init/reset utilities for Performance test site.
 *
 * @package   moodlehq_performancetoolkit_sitegenerator
 * @copyright 2015 Rajesh Taneja
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class installer extends \testing_util {

    /**
     * @var list of exit codes.
     */
    const SITE_ERROR_CONFIG = "err_config";
    const SITE_ERROR_INSTALL = "err_install";
    const SITE_INSTALLED = "installed";
    const SITE_ERROR_REINSTALL = "err_reinstall";

    /**
     * Install a site using $CFG->dataroot and $CFG->prefix
     *
     * @return string|bool true on success, else exception code.
     */
    public static function install_site($sitefullname = "Performance test site",
                                        $siteshortname = "Performance test site",
                                        $adminpass = "admin",
                                        $adminemail = "admin@example.com") {
        global $DB, $CFG;

        require_once($CFG->dirroot.'/user/lib.php');

        if (!defined('PERFORMANCE_SITE_GENERATOR')) {
            util::performance_exception('This method can be only used by performance site generator.');
        }

        // If already installed, then return with error.
        $tables = $DB->get_tables(false);
        if (!empty($tables)) {
            return(self::SITE_INSTALLED);
        }

        $options = array();
        $options['adminuser'] = 'admin';
        $options['adminpass'] = $adminpass;
        $options['fullname'] = $sitefullname;
        $options['shortname'] = $siteshortname;

        install_cli_database($options, false);

        $frontpagesummary = new \admin_setting_special_frontpagedesc();
        $frontpagesummary->write_setting($sitefullname);

        // Update admin user info.
        $user = $DB->get_record('user', array('username' => 'admin'));
        $user->email = $adminemail;
        $user->firstname = 'Admin';
        $user->lastname = 'User';
        $user->city = 'Perth';
        $user->country = 'AU';
        user_update_user($user, false);

        // Disable email message processor.
        $DB->set_field('message_processors', 'enabled', '0', array('name' => 'email'));

        // Disable some settings that are not wanted on test sites.
        set_config('noemailever', 1);

        // Enable web cron.
        set_config('cronclionly', 0);

        $CFG->dboptions =  array ('dbpersist' =>1);

        // Keeps the current version of components hash.
        self::store_versions_hash();
    }

    /**
     * Enables test mode
     *
     * It uses CFG->dataroot/performance
     *
     * Starts the test mode checking the composer installation and
     * the test environment and updating the available
     * features and steps definitions.
     *
     * Stores a file in dataroot/performance to allow Moodle to switch
     * to the test environment when using cli-server.
     *
     * @param string $sitesize size of site
     * @param array $optionaltestdata (optional), replace default template with this value.
     * @throws performance_exception
     * @return int
     */
    public static function enable_performance_sitemode($sitesize, $optionaltestdata = array()) {
        global $CFG;

        if (!defined('PERFORMANCE_SITE_GENERATOR')) {
            self::performance_exception('This method can be only used by performance site generator.');
        }

        // Checks the behat set up and the PHP version.
        if ($errorcode = self::check_setup_problem()) {
            return $errorcode;
        }

        // Check that test environment is correctly set up.
        if (self::test_environment_problem() !== self::SITE_INSTALLED) {
            return $errorcode;
        }

        // Make it a performance site, we have already checked for tables.
        if (!self::is_performance_site() && empty(get_config('core', 'perfromancesitehash'))) {
            self::store_versions_hash();
        }

        util::get_performance_dir(true);

        // Add moodle release and tool hash to performancesite.txt.
        $release = null;
        require("$CFG->dirroot/version.php");
        $contents = "release=".$release.PHP_EOL;
        if ($hash = util::get_performance_tool_hash()) {
            $contents .= "hash=" . $hash . PHP_EOL;
        }
        // Add tool version to the file. This will help identify the tool version used to generate site.
        $generatorconfig = util::get_tool_version();
        $contents .= "generatorversion=" . $generatorconfig . PHP_EOL;

        // Add feature data hash.
        $featuresethash = md5(serialize($optionaltestdata));
        $contents .= "featurehash=" . $featuresethash . PHP_EOL;

        // Finally add site size.
        $contents .= "sitesize=" . $sitesize . PHP_EOL;

        $filepath = util::get_tool_dir() . DIRECTORY_SEPARATOR . 'performancesite.txt';
        if (!file_put_contents($filepath, $contents)) {
            echo 'File ' . $filepath . ' can not be created' . PHP_EOL;
            exit(1);
        }

        // Check composer dependencies.
        if (!is_dir($CFG->dirroot.'/vendor')) {
            util::testing_update_composer_dependencies($CFG->dirroot);
        }

        util::create_test_feature($sitesize, $optionaltestdata);

        return 0;
    }

    /**
     * Disables test mode
     *
     * @throws performance_exception
     * @return bool true on success.
     */
    public static function disable_performance_sitemode() {

        if (!defined('PERFORMANCE_SITE_GENERATOR')) {
            util::performance_exception('This method can be only used by performance site generator.');
        }

        if (!self::is_performance_site()) {
            echo "Test environment was already disabled\n";
        } else {
            if (file_exists(util::get_tool_dir() . DIRECTORY_SEPARATOR . 'performancesite.txt')) {
                unlink(util::get_tool_dir() . DIRECTORY_SEPARATOR . 'performancesite.txt');
            }
        }

        return true;
    }

    /**
     * Returns the status of the behat test environment
     *
     * @return int Error code
     */
    public static function get_site_status(OutputInterface $output) {

        if (!defined('PERFORMANCE_SITE_GENERATOR')) {
            $output->writeln("<error>get_site_status(): PERFORMANCE_SITE_GENERATOR is not defined.</error>");
            exit(1);
        }

        // Checks the behat set up and the PHP version, returning an error code if something went wrong.
        if ($errorcode = self::check_setup_problem()) {
            return $errorcode;
        }

        // Check that test environment is correctly set up, stops execution.
        return self::test_environment_problem();
    }

    /**
     * Stores the version hash in both database and dataroot.
     */
    public static function store_versions_hash() {
        // Create performace dir., where all hash/data will be backed up.
        util::get_performance_dir(true);
        $hash = \core_component::get_all_versions_hash();

        // Add test db flag.
        set_config('perfromancesitehash', $hash);

        // Hash all plugin versions - helps with very fast detection of db structure changes.
        $hashfile = util::get_tool_dir() . '/versionshash.txt';
        file_put_contents($hashfile, $hash);
        testing_fix_file_permissions($hashfile);
    }

    /**
     * Stores the status of the database
     *
     * Serializes the contents and the structure and
     * stores it in the test framework space in dataroot
     * @param string $statename name - Name of the site state.
     *  - $filename_data.ser and
     *  - $filename_structure.ser
     */
    public static function store_database_state($statename = 'default') {
        global $DB;

        // Store data for all tables.
        $data = array();
        $structure = array();
        $tables = $DB->get_tables();
        foreach ($tables as $table) {
            $columns = $DB->get_columns($table);
            $structure[$table] = $columns;
            if (isset($columns['id']) and $columns['id']->auto_increment) {
                $data[$table] = $DB->get_records($table, array(), 'id ASC');
            } else {
                // There should not be many of these.
                $data[$table] = $DB->get_records($table, array());
            }
        }
        $data = serialize($data);
        $datafile = util::get_tool_dir() . DIRECTORY_SEPARATOR . $statename . '_data.ser';
        file_put_contents($datafile, $data);
        testing_fix_file_permissions($datafile);

        $structure = serialize($structure);
        $structurefile = util::get_tool_dir() . DIRECTORY_SEPARATOR . $statename . '_structure.ser';
        file_put_contents($structurefile, $structure);
        testing_fix_file_permissions($structurefile);
    }

    /**
     * Zip dataroot and save it with the file name.
     *
     * @param string $statename state of site.
     */
    public static function store_data_root_state($statename = 'default') {
        global $CFG;

        $datafile = util::get_tool_dir() . DIRECTORY_SEPARATOR . $statename . '.zip';

        // Get real path for our folder.
        $rootPath = realpath($CFG->dataroot);

        // Drop sessions foldre, as it's not needed while restoring.
        util::drop_dir($rootPath.DIRECTORY_SEPARATOR.'sessions');

        // Initialize archive object
        $zip = new \ZipArchive();

        // Create empty file, as it fails otherwise to create zip at times.
        touch($datafile);

        if ($zip->open($datafile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !==TRUE ) {
            echo("cannot create data root backup at <$datafile>\n");
            return false;
        }

        // Create recursive directory iterator.
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically).
            if (!$file->isDir()) {
                // Get real and relative path for current file.
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                if(file_exists($filePath)) {
                    // Add current file to archive.
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        // Zip archive will be created only after closing object.
        return $zip->close();
    }

    /**
     * Restore data root to the value before contents were generated.
     *
     * @param string $statename state of site.
     * @return bool
     */
    public static function restore_dataroot($statename) {

        $datafile = util::get_tool_dir() . DIRECTORY_SEPARATOR . $statename . '.zip';

        if (!file_exists($datafile)) {
            return false;
        }

        // Delete existing dataroot.
        // Clear file status cache, before checking file_exists.
        clearstatcache();

        // Clean up the dataroot folder.
        util::drop_dir(self::get_dataroot() . '/');

        $zip = new \ZipArchive;
        if ($zip->open($datafile) === TRUE) {
            $zip->extractTo(self::get_dataroot());
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Reset all database tables to default values.
     * @static
     * @param static $statename state of site.
     * @return bool true if reset done, false if skipped
     */
    public static function restore_database_state($statename) {
        global $DB, $CFG;

        if (!$data = self::get_table_data($statename)) {
            // Not initialised yet.
            return false;
        }
        if (!$structure = self::get_table_structure($statename)) {
            // Not initialised yet.
            return false;
        }

        // Install a new site and then restore data and sequence.
        self::drop_database(false);
        // xmldb_main_install gets system id from db which is causing it to fail randomly.
        // Set it to make it initial install.
        // Check: during_initial_install()
        $CFG->rolesactive = false;
        self::install_site();

        $tables = $DB->get_tables(false);

        $borkedmysql = false;
        if ($DB->get_dbfamily() === 'mysql') {
            $version = $DB->get_server_info();
            if (version_compare($version['version'], '5.6.0') == 1 and version_compare($version['version'], '5.6.16') == -1) {
                // Everything that comes from Oracle is evil!
                //
                // See http://dev.mysql.com/doc/refman/5.6/en/alter-table.html
                // You cannot reset the counter to a value less than or equal to to the value that is currently in use.
                //
                // From 5.6.16 release notes:
                //   InnoDB: The ALTER TABLE INPLACE algorithm would fail to decrease the auto-increment value.
                //           (Bug #17250787, Bug #69882)
                $borkedmysql = true;

            } else if (version_compare($version['version'], '10.0.0') == 1) {
                // And MariaDB is no better!
                // Let's hope they pick the patch sometime later...
                $borkedmysql = true;
            }
        }

        if ($borkedmysql) {
            $mysqlsequences = array();
            $prefix = $DB->get_prefix();
            $rs = $DB->get_recordset_sql("SHOW TABLE STATUS LIKE ?", array($prefix.'%'));
            foreach ($rs as $info) {
                $table = strtolower($info->name);
                if (strpos($table, $prefix) !== 0) {
                    // Incorrect table match caused by _ char.
                    continue;
                }
                if (!is_null($info->auto_increment)) {
                    $table = preg_replace('/^'.preg_quote($prefix, '/').'/', '', $table);
                    $mysqlsequences[$table] = $info->auto_increment;
                }
            }
        }

        foreach ($data as $table => $records) {
            if ($borkedmysql) {
                if (empty($records)) {
                    continue;
                }

                if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                    $current = $DB->get_records($table, array(), 'id ASC');
                    if ($current == $records) {
                        if (isset($mysqlsequences[$table]) and $mysqlsequences[$table] == $structure[$table]['id']->auto_increment) {
                            continue;
                        }
                    }
                }

                // Use TRUNCATE as a workaround and reinsert everything.
                $DB->delete_records($table, null);
                foreach ($records as $record) {
                    $DB->import_record($table, $record, false, true);
                }
                continue;
            }

            if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                $currentrecords = $DB->get_records($table, array(), 'id ASC');
                $changed = false;
                foreach ($records as $id => $record) {
                    if (!isset($currentrecords[$id])) {
                        $changed = true;
                        break;
                    }
                    if ((array)$record != (array)$currentrecords[$id]) {
                        $changed = true;
                        break;
                    }
                    unset($currentrecords[$id]);
                }
                if (!$changed) {
                    if ($currentrecords) {
                        $lastrecord = end($records);
                        $DB->delete_records_select($table, "id > ?", array($lastrecord->id));
                        continue;
                    } else {
                        continue;
                    }
                }
            }

            $DB->delete_records($table, array());
            foreach ($records as $record) {
                $DB->import_record($table, $record, false, true);
            }
        }

        // Reset all next record ids - aka sequences
        self::reset_database_sequences($statename);

        // Remove extra tables
        foreach ($tables as $table) {
            if (!isset($data[$table])) {
                $DB->get_manager()->drop_table(new \xmldb_table($table));
            }
        }

        return true;
    }

    /**
     * Reset all database sequences to initial values.
     *
     * @static
     * @return void
     */
    public static function reset_database_sequences($statename) {
        global $DB;

        if (!$data = self::get_table_data($statename)) {
            // Not initialised yet.
            return;
        }
        if (!$structure = self::get_table_structure($statename)) {
            // Not initialised yet.
            return;
        }

        self::$sequencenextstartingid = 1;


        $dbfamily = $DB->get_dbfamily();
        if ($dbfamily === 'postgres') {
            $queries = array();
            $prefix = $DB->get_prefix();
            foreach ($data as $table => $records) {
                if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                    $nextid = self::get_next_sequence_starting_value($records);
                    $queries[] = "ALTER SEQUENCE {$prefix}{$table}_id_seq RESTART WITH $nextid";
                }
            }
            if ($queries) {
                $DB->change_database_structure(implode(';', $queries));
            }

        } else if ($dbfamily === 'mysql') {
            $sequences = array();
            $prefix = $DB->get_prefix();
            $rs = $DB->get_recordset_sql("SHOW TABLE STATUS LIKE ?", array($prefix.'%'));
            foreach ($rs as $info) {
                $table = strtolower($info->name);
                if (strpos($table, $prefix) !== 0) {
                    // incorrect table match caused by _
                    continue;
                }
                if (!is_null($info->auto_increment)) {
                    $table = preg_replace('/^'.preg_quote($prefix, '/').'/', '', $table);
                    $sequences[$table] = $info->auto_increment;
                }
            }
            $rs->close();
            $prefix = $DB->get_prefix();
            foreach ($data as $table => $records) {
                if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                    if (isset($sequences[$table])) {
                        $nextid = self::get_next_sequence_starting_value($records);
                        if ($sequences[$table] != $nextid) {
                            $DB->change_database_structure("ALTER TABLE {$prefix}{$table} AUTO_INCREMENT = $nextid");
                        }

                    } else {
                        // some problem exists, fallback to standard code
                        $DB->get_manager()->reset_sequence($table);
                    }
                }
            }

        } else if ($dbfamily === 'oracle') {
            $sequences = self::get_sequencenames();
            $sequences = array_map('strtoupper', $sequences);
            $lookup = array_flip($sequences);

            $current = array();
            list($seqs, $params) = $DB->get_in_or_equal($sequences);
            $sql = "SELECT sequence_name, last_number FROM user_sequences WHERE sequence_name $seqs";
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $seq) {
                $table = $lookup[$seq->sequence_name];
                $current[$table] = $seq->last_number;
            }
            $rs->close();

            foreach ($data as $table => $records) {
                if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                    $lastrecord = end($records);
                    if ($lastrecord) {
                        $nextid = $lastrecord->id + 1;
                    } else {
                        $nextid = 1;
                    }
                    if (!isset($current[$table])) {
                        $DB->get_manager()->reset_sequence($table);
                    } else if ($nextid == $current[$table]) {
                        continue;
                    }
                    // reset as fast as possible - alternatively we could use http://stackoverflow.com/questions/51470/how-do-i-reset-a-sequence-in-oracle
                    $seqname = $sequences[$table];
                    $cachesize = $DB->get_manager()->generator->sequence_cache_size;
                    $DB->change_database_structure("DROP SEQUENCE $seqname");
                    $DB->change_database_structure("CREATE SEQUENCE $seqname START WITH $nextid INCREMENT BY 1 NOMAXVALUE CACHE $cachesize");
                }
            }

        } else {
            // note: does mssql support any kind of faster reset?
            // This also implies mssql will not use unique sequence values.
            foreach ($data as $table => $records) {
                if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                    $DB->get_manager()->reset_sequence($table);
                }
            }
        }
    }

    /**
     * Returns contents of all tables right after installation.
     * @static
     * @param string $statename name of state.
     * @return array  $table=>$records
     */
    protected static function get_table_data($statename) {

        $datafile = util::get_tool_dir() . DIRECTORY_SEPARATOR . $statename . "_data.ser";
        if (!file_exists($datafile)) {
            // Not initialised yet.
            return array();
        }

        if (!isset(self::$tabledata)) {
            $data = file_get_contents($datafile);
            self::$tabledata = unserialize($data);
        }

        if (!is_array(self::$tabledata)) {
            testing_error(1, 'Can not read dataroot/' . $statename . '_data.ser or invalid format, reinitialize test database.');
        }

        return self::$tabledata;
    }

    /**
     * Returns structure of all tables right after installation.
     * @static
     * @param string $statename name of state.
     * @return array $table=>$records
     */
    public static function get_table_structure($statename) {

        $structurefile = util::get_tool_dir() . DIRECTORY_SEPARATOR . $statename . "_structure.ser";
        if (!file_exists($structurefile)) {
            // Not initialised yet.
            return array();
        }

        if (!isset(self::$tablestructure)) {
            $data = file_get_contents($structurefile);
            self::$tablestructure = unserialize($data);
        }

        if (!is_array(self::$tablestructure)) {
            testing_error(1, 'Can not read dataroot/' . $statename . '_structure.ser or invalid format, reinitialize test database.');
        }

        return self::$tablestructure;
    }

    /**
     * Determine the next unique starting id sequences.
     *
     * @static
     * @param array $records The records to use to determine the starting value for the table.
     * @return int The value the sequence should be set to.
     */
    private static function get_next_sequence_starting_value($records) {
        $id = self::$sequencenextstartingid;

        // If there are records, calculate the minimum id we can use.
        // It must be bigger than the last record's id.
        if (!empty($records)) {
            $lastrecord = end($records);
            $id = max($id, $lastrecord->id + 1);
        }

        self::$sequencenextstartingid = $id;
        return $id;
    }

    /**
     * Drop the whole test database
     *
     * @param bool $displayprogress
     * @throws moodle_exception
     * @return bool. true on success.
     */
    public static function drop_database($displayprogress = false) {
        global $DB;

        $tables = $DB->get_tables(false);
        if (isset($tables['config'])) {
            // config always last to prevent problems with interrupted drops!
            unset($tables['config']);
            $tables['config'] = 'config';
        }

        if ($displayprogress) {
            echo "Dropping tables:\n";
        }
        $dotsonline = 0;
        foreach ($tables as $tablename) {
            $table = new \xmldb_table($tablename);
            $DB->get_manager()->drop_table($table);

            if ($dotsonline == 60) {
                if ($displayprogress) {
                    echo "\n";
                }
                $dotsonline = 0;
            }
            if ($displayprogress) {
                echo '.';
            }
            $dotsonline += 1;
        }
        if ($displayprogress) {
            echo "\n";
        }

        return true;
    }

    /**
     * Drops dataroot and remove test database tables
     *
     * @param bool $onlysite if true then only site will be removed.
     * @throws coding_exception
     * @return bool true on success.
     */
    public static function drop_site($onlysite = false) {

        if (!defined('PERFORMANCE_SITE_GENERATOR')) {
            util::performance_exception('This method can be only used by performance site generator.');
        }

        self::drop_database(true);
        util::drop_dir(self::get_dataroot() . '/');
        if (!$onlysite) {
            util::drop_dir(util::get_tool_dir() . '/', true);
        }

        return true;
    }

    /**
     * Checks if $CFG->wwwroot is available
     *
     * @return bool
     */
    public static function is_server_running() {
        global $CFG;

        $request = new \curl();
        $request->get($CFG->wwwroot);

        if ($request->get_errno() === 0) {
            return true;
        }
        return false;
    }

    /**
     * Does this site (db and dataroot) appear to be used for production?
     * We try very hard to prevent accidental damage done to production servers!!
     *
     * @static
     * @return bool
     */
    public static function is_performance_site() {
        global $DB;

        if (!file_exists(util::get_tool_dir() . DIRECTORY_SEPARATOR . 'performancesite.txt')) {
            // This is already tested in bootstrap script,
            // but anyway presence of this file means that site is enabled for performance testing.
            return false;
        }

        $tables = $DB->get_tables(false);
        if ($tables) {
            if (!$DB->get_manager()->table_exists('config')) {
                return false;
            }
            if (!get_config('core', 'perfromancesitehash')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns whether test database and dataroot were created using the current version codebase
     *
     * @return bool
     */
    public static function is_site_data_updated() {

        $datarootpath = util::get_performance_dir();

        if (!is_dir($datarootpath)) {
            return 1;
        }

        if (!file_exists($datarootpath . '/versionshash.txt')) {
            return 1;
        }

        $hash = \core_component::get_all_versions_hash();
        $oldhash = file_get_contents($datarootpath . '/versionshash.txt');

        if ($hash !== $oldhash) {
            return false;
        }

        $dbhash = get_config('core', 'perfromancesitehash');
        if ($hash !== $dbhash) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the test database and dataroot is ready
     * Stops execution if something went wrong
     * @throws moodle_exception
     * @return string error code.
     */
    protected static function test_environment_problem() {
        global $DB;

        if (!defined('PERFORMANCE_SITE_GENERATOR')) {
            util::performance_exception('This method can be only used by performance site generator.');
        }

        $tables = $DB->get_tables(false);
        if (empty($tables)) {
            return self::SITE_ERROR_INSTALL;
        }

        if (!self::is_site_data_updated()) {
            return self::SITE_ERROR_REINSTALL;
        }

        // No error.
        return self::SITE_INSTALLED;
    }

    /**
     * Checks if required config vaues are set.
     *
     * @return int Error code or 0 if all ok
     */
    public static function check_setup_problem() {
        global $CFG;

        // No empty values.
        if (empty($CFG->dataroot) || empty($CFG->prefix) || empty($CFG->wwwroot)) {
            return self::SITE_ERROR_CONFIG;
        }

        if (empty($CFG->dataroot) || !is_dir($CFG->dataroot) || !is_writable($CFG->dataroot)) {
            return self::SITE_ERROR_CONFIG;
        }

        return 0;
    }

    /**
     * Save state of current site. Dataroot and database.
     *
     * @param string $statename
     * @return int 0 on success else error code.
     */
    public static function store_site_state($statename = "default") {
        echo "Saving database state" . PHP_EOL;
        // Save database and dataroot state, before proceeding.
        self::store_database_state($statename);

        echo "Saving dataroot state" . PHP_EOL;
        self::store_data_root_state($statename);

        echo "Site state is stored at " . util::get_tool_dir() . DIRECTORY_SEPARATOR . $statename
            . ".*" . PHP_EOL;
        return 0;
    }

    /**
     * Restore state of current site. Dataroot and database.
     *
     * @param string $statename
     * @return int 0 on success else error code.
     */
    public static function restore_site_state($statename = "default") {
        // Clean up the dataroot folder.
        util::drop_dir(self::get_dataroot() . '/');

        // Restore database and dataroot state, before proceeding.
        echo "Restoring database state" . PHP_EOL;
        if (!self::restore_database_state($statename)) {
            util::performance_exception("Error restoring state db: " . $statename);
        }

        echo "Restoring dataroot state" . PHP_EOL;
        if (!self::restore_dataroot($statename)) {
            util::performance_exception("Error restoring state data: " . $statename);
        }

        echo "Site restored to $statename state" . PHP_EOL;
        return 0;
    }
}