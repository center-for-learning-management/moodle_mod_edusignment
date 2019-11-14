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
 * The mod_edusign reveal identities confirmation page viewed event.
 *
 * @package    mod_edusign
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_edusign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_edusign reveal identities confirmation page viewed event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int edusignid: the id of the edusignment.
 * }
 *
 * @package    mod_edusign
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reveal_identities_confirmation_page_viewed extends base {
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
     * @return reveal_identities_confirmation_page_viewed
     */
    public static function create_from_edusign(\edusign $edusign) {
        $data = array(
                'context' => $edusign->get_context(),
                'other' => array(
                        'edusignid' => $edusign->get_instance()->id,
                ),
        );
        self::$preventcreatecall = false;
        /** @var reveal_identities_confirmation_page_viewed $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_edusign($edusign);
        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventrevealidentitiesconfirmationpageviewed', 'mod_edusign');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the confirmation page for revealing identities for the " .
                "edusignment with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $this->set_legacy_logdata('view', get_string('viewrevealidentitiesconfirm', 'edusign'));
        return parent::get_legacy_logdata();
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call reveal_identities_confirmation_page_viewed::create() directly, 
            use reveal_identities_confirmation_page_viewed::create_from_grade() instead.');
        }

        parent::validate_data();

        if (!isset($this->other['edusignid'])) {
            throw new \coding_exception('The \'edusignid\' value must be set in other.');
        }
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['edusignid'] = array('db' => 'edusign', 'restore' => 'edusign');

        return $othermapped;
    }
}
