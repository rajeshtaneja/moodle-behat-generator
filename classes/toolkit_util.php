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
use Symfony\Component\Yaml\Yaml as symfonyyaml;

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
trait toolkit_util {

    /** @var array Keeps array of tool config */
    protected $config = array();

    /**
     * Return directory in which performance data is saved.
     *
     * @static
     * @param bool $create if ture then it will also create directory if not present.
     * @return string
     */
    public static function get_performance_dir($create = false) {

        $datapath = self::get_data_path();

        // Create dir if required.
        if ($create && !is_dir($datapath)) {
            make_writable_directory($datapath, true);
        }

        if (!is_writeable($datapath)) {
            self::performance_exception("Data directory is not writable.");
        }

        return $datapath;
    }

    /**
     * Return directory where moodle source code is present.
     *
     * @static
     * @return string
     */
    public static function get_moodle_path() {
        $configjson = self::get_config();

        return $configjson['config']['moodlepath'];
    }
    /**
     * Return directory where moodle source code is present.
     *
     * @static
     * @return string
     */
    public static function get_data_path() {
        $configjson = self::get_config();

        return $configjson['config']['datapath'];

    }

    /**
     * Try to get current git hash of the performance-tool-kit
     * @return string null if unknown, sha1 hash if known
     */
    public static function get_performance_tool_hash() {

        // This is a bit naive, but it should mostly work for all platforms.

        if (!file_exists(__DIR__ . "/../.git/HEAD")) {
            return null;
        }

        $headcontent = file_get_contents(__DIR__ . "/../.git/HEAD");
        if ($headcontent === false) {
            return null;
        }

        $headcontent = trim($headcontent);

        // If it is pointing to a hash we return it directly.
        if (strlen($headcontent) === 40) {
            return $headcontent;
        }

        if (strpos($headcontent, 'ref: ') !== 0) {
            return null;
        }

        $ref = substr($headcontent, 5);

        if (!file_exists(__DIR__ . "/../.git/$ref")) {
            return null;
        }

        $hash = file_get_contents(__DIR__ . "/../.git/$ref");

        if ($hash === false) {
            return null;
        }

        $hash = trim($hash);

        if (strlen($hash) != 40) {
            return null;
        }

        return $hash;
    }

    /**
     * Execption used by performance tool.
     *
     * @param string $msg message in exception.
     * @throws \moodle_exception
     */
    public static function performance_exception($msg, $existstatus = false) {
        throw new \Exception($msg);
        if ($existstatus !== false) {
            exit($existstatus);
        }
    }

    /**
     * Return config for specific feature or all features.
     *
     * @param string $featurename feature name for which config is needed.
     * @return array.
     */
    public static function get_feature_config($featurename = '') {
        $featureconfig = self::get_config();
        if (empty($featurename)) {
            return $featureconfig['scenarios'];
        } else {
            return $featureconfig['scenarios'][$featurename];
        }
    }

    /**
     * Get feature path for the tool.
     *
     * @param string $featurename name of the feature for which path should be returned.
     * @return string
     */
    public static function get_feature_path($featurename) {
        $generatorconfig = self::get_feature_config();

        if (!empty($generatorconfig[$featurename]['featurepath'])) {
            $featurepath = $generatorconfig[$featurename]['featurepath'];
        } else {
            // Add generator contexts.
            $classname = get_called_class();
            $featurepath = __DIR__ . "/../" . 'features/' . $featurename . '.feature';
        }

        if (file_exists($featurepath)) {
            return $featurepath;
        } else {
            return '';
        }
    }

    /**
     * Return tool version from config. Used to identify the tool differences.
     *
     * @return int.
     */
    public static function get_tool_version() {
        $config = self::get_config(false);

        return $config['version'];
    }

    /**
     * Create test feature and enable behat config.
     *
     * @param string $sitesize size of site
     * @param array $optionaltestdata (optional), replace default template with this value.
     * @param string $proxy proxy url which will be used for test plan generation example: "localhost:9090"
     */
    public static function create_test_feature($sitesize, $optionaltestdata = array(), $proxy= "") {
        $generatorfeaturepath = self::get_tool_dir();
        $generatorconfig = self::get_feature_config();

        if (empty($generatorconfig)) {
            self::performance_exception("Check generator config file.");
        }


        // Create test feature file depending on what is given.
        foreach ($generatorconfig as $featuretoadd => $config) {
            if (!isset($optionaltestdata[$featuretoadd])) {
                if ($featurecontents = self::get_feature_contents($featuretoadd, $sitesize)) {
                    $optionaltestdata[$featuretoadd] = $featurecontents;
                } else {
                    echo "Feature file not found for: " . $featuretoadd;
                }
            }
            if (isset($optionaltestdata[$featuretoadd])) {
                $finalfeaturepath = $generatorfeaturepath . DIRECTORY_SEPARATOR . $featuretoadd . '.feature';
                file_put_contents($finalfeaturepath, $optionaltestdata[$featuretoadd]);
            }
        }

        // Update config file.
        self::update_config_file(array_keys($optionaltestdata), $proxy);
    }

    /**
     * Updates a config file
     *
     * @param  array $featurestoadd list of features to add.
     * @param string $proxy proxy url which will be used for test plan generation example: "localhost:9090"
     * @return void
     */
    protected static function update_config_file($featurestoadd, $proxy= "") {
        global $CFG;

        // Behat must have a separate behat.yml to have access to the whole set of features and steps definitions.
        $configfilepath = self::get_tool_dir() . DIRECTORY_SEPARATOR . 'behat.yml';
        $featurepath = self::get_tool_dir();

        // Gets all the components with features.
        $featureslist = glob("$featurepath/*.feature");
        $features = array();

        foreach ($featurestoadd as $featuretoadd) {
            $feature = preg_grep('/.\/' . $featuretoadd . '\.feature/', $featureslist);
            if (!empty($feature)) {
                if (count($feature) > 1) {
                    echo "Found more then 1 feature for the requested order set: " , $featuretoadd . PHP_EOL;
                }
                $features = array_merge($features, $feature);
            }
        }

        // Gets all the components with steps definitions.
        $stepsdefinitions = array();
        $steps = \behat_config_manager::get_components_steps_definitions();
        if ($steps) {
            foreach ($steps as $key => $filepath) {
                $stepsdefinitions[$key] = $filepath;
            }
        }

        // We don't want the deprecated steps definitions here.
        unset($stepsdefinitions['behat_deprecated']);

        // Remove default hooks.
        unset($stepsdefinitions['behat_hooks']);

        // Add generator contexts.
        //$classname = get_called_class();
        //preg_match('/.*\\\([a-z-_]+)\\\[a-z]+$/i', $classname, $behatcontextdir);
        $contexts = glob(__DIR__ . "/behat_*.php");

        foreach ($contexts as $context) {
            preg_match('/.*\/(behat_[a-z_0-9].*)\.php$/', $context, $matches);
            $classname = $matches[1];
            $stepsdefinitions[$classname] = $context;
        }

        // Add any other context file defined in config.
        $generatorconfig = self::get_config();

        foreach ($featurestoadd as $featurename) {
            if (!empty($generatorconfig[$featurename]['contextpath'])) {
                if (!is_array($generatorconfig[$featurename]['contextpath'])) {
                    $customcontextspaths = array($generatorconfig[$featurename]['contextpath']);
                } else {
                    $customcontextspaths = $generatorconfig[$featurename]['contextpath'];
                }

                foreach ($customcontextspaths as $customcontextpath) {
                    preg_match('/.*\/(behat_[a-z_].*)\.php$/', $customcontextpath, $matches);
                    $classname = $matches[1];
                    $stepsdefinitions[$classname] = $customcontextpath;
                }

            }
        }

        $moodlepath = self::get_moodle_path();
        $branch = 0;
        if (!defined('MOODLE_INTERNAL')) {
            define('MOODLE_INTERNAL', true);
        }
        require($moodlepath . '/version.php');
        $moodlebranch = (int)$branch;
        // Behat config file specifing the main context class,
        // the required Behat extensions and Moodle test wwwroot.
        if ($moodlebranch <= 30) {
            $contents = self::get_config_file_contents($features, $stepsdefinitions, $proxy);
        } else if ($moodlebranch == 31) {
            $contents = self::get_config_file_contents_31($features, $stepsdefinitions);
        } else {
            $behatconfigutil = new \behat_config_util();
            $behatconfigutil->set_theme_suite_to_include_core_features('boost');
            $contents = $behatconfigutil->get_config_file_contents($features, $stepsdefinitions);
            if (!empty($proxy)) {
                $contents = add_proxy_to_config($contents, $proxy);
            }
        }

        // Stores the file.
        if (!file_put_contents($configfilepath, $contents)) {
            self::performance_exception('File ' . $configfilepath . ' can not be created');
        }
    }

    /**
     * Add proxy to the config.
     *
     * @param String $contents
     * @param String $proxy
     * @return string
     */
    protected static function add_proxy_to_config($contents, $proxy) {
        $config = \Symfony\Component\Yaml\Yaml::parse($contents);

        $proxyconfig = array('capabilities' => array(
            'proxy' => array(
                "httpProxy" => $proxy,
                "proxyType" => "manual"
            )
        ));
        $config['default']['extensions']['Moodle\BehatExtension'] =
            array_merge($config['default']['extensions']['Moodle\BehatExtension'], $proxyconfig);

        return \Symfony\Component\Yaml\Yaml::dump($config, 10, 2);
    }

    /**
     * Behat config file specifing the main context class,
     * the required Behat extensions and Moodle test wwwroot.
     *
     * @param array $features The system feature files
     * @param array $stepsdefinitions The system steps definitions
     * @param string $proxy proxy url which will be used for test plan generation example: "localhost:9090"
     * @return string
     */
    protected static function get_config_file_contents($features, $stepsdefinitions, $proxy= "") {
        global $CFG;

        // We require here when we are sure behat dependencies are available.
        if (!file_exists($CFG->dirroot . '/vendor/autoload.php')) {
            echo "Behat is not installed on site, installing";
            exit(1);
        }

        require_once($CFG->dirroot . '/vendor/autoload.php');

        $selenium2wdhost = array('wd_host' => 'http://localhost:4444/wd/hub');

        $basedir = $CFG->dirroot . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'behat';

        $config = array(
            'default' => array(
                'paths' => array(
                    'features' => $basedir . DIRECTORY_SEPARATOR . 'features',
                    'bootstrap' => $basedir . DIRECTORY_SEPARATOR . 'features' . DIRECTORY_SEPARATOR . 'bootstrap',
                ),
                'context' => array(
                    'class' => 'behat_init_context'
                ),
                'extensions' => array(
                    'Behat\MinkExtension\Extension' => array(
                        'base_url' => $CFG->wwwroot,
                        'goutte' => null,
                        'selenium2' => $selenium2wdhost
                    ),
                    'Moodle\BehatExtension\Extension' => array(
                        'features' => $features,
                        'steps_definitions' => $stepsdefinitions,
                    )
                ),
                'formatter' => array(
                    'name' => 'progress'
                )
            )
        );

        if (!empty($proxy)) {
            $proxyconfig = array('capabilities' => array(
                    'proxy' => array(
                        "httpProxy" => $proxy,
                        "proxyType" => "manual"
                        )
                    ));
            $config['default']['extensions']['Moodle\BehatExtension\Extension'] =
                array_merge($config['default']['extensions']['Moodle\BehatExtension\Extension'], $proxyconfig);
        }

        return symfonyyaml::dump($config, 10, 2);
    }

    /**
     * Behat config file specifing the main context class,
     * the required Behat extensions and Moodle test wwwroot.
     *
     * @param array $features The system feature files
     * @param array $stepsdefinitions The system steps definitions
     * @return string
     */
    protected static function get_config_file_contents_31($features, $stepsdefinitions) {
        global $CFG;

        // We require here when we are sure behat dependencies are available.
        require_once($CFG->dirroot . '/vendor/autoload.php');

        $selenium2wdhost = array('wd_host' => 'http://localhost:4444/wd/hub');

        // Comments use black color, so failure path is not visible. Using color other then black/white is safer.
        // https://github.com/Behat/Behat/pull/628.
        $config = array(
            'default' => array(
                'formatters' => array(
                    'moodle_progress' => array(
                        'output_styles' => array(
                            'comment' => array('magenta'))
                    )
                ),
                'suites' => array(
                    'default' => array(
                        'paths' => $features,
                        'contexts' => array_keys($stepsdefinitions)
                    )
                ),
                'extensions' => array(
                    'Behat\MinkExtension' => array(
                        'base_url' => $CFG->behat_wwwroot,
                        'goutte' => null,
                        'selenium2' => $selenium2wdhost
                    ),
                    'Moodle\BehatExtension' => array(
                        'moodledirroot' => $CFG->dirroot,
                        'steps_definitions' => $stepsdefinitions
                    )
                )
            )
        );

        if (!empty($proxy)) {
            $proxyconfig = array('capabilities' => array(
                'proxy' => array(
                    "httpProxy" => $proxy,
                    "proxyType" => "manual"
                )
            ));
            $config['default']['extensions']['Moodle\BehatExtension\Extension'] =
                array_merge($config['default']['extensions']['Moodle\BehatExtension\Extension'], $proxyconfig);
        }

        return symfonyyaml::dump($config, 10, 2);
    }

    /**
     * Delete directory.
     *
     * @param string $dir directory path.
     * @param bool $includingself if true then the directory itself will be removed.
     * @return bool true on success.
     */
    public static function drop_dir($dir, $includingself = false) {

        $files = scandir($dir);
        foreach ($files as $file) {
            // Don't delete the dataroot directory. Just contents.
            if (!$includingself && ($file == "." || $file == "..")) {
                continue;
            }

            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                @remove_dir($path, false);
            } else {
                @unlink($path);
            }
        }
        return true;
    }

    /**
     * Updates the composer installer and the dependencies.
     *
     * @param string $dirroot root of directory where to check for composer install.
     * @return void exit() if something goes wrong
     */
    public static function testing_update_composer_dependencies($dirroot) {
        // To restore the value after finishing.
        $cwd = getcwd();

        $composerpath = $dirroot . DIRECTORY_SEPARATOR . 'composer.phar';
        $composerurl = 'https://getcomposer.org/composer.phar';

        // Switch to Moodle's dirroot for easier path handling.
        chdir($dirroot);

        // Download or update composer.phar. Unfortunately we can't use the curl
        // class in filelib.php as we're running within one of the test platforms.
        if (!file_exists($composerpath)) {
            $file = @fopen($composerpath, 'w');
            if ($file === false) {
                $errordetails = error_get_last();
                util::performance_exception(sprintf("Unable to create composer.phar\nPHP error: %s",
                    $errordetails['message']));

            }
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL,  $composerurl);
            curl_setopt($curl, CURLOPT_FILE, $file);
            $result = curl_exec($curl);

            $curlerrno = curl_errno($curl);
            $curlerror = curl_error($curl);
            $curlinfo = curl_getinfo($curl);

            curl_close($curl);
            fclose($file);

            if (!$result) {
                $error = sprintf("Unable to download composer.phar\ncURL error (%d): %s",
                    $curlerrno, $curlerror);
                util::performance_exception($error);
            } else if ($curlinfo['http_code'] === 404) {
                if (file_exists($composerpath)) {
                    // Deleting the resource as it would contain HTML.
                    unlink($composerpath);
                }
                $error = sprintf("Unable to download composer.phar\n" .
                    "404 http status code fetching $composerurl");
                util::performance_exception($error);
            }
        } else {
            passthru("php composer.phar self-update", $code);
            if ($code != 0) {
                exit($code);
            }
        }

        // Update composer dependencies.
        passthru("php composer.phar install", $code);
        if ($code != 0) {
            exit($code);
        }

        // Return to our original location.
        chdir($cwd);
    }

}