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
 * Event listener
 *
 * @package   local_pickroles
 * @copyright 2018 Darko Miletic
 * @author    Darko Miletic <darko.miletic@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

use local_pickroles\plugin;
use core\event\role_deleted;

defined('MOODLE_INTERNAL') || die();

/**
 * @param  role_deleted $event
 * @throws dml_exception
 */
function local_pickroles_role_deleted(role_deleted $event) {
    global $DB;
    $DB->delete_records(plugin::COMPONENT, ['roleid' => $event->objectid]);
}