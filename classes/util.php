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
require_once(__DIR__.'/toolkit_util.php');

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
class util {

    use toolkit_util;
    /**
     * Create generator_dir where all feature and context will be saved.
     *
     * @return string
     * @throws \coding_exception
     * @throws \invalid_dataroot_permissions
     */
    public static function get_tool_dir() {
        $dir = self::get_performance_dir() . DIRECTORY_SEPARATOR . 'sitegenerator';

        // Create dir if required.
        if (!is_dir($dir)) {
            make_writable_directory($dir, true);
        }
        return $dir;
    }

    /**
     * Return the path where json config is saved finally.
     * This coould be main tool directory or via vendor.
     *
     * @throws \moodle_exception
     */
    public static function get_json_config_path($checkdirperm = false) {
        // Check if  user has a custom config file.
        if (file_exists(__DIR__ . '/../behatgenerator.json')) {
            // Check if it's cloned.
            $jsonconfig = __DIR__ . '/../behatgenerator.json';

        } else if (file_exists(__DIR__ . '/../../../behatgenerator.json')) {
            // Check if it's installed by vendor.
            $jsonconfig = __DIR__ . '/../../../behatgenerator.json';

        } else if ($checkdirperm) {
            // Check in which dir it can be written.
            if (is_writeable(__DIR__ . '/../')) {
                $jsonconfig = __DIR__ . '/../behatgenerator.json';
            } else if (is_writeable(__DIR__ . '/../../../')) {
                $jsonconfig = __DIR__ . '/../../../behatgenerator.json';
            } else {
                self::performance_exception('No default behatgenerator.json found and tool dir is not writable', 1);
            }

        }
        return $jsonconfig;
    }

    /**
     * Return config value for generator.
     *
     * @param bool $shouldexist throw exception if config not found.
     * @return array.
     */
    public static function get_config($shouldexist = true) {
        $jsonconfig = self::get_json_config_path();

        $config = json_decode(file_get_contents($jsonconfig), true);
        if (empty($config) && $shouldexist) {
            self::performance_exception("Check config file: ".$jsonconfig);
        }
        return $config;
    }

    /**
     * Create feature contents and return contents.
     *
     * @param string $featurename resource to create.
     * @param string $sitesize size of site to create
     * @return bool|string
     */
    protected static function get_feature_contents($featurename, $sitesize) {

        $generatorconfig = self::get_feature_config();

        $featurepath = self::get_feature_path($featurename);

        if (empty($featurepath)) {
            return false;
        }

        // Create feature file for creating resource.
        $data = file_get_contents($featurepath);

        // Replace required values in feature file.
        $data = self::replace_values_in_feature($generatorconfig, $featurename, $sitesize, $data);

        if (empty($generatorconfig[$featurename]['scenario_outline'])) {
            return $data;
        }

        // Get count for scenario_outline example and unset it.
        if (!isset($generatorconfig[$featurename]['scenario_outline']['count'])) {
            self::performance_exception("Reference counter is not set for ");
        }

        $examplecount = $generatorconfig[$featurename]['scenario_outline']['count'];
        $generatorconfig[$featurename]['scenario_outline']['count'] = null;
        unset($generatorconfig[$featurename]['scenario_outline']['count']);
        // Check if reference or actual value passed.
        if (isset($examplecount[$sitesize])) {
            $scenarioreferencecounter = $examplecount[$sitesize];
        } else {
            $scenarioreferencecounter = $generatorconfig;
            foreach ($examplecount as $value) {
                $scenarioreferencecounter = $scenarioreferencecounter[$value];
            }
            if (!empty($scenarioreferencecounter[$sitesize])) {
                $scenarioreferencecounter = $scenarioreferencecounter[$sitesize];
            } else {
                self::performance_exception("Invalid refrence count for example passed: " . $examplecount);
            }
        }

        $replacementparams = array_keys($generatorconfig[$featurename]['scenario_outline']);

        $data .= "    Examples:\n    | ";

        // Write paramters to replace.
        foreach ($replacementparams as $param) {
            $data .= $param ." |";
        }

        // Write data.
        $count = 1;
        for ($i = 1; $i <= $scenarioreferencecounter; $i++) {
            $data .= PHP_EOL."    |";
            foreach ($replacementparams as $param) {
                $data .= " " . str_replace('#!count!#', $count, $generatorconfig[$featurename]['scenario_outline'][$param]) . " |";
            }
            $data .= PHP_EOL;
            $count++;
        }
        return $data;
    }

    /**
     * Replace required values in feature with the config.
     *
     * @param array $generatorconfig generator config with all config values.
     * @param string $featurename feature name if know.
     * @param string $sitesize site size
     * @param string $data raw feature file data.
     * @return $data modified feature data with replaced values from config.
     *
     * @throws \moodle_exception
     */
    public static function replace_values_in_feature($generatorconfig, $featurename, $sitesize, $data) {
        // Replace given values, which is quick way to replace values in feature file.
        if (!empty($generatorconfig[$featurename]['scenario'])) {
            foreach ($generatorconfig[$featurename]['scenario'] as $search => $replace) {
                if (isset($replace[$sitesize])) {
                    $replace = $replace[$sitesize];
                } else if (is_array($replace)) {
                    $size = $generatorconfig;
                    foreach ($replace as $value) {
                        $size = $size[$value];
                    }
                    if (isset($size[$sitesize])) {
                        $replace = $size[$sitesize];
                    } else {
                        self::performance_exception("Invalid size passed for feature $featurename, param: " . $search);
                    }
                } else if (is_number($replace)) {
                    $replace = (int) $replace;
                } else {
                    self::performance_exception("Invalid size passed for feature $featurename, param: " . $search);
                }
                $data = str_replace('#!'.$search.'!#', $replace, $data);
            }
        }

        // Search and replace any other refrences used.
        preg_match_all('/#!([a-z]*)!#/i', $data, $matches);
        $matches = $matches[1];
        // For each match search and replace the values.
        foreach ($matches as $match) {
            $replace = false;
            foreach ($generatorconfig as $featureconfig => $config) {
                if (isset($config['scenario']) && isset($config['scenario'][$match])) {
                    if (isset($config['scenario'][$match][$sitesize])) {
                        $replace = $config['scenario'][$match][$sitesize];
                    } else if (is_number($config['scenario'][$match])) {
                        $replace = (int) $config['scenario'][$match];
                    } else if (is_array($config['scenario'][$match])) {
                        $replace = $generatorconfig;
                        foreach ($config['scenario'][$match] as $value) {
                            $replace = $replace[$value];
                        }
                        if (isset($replace[$sitesize])) {
                            $replace = $replace[$sitesize];
                        } else {
                            self::performance_exception("Invalid size passed for feature $featurename, param: " . $match);
                        }
                    }
                    // Once found the replacement value, then break.
                    if ($replace !== false) {
                        break;
                    }
                }
            }
            if ($replace === false) {
                self::performance_exception("Replacement value not found: " . $match . " in feature: " . $featurename);
            } else {
                $data = str_replace('#!'.$match.'!#', $replace, $data);
            }
        }

        return $data;
    }
}