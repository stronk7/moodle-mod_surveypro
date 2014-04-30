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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class surveypro_exportform extends moodleform {

    public function definition() {
        global $CFG, $DB;
        // ----------------------------------------
        $surveypro = $this->_customdata->surveypro;

        // ----------------------------------------
        $mform = $this->_form;

        // ----------------------------------------
        // submissionexport::settingsheader
        // ----------------------------------------
        $mform->addElement('header', 'settingsheader', get_string('download'));

        // ----------------------------------------
        // submissionexport::status
        // ----------------------------------------
        $fieldname = 'status';
        if ($DB->get_records('surveypro_submission', array('surveyproid' => $surveypro->id, 'status' => SURVEYPRO_STATUSINPROGRESS))) {
            $options = array(SURVEYPRO_STATUSCLOSED => get_string('statusclosed', 'surveypro'),
                             SURVEYPRO_STATUSINPROGRESS => get_string('statusinprogress', 'surveypro'),
                             SURVEYPRO_STATUSALL => get_string('statusboth', 'surveypro'));
            $mform->addElement('select', $fieldname, get_string($fieldname, 'surveypro'), $options);
        } else {
            $mform->addElement('hidden', $fieldname, SURVEYPRO_STATUSCLOSED);
            $mform->setType($fieldname, PARAM_INT);
        }

        // ----------------------------------------
        // submissionexport::includehidden
        // ----------------------------------------
        $fieldname = 'includehidden';
        $mform->addElement('checkbox', $fieldname, get_string($fieldname, 'surveypro'));
        $mform->setType($fieldname, PARAM_INT);

        // ----------------------------------------
        // submissionexport::advanced
        // ----------------------------------------
        $fieldname = 'advanced';
        if ($this->_customdata->canaccessadvanceditems) {
            $mform->addElement('checkbox', $fieldname, get_string($fieldname, 'surveypro'));
        } else {
            $mform->addElement('hidden', $fieldname, 0);
            $mform->setType($fieldname, PARAM_INT);
        }

        // ----------------------------------------
        // submissionexport::downloadtype
        // ----------------------------------------
        $fieldname = 'downloadtype';
        $pluginlist = array(SURVEYPRO_DOWNLOADCSV => get_string('downloadtocsv', 'surveypro'),
                            SURVEYPRO_DOWNLOADTSV => get_string('downloadtotsv', 'surveypro'),
                            SURVEYPRO_DOWNLOADXLS => get_string('downloadtoxls', 'surveypro'));
        $mform->addElement('select', $fieldname, get_string($fieldname, 'surveypro'), $pluginlist);

        $this->add_action_buttons(false, get_string('continue'));
    }
}