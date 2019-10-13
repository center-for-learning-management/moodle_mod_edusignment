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
 * The mod_edusign statement accepted event.
 *
 * @package    mod_edusign
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_edusign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_edusign statement accepted event class.
 *
 * @package    mod_edusign
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class statement_accepted extends base {
    /**
     * Flag for prevention of direct create() call.
     *
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create instance of event.
     *
     * @param \edusign $edusign
     * @param \stdClass $submission
     * @return statement_accepted
     * @since Moodle 2.7
     *
     */
    public static function create_from_submission(\edusign $edusign, \stdClass $submission) {
        $data = array(
                'context' => $edusign->get_context(),
                'objectid' => $submission->id
        );
        self::$preventcreatecall = false;
        /** @var statement_accepted $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_edusign($edusign);
        $event->add_record_snapshot('edusign_submission', $submission);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has accepted the statement of the submission with id '$this->objectid' " .
                "for the edusignment with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventstatementaccepted', 'mod_edusign');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'edusign_submission';
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        global $USER;
        $logmessage = get_string('submissionstatementacceptedlog', 'mod_edusign', fullname($USER)); // Nasty hack.
        $this->set_legacy_logdata('submission statement accepted', $logmessage);
        return parent::get_legacy_logdata();
    }

    /**
     * Custom validation.
     *
     * @return void
     * @throws \coding_exception
     */
    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call statement_accepted::create() directly, use statement_accepted::create_from_submission() instead.');
        }

        parent::validate_data();
    }

    public static function get_objectid_mapping() {
        return array('db' => 'edusign_submission', 'restore' => 'submission');
    }
}
