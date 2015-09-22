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

/**
 * Command line for behat_generator
 *
 * @package    moodlehq_behat_generator
 * @copyright  2015 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace moodlehq\behat_generator;

require_once(__DIR__.'/util.php');

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use moodlehq\behat_generator\util;

class generator_command extends Command {
    /**
     * Configure behat generator command.
     */
    protected function configure()  {
        $this
            ->setName("generator")
            ->setDescription('Generate moodle data')
            ->addOption(
                'install',
                'i',
                InputOption::VALUE_NONE,
                'Install moodle site'
            )
            ->addOption(
                'testdata',
                'd',
                InputOption::VALUE_REQUIRED,
                'Generate site contents. SiteSize can be either one of xs, s, m, l, xl'
            )
            ->addOption(
                'moodlepath',
                null,
                InputOption::VALUE_REQUIRED,
                'Path of moodle source to use.'
            )
            ->addOption(
                'datapath',
                null,
                InputOption::VALUE_REQUIRED,
                'Path of directory where moodle state will be saved'
            )
            ->addOption(
                'drop',
                null,
                InputOption::VALUE_NONE,
                'Drop installed site and all generated data files.'
            )
            ->addOption(
                'dropsite',
                null,
                InputOption::VALUE_NONE,
                'Drop installed site only, leaving generated data files in data dir.'
            )
            ->addOption(
                'enable',
                null,
                InputOption::VALUE_REQUIRED,
                'Enable site for performance site generation'
            )
            ->addOption(
                'disable',
                null,
                InputOption::VALUE_NONE,
                'Disable site for performance site generation'
            )
            ->addOption(
                'backup',
                null,
                InputOption::VALUE_REQUIRED,
                'Backup site with specific state.'
            )
            ->addOption(
                'restore',
                null,
                InputOption::VALUE_REQUIRED,
                'Restore site to specific state.'
            )
            ->addOption(
                'value',
                null,
                InputOption::VALUE_REQUIRED,
                'Output which value you want to return, version|moodlepath|datapath'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Used with data generation, to drop any exsiting data and generate new data'
            )
        ;
    }

    /**
     * Ask user about moodle and data path for storing moodle state.
     *
     * @param OutputInterface $output
     * @return array
     */
    private function ask_install_dirs(OutputInterface $output) {
        $dialog = $this->getHelperSet()->get('dialog');
        while (!$moodlepath = $dialog->ask($output, '<question>Path of your moodle source code: </question>')) {
            // Keep looping till you don't get proper path from user.
        }
        while (!$datapath = $dialog->ask($output, '<question>Directory path to store data: </question>')) {
            // Keep looping till you don't get proper path from user.
        }

        return array($moodlepath, $datapath);
    }

    /**
     * Ask user about moodle and data path for storing moodle state.
     *
     * @param OutputInterface $output
     * @return array
     */
    private function ask_install_params(OutputInterface $output, $moodlepath, $datapath) {
        $listofparams = array(
            'Database type' => 'dbtype',
            'Database host' => 'dbhost',
            'database name' => 'dbname',
            'database user' => 'dbuser',
            'database pass' => 'dbpass',
            'database prefix' => 'dbprefix',
            'Moodle wwwroot' => 'wwwroot',
            'Moodle dataroot' => 'dataroot',

        );

        $config = file_get_contents(__DIR__.'/../fixtures/config.php.template');

        $dialog = $this->getHelperSet()->get('dialog');
        foreach ($listofparams as $name => $param) {
            while (!$paramval = $dialog->ask($output, '<question>'.$name.': </question>')) {
                // Keep looping till you don't get proper path from user.
            }
            $config = str_replace('%%'.$param.'%%', $paramval, $config);
        }

        $config = str_replace('%%performancedataroot%%', $datapath, $config);

        file_put_contents($moodlepath.DIRECTORY_SEPARATOR.'config.php', $config);
    }

    /**
     * Interacts with the user.
     *
     * This method is executed before the InputDefinition is validated.
     * This means that this is the only place where the command can
     * interactively ask for values of missing required arguments.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function interact(InputInterface $input, OutputInterface $output) {
        // If we can write to current directory then use it by default for moodlepath and datapath.
        $behatgeneratorjson = util::get_json_config_path(true);

        // Make sure we have moodlepath and datapath, before excuting any option.
        if (!$input->getOption('moodlepath') || !$input->getOption('datapath')) {
            // Check if it's already set, if not then ask user.
            if (file_exists($behatgeneratorjson)) {
                $configjson = file_get_contents($behatgeneratorjson);
                $configjson = json_decode($configjson, true);
                if (isset($configjson['config']['moodlepath']) && isset($configjson['config']['datapath'])) {
                    return array($configjson['config']['moodlepath'], $configjson['config']['datapath']);
                }
            }

            $dialog = $this->getHelperSet()->get('dialog');

            list($moodlepath, $datapath) = $this->ask_install_dirs($output);
            while (!$dialog->askConfirmation($output, '<question>Are you sure to use following paths</question>'.PHP_EOL.'
                <info>Moodle path: '.$moodlepath.'</info>'.PHP_EOL.'<info>Data path:'.$datapath.' (Y/N): </info>')) {

                // Keep asking user till we get final input.
                list($moodlepath, $datapath) = $this->ask_install_dirs($output);
            }
        } else {
            $moodlepath = $input->getOption('moodlepath');
            $datapath = $input->getOption('datapath');
        }

        // Get the config if it exists or default and update moodlepath and datapath.
        if (file_exists($behatgeneratorjson)) {
            $configjson = file_get_contents($behatgeneratorjson);
            $configjson = json_decode($configjson, true);
        } else {
            $behatgeneratorjsondist = __DIR__ . '/../behatgenerator.json-dist';
            $configjson = file_get_contents($behatgeneratorjsondist);
            $configjson = json_decode($configjson, true);
        }
        $configjson['config'] = array(
            'moodlepath' => $moodlepath,
            'datapath' => $datapath,
        );

        // Write moodle path and directory path to final json to be used.
        file_put_contents($behatgeneratorjson, json_encode($configjson, JSON_PRETTY_PRINT));

        // Write config if not already available.
        if (!file_exists($moodlepath.'/config.php')) {
            $this->ask_install_params($output, $moodlepath, $datapath);
        }
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        // We don't need moodle for getting value.
        if ($value = $input->getOption('value')) {
            switch ($value) {
                case 'version':
                    $output->writeln(\moodlehq\behat_generator\util::get_tool_version());
                    break;
                case 'moodlepath':
                    $output->writeln(util::get_moodle_path());
                    break;
                case 'datapath':
                    $output->writeln(util::get_data_path());
                    break;
                default:
                    $output->writeln('<error>Not a valid option.</error><info> Should be wither version|moodlepath|datapath</info>');
                    return 1;
                    break;
            }
            return 0;
        }

        // Include moodle config.
        define('PERFORMANCE_SITE_GENERATOR', true);
        define('CLI_SCRIPT', true);
        define('NO_OUTPUT_BUFFERING', true);
        define('IGNORE_COMPONENT_CACHE', true);

        // Load moodle config and all classes.
        if (!$moodlepath = util::get_moodle_path()) {
            util::performance_exception("Moodlepath should have been set by now.");
        }

        // Autoload files and ensure we load moodle config, as we will be using moodle code for behat context.
        require_once($moodlepath . '/config.php');
        require_once(__DIR__ . '/inc.php');

        raise_memory_limit(MEMORY_HUGE);
        $status = false;

        // Do action.
        $sitedatagenerator = new \moodlehq\behat_generator\generator();

        if ($input->getOption('drop')) {
            // Don't check for any option, just try drop site..
            $force = ($input->getOption('force')) ? true : false;
            $status = $sitedatagenerator->run_drop($output, $force);

        }  else if ($input->getOption('dropsite')) {
            // Don't check for any option, just try drop site..
            $force = ($input->getOption('force')) ? true : false;
            $status = $sitedatagenerator->run_dropsite($output, $force);

        }

        // Check if site enable/disable is needed.
        if ($input->getOption('enable')) {
            $status = $sitedatagenerator->run_enable($output, $input->getOption('enable'));

        } else if ($input->getOption('disable')) {
            $status = $sitedatagenerator->run_disable($output);

        }

        if ($input->getOption('install')) {
            $status = $sitedatagenerator->run_install($output);

        }

        // Check if testdata needs to be generated.
        if ($input->getOption('testdata')) {
            $status = $sitedatagenerator->run_testdata($output, $input->getOption('testdata'), $input->getOption('force'));
        }

        // Finally, check if backup/restore is needed.
        if ($input->getOption('backup')) {
            $status = $sitedatagenerator->run_backup($output, $input->getOption('backup'));

        } else if ($input->getOption('restore')) {
            $status = $sitedatagenerator->run_restore($output,$input->getOption('restore'));

        }

        if ($status === false) {
            $output->write($this->getHelp(), true);
        }
        return $status;
    }

    /**
     * Gets the help message.
     *
     * @return string A help message.
     */
    public function getHelp() {
        $help = "
<error>This script have generator utilities.</error>

Usage to create site:
  <comment>vendor/bin/moodle_behat_generator [--install|--testdata SiteSize|--drop|--enable SiteSize|--disable|--force|--help]</comment>

Options:
<info>
--install | -i   Installs the site for performance test
--testdata | -d  Generate site contents. SiteSize can be either one of xs, s, m, l, xl
--moodlepath     Path of moodle source
--datapath       Path to dir (different from moodle dataroot), to store test data.
--drop           Drops the database tables and the dataroot contents. Pass force to run drop command, without checks.
--dropsite       Drops the current site leaving performance data. Pass force to run drop command, without checks.
--enable         Enables performance environment and updates tests list
--disable        Disables test environment
--backup | -b    Backup site
--restore | -r   Restore site
--value          Output which value you check, version|moodlepath|datapath

-h, --help Print out this help
</info>

Example from Moodle root directory:
<comment>
\$ vendor/bin/moodle_behat_generator --install --testdata=s
</comment>
More info in http://docs.moodle.org/dev/Performance_testing#Running_tests
";
        return $help;
    }
}
