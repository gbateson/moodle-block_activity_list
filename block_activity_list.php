<?php
/**
 * blocks/activity_list/block_activity_list.php
 *
 * @package   block_activity_list
 * @copyright 2012 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   you may not copy of distribute any part of this package without prior written permission
 */

 /**** TO DO ********************
(1) use block backup/restore functions for cm ids in $cmids
(2) allow comment for include, exclude, search, replace
(3) allow lists to be flattened (sorted or unsorted)
(4) allow custom CSS (external and internal)
(5) allow custom JS (e.g. PrettyPhoto)
(6) open in new window (+ settings !!)
(7) rename to taskchain_links ?
 ********************************/

 // disable direct access to this block
defined('MOODLE_INTERNAL') || die();

/**
 * block_activity_list
 *
 * @copyright 2012 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 1.9
 */
class block_activity_list extends block_base {

    /**
     * internal codes to specify special Moodle links
     */
    const SPECIAL_GRADES       = 0x01;
    const SPECIAL_PARTICIPANTS = 0x02;
    const SPECIAL_CALENDAR     = 0x04;
    const SPECIAL_COURSES      = 0x08;
    const SPECIAL_MYMOODLE     = 0x10;
    const SPECIAL_SITEPAGE     = 0x20;

    /**
     * init
     */
    function init() {
        $this->title = get_string('blockname', 'block_activity_list');
        $this->version = 2012040103;
    }

    /**
     * hide_header
     *
     * @return xxx
     */
    function hide_header() {
        return empty($this->config->title);
    }

    /**
     * applicable_formats
     *
     * @return xxx
     */
    function applicable_formats() {
        return array('course' => true); // 'course-view-social' ?
    }

    /**
     * instance_allow_config
     *
     * @return xxx
     */
    function instance_allow_config() {
        return true;
    }

    /**
     * instance_allow_multiple
     *
     * @return xxx
     */
    function instance_allow_multiple() {
        return true;
    }

    /**
     * specialization
     */
    function specialization() {
        $defaults = array(
            'title' => get_string('blockname', 'block_activity_list'),
            'separator' => '-----',
            'listcount' => 1,
            'namelength'  => 22, // 0=no limit
            'headlength'  => 10, // 0=no limit
            'taillength'  => 10, // 0=no limit
        );

        if (isset($this->config->listcount)) {
            $count = $this->config->listcount;
        } else {
            $count = $defaults['listcount'];
        }
        for ($i=0; $i<$count; $i++) {
            $defaults['title'.$i]   = ''; // title
            $defaults['text'.$i]    = ''; // html text
            $defaults['cmids'.$i]   = ''; // specific cm ids
            $defaults['index'.$i]   = ''; // activity index e.g. forum
            $defaults['modname'.$i] = ''; // activity type e.g. glossary
            $defaults['include'.$i] = ''; // include activities whose name matches this
            $defaults['exclude'.$i] = ''; // exclude activities whose name matches this
            $defaults['search'.$i]  = ''; // e.g. ([0-9]+) Glossary
            $defaults['case'.$i]    = 0;  // 0=case insensitive, 1=case sensitive
            $defaults['replace'.$i] = ''; // e.g. Unit $1
            $defaults['limit'.$i]   = 0;  // e.g.0=no limit, otherwise >0
            $defaults['sort'.$i]    = 0;  // 0=none (i.e course page order), 1=original name, 2=filtered name
            $defaults['params'.$i]  = ''; // e.g. mode=cat
            $defaults['special'.$i] = ''; // 0=none, 1=grades, 2=participants, 4=calendar, 8=site page, 16=my moodle
        }

        if (! isset($this->config)) {
            $this->config = new stdClass();
        }
        foreach ($defaults as $name => $value) {
            if (! isset($this->config->$name)) {
                $this->config->$name = $value;
            }
        }

        // load user-defined title (may be empty)
        $this->title = $this->config->title;
    }

    /**
     * the method overrides the standard instance_config_save()
     * it tries to apply selected settings to similar blocks
     * in other courses in which this user can edit blocks
     *
     * @param object $config contains the new config form data
     * @param boolean $pinned (optional, default=false)
     * @return xxx
     */
    function instance_config_save($config, $pinned=false) {
        global $DB;

        // do nothing if user hit the "cancel" button
        if (optional_param('cancel', 0, PARAM_INT)) {
            return true;
        }

        // ensure sensible value for $config->listcount
        $name = 'listcount';
        $min = 0; // min number of lists
        $max = 5; // max number of lists
        if (isset($config->$name)) {
            $config->$name = max($min, min($max, $config->$name));
        } else {
            $config->$name = $min;
        }

        // selected fields to be copied to other occurrences of this block
        $selected = array();

        // single occurrence fields
        $names = array('title', 'textlength', 'listcount', 'separator');
        foreach ($names as $name) {
            $selectname = 'select_'.$name;
            if (empty($_POST[$selectname])) {
                continue;
            }
            switch ($name) {
                case 'textlength':
                    $langs = get_string_manager()->get_list_of_translations();
                    $langs = array_keys($langs);
                    array_unshift($langs, '');
                    foreach ($langs as $lang) {
                        $selected[] = 'namelength'.$lang;
                        $selected[] = 'headlength'.$lang;
                        $selected[] = 'taillength'.$lang;
                    }
                    break;
                default:
                    $selected[] = $name;
            }
        }

        // multiple occurrence fields (one per list)
        $names = array(
            'listtitle', 'text', 'modname', 'namefilter', 'cmids',
            'namedisplay', 'sort', 'params', 'index', 'special',
        );

        // reduce arrays: cmids, index, special
        for ($i=0; $i<$config->listcount; $i++) {

            foreach ($names as $name) {
                $selectname = 'select_'.$name.$i;
                if (empty($_POST[$selectname])) {
                    continue;
                }
                switch ($name) {
                    case 'namefilter':
                        $selected[] = 'include'.$i;
                        $selected[] = 'exclude'.$i;
                        break;
                    case 'namedisplay':
                        $selected[] = 'search'.$i;
                        $selected[] = 'case'.$i;
                        $selected[] = 'replace'.$i;
                        $selected[] = 'limit'.$i;
                        break;
                    default:
                        $selected[] = $name.$i;
                }
            }

            // convert cmids array to string
            $name = 'cmids'.$i;
            if (isset($config->$name) && is_array($config->$name)) {
                $config->$name = array_filter($config->$name); // remove empties
                $config->$name = implode(',', $config->$name); // convert to string
            }

            // convert activity index array to string
            $name = 'index'.$i;
            if (isset($config->$name) && is_array($config->$name)) {
                $config->$name = array_keys($config->$name, 1); // selected keys only
                $config->$name = implode(',', $config->$name); // convert to string
            }

            // convert special moodle links array to string
            $name = 'special'.$i;
            if (isset($config->$name) && is_array($config->$name)) {
                $config->$name = array_keys($config->$name, 1);
                $config->$name = array_reduce($config->$name, array($this, 'bitwise_or'), 0);
            }
        }

        // remove superfluous list fields
        $i = $config->listcount;
        while (isset($this->config->{$names[0].$i})) {
            foreach ($names as $name) {
                unset($this->config->{$name.$i});
            }
            $i++;
        }

        // copy selected values to block instance in another course
        if (isset($config->mycourses) && is_array($config->mycourses)) {
            $contextids = implode(',', $config->mycourses);

            // get Activity List block instances in selected courses
            $select = "blockname = ? AND pagetypepattern = ? AND parentcontextid IN ($contextids)";
            $params = array($this->instance->blockname, 'course-view-*');
            if ($instances = $DB->get_records_select('block_instances', $select, $params)) {

                // user requires this capbility to update blocks
                $capability = 'block/activity_list:addinstance';

                // update values in the selected block instances
                foreach ($instances as $instance) {
                    if (class_exists('context')) {
                        $context = context::instance_by_id($instance->parentcontextid);
                    } else {
                        $context = get_context_instance_by_id($instance->parentcontextid);
                    }
                    if (has_capability($capability, $context)) {
                        $instance->config = unserialize(base64_decode($instance->configdata));
                        if (empty($instance->config)) {
                            $instance->config = new stdClass();
                        }
                        foreach ($selected as $name) {
                            if (empty($config->$name)) {
                                unset($instance->config->$name);
                            } else {
                                $instance->config->$name = $config->$name;
                            }
                        }
                        $instance->configdata = base64_encode(serialize($instance->config));
                        $DB->set_field('block_instances', 'configdata', $instance->configdata, array('id' => $instance->id));
                    }
                }
            }
        }
        unset($config->mycourses);

        //  save config settings as usual
        return parent::instance_config_save($config, $pinned);
    }

    /**
     * get_content
     *
     * @return xxx
     */
    function get_content() {
        global $CFG, $COURSE, $DB, $PAGE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = (object)array(
            'text' => '',
            'footer' => ''
        );

        if (empty($this->instance)) {
            return $this->content; // shouldn't happen !!
        }

        if (empty($COURSE)) {
            return $this->content; // shouldn't happen !!
        }

        // get modinfo (used to find out which section each mod is in)
        $modinfo = get_fast_modinfo($COURSE, $USER->id);

        $lists = array();
        for ($i=0; $i<$this->config->listcount; $i++) {
            $title   = 'title'.$i;
            $text    = 'text'.$i;
            $cmids   = 'cmids'.$i;
            $index   = 'index'.$i;
            $modname = 'modname'.$i;
            $include = 'include'.$i;
            $exclude = 'exclude'.$i;
            $search  = 'search'.$i;
            $case    = 'case'.$i;
            $replace = 'replace'.$i;
            $limit   = 'limit'.$i;
            $sort    = 'sort'.$i;
            $params  = 'params'.$i;
            $special = 'special'.$i;

            $cmids_array = explode(',', $this->config->$cmids);
            $cmids_array = array_filter($cmids_array); // remove blanks

            $index_array = explode(',', $this->config->$index);
            $index_array = array_filter($index_array); // remove blanks

            $list = (object)array(
                'title' => self::filter_text($this->config->$title),
                'text' => self::filter_text($this->config->$text),
                'items' => array(),
                'indexes' => array(),
                'specials' => array(),
            );

            if ($this->config->$index) {
                foreach ($modinfo->cms as $cmid => $cm) {
                    if ($cm->modname=='label') {
                        continue; // always skip labels
                    }

                    if (! $cm->uservisible) {
                        continue; // activity is hidden from this user
                    }

                    if (! is_numeric($ii = array_search($cm->modname, $index_array))) {
                        continue; // not a required activity type
                    }

                    $originalname = get_string('modulenameplural', $cm->modname);
                    $list->indexes[] = (object)array(
                        'originalname' => $originalname,
                        'displayname'  => $originalname,
                        'href'         => $CFG->wwwroot.'/mod/'.$cm->modname.'/index.php?id='.$COURSE->id,
                        'icon'         => $PAGE->theme->pix_url('icon', $cm->modname)->out()
                    );

                    // remove this $cm->modname, so it only appears once in the list
                    array_splice($index_array, $ii, 1);
                }
            }

            if ($this->config->$cmids || $this->config->$modname) {
                foreach ($modinfo->cms as $cmid => $cm) {
                    if ($cm->modname=='label') {
                        continue; // always skip labels
                    }

                    if (! $cm->uservisible) {
                        continue; // activity is hidden from this user
                    }

                    $add = false;
                    if ($this->config->$cmids && in_array($cmid, $cmids_array)) {
                        $add = true; // one of the specified activities
                    }

                    if ($this->config->$modname && $this->config->$modname==$cm->modname) {
                        $add = true;
                        if ($this->config->$include && ! preg_match('/'.$this->config->$include.'/', $cm->name)) {
                            $add = false; // name does not match $include string
                        }
                        if ($this->config->$exclude && preg_match('/'.$this->config->$exclude.'/', $cm->name)) {
                            $add = false; // name matches $exclude string
                        }
                    }

                    if ($add) {
                        // format activity display name
                        $originalname = strip_tags(self::filter_text($cm->name));
                        $displayname = $originalname;

                        if ($this->config->$search) {
                            $searchstring = '/'.$this->config->$search.'/';
                            if ($this->config->$case) {
                                $searchstring .= 'i';
                            }
                            if (empty($this->config->$limit)) {
                                $limit = -1; // no limit
                            } else {
                                $limit = $this->config->$limit;
                            }
                            $displayname = preg_replace($searchstring, $this->config->$replace, $displayname, $limit);
                        }

                        // format link to this activity
                        $href = $CFG->wwwroot.'/mod/'.$cm->modname.'/view.php?id='.$cm->id;
                        if ($this->config->$params) {
                            $href .= $this->fix_params($cm, $this->config->$params);
                        }

                        // add this activity to the current activity list
                        $list->items[] = (object)array(
                            'originalname' => $originalname,
                            'displayname'  => $displayname,
                            'href'         => $href,
                            'icon'         => $PAGE->theme->pix_url('icon', $cm->modname)->out()
                        );
                    }
                }
            }

            if ($this->config->$special) {
                switch (true) {
                    case isset($PAGE->context):   $context = $PAGE->context; break;
                    case isset($COURSE->context): $context = $COURSE->context; break;
                    default:                      $context = self::context(CONTEXT_COURSE, $COURSE->id);
                }
                if ($this->config->$special & self::SPECIAL_GRADES) {
                    if ($COURSE->showgrades && has_capability('moodle/grade:view', $context)) {
                        $showgrades = true; // student
                    } else if (has_capability('moodle/grade:viewall', $context)) {
                        $showgrades = true; // teacher
                    } else {
                        $showgrades = false;
                    }
                    if ($showgrades) {
                        $originalname = get_string('grades');
                        $list->specials[] = (object)array(
                            'originalname' => $originalname,
                            'displayname'  => $originalname,
                            'href'         => $CFG->wwwroot.'/grade/report/index.php?id='.$COURSE->id,
                            'icon'         => $PAGE->theme->pix_url('i/grades', 'core')->out()
                        );
                    }
                }
                if ($this->config->$special & self::SPECIAL_PARTICIPANTS) {
                    if (has_capability('moodle/course:viewparticipants', $context)) {
                        $originalname = get_string('participants');
                        $list->specials[] = (object)array(
                            'originalname' => $originalname,
                            'displayname'  => $originalname,
                            'href'         => $CFG->wwwroot.'/user/index.php?id='.$COURSE->id,
                            'icon'         => $PAGE->theme->pix_url('i/users', 'core')->out()
                        );
                    }
                }
                if ($this->config->$special & self::SPECIAL_CALENDAR) {
                    $originalname = get_string('calendar', 'calendar');
                    $href = $CFG->wwwroot.'/calendar/view.php?course='.$COURSE->id;
                    $href .= '&amp;view=month&amp;cal_d=1&amp;cal_m='.date('m', time()).'&amp;cal_y='.date('Y', time());
                    $list->specials[] = (object)array(
                        'originalname' => $originalname,
                        'displayname'  => $originalname,
                        'href'         => $href,
                        'icon'         => $PAGE->theme->pix_url('t/calendar', 'core')->out()
                    );
                }
                if ($this->config->$special & self::SPECIAL_COURSES) {
                    $originalname = get_string('courses');
                    $list->specials[] = (object)array(
                        'originalname' => $originalname,
                        'displayname'  => $originalname,
                        'href'         => $CFG->wwwroot.'/course/index.php',
                        'icon'         => $PAGE->theme->pix_url('i/course', 'core')->out()
                    );
                }
                if ($this->config->$special & self::SPECIAL_SITEPAGE) {
                    $originalname = get_string('frontpage', 'admin');
                    $list->specials[] = (object)array(
                        'originalname' => $originalname,
                        'displayname'  => $originalname,
                        'href'         => $CFG->wwwroot.'/course/view.php?id='.SITEID,
                        'icon'         => $PAGE->theme->pix_url('i/siteevent', 'core')->out()
                    );
                }
                if ($this->config->$special & self::SPECIAL_MYMOODLE) {
                    $originalname = get_string('mymoodle', 'admin');
                    $list->specials[] = (object)array(
                        'originalname' => $originalname,
                        'displayname'  => $originalname,
                        'href'         => $CFG->wwwroot.'/my/',
                        'icon'         => $PAGE->theme->pix_url('i/moodle_host', 'core')->out()
                    );
                }
            }

            if (count($list->items)) {
                switch ($this->config->$sort) {
                    case 0: break; // keep same order as on course page
                    case 1: usort($list->items, array($this, 'usort_originalname')); break;
                    case 2: usort($list->items, array($this, 'usort_displayname')); break;
                }
            }

            // sort and append activity index links, if any
            usort($list->indexes, array($this, 'usort_originalname'));
            $list->items = array_merge($list->items, $list->indexes);
            unset($list->indexes);

            // sort and append special moodle links, if any
            $list->items = array_merge($list->items, $list->specials);
            unset($list->specials);

            if ($list->title || $list->text || count($list->items)) {
                $lists[$i] = $list;
            }
        }

        if (count($lists)) {
            $show_separator = false;
            foreach ($lists as $i => $list) {
                $lists[$i] = '';
                if ($list->title) {
                    $lists[$i] .= '<h3 class="title">'.$list->title.'</h3>'."\n";
                } else if ($show_separator && $this->config->separator) {
                    $lists[$i] .= '<p class="separator">'.$this->config->separator.'</p>'."\n";
                }
                if ($list->text) {
                    $lists[$i] .= '<p class="text">'.$list->text.'</p>'."\n";
                }
                if (count($list->items)) {
                    foreach ($list->items as $ii => $item) {
                        if (empty($item->icon)) {
                            $icon = '';
                        } else {
                            $icon = '<img src="'.$item->icon.'" class="icon" />';
                        }
                        $list->items[$ii] = '<li><a href="'.$item->href.'" title="'.s($item->originalname).'">'.$icon.' '.$this->trim_name($item->displayname).'</a></li>';
                    }
                    $lists[$i] .= '<ul>'."\n".implode("\n", $list->items)."\n".'</ul>';
                }
                if ($lists[$i]) {
                    $show_separator = true;
                }
            }
            $this->content->text = implode("\n", $lists);
        }

        return $this->content;
    }

    /**
     * usort_displayname
     *
     * @param xxx $a
     * @param xxx $b
     * @return int -1, 1 or 0
     */
    function usort_displayname($a, $b) {
        return $this->usort_field($a, $b, 'displayname');
    }

    /**
     * usort_originalname
     *
     * @param xxx $a
     * @param xxx $b
     * @return int -1, 1 or 0
     */
    function usort_originalname($a, $b) {
        return $this->usort_field($a, $b, 'originalname');
    }

    /**
     * usort
     *
     * @param xxx $a
     * @param xxx $b
     * @param xxx $field
     * @return int -1, 1 or 0
     */
    function usort_field($a, $b, $field) {
        if ($a->$field < $b->$field) {
            return -1;
        }
        if ($a->$field > $b->$field) {
            return 1;
        }
        return 0; // equal values
    }

    /**
     * bitwise_or
     *
     * @param int $a
     * @param int $b
     * @return
     */
    function bitwise_or($a, $b) {
        return ($a | $b);
    }

    /**
     * trim_name
     *
     * @param string  $name
     * @param integer $namelength (optional, default = 0)
     * @param integer $headlength (optional, default = 0)
     * @param integer $taillength (optional, default = 0)
     * @return
     */
    function trim_name($name, $namelength=0, $headlength=0, $taillength=0) {

        if ($namelength) {
            $name = self::filter_text($name);
            $name = trim(strip_tags($name));
        } else {
            list($namelength, $headlength, $taillength) = $this->get_namelength();
        }

        $strlen = self::textlib('strlen', $name);

        if ($strlen > $namelength) {
            $head = self::textlib('substr', $name, 0, $headlength);
            $tail = self::textlib('substr', $name, $strlen - $taillength, $taillength);
            $name = $head.' ... '.$tail;
        }

        return $name;
    }

    /**
     * get_namelength
     *
     * @return array($namelength, $headlength, $taillength)
     */
    function get_namelength() {
        static $namelength = null;
        static $headlength = null;
        static $taillength = null;

        if (is_null($namelength)) {
            $lang = $this->get_lang_code();

            $namelength = 'namelength'.$lang;
            $headlength = 'headlength'.$lang;
            $taillength = 'taillength'.$lang;

            // get name length details for this language
            $namelength = $this->config->$namelength; // 22
            $headlength = $this->config->$headlength; // 10
            $taillength = $this->config->$taillength; // 10

            if ($namelength < 0) {
                $namelength = 0;
            }
            if ($headlength < 0) {
                $headlength = 0;
            }
            if ($taillength < 0) {
                $taillength = 0;
            }
        }

        return array($namelength, $headlength, $taillength);
    }

    /**
     * get_lang_code
     *
     * @return string
     */
    function get_lang_code() {
        static $lang = null;

        if (isset($lang)) {
            return $lang;
        }

        $lang = substr(current_language(), 0, 2);

        $namelength = 'namelength'.$lang;
        if (isset($this->config->$namelength)) {
            return $lang;
        }

        $lang = 'en';

        $namelength = 'namelength'.$lang;
        if (isset($this->config->$namelength)) {
            return $lang;
        }

        $lang = '';
        return $lang;
    }

    function fix_params(&$cm, $params) {
        global $DB;

        // sanitize the params
        $params = stripslashes(urldecode($params));

        // different modules require different fixes
        switch ($cm->modname) {

            case 'book':
                // go to a specific page in a book
                break;

            case 'database':
                // go to a specific category in a database
                break;

            case 'forum':
                // go to a specific thread in a forum
                break;

            case 'glossary':
                // go to a specific category in a glossary
                // "hook" is the glossary_categories id

                if (preg_match('/&?hook=([^&]*)/u', $params, $match, PREG_OFFSET_CAPTURE)) {
                    if (is_numeric($match[1][0])) {
                        // do nothing - this is already an id (hope it is valid !)
                    } else {
                        $sqlselect = 'glossaryid = ? AND name REGEXP ?';
                        $sqlparams = array($cm->instance, $match[1][0]);
                        if ($id = $DB->get_records_select('glossary_categories', $sqlselect, $sqlparams)) {
                            uasort($id, array($this, 'usort_by_namelength'));
                            $id = reset($id);
                            $id = $id->id;
                            // insert id of this glossary category
                            list($match, $start) = $match[1];
                            $params = substr_replace($params, $id, $start, strlen($match));
                        } else {
                            // remove this parameter from the param list
                            list($match, $start) = $match[0];
                            $params = substr_replace($params, '', $start, strlen($match));
                        }
                    }
                }
                break;

            case 'lesson':
                // go to a specific page in a lesson
                break;

            case 'taskchain':
                // go to a specific quiz in a taskchain
                break;

            case 'wiki':
                // go to a specific page in a wiki
                break;
        }

        return str_replace('&', '&amp;', '&'.$params);
    }

    /**
     * usort_by_namelength
     *
     * @param object $a
     * @param object $b
     * @return integer
     */
    public function usort_by_namelength($a, $b) {
        $a_len = self::textlib('strlen', $a->name);
        $b_len = self::textlib('strlen', $b->name);
        if ($a_len < $b_len) {
            return -1;
        }
        if ($a_len > $b_len) {
            return 1;
        }
        return 0;
    }

    /**
     * context
     *
     * a wrapper method to offer consistent API to get contexts
     * in Moodle 2.0 and 2.1, we use self::context() function
     * in Moodle >= 2.2, we use static context_xxx::instance() method
     *
     * @param integer $contextlevel
     * @param integer $instanceid (optional, default=0)
     * @param int $strictness (optional, default=0 i.e. IGNORE_MISSING)
     * @return required context
     * @todo Finish documenting this function
     */
    static public function context($contextlevel, $instanceid=0, $strictness=0) {
        if (class_exists('context_helper')) {
            // use call_user_func() to prevent syntax error in PHP 5.2.x
            $class = context_helper::get_class_for_level($contextlevel);
            return call_user_func(array($class, 'instance'), $instanceid, $strictness);
        } else {
            return get_context_instance($contextlevel, $instanceid);
        }
    }

    /**
     * textlib
     *
     * a wrapper method to offer consistent API for textlib class
     * in Moodle 2.0 and 2.1, $textlib is first initiated, then called
     * in Moodle 2.2 - 2.5, we use only static methods of the "textlib" class
     * in Moodle >= 2.6, we use only static methods of the "core_text" class
     *
     * @param string $method
     * @param mixed any extra params that are required by the textlib $method
     * @return result from the textlib $method
     * @todo Finish documenting this function
     */
    static public function textlib() {
        if (class_exists('core_text')) {
            // Moodle >= 2.6
            $textlib = 'core_text';
        } else if (method_exists('textlib', 'textlib')) {
            // Moodle 2.0 - 2.2
            $textlib = textlib_get_instance();
        } else {
            // Moodle 2.3 - 2.5
            $textlib = 'textlib';
        }
        $args = func_get_args();
        $method = array_shift($args);
        $callback = array($textlib, $method);
        return call_user_func_array($callback, $args);
    }

    /**
     * get_numsections
     *
     * a wrapper method to offer consistent API for $course->numsections
     * in Moodle 2.0 - 2.3, "numsections" is a field in the "course" table
     * in Moodle >= 2.4, "numsections" is in the "course_format_options" table
     *
     * @uses   $DB
     * @param  mixed   $course, either object (DB record) or integer (id)
     * @return integer $numsections
     */
    static public function get_numsections($course) {
        global $DB;
        if (is_numeric($course)) {
            $course = $DB->get_record('course', array('id' => $course));
        }
        if (isset($course->numsections)) {
            // Moodle <= 2.3
            return $course->numsections;
        }
        if (isset($course->format)) {
            // Moodle >= 2.4
            $params = array('courseid' => $course->id, 'format' => $course->format, 'name' => 'numsections');
            return $DB->get_field('course_format_options', 'value', $params);
        }
        return 0; // shouldn't happen !!
    }

    /**
     * filter_text
     *
     * @param string $text
     * @return string
     */
    static public function filter_text($text) {
        global $COURSE, $PAGE;

        $filter = filter_manager::instance();

        if (method_exists($filter, 'setup_page_for_filters')) {
            // Moodle >= 2.3
            $filter->setup_page_for_filters($PAGE, $PAGE->context);
        }

        return $filter->filter_text($text, $PAGE->context);
    }
}
