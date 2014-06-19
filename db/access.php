<?php
/**
 * blocks/activity_list/db/access.php
 *
 * @package    blocks
 * @subpackage activity_list
 * @copyright  2014 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    you may not copy of distribute any part of this package without prior written permission
 */

// prevent direct access to this script
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'block/activity_list:addinstance' => array(
        'riskbitmask'  => (RISK_SPAM | RISK_XSS),
        'captype'      => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes'   => array('editingteacher' => CAP_ALLOW, 'manager' => CAP_ALLOW),
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    )
);
