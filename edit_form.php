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

    public $modnames = array();

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

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'title');
        //-----------------------------------------------------------------------------

        $this->add_field_description($mform, $plugin, 'description');

        $name = 'title';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string($name, $plugin), array('size' => 50));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $this->add_field_languages($mform, $plugin);

        $name = 'listcount';
        $config_name = 'config_'.$name;
        $options = array_slice(range(0, 5), 1, 5, true);
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'separator';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string($name, $plugin), array('size' => 10));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_lists($mform, $plugin);
        //-----------------------------------------------------------------------------

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

                $this->add_selectallnone($mform, $plugin);
            }
        }
    }

    /**
     * add_lists
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_lists($mform, $plugin) {
        global $COURSE, $DB, $USER;

        $courseformat = $COURSE->format;
        $startdate = $COURSE->startdate;
        switch ($courseformat) {
            case 'weeks': $strsection = get_string('strftimedateshort'); break;
            case 'topics': $strsection = get_string('topic'); break;
            default: $strsection = get_string('section');
        }

        for ($i=0; $i<$this->block->config->listcount; $i++) {

            $name = 'list';
            $label = get_string($name, $plugin, ($i+1));
            $mform->addElement('header', $name.$i, $label);
            if (method_exists($mform, 'setExpanded')) {
                $mform->setExpanded($name.$i, true);
            }

            $name = 'listtitle';
            $config_name = 'config_'.$name.$i;
            $mform->addElement('text', $config_name, get_string($name, $plugin), array('size' => 24));
            $mform->setType($config_name, PARAM_TEXT);
            $mform->setDefault($config_name, $this->defaultvalue($name.$i));
            $mform->addHelpButton($config_name, $name, $plugin);

            $name = 'text';
            $config_name = 'config_'.$name.$i;
            $params = array('wrap' => 'virtual', 'rows' => 6, 'cols' => 48);
            $mform->addElement('textarea', $config_name, get_string($name, $plugin), $params);
            $mform->setType($config_name, PARAM_TEXT);
            $mform->setDefault($config_name, $this->defaultvalue($name.$i));
            $mform->addHelpButton($config_name, $name, $plugin);

            $name = 'modname';
            $config_name = 'config_'.$name.$i;
            $options = array('' => get_string('none')) + $this->modnames;
            $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
            $mform->setType($config_name, PARAM_PLUGIN);
            $mform->setDefault($config_name, $this->defaultvalue($name.$i));
            $mform->addHelpButton($config_name, $name, $plugin);

            $this->add_field_namefilter($mform, $plugin, $i);
            $this->add_field_cmids($mform, $plugin, $i);
            $this->add_field_namedisplay($mform, $plugin, $i);
            $this->add_field_sort($mform, $plugin, $i);
            $this->add_field_params($mform, $plugin, $i);
            $this->add_field_index($mform, $plugin, $i);
            $this->add_field_special($mform, $plugin, $i);
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
     * add_field_description
     *
     * @param object  $mform
     * @param string  $plugin
     * @param string  $name of field
     * @return void, but will update $mform
     */
    protected function add_field_description($mform, $plugin, $name) {
        global $OUTPUT;

        $label = get_string($name);
        $text = get_string('block'.$name, $plugin);

        if (isset($this->block->instance)) {
            $blockname = $this->block->instance->blockname;
        } else {
            // strip "block_" prefix and "_edit_form" suffix
            $blockname = substr(get_class($this), 6, -10);
        }

        $params = array('id' => $this->block->instance->id);
        $params = array('href' => new moodle_url('/blocks/'.$blockname.'/export.php', $params));

        $text .= html_writer::empty_tag('br');
        $text .= html_writer::tag('a', get_string('exportsettings', $plugin), $params);
        $text .= ' '.$OUTPUT->help_icon('exportsettings', $plugin);

        $params = array('id' => $this->block->instance->id);
        $params = array('href' => new moodle_url('/blocks/'.$blockname.'/import.php', $params));

        $text .= html_writer::empty_tag('br');
        $text .= html_writer::tag('a', get_string('importsettings', $plugin), $params);
        $text .= ' '.$OUTPUT->help_icon('importsettings', $plugin);

        $mform->addElement('static', $name, $label, $text);
    }

    /**
     * add_field_languages
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_field_languages($mform, $plugin) {
        global $COURSE, $DB, $USER;

        $langs = array('' => get_string('default'));

        // pick out mod names and languages used in this course
        $modinfo = get_fast_modinfo($COURSE, $USER->id);
        foreach ($modinfo->cms as $cmid => $cm) {
            if ($cm->modname=='label') {
                continue; // ignore labels
            }
            if (empty($this->modnames[$cm->modname])) {
                $this->modnames[$cm->modname] = get_string('modulenameplural', $cm->modname);
            }

            // get language, if any
            if (preg_match_all('/<span[^>]*class="multilang"[^>]*>/', $cm->name, $matches)) {
                foreach ($matches[0] as $match) {
                    if (preg_match('/lang="(\w+)"/', $match, $lang)) {
                        $lang = substr($lang[1], 0, 2);
                        $langs[$lang] = '';
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
        ksort($langs);
        asort($this->modnames);

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

        $name = 'textlength';
        $elements_name = 'elements_'.$name;
        $mform->addGroup($elements, $elements_name, get_string($name, $plugin), ' ', false);
        $mform->addHelpButton($elements_name, $name, $plugin);

        foreach ($elements as $element) {
            if ($element->getType()=='text') {
                $mform->setType($element->getName(), PARAM_INT);
            }
        }
    }

    /**
     * add_field_namefilter
     *
     * @param object $mform
     * @param string $plugin
     * @param integer $i
     * @return void, but will modify $mform
     */
    protected function add_field_namefilter($mform, $plugin, $i) {
        $elements = array();

        $names = array('include', 'exclude');
        foreach ($names as $name) {
            $config_name = 'config_'.$name.$i;
            $elements[] = $mform->createElement('static', '', '', get_string($name, $plugin));
            $elements[] = $mform->createElement('text', $config_name, '', array('size' => 15));
            $elements[] = $mform->createElement('static', '', '', html_writer::empty_tag('br'));
        }
        array_pop($elements); // remove last <br />

        $name = 'namefilter';
        $elements_name = 'elements_'.$name.$i;
        $mform->addGroup($elements, $elements_name, get_string($name, $plugin), ' ', false);
        $mform->addHelpButton($elements_name, $name, $plugin);

        foreach ($names as $name) {
            $config_name = 'config_'.$name.$i;
            $mform->setType($config_name, PARAM_TEXT);
            $mform->setDefault($config_name, $this->defaultvalue($name.$i));
        }
    }

    /**
     * add_field_cmids
     *
     * @param object $mform
     * @param string $plugin
     * @param integer $i
     * @return void, but will modify $mform
     */
    protected function add_field_cmids($mform, $plugin, $i) {
        global $COURSE;

        if (! $modinfo = get_fast_modinfo($COURSE)) {
            return false; // shouldn't happen !!
        }

        // set course section descriptor
        switch ($COURSE->format) {
            case 'weeks': $strsection = get_string('strftimedateshort'); break;
            case 'topics': $strsection = get_string('topic'); break;
            default: $strsection = get_string('section');
        }

        $count = 0;

        // create activity list
        $sectionnum = -1;
        foreach ($modinfo->cms as $cmid=>$mod) {
            if ($mod->modname=='label') {
                continue; // ignore labels
            }
            if ($sectionnum==$mod->sectionnum) {
                // do nothing (same section)
            } else {
                // start new optgroup for this course section
                $sectionnum = $mod->sectionnum;
                if ($sectionnum==0) {
                    $optgroup = get_string('activities');
                } else if ($COURSE->format=='weeks') {
                    $date = $COURSE->startdate + 7200 + ($sectionnum * 604800);
                    $optgroup = userdate($date, $strsection).' - '.userdate($date + 518400, $strsection);
                } else {
                    $optgroup = $strsection.': '.$sectionnum;
                }
                if (empty($options[$optgroup])) {
                    $options[$optgroup] = array();
                }
            }
            $count++;

            $name = block_activity_list::filter_text($mod->name);
            //$name = $this->format_longtext($name);

            // add this activity to the list
            $optgroups[$optgroup][$cmid] = $name;
        }

        $name = 'cmids';
        $config_name = 'config_'.$name.$i;
        $params = array('multiple' => 'multiple', 'size' => min(8, $count));
        $mform->addElement('selectgroups', $config_name, get_string($name, $plugin), $optgroups, $params);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name.$i));
        $mform->addHelpButton($config_name, $name, $plugin);
    }

    /**
     * add_field_namedisplay
     *
     * @param object $mform
     * @param string $plugin
     * @param integer $i
     * @return void, but will modify $mform
     */
    protected function add_field_namedisplay($mform, $plugin, $i) {

        $textoptions = array('size' => 15);
        $lengthoptions = range(0, 20);
        $longoptions = array(
            0 => get_string('short', $plugin),
            1 => get_string('long', $plugin)
        );
        $keepoptions = array(
            0 => get_string('remove'),
            1 => get_string('keep')
        );


        $name = 'shortentext';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('selectyesno', $config_name, $label);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, 0);
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'ignorecase';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('selectyesno', $config_name, $label);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, 0);
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'ignorechars';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('text', $config_name, $label, $textoptions);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, '');
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'prefixlength';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('select', $config_name, $label, $lengthoptions);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, 0);
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'prefixchars';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('text', $config_name, $label, $textoptions);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, '');
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'prefixlong';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('select', $config_name, $label, $longoptions);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, 0);
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'prefixkeep';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('select', $config_name, $label, $keepoptions);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, 0);
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'suffixlength';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('select', $config_name, $label, $lengthoptions);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, 0);
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'suffixchars';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('text', $config_name, $label, $textoptions);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, '');
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'suffixlong';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('select', $config_name, $label, $longoptions);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, 0);
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'suffixkeep';
        $config_name = 'config_'.$name.$i;
        $label = get_string($name, $plugin);
        $mform->addElement('select', $config_name, $label, $keepoptions);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, 0);
        $mform->addHelpButton($config_name, $name, $plugin);

        $elements = array();

        $name = 'search';
        $config_name = 'config_'.$name.$i;
        $elements[] = $mform->createElement('static', '', '', get_string($name, $plugin));
        $elements[] = $mform->createElement('text', $config_name, '', array('size' => 15));

        $name = 'case';
        $config_name = 'config_'.$name.$i;
        $options = array(
            0 => get_string('caseinsensitive', $plugin),
            1 => get_string('casesensitive', $plugin),
        );
        $elements[] = $mform->createElement('select', $config_name, '', $options);

        $elements[] = $mform->createElement('static', '', '', html_writer::empty_tag('br'));

        $name = 'replace';
        $config_name = 'config_'.$name.$i;
        $elements[] = $mform->createElement('static', '', '', get_string($name, $plugin));
        $elements[] = $mform->createElement('text', $config_name, '', array('size' => 15));

        $name = 'limit';
        $config_name = 'config_'.$name.$i;
        $options = array_slice(range(0, 5), 1, 5, true);
        $elements[] = $mform->createElement('static', '', '', get_string($name, $plugin));
        $elements[] = $mform->createElement('select', $config_name, '', $options);

        $name = 'namedisplay';
        $elements_name = 'elements_'.$name.$i;
        $mform->addGroup($elements, $elements_name, get_string($name, $plugin), ' ', false);
        $mform->addHelpButton($elements_name, $name, $plugin);

        $names = array('search'  => PARAM_TEXT,
                       'case'    => PARAM_INT,
                       'replace' => PARAM_TEXT,
                       'limit'   => PARAM_INT);
        foreach ($names as $name => $type) {
            $config_name = 'config_'.$name.$i;
            $mform->setType($config_name, $type);
            $mform->setDefault($config_name, $this->defaultvalue($name.$i));
        }
    }

    /**
     * add_field_sort
     *
     * @param object $mform
     * @param string $plugin
     * @param integer $i
     * @return void, but will modify $mform
     */
    protected function add_field_sort($mform, $plugin, $i) {
        $name = 'sort';
        $config_name = 'config_'.$name.$i;
        $options = array(
            0 => get_string('sortsectionsequence', $plugin),
            1 => get_string('sortoriginalname', $plugin),
            2 => get_string('sortdisplayname', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name.$i));
        $mform->addHelpButton($config_name, $name, $plugin);
    }

    /**
     * add_field_params
     *
     * @param object $mform
     * @param string $plugin
     * @param integer $i
     * @return void, but will modify $mform
     */
    protected function add_field_params($mform, $plugin, $i) {
        $name = 'params';
        $config_name = 'config_'.$name.$i;
        $mform->addElement('text', $config_name, get_string($name, $plugin), array('size' => 24));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name.$i));
        $mform->addHelpButton($config_name, $name, $plugin);
    }

    /**
     * add_field_index
     *
     * @param object $mform
     * @param string $plugin
     * @param integer $i
     * @return void, but will modify $mform
     */
    protected function add_field_index($mform, $plugin, $i) {
        $name = 'index';
        $config_name = 'config_'.$name.$i;
        $elements_name = 'elements_'.$name.$i;

        $elements = array();
        foreach ($this->modnames as $modname => $text) {
            $elements[] = $mform->createElement('checkbox', $config_name.'['.$modname.']', '', $text);
        }

        $mform->addGroup($elements, $elements_name, get_string($name, $plugin), html_writer::empty_tag('br'), false);
        $mform->addHelpButton($elements_name, $name, $plugin);

        $defaultvalue = $this->defaultvalue($name.$i);
        $defaultvalue = explode(',', $defaultvalue);

        foreach ($this->modnames as $modname => $text) {
            $mform->setType($config_name.'['.$modname.']', PARAM_INT);
            $mform->setDefault($config_name.'['.$modname.']', in_array($modname, $defaultvalue));
        }
    }

    /**
     * add_field_special
     *
     * @param object $mform
     * @param string $plugin
     * @param integer $i
     * @return void, but will modify $mform
     */
    protected function add_field_special($mform, $plugin, $i) {
        $name = 'special';
        $config_name = 'config_'.$name.$i;
        $elements_name = 'elements_'.$name.$i;

        $specials = array(
            block_activity_list::SPECIAL_GRADES   => get_string('grades'),
            block_activity_list::SPECIAL_PARTICIPANTS => get_string('participants'),
            block_activity_list::SPECIAL_CALENDAR => get_string('calendar', 'calendar'),
            block_activity_list::SPECIAL_COURSES  => get_string('courses', 'admin'),
            block_activity_list::SPECIAL_MYMOODLE => get_string('mymoodle', 'admin'),
            block_activity_list::SPECIAL_SITEPAGE => get_string('frontpage', 'admin')
        );

        $elements = array();
        foreach ($specials as $special => $text) {
            $elements[] = $mform->createElement('checkbox', $config_name.'['.$special.']', '', $text);
        }

        $mform->addGroup($elements, $elements_name, get_string($name, $plugin), html_writer::empty_tag('br'), false);
        $mform->addHelpButton($elements_name, $name, $plugin);

        $defaultvalue = $this->defaultvalue($name.$i);

        foreach ($specials as $special => $text) {
            $mform->setType($config_name.'['.$special.']', PARAM_INT);
            $mform->setDefault($config_name.'['.$special.']', min(1, $defaultvalue & $special));
        }
    }

    /**
     * get_mycourses
     *
     * @return mixed, either an array(coursecontextid) of accessible courses with similar block, or FALSE
     */
    protected function get_mycourses() {
        global $COURSE, $DB;

        $mycourses = array();

        $select = 'bi.id, ctx.id AS contextid, c.id AS courseid, c.shortname';
        $from   = '{block_instances} bi '.
                  'JOIN {context} ctx ON bi.parentcontextid = ctx.id '.
                  'JOIN {course} c ON ctx.instanceid = c.id';
        $where  = 'bi.blockname = ? AND bi.pagetypepattern = ? AND ctx.contextlevel = ? AND ctx.instanceid <> ? AND ctx.instanceid <> ?';
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
     * add_selectallnone
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_selectallnone($mform, $plugin) {
        global $OUTPUT;

        $str = (object)array(
            'all'        => addslashes_js(get_string('all')),
            'apply'      => addslashes_js(get_string('apply', $plugin)),
            'none'       => addslashes_js(get_string('none')),
            'select'     => addslashes_js(get_string('selectallnone', $plugin)),
            'selecthelp' => addslashes_js($OUTPUT->help_icon('selectallnone', $plugin))
        );

        $js = '';
        $js .= '<script type="text/javascript">'."\n";
        $js .= "//<![CDATA[\n";
        $js .= "function add_selectallnone() {\n";
        $js .= "    var obj = document.getElementsByTagName('DIV');\n";
        $js .= "    if (obj) {\n";
        $js .= "        var fbuttons = new RegExp('\\\\bfitem_actionbuttons\\\\b');\n";
        $js .= "        var fcontainer = new RegExp('\\\\bfcontainer\\\\b');\n";
        $js .= "        var fempty = new RegExp('\\\\bfemptylabel\\\\b');\n";
        $js .= "        var fitem = new RegExp('\\\\bfitem\\\\b');\n";
        $js .= "        var fid = new RegExp('^f[a-z]+_id_(elements_)?(config_)?(.*)');\n";
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
        $js .= "                    elm.style.margin = '6px auto';\n";

        $js .= "                    if (obj[i].id=='fitem_id_config_mycourses') {\n";
        $js .= "                        elm.type = 'submit';\n";
        $js .= "                        elm.value = '$str->apply';\n";
        $js .= "                    } else {\n";
        $js .= "                        elm.type = 'checkbox';\n";
        $js .= "                        elm.value = 1;\n";
        $js .= "                        elm.name = 'select_' + obj[i].id.replace(fid, '\$3');\n";
        $js .= "                        elm.id = 'id_select_' + obj[i].id.replace(fid, '\$3');\n";
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
        $js .= "    window.addEventListener('load', add_selectallnone, false);\n";
        $js .= "} else if (window.attachEvent) {\n";
        $js .= "    window.attachEvent('onload', add_selectallnone);\n";
        $js .= "} else {\n";
        $js .= "    window.onload = add_selectallnone;\n";
        $js .= "}\n";
        $js .= "//]]>\n";
        $js .= "</script>\n";
        $mform->addElement('static', '', '', $js);
    }
}
