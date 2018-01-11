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
 * Some tests covering different aspects of mod_surveypro locallib
 *
 * @package   mod_surveypro
 * @copyright 2018 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/surveypro/locallib.php');

class mod_surveypro_locallib_testcase extends advanced_testcase {

    /**
     * Provide cases to {link test_multilinetext_to_array()}
     */
    public function test_multilinetext_to_array_provider() {
        return array(
            'single_element' => array(
                'case' => 'single',
                'expected' => array('single')
            ),
            'multiple_element' => array(
                'case' => "one\ntwo",
                'expected' => array('one', 'two')
            ),
            'completely_empty' => array(
                'case' => " \t\r  \n\n   \n\n \t\r  ",
                'expected' => array()
            ),
            'redundant_whitespace' => array(
                'case' => " \t\r  \n\n  one  \n\n   two \n\n \t\r   ",
                'expected' => array('one', 'two')
            ),
            'value_label_separated' => array(
                'case' => '  value1  ' . SURVEYPRO_VALUELABELSEPARATOR . ' label1  ',
                'expected' => array('value1' . SURVEYPRO_VALUELABELSEPARATOR . 'label1')
            ),
        );
    }

    /**
     * Test {link surveypro_multilinetext_to_array()}
     *
     * @dataProvider test_multilinetext_to_array_provider
     */
    public function test_multilinetext_to_array($case, $expected) {
        $this->assertSame($expected, surveypro_multilinetext_to_array($case));
    }
}
