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
 * Pickroles
 *
 * @package   local_pickroles
 * @author    Darko Miletic
 * @copyright 2018 Darko Miletic
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pickroles;

use admin_setting_pickroles;
use coding_exception;
use dml_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class admin_setting_pickroles_plus
 * @package   local_pickroles
 * @author    Darko Miletic
 * @copyright 2018 Darko Miletic
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_pickroles_plus extends admin_setting_pickroles {

    /**
     * @param  string $name
     * @param  string $value
     * @param  string $plugin
     * @throws coding_exception
     * @throws dml_exception
     * @return void
     */
    public static function update_data($name, $value, $plugin) {
        global $DB;

        $roles = array_map('intval', explode(',', $value));
        list($notinsql, $params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'param', false);
        $params += ['plugin' => $plugin, 'setting' => $name];
        $formatsql = "plugin = :plugin AND setting = :setting AND roleid %s";
        $DB->delete_records_select(plugin::COMPONENT, sprintf($formatsql, $notinsql), $params);
        list($insql, $pparams) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED);
        $pparams += ['plugin' => $plugin, 'setting' => $name];
        $items = $DB->get_records_select_menu(
            plugin::COMPONENT, sprintf($formatsql, $insql), $pparams, '', 'id, roleid'
        );
        foreach ($roles as $role) {
            if (!in_array($role, $items)) {
                $DB->insert_record(
                    plugin::COMPONENT, (object)['plugin' => $plugin, 'setting' => $name, 'roleid' => $role]
                );
            }
        }

    }

    /**
     * @param  string $name
     * @param  mixed $value
     * @return bool
     */
    public function config_write($name, $value) {
        $oldvalue = get_config($this->plugin, $name);
        $oldvalue = ($oldvalue === false) ? null : $oldvalue;
        $value = ($value === null) ? null : (string)$value;

        if ($oldvalue === $value) {
            return true;
        }

        self::update_data($name, $value, $this->plugin);
        return parent::config_write($name, $value);
    }

}