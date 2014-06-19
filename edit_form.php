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
 * blocks/activity_list/mod_form.php
 *
 * @package    blocks
 * @subpackage taskchain_navigatino
 * @copyright  2014 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 */

/** Prevent direct access to this script */
defined('MOODLE_INTERNAL') || die();

/**
 * block_activity_list_mod_form
 *
 * @copyright  2014 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 * @package    mod
 * @subpackage taskchain
 */
class block_activity_list_edit_form extends block_edit_form {

    /**
     * specific_definition
     *
     * @param object $mform
     * @return void, but will update $mform
     */
    protected function specific_definition($mform) {

        $this->set_form_id($mform, get_class($this));

        // cache the plugin name, because
        // it is quite long and we use it a lot
        $plugin = 'block_activity_list';

        // cache commonly used menu options
        $depth_options  = range(0, 10);
        $length_options = range(0, 20);
        $grade_options  = array_reverse(range(0, 100), true);
        $keep_options   = array(0 => get_string('remove'), 1 => get_string('keep'));

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'title');
        //-----------------------------------------------------------------------------

        $element = $mform->addElement('static', 'description', get_string('description'), get_string('blockdescription', $plugin));

        $name = 'title';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string($name, $plugin), array('size' => 50));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, 'grades', 'coursegradecategory');
        //-----------------------------------------------------------------------------

        $name = 'showcourse';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string('show'));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'coursenamefield';
        $config_name = 'config_'.$name;
        $options = array(
            'fullname'  => get_string('fullname'),
            'shortname' => get_string('shortname')
        );
        $mform->addElement('select', $config_name, get_string('name'), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        /* =========================================== *\
        $name = 'coursegradeposition';
        $config_name = 'config_'.$name;
        $options = array(
            '0' => get_string('positionfirst', 'grades'),
            '1' => get_string('positionlast', 'grades')
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);
        \* =========================================== */

        //-----------------------------------------------------------------------------
        $this->add_header($mform, 'grades', 'gradecategories');
        //-----------------------------------------------------------------------------

        $name = 'minimumdepth';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $depth_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'maximumdepth';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $depth_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryskipempty';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryskiphidden';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryskipzeroweighted';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categorycollapse';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('no'),
            1 => get_string('usechildcategory', $plugin),
            2 => get_string('useparentcategory', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'gradecategorynames');
        //-----------------------------------------------------------------------------

        $name = 'categoryshortnames';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryshowweighting';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryignorechars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string($name, $plugin), array('size' => 10));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'categoryprefixes');
        //-----------------------------------------------------------------------------

        $name = 'categoryprefixlength';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('prefixlength', $plugin), $length_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixlength', $plugin);

        $name = 'categoryprefixchars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('prefixchars', $plugin), array('size' => 10));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixchars', $plugin);

        $name = 'categoryprefixlong';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('short', $plugin),
            1 => get_string('long', $plugin)
        );
        $mform->addElement('select', $config_name, get_string('prefixlong', $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixlong', $plugin);

        $name = 'categoryprefixkeep';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('prefixkeep', $plugin), $keep_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixkeep', $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'categorysuffixes');
        //-----------------------------------------------------------------------------

        $name = 'categorysuffixlength';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('suffixlength', $plugin), $length_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixlength', $plugin);

        $name = 'categorysuffixchars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('suffixchars', $plugin), array('size' => 10));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixchars', $plugin);

        $name = 'categorysuffixlong';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('short', $plugin),
            1 => get_string('long', $plugin)
        );
        $mform->addElement('select', $config_name, get_string('suffixlong', $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixlong', $plugin);

        $name = 'categorysuffixkeep';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('suffixkeep', $plugin), $keep_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixkeep', $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'sections');
        //-----------------------------------------------------------------------------

        $name = 'sectionshowhidden';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('hide'),
            1 => get_string('showwithlink', $plugin),
            2 => get_string('showwithoutlink', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionshowburied';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('hide'),
            1 => get_string('promotetovisiblegradecategory', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionshowungraded';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('hide'),
            1 => get_string('ungradedshow1', $plugin),
            2 => get_string('ungradedshow2', $plugin),
            3 => get_string('ungradedshow3', $plugin),
            4 => get_string('ungradedshow4', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        /* =========================================== *\
        $name = 'sectionshowuncategorized';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('showabovemaingradecategories', $plugin),
            1 => get_string('showbelowmaingradecategories', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);
        \* =========================================== */

        $name = 'sectionshowzeroweighted';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('hide'),
            1 => get_string('show'),
            2 => get_string('mergewithungradedsections', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'sectiontitles');
        //-----------------------------------------------------------------------------

        $name = 'sectiontitletags';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('sectiontitletags', $plugin), array('size' => 10));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionshorttitles';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionignorecase';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string('ignorecase', $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'ignorecase', $plugin);

        $name = 'sectionignorechars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string($name, $plugin), array('size' => 10));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'sectionprefixes');
        //-----------------------------------------------------------------------------

        $name = 'sectionprefixlength';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('prefixlength', $plugin), $length_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixlength', $plugin);

        $name = 'sectionprefixchars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('prefixchars', $plugin), array('size' => 10));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixchars', $plugin);

        $name = 'sectionprefixlong';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('short', $plugin),
            1 => get_string('long', $plugin)
        );
        $mform->addElement('select', $config_name, get_string('prefixlong', $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixlong', $plugin);

        $name = 'sectionprefixkeep';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('prefixkeep', $plugin), $keep_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixkeep', $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'sectionsuffixes');
        //-----------------------------------------------------------------------------

        $name = 'sectionsuffixlength';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('suffixlength', $plugin), $length_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixlength', $plugin);

        $name = 'sectionsuffixchars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('suffixchars', $plugin), array('size' => 10));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixchars', $plugin);

        $name = 'sectionsuffixlong';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('short', $plugin),
            1 => get_string('long', $plugin)
        );
        $mform->addElement('select', $config_name, get_string('suffixlong', $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixlong', $plugin);

        $name = 'sectionsuffixkeep';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('suffixkeep', $plugin), $keep_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixkeep', $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, 'moodle', 'groups');
        //-----------------------------------------------------------------------------

        $name = 'groupsmenu';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'groupslabel';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'groupscountusers';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'loginasmenu';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'loginassort';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('fullname'),
            1 => get_string('firstname'),
            2 => get_string('lastname'),
            3 => get_string('username'),
            4 => get_string('idnumber')
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, 'moodle', 'grades');
        //-----------------------------------------------------------------------------

        /* =========================================== *\
        $name = 'gradedisplay';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('displaypoints',      'grades'),
            1 => get_string('displaypercent',     'grades'),
            2 => get_string('displayweighted',    'grades'),
            3 => get_string('displaylettergrade', 'grades')
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);
        \* =========================================== */

        $name = 'showaverages';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'highgrade';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $grade_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'mediumgrade';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $grade_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'lowgrade';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $grade_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'coursesections');
        //-----------------------------------------------------------------------------

        $name = 'sectionjumpmenu';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionnumbers';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'singlesection';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'defaultsection';
        $config_name = 'config_'.$name;
        $options = array_slice(range(0, $this->block->config->numsections), 1, $this->block->config->numsections, true);
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'coursepageshortcuts');
        //-----------------------------------------------------------------------------

        $name = 'accesscontrol';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'gradebooklink';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $this->add_field_hiddensections($mform, $plugin);
        $this->add_field_languages($mform, $plugin);

        $name = 'currentsection';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'styles');
        //-----------------------------------------------------------------------------

        $name = 'moodlecss';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('none'),
            1 => get_string('simpleview', 'grades'),
            2 => get_string('pluginname', 'gradereport_user')
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_RAW);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'externalcss';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('externalcss', $plugin), array('size' => 50));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'internalcss';
        $config_name = 'config_'.$name;
        $params = array('wrap' => 'virtual', 'rows' => 6, 'cols' => 48);
        $mform->addElement('textarea', $config_name, get_string($name, $plugin), $params);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        if (isset($this->block->instance)) {
            if ($mycourses = $this->get_mycourses()) {

                //-----------------------------------------------------------------------------
                $this->add_header($mform, $plugin, 'applyselectedvalues');
                //-----------------------------------------------------------------------------

                $name = 'mycourses';
                $config_name = 'config_'.$name;
                $params = array('multiple' => 'multiple', 'size' => min(5, count($mycourses)));
                $mform->addElement('select', $config_name, get_string($name, $plugin), $mycourses, $params);
                $mform->setType($config_name, PARAM_INT);
                $mform->setDefault($config_name, $this->defaultvalue($name));
                $mform->addHelpButton($config_name, $name, $plugin);

                $this->add_importexport($mform, $plugin);
            }
        }
    }

    /**
     * set_form_id
     *
     * @param  object $mform
     * @param  string $id
     * @return mixed default value of setting
     */
    protected function set_form_id($mform, $id) {
        $attributes = $mform->getAttributes();
        $attributes['id'] = $id;
        $mform->setAttributes($attributes);
    }

    /**
     * get default value for a setting in this block
     *
     * @param  string $name of setting
     * @return mixed default value of setting
     */
    protected function defaultvalue($name) {
        if (isset($this->block->config->$name)) {
            return $this->block->config->$name;
        } else {
            return null;
        }
    }

    /**
     * add_header
     *
     * @param object  $mform
     * @param string  $component
     * @param string  $name of string
     * @param boolean $expanded (optional, default=TRUE)
     * @return void, but will update $mform
     */
    protected function add_header($mform, $component, $name, $expanded=true) {
        $label = get_string($name, $component);
        $mform->addElement('header', $name, $label);
        if (method_exists($mform, 'setExpanded')) {
            $mform->setExpanded($name, $expanded);
        }
    }

    /**
     * add_field_languages
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_field_languages($mform, $plugin) {
        global $COURSE, $DB;

        $langs = array('' => get_string('default'));
        if ($sections = $DB->get_records('course_sections', array('course' => $COURSE->id), '', 'id,summary')) {
            foreach ($sections as $section) {
                if (preg_match_all('/<span[^>]*class="multilang"[^>]*>/', $section->summary, $matches)) {
                    foreach ($matches[0] as $match) {
                        if (preg_match('/lang="(\w+)"/', $match, $lang)) {
                            $lang = substr($lang[1], 0, 2);
                            $langs[$lang] = '';
                        }
                    }
                }
            }
        }

        // get localized lang names
        $translations = get_string_manager()->get_list_of_translations();
        foreach ($translations as $lang => $text) {
            $lang = substr($lang, 0, 2);
            if (isset($langs[$lang])) {
                $langs[$lang] = $text;
            }
        }
        unset($translations);

        // remove languages that are not available on this site
        $langs = array_filter($langs);

        // cache some useful strings and textbox params
        $total = html_writer::tag('small', get_string('total', $plugin).': ');
        $head  = html_writer::tag('small', get_string('head',  $plugin).': ');
        $tail  = html_writer::tag('small', get_string('tail',  $plugin).': ');
        $params = array('size' => 2);

        $elements = array();
        foreach ($langs as $lang => $text) {

            $lang = substr($lang, 0, 2);
            $namelength = 'config_namelength'.$lang;
            $headlength = 'config_headlength'.$lang;
            $taillength = 'config_taillength'.$lang;

            // add line break (except before the first language, the default, which has $lang=='')
            if ($lang) {
                $elements[] = $mform->createElement('static', '', '', html_writer::empty_tag('br'));
            }

            // add length fields for this language
            $elements[] = $mform->createElement('static', '', '', $total);
            $elements[] = $mform->createElement('text', $namelength, '', $params);
            $elements[] = $mform->createElement('static', '', '', $head);
            $elements[] = $mform->createElement('text', $headlength, '', $params);
            $elements[] = $mform->createElement('static', '', '', $tail);
            $elements[] = $mform->createElement('text', $taillength, '', $params);
            $elements[] = $mform->createElement('static', '', '', html_writer::tag('small', $text));
        }

        $name = 'sectiontextlength';
        $mform->addGroup($elements, $name, get_string($name, $plugin), ' ', false);
        $mform->addHelpButton($name, $name, $plugin);

        foreach ($elements as $element) {
            if ($element->getType()=='text') {
                $mform->setType($element->getName(), PARAM_INT);
            }
        }
    }

    /**
     * get_mycourses
     *
     * @return mixed, either an array() of accessible courses with similar block, or FALSE
     */
    protected function get_mycourses() {
        global $COURSE, $DB;

        $mycourses = array();

        $select = 'bi.id, c.id AS courseid, c.shortname, ctx.id AS contextid';
        $from   = '{block_instances} bi '.
                  'JOIN {context} ctx ON bi.parentcontextid = ctx.id '.
                  'JOIN {course} c ON ctx.instanceid = c.id';
        $where  = 'bi.blockname = ? AND bi.pagetypepattern = ? AND ctx.contextlevel = ? AND c.id <> ? AND c.id <> ?';
        $order  = 'c.sortorder ASC';
        $params = array('activity_list', 'course-view-*', CONTEXT_COURSE, SITEID, $COURSE->id);

        if ($instances = $DB->get_records_sql("SELECT $select FROM $from WHERE $where ORDER BY $order", $params)) {
            $capability = 'block/activity_list:addinstance';
            if (class_exists('context_course')) {
                $context = context_course::instance(SITEID);
            } else {
                $context = get_context_instance(COURSE_CONTEXT, SITEID);
            }
            $has_site_capability = has_capability($capability, $context);
            foreach ($instances as $instance) {
                if ($has_site_capability) {
                    $has_course_capability = true;
                } else {
                    if (class_exists('context')) {
                        $context = context::instance_by_id($instance->contextid);
                    } else {
                        $context = get_context_instance_by_id($instance->contextid);
                    }
                    $has_course_capability = has_capability($capability, $context);
                }
                if ($has_course_capability) {
                    $mycourses[$instance->contextid] = $instance->shortname;
                }
            }
        }

        if (empty($mycourses)) {
            return false;
        } else {
            return $mycourses;
        }
    }

    /**
     * add_importexport
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_importexport($mform, $plugin) {
        global $CFG, $OUTPUT;

        $str = (object)array(
            'all'        => addslashes_js(get_string('all')),
            'apply'      => addslashes_js(get_string('apply', $plugin)),
            'export'     => addslashes_js(get_string('exportsettings', $plugin)),
            'exporthelp' => addslashes_js($OUTPUT->help_icon('exportsettings', $plugin)),
            'exportlink' => addslashes_js($CFG->wwwroot.'/blocks/activity_list/export.php?id='.$this->block->instance->id),
            'import'     => addslashes_js(get_string('importsettings', $plugin)),
            'importhelp' => addslashes_js($OUTPUT->help_icon('importsettings', $plugin)),
            'importlink' => addslashes_js($CFG->wwwroot.'/blocks/activity_list/import.php?id='.$this->block->instance->id),
            'none'       => addslashes_js(get_string('none')),
            'select'     => addslashes_js(get_string('selectallnone', $plugin)),
            'selecthelp' => addslashes_js($OUTPUT->help_icon('selectallnone', $plugin))
        );

        $js = '';
        $js .= '<script type="text/javascript">'."\n";
        $js .= "//<![CDATA[\n";
        $js .= "function add_importexport() {\n";
        $js .= "    var obj = document.getElementsByTagName('DIV');\n";
        $js .= "    if (obj) {\n";
        $js .= "        var fbuttons = new RegExp('\\\\bfitem_actionbuttons\\\\b');\n";
        $js .= "        var fcontainer = new RegExp('\\\\bfcontainer\\\\b');\n";
        $js .= "        var fempty = new RegExp('\\\\bfemptylabel\\\\b');\n";
        $js .= "        var fitem = new RegExp('\\\\bfitem\\\\b');\n";
        $js .= "        var i_max = obj.length;\n";
        $js .= "        var addSelect = true;\n";
        $js .= "        for (var i=0; i<i_max; i++) {\n";
        $js .= "            if (obj[i].className.match(fbuttons)) {\n";
        $js .= "                continue;\n";
        $js .= "            }\n";
        $js .= "            if (obj[i].className.match(fempty)) {\n";
        $js .= "                continue;\n";
        $js .= "            }\n";
        $js .= "            if (obj[i].className.match(fitem)) {\n";

        $js .= "                if (addSelect && obj[i].id=='') {\n";
        $js .= "                    addSelect = false;\n";

        $js .= "                    var elm = document.createElement('SPAN');\n";
        $js .= "                    elm.style.margin = '6px auto';\n";

/**
        $js .= "                    var lnk = document.createElement('A');\n";
        $js .= "                    lnk.appendChild(document.createTextNode('$str->import'));\n";
        $js .= "                    lnk.href = '$str->importlink';\n";
        $js .= "                    elm.appendChild(lnk);\n";
        $js .= "                    elm.innerHTML += '$str->importhelp';\n";
        $js .= "                    elm.appendChild(document.createElement('BR'));\n";

        $js .= "                    var lnk = document.createElement('A');\n";
        $js .= "                    lnk.appendChild(document.createTextNode('$str->export'));\n";
        $js .= "                    lnk.href = '$str->exportlink';\n";
        $js .= "                    elm.appendChild(lnk);\n";
        $js .= "                    elm.innerHTML += '$str->exporthelp';\n";
**/

        $js .= "                    var elm = document.createElement('SPAN');\n";
        $js .= "                    elm.style.margin = '6px auto';\n";

        $js .= "                    elm.appendChild(document.createTextNode('$str->select'));\n";
        $js .= "                    elm.innerHTML += '$str->selecthelp';\n";
        $js .= "                    elm.appendChild(document.createElement('BR'));\n";

        $js .= "                    var lnk = document.createElement('A');\n";
        $js .= "                    lnk.appendChild(document.createTextNode('$str->all'));\n";
        $js .= "                    lnk.href = \"javascript:select_all_in('DIV','itemselect',null);\";\n";
        $js .= "                    elm.appendChild(lnk);\n";

        $js .= "                    elm.appendChild(document.createTextNode(' / '));\n";

        $js .= "                    var lnk = document.createElement('A');\n";
        $js .= "                    lnk.appendChild(document.createTextNode('$str->none'));\n";
        $js .= "                    lnk.href = \"javascript:deselect_all_in('DIV','itemselect',null);\";\n";
        $js .= "                    elm.appendChild(lnk);\n";

        $js .= "                } else {\n";
        $js .= "                    var elm = document.createElement('INPUT');\n";
        $js .= "                    elm.id = 'select_' + obj[i].id.substr(9);\n";
        $js .= "                    elm.style.margin = '6px auto';\n";

        $js .= "                    if (obj[i].id=='fitem_id_config_mycourses') {\n";
        $js .= "                        elm.type = 'submit';\n";
        $js .= "                        elm.value = '$str->apply';\n";
        $js .= "                    } else {\n";
        $js .= "                        elm.type = 'checkbox';\n";
        $js .= "                    }\n";
        $js .= "                }\n";

        $js .= "                var div = document.createElement('DIV');\n";
        $js .= "                div.appendChild(elm);\n";
        $js .= "                div.className = 'itemselect';\n";
        $js .= "                div.style.marginRight = (obj[i].offsetWidth - 720) + 'px';\n";

        $js .= "                obj[i].insertBefore(div, obj[i].firstChild);\n";
        $js .= "                div.style.height = obj[i].offsetHeight + 'px';\n";
        $js .= "            }\n";
        $js .= "        }\n";
        $js .= "    }\n";
        $js .= "}\n";
        $js .= "if (window.addEventListener) {\n";
        $js .= "    window.addEventListener('load', add_importexport, false);\n";
        $js .= "} else if (window.attachEvent) {\n";
        $js .= "    window.attachEvent('onload', add_importexport);\n";
        $js .= "} else {\n";
        $js .= "    window.onload = add_importexport;\n";
        $js .= "}\n";
        $js .= "//]]>\n";
        $js .= "</script>\n";
        $mform->addElement('static', '', '', $js);
    }
}
