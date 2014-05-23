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
 * @package    mod_surveypro
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_surveypro_activity_task
 */

/**
 * Structure step to restore one surveypro activity
 */
class restore_surveypro_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('surveypro', '/activity/surveypro');
        $paths[] = new restore_path_element('surveypro_item', '/activity/surveypro/items/item');
        if ($userinfo) {
            $paths[] = new restore_path_element('surveypro_submission', '/activity/surveypro/submissions/submission');
            $paths[] = new restore_path_element('surveypro_answer', '/activity/surveypro/answers/answer');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_surveypro($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the surveypro record
        $newitemid = $DB->insert_record('surveypro', $data);

        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_surveypro_item($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->surveyproid = $this->get_new_parentid('surveypro');
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('surveypro_item', $data);
        $this->set_mapping('surveypro_item', $oldid, $newitemid);
    }

    protected function process_surveypro_submission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->surveyproid = $this->get_new_parentid('surveypro');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('surveypro_submission', $data);
        $this->set_mapping('surveypro_submission', $oldid, $newitemid);
    }

    protected function process_surveypro_answer($data) {
        global $DB;

        $data = (object)$data;

        $data->submissionid = $this->get_mappingid('surveypro_submission', $data->submissionid);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('surveypro_answers', $data);
        // No need to save this mapping as far as nothing depend on it
        // (child paths, file areas nor links decoder)
    }

    protected function after_execute() {
        // Add surveypro related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_surveypro', 'intro', null);
    }
}
