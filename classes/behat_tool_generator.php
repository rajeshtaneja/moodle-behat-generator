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
 * Data generators for acceptance testing.
 *
 * @package   core_generator
 * @copyright 2015 rajesh Taneja
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__.'/util.php');
$moodlepath = \moodlehq\behat_generator\util::get_moodle_path();
require_once($moodlepath . '/lib/tests/behat/behat_data_generators.php');

/*use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Exception\PendingException;*/
use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Behat\Tester\Exception\PendingException as PendingException;
use moodlehq\behat_generator\generator;

/**
 * Class containing bulk steps for setting up site for performance testing.
 *
 * @package   core_generator
 * @copyright 2015 rajesh Taneja
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_generator extends behat_base {

    /**
     * Creates the specified element. More info about available elements in http://docs.moodle.org/dev/Acceptance_testing#Fixtures.
     *
     * @Given /^the following "(?P<element_string>(?:[^"]|\\")*)" instances exist:$/
     *
     * This step overrides behat generator step and accepts following parameters to call generator steps for multiple instances.
     * - instances : Number of instances to create
     *   * It replaces #!count!# with the incremental value.
     * - refencecont: This is used in case you want a counter to be modulo of some value.
     *   * It replaces #!count#! with incremental value.
     *   * #!refcount!# will
     * - repeat: This will repeat the table row  x times.
     *   * Replaces #!repeatcount!3 with the repeating value.
     *
     * @throws Exception
     * @throws PendingException
     * @param string    $elementname The name of the entity to add
     * @param TableNode $data
     */
    public function the_following_instances_exist($elementname, TableNode $data) {

        $datageneratorcontext = behat_context_helper::get('behat_data_generators');

        $datanodes = $this->fix_data_counter($data);

        foreach ($datanodes as $datanode) {
            generator::dot();
            $datageneratorcontext->the_following_exist($elementname, $datanode);
        }
    }

    /**
     * Helper function to modify #count#, #refcount# and #repeatcount# in table.
     *
     * @param TableNode $data Data to be passed to the resource.
     * @throws
     */
    protected function fix_data_counter(TableNode $data) {
        $datanodes = array();
        $rows = $data->getRows();

        // Get table keys and remove instances and reference
        $firstrow = array_shift($rows);
        if(($instancekey = array_search('instances', $firstrow)) !== false) {
            unset($firstrow[$instancekey]);
        }
        if(($refkey = array_search('referencecount', $firstrow)) !== false) {
            unset($firstrow[$refkey]);
        }
        if(($repeatkey = array_search('repeat', $firstrow)) !== false) {
            unset($firstrow[$repeatkey]);
        }

        // Create all instances.
        foreach ($rows as $row) {
            // Get count and unset instances key.
            $instances = $row[$instancekey];
            unset($row[$instancekey]);

            // Check if there is an refrence which needs to be met.
            // Reference is like how many categories to use to create given instances of sub-category.
            $reference = 0;
            if ($refkey) {
                $reference = $row[$refkey];
                unset($row[$refkey]);
                // We want sequential filling of data so keep track of counter.
                $referencecounter = 1;
                $maxperreference = ceil($instances/$reference);
            }

            // Check if repeat is given to repeat process.
            $repeat = 1;
            if ($repeatkey) {
                $repeat = $row[$repeatkey];
                unset($row[$repeatkey]);
            }

            for ($repeatcounter = 1; $repeatcounter <= $repeat; $repeatcounter++) {
                for ($i = 1; $i <= $instances; $i++) {
                    $rowtoadd = $row;
                    $datanode = array();
                    $datanode[] = $firstrow;

                    foreach ($rowtoadd as $key => $value) {
                        if ($reference) {
                            $rowtoadd[$key] = str_replace("#count#", $referencecounter, $value);
                        } else {
                            $rowtoadd[$key] = str_replace("#count#", $i, $value);
                        }
                        $rowtoadd[$key] = str_replace("#refcount#", $i, $rowtoadd[$key]);
                        $rowtoadd[$key] = str_replace("#repeatcount#", $repeatcounter, $rowtoadd[$key]);
                    }
                    $datanode[] = $rowtoadd;

                    // Call generator function.
                    //$contexttouse->$function($elementname, $datanode);
                    $datanodes[] = new TableNode($datanode);

                    // We want sequential filling of data so increment reference counter.
                    if ($reference && ($i % $maxperreference == 0)) {
                        $referencecounter++;
                    }
                }
            }
        }
        return $datanodes;
    }
}
