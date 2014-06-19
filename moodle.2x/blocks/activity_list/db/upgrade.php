<?php
/**
 * blocks/activity_list/db/upgrade.php
 *
 * @package    blocks
 * @subpackage activity_list
 * @copyright  2014 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    you may not copy of distribute any part of this package without prior written permission
 */

// prevent direct access to this script
defined('MOODLE_INTERNAL') || die();

function xmldb_block_activity_list_upgrade($oldversion=0) {

    global $CFG, $DB;

    $result = true;

    $newversion = 2014051601;
    if ($oldversion < $newversion) {
        update_capabilities('block/activity_list');
        upgrade_block_savepoint($result, "$newversion", 'activity_list');
    }

    return $result;
}
