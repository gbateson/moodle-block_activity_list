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
(7) rename to quizport_links ?
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

        foreach ($defaults as $name => $value) {
            if (! isset($this->config->$name)) {
                $this->config->$name = $value;
            }
        }

        // load user-defined title (may be empty)
        $this->title = $this->config->title;
    }

    /**
     * instance_config_save
     *
     * @param xxx $data
     * @param xxx $pinned (optional, default=false)
     * @return xxx
     */
    function instance_config_save($data, $pinned=false) {
        global $CFG, $COURSE, $USER;

        // do nothing if user hit the "cancel" button
        if (optional_param('cancel', 0, PARAM_INT)) {
            return true;
        }

        // expand "select_textlength", if required
        if (isset($data->select_textlength)) {
            $names = array('name', 'head', 'tail');

            $langs = get_list_of_languages();
            $langs = array_keys($langs);
            array_unshift($langs, '');

            foreach ($langs as $lang) {
                $lang = substr($lang, 0, 2);
                foreach ($names as $name) {
                    $selectname = 'select_'.$name.'length'.$lang;
                    $data->$selectname = $data->select_textlength;
                }
            }
            unset($data->select_textlength);
        }

        // ensure sensible value for $data->listcount
        $name = 'listcount';
        $count = 5; // max number of filters
        if (isset($data->$name) && is_numeric($data->$name)) {
            $data->$name = max(0, min($count, $data->$name));
        } else {
            $data->$name = 1; // default
        }

        // field names in name filter and name display settings
        $groups = array(
            'namefilter' => array('include', 'exclude'),
            'namedisplay' => array('search', 'case', 'replace', 'limit'),
        );

        for ($i=0; $i<$data->listcount; $i++) {

            // expand name filter and name display settings, if required
            foreach ($groups as $group => $names) {
                $selectgroup = 'select_'.$group.$i;
                if (empty($data->$selectgroup)) {
                    $data->$selectgroup = 0;
                }
                foreach ($names as $name) {
                    $selectname = 'select_'.$name.$i;
                    $data->$selectname = $data->$selectgroup;
                }
                unset($data->$selectgroup);
            }

            // convert cmids array to string
            $cmids = 'cmids'.$i;
            if (isset($data->$cmids) && is_array($data->$cmids)) {
                $data->$cmids = array_filter($data->$cmids); // remove empties
                $data->$cmids = implode(',', $data->$cmids); // convert to string
            }

            // convert activity index array to string
            $index = 'index'.$i;
            if (isset($data->$index) && is_array($data->$index)) {
                $data->$index = array_keys($data->$index, 1); // selected keys only
                $data->$index = implode(',', $data->$index); // convert to string
            }

            // convert special moodle links array to string
            $special = 'special'.$i;
            if (isset($data->$special) && is_array($data->$special)) {
                $data->$special = array_keys($data->$special, 1);
                $data->$special = array_reduce($data->$special, array($this, 'bitwise_or'), 0);
            }
        }

        $names = array(
            'title', 'text', 'cmids', 'index', 'modname', 'include', 'exclude',
            'search', 'case', 'replace', 'limit', 'sort', 'params'
        );

        $i = $this->config->listcount;
        while (($name = $names[0].$i) && isset($this->config->$name)) {
            foreach ($names as $name) {
                $name .= "$i";
                unset($this->config->$name);
            }
            $i++;
        }

        $selected = array();
        $courseids = array();

        $data = (array)$data;
        $names = array_keys($data);
        foreach ($names as $name) {

            if (substr($name, 0, 7)=='select_') {
                continue;
            }

            $selectname = 'select_'.$name;
            if (empty($data[$selectname])) {
                $data[$selectname] = 0;
            }

            if ($name=='mycourses') {
                $courseids = $data[$name];
            } else if ($data[$selectname]) {
                $selected[$name] = stripslashes_recursive($data[$name]);
            }

            // remove "select_" field
            unset($data[$selectname]);
        }

        $modinfo = get_fast_modinfo($COURSE, $USER->id);

        // get ids of courses (excluding this one) in which user can edit blocks
        $courses = $this->get_my_other_courses($courseids);

        if ($ids = implode(',', array_keys($courses))) {

            // get QuizPort navigation blocks in selected courses
            $select = "blockid={$this->instance->blockid} AND pagetype='course-view' AND pageid IN ($ids)";
            if ($instances = get_records_select('block_instance', $select)) {

                // update values in the selected block instances
                foreach ($instances as $instance) {
                    $instance->config = unserialize(base64_decode($instance->configdata));
                    foreach ($selected as $name => $value) {

                        // special processing for $cmids of cmids
                        // we want to search for the equivalent cm in the target course
                        // (same course section, same activity type, same activity name)
                        if (preg_match('/^list[0-9]+/', $name) && $value) {

                            $value = explode(',', $value);
                            foreach ($value as $v => $cmid) {

                                if (empty($modinfo->cms[$cmid])) {
                                    $new_cmid = 0; // shouldn't happen
                                } else {
                                    $select = 'cm2.id';
                                    $from   = $CFG->prefix.'course_modules cm1,'.
                                              $CFG->prefix.'course_sections s1,'.
                                              $CFG->prefix.$modinfo->cms[$cmid]->modname.' x1,'.
                                              $CFG->prefix.$modinfo->cms[$cmid]->modname.' x2,'.
                                              $CFG->prefix.'course_modules cm2,'.
                                              $CFG->prefix.'course_sections s2';
                                    $where  = 'cm1.id = '.$modinfo->cms[$cmid]->id.
                                              ' AND cm1.section = s1.id'.
                                              ' AND x1.id = cm1.instance'.
                                              ' AND x1.name = x2.name'.
                                              ' AND x2.course = '.$instance->pageid. // target course id
                                              ' AND x2.id = cm2.instance'.
                                              ' AND cm2.module = cm1.module'.
                                              ' AND cm2.section = s2.id'.
                                              ' AND s2.section = s1.section'; // $modinfo->cms[$cmid]->sectionnum
                                    $new_cmid = get_field_sql("SELECT $select FROM $from WHERE $where");
                                }
                                $value[$v] = $new_cmid;
                            } // end foreach $value

                            $value = array_filter($value); // remove blanks
                            $value = implode(',', $value); // convert to string

                        } // end if
                        $instance->config->$name = $value;
                    }
                    $instance->configdata = base64_encode(serialize($instance->config));
                    set_field('block_instance', 'configdata', $instance->configdata, 'id', $instance->id);
                } // end foreach $instance
            }
        }

        //  save config settings as usual
        $data = (object)$data;
        return parent::instance_config_save($data, $pinned);
    }

    /**
     * get_my_other_courses
     *
     * @return xxx
     */
    function get_my_other_courses($courseids=null) {
        global $CFG, $USER;

        $my_other_courses = array();
        $capability = 'moodle/site:manageblocks';

        if (function_exists('get_user_access_sitewide')) {
            // Moodle >= 1.8 : get access info for this user
            if (isset($USER->access)) {
                $access = $USER->access;
            } else {
                $access = get_user_access_sitewide($USER->id);
            }
            $courses = get_user_courses_bycap($USER->id, $capability, $access, true);
        } else if (function_exists('get_user_capability_course')) {
            // Moodle 1.7
            if ($courses = get_user_capability_course($capability, $USER->id)) {
                $ids = array();
                foreach ($courses as $course) {
                    if (is_null($courseids) || in_array($course->id, $courseids)) {
                        $ids[$course->id] = true;
                    }
                }
                $ids = implode(',', array_keys($ids));
                $courses = get_records_select('course', "id IN ($ids)", 'sortorder', 'id,shortname');
            }
        } else {
            // Moodle <= 1.6
            $select = 'c.id,c.shortname';
            $from   = "{$CFG->prefix}user_teachers ut, {$CFG->prefix}course c";
            $where  = "ut.userid=$USER->id AND ut.course=c.id";
            if ($courseids) {
                switch (count($courseids)) {
                    case 0: $where .= ' AND c.id<0'; break;
                    case 1: $where .= ' AND c.id='.$courseids[0]; break;
                    default: $where .= ' AND c.id IN ('.implode(',', $courseids).')';
                }
            }
            $courses = get_records_sql("SELECT $select FROM $from WHERE $where");
        }

        if ($courses) {
            foreach ($courses as $course) {
                if (is_null($courseids) || in_array($course->id, $courseids)) {
                    $my_other_courses[$course->id] = $course->shortname;
                }
            }
        }

        // remove the current course
        unset($my_other_courses[$this->instance->pageid]);

        // only allow courses that have a QuizPort navigation block
        if (is_null($courseids)) {
            $pageids = array();
            if ($ids = implode(',', array_keys($my_other_courses))) {

                // get QuizPort navigation blocks in selected courses
                $select = "blockid={$this->instance->blockid} AND pagetype='course-view' AND pageid IN ($ids)";
                if ($instances = get_records_select('block_instance', $select, '', 'id,pageid')) {

                    // get this pageid (= a course id)
                    foreach ($instances as $instance) {
                        $pageids[] = $instance->pageid;
                    }
                }
            }

            $ids = array_keys($my_other_courses);
            foreach ($ids as $id) {
                if (! in_array($id, $pageids)) {
                    unset($my_other_courses[$id]);
                }
            }
        }

        return $my_other_courses;
    }

    /**
     * get_content
     *
     * @return xxx
     */
    function get_content() {
        global $CFG, $COURSE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = (object)array(
            'text' => '',
            'footer' => ''
        );

        // get the current context
        if (empty($this->instance->pageid)) {
            $context = self::context(CONTEXT_COURSE, SITEID);
            $course = get_site();
        } else {
            $context = self::context(CONTEXT_COURSE, $this->instance->pageid);
            if (isset($COURSE->id) && $COURSE->id == $this->instance->pageid) {
                $course = $COURSE;
            } else {
                $course = get_record('course', 'id', $this->instance->pageid);
            }
        }

        $modinfo = get_fast_modinfo($course, $USER->id);

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
                        'icon'         => $CFG->modpixpath.'/'.$cm->modname.'/icon.gif'
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
                            'icon'         => $CFG->modpixpath.'/'.$cm->modname.'/icon.gif'
                        );
                    }
                }
            }

            if ($this->config->$special) {
                if ($this->config->$special & self::SPECIAL_GRADES) {
                    if ($COURSE->showgrades && has_capability('moodle/grade:view', $COURSE->context)) {
                        $showgrades = true; // student
                    } else if (has_capability('moodle/grade:viewall', $COURSE->context)) {
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
                            'icon'         => $CFG->pixpath.'/i/grades.gif'
                        );
                    }
                }
                if ($this->config->$special & self::SPECIAL_PARTICIPANTS) {
                    if (has_capability('moodle/course:viewparticipants', $COURSE->context)) {
                        $originalname = get_string('participants');
                        $list->specials[] = (object)array(
                            'originalname' => $originalname,
                            'displayname'  => $originalname,
                            'href'         => $CFG->wwwroot.'/user/index.php?id='.$COURSE->id,
                            'icon'         => $CFG->pixpath.'/i/users.gif'
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
                        'icon'         => $CFG->pixpath.'/t/calendar.gif'
                    );
                }
                if ($this->config->$special & self::SPECIAL_COURSES) {
                    $originalname = get_string('courses');
                    $list->specials[] = (object)array(
                        'originalname' => $originalname,
                        'displayname'  => $originalname,
                        'href'         => $CFG->wwwroot.'/course/index.php',
                        'icon'         => $CFG->pixpath.'/i/course.gif'
                    );
                }
                if ($this->config->$special & self::SPECIAL_SITEPAGE) {
                    $originalname = get_string('frontpage', 'admin');
                    $list->specials[] = (object)array(
                        'originalname' => $originalname,
                        'displayname'  => $originalname,
                        'href'         => $CFG->wwwroot.'/course/view.php?id='.SITEID,
                        'icon'         => $CFG->pixpath.'/c/site.gif'
                    );
                }
                if ($this->config->$special & self::SPECIAL_MYMOODLE) {
                    $originalname = get_string('mymoodle', 'admin');
                    $list->specials[] = (object)array(
                        'originalname' => $originalname,
                        'displayname'  => $originalname,
                        'href'         => $CFG->wwwroot.'/my/',
                        'icon'         => $CFG->pixpath.'/i/moodle_host.gif'
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

        $textlib = textlib_get_instance();
        $strlen = $textlib->strlen($name);

        if ($strlen > $namelength) {
            $head = $textlib->substr($name, 0, $headlength);
            $tail = $textlib->substr($name, $strlen - $taillength, $taillength);
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
                        $select = 'glossaryid='.$cm->instance.' AND name REGEXP '."'".addslashes($match[1][0])."'";
                        if ($id = get_field_select('glossary_categories', 'id', $select)) {
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

            case 'quizport':
                // go to a specific quiz in a quizport
                break;

            case 'wiki':
                // go to a specific page in a wiki
                break;
        }

        return str_replace('&', '&amp;', '&'.$params);
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
            return self::context($contextlevel, $instanceid);
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
     * get_userfields
     *
     * @param string $tableprefix name of database table prefix in query
     * @param array  $extrafields extra fields to be included in result (do not include TEXT columns because it would break SELECT DISTINCT in MSSQL and ORACLE)
     * @param string $idalias     alias of id field
     * @param string $fieldprefix prefix to add to all columns in their aliases, does not apply to 'id'
     * @return string
     */
     static public function get_userfields($tableprefix='', array $extrafields=null, $idalias='id', $fieldprefix='') {
        if (class_exists('user_picture')) {
            // Moodle >= 2.6
            return user_picture::fields($tableprefix, $extrafields, $idalias, $fieldprefix);
        } else {
            // Moodle <= 2.5
            $fields = array('id', 'firstname', 'lastname', 'picture', 'imagealt', 'email');
            if ($tableprefix || $extrafields || $idalias) {
                if ($tableprefix) {
                    $tableprefix .= '.';
                }
                if ($extrafields) {
                    $fields = array_unique(array_merge($fields, $extrafields));
                }
                if ($idalias) {
                    $idalias = " AS $idalias";
                }
                if ($fieldprefix) {
                    $fieldprefix = " AS $fieldprefix";
                }
                foreach ($fields as $i => $field) {
                    $fields[$i] = "$tableprefix$field".($field=='id' ? $idalias : ($fieldprefix=='' ? '' : "$fieldprefix$field"));
                }
            }
            return implode(',', $fields); // 'u.id AS userid, u.username, u.firstname, u.lastname, u.picture, u.imagealt, u.email';
        }
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
