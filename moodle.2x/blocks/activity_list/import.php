<?php
/**
 * blocks/activity_list/import.php
 *
 * @package    blocks
 * @subpackage activity_list
 * @copyright  2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    you may not copy of distribute any part of this package without prior written permission
 */

/** Include required files */
require_once('../../config.php');
require_once($CFG->dirroot.'/lib/xmlize.php');
require_once($CFG->dirroot.'/blocks/activity_list/import_form.php');

// cache the plugin name  - because it is quite long ;-)
$plugin = 'block_activity_list';

// get the incoming block_instance id
$id = required_param('id', PARAM_INT);

if (! $block_instance = $DB->get_record('block_instances', array('id' => $id))) {
    print_error('invalidinstanceid', $plugin, '', $id);
}
if (! $block = $DB->get_record('block', array('name' => $block_instance->blockname))) {
    print_error('invalidblockname', $plugin, '', $block_instance);
}
if (! $context = $DB->get_record('context', array('id' => $block_instance->parentcontextid))) {
    print_error('invalidcontextid', $plugin, '', $block_instance);
}
if (! $course = $DB->get_record('course', array('id' => $context->instanceid))) {
    print_error('invalidcourseid', $plugin, '', $context);
}

require_login($course->id);

if (class_exists('context')) {
    $context = context::instance_by_id($context->id);
} else {
    $context = get_context_instance_by_id($context->id);
}
require_capability('moodle/site:manageblocks', $context);

// $SCRIPT is set by initialise_fullme() in 'lib/setuplib.php'
// It is the path below $CFG->wwwroot of this script
$url = new moodle_url($SCRIPT, array('id' => $id));

// initialize form
$mform = new block_activity_list_import_form($url);

if ($mform->is_cancelled()) {
    $params = array('id' => $course->id,
                    'sesskey' => sesskey(),
                    'bui_editid' => $block_instance->id);
    redirect(new moodle_url('/course/view.php', $params));
}

$blockname = get_string('blockname', $plugin);
$pagetitle = get_string('importsettings', $plugin);

$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($pagetitle, $url);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
echo $OUTPUT->box_start('generalbox');

if ($xml = $mform->get_file_content('importfile')) {
    if ($mform->import($xml, $block_instance, $course)) {
        // successful import
        $msg   = get_string('validimportfile', $plugin);
        $style = 'notifysuccess';
    } else {
        // import didn't work - shouldn't happen !!
        $msg   = get_string('invalidimportfile', $plugin);
        $style = 'notifyproblem';
    }
    echo $OUTPUT->notification($msg, $style);

    $params = array('id' => $course->id,
                    'sesskey' => sesskey(),
                    'bui_editid' => $block_instance->id);
    $url   = new moodle_url('/course/view.php', $params);

    echo $OUTPUT->continue_button($url);

} else {
    // show the import form
    $mform->display();
}

echo $OUTPUT->box_end();
echo $OUTPUT->footer($course);
