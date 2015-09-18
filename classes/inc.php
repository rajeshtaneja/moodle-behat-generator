<?php
/**
 * Include all classes for now manually. Later convert that to autloader.
 */
global $CFG;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Cloned.
    require_once(__DIR__ . '/../vendor/autoload.php');

} else if (file_exists(__DIR__ . '/../../../autoload.php')) {
    // Via composer.
    require_once(__DIR__ . '/../../../autoload.php');

}

require_once(__DIR__ . '/generator_command.php');
require_once(__DIR__ . '/generator.php');
require_once(__DIR__ . '/installer.php');
require_once(__DIR__ . '/util.php');
require_once($CFG->libdir . '/behat/classes/behat_config_manager.php');

