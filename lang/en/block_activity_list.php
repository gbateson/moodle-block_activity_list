<?php
/**
 * blocks/activity_list/lang/en_utf8/block_activity_list.php
 *
 * @package   block_activity_list
 * @copyright 2011 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   you may not copy of distribute any part of this package without prior written permission
 */

// essential strings
$string['pluginname'] = 'Activity list';
$string['blockdescription'] = 'This block displays a list of useful links in a course.';
$string['blockname'] = 'Activity list';
$string['blocknameplural'] = 'Activity lists';

// more strings
$string['applyselectedvalues'] = 'Apply selected values to the following courses';
$string['caseinsensitive'] = 'Case insensitive';
$string['casesensitive'] = 'Case sensitive';
$string['charcount'] = 'Prefix length';
$string['cmids'] = 'Specific activities';
$string['cmids_help'] = 'This setting allows you to easily add links to the activities you select from the activity list.';
$string['exclude'] = 'Exclude';
$string['exportsettings'] = 'Export';
$string['exportsettings_help'] = 'This link allows you export the configuration settings for this block to a file that you can import into a similar block in another course.';
$string['head'] = 'Head';
$string['importsettings'] = 'Import';
$string['importsettings_help'] = 'This link takes you to a screen where you can import configuration settings from a configuration settings file exported from the same type of block in another course.';
$string['include'] = 'Include';
$string['index'] = 'Activity index pages';
$string['invalidimportfile'] = 'Import file was missing, empty or invalid';
$string['limit'] = 'Limit';
$string['list'] = 'List ($a)';
$string['listcount'] = 'Number of lists';
$string['listcount_help'] = 'This setting specifies how many lists will appear in this block. The maximum number of lists is five.';
$string['listtitle'] = 'List title';
$string['listtitle_help'] = 'This string will be displayed as the title for this list. If this field is blank, no title will be displayed for this list.';
$string['modfilter'] = 'Activity type filter';
$string['modfilter_help'] = 'This setting specifies which type of activities are to be included in the activity list. Only activity types that occur in this course are included in the list.';
$string['modindex_help'] = 'These checkboxes allow you to easily add links to the index pages of activity types listed here.';
$string['mycourses_help'] ='	On this list you can specify other courses to which you wish to copy this block\'s settings. The list only includes courses where you are a teacher and which already have an Activity List block.';
$string['namedisplay'] = 'Name display';
$string['namedisplay_help'] = 'These settings allow you to modify the activity names when they are displayed in this activity list.

Any text in the activity name that matches the **search** string will be replaced by the content of the **replace** string.

* The search can be case sensitive or case insensitive.
* You can specify the number of matches to be replaced using the **limit** setting.

Note that the search and replace strings are PHP regular expressions so you can use
<a href="http://www.php.net/manual/en/regexp.reference.meta.php">meta-characters</a>,
<a href="http://www.php.net/manual/en/regexp.reference.escape.php">escape sequences</a>,
<a href="http://www.php.net/manual/en/regexp.reference.anchors.php">anchors</a>,
<a href="http://www.php.net/manual/en/regexp.reference.subpatterns.php">subpatterns</a>,
<a href="http://www.php.net/manual/en/regexp.reference.repetition.php">repitition</a>,
<a href="http://www.php.net/manual/en/regexp.reference.back-references.php">back references</a>,
<a href="http://www.php.net/manual/en/regexp.reference.assertions.php">assertions</a>
and so on.';
$string['namefilter'] = 'Activity name filter';
$string['namefilter_help'] = 'These settings allow you to include or exclude activities depending on their name.

* If the **include** string is specified, then an activity will only be added to the activity list if its name matches the include string.
* If the **exclude** string is specified, then an activity will not be added to the activity list if its name matches the exclude string.';
$string['params'] = 'Link parameters';
$string['params'] = '
    Any parameters specified here will be added to the links to the activities.
</p>
<p>
    The parameters are specified as: <i>name=value</i>.
    If there are several parameters, they should be joined with an ampersand, &quot;&amp;&quot;, like this:
</p>
* *name1=value1&amp;name2=value2&amp;name3=value3*';
$string['replace'] = 'Replace';
$string['save'] = 'Save';
$string['search'] = 'Search';
$string['separator'] = 'Separator';
$string['separator_help'] = 'If specified, this text or html is inserted between each list displayed in this block. However, the separator will not be inserted before lists that have a title.';
$string['settingsmenu'] = 'Settings menu';
$string['sort'] = 'Sort method';
$string['sort_help'] = 'This settting specifies how the items in the activity list are to be sorted.

**Keep same order as course sections**
: the activities will appear in the same order as they do in the main content column of the course page.

**Sort by original name**
: the activities will be sorted by the activity names that are used in the main content column of the course page

**Sort by display name**
: the activities will be sorted by the display names, i.e. the activity names after they have been modified by the search and replace strings in the &quot;Name display&quot; settings';
$string['sortdisplayname'] = 'Sort by display name';
$string['sortoriginalname'] = 'Sort by original name';
$string['sortsectionsequence'] = 'Keep same order as course sections';
$string['special'] = 'Special Moodle pages';
$string['special_help'] ='These checkboxes allow you to easily add links to the following special Moodle pages.

**Grades**
: the grades page for this course

**Participants**
: the participant list, showing the names of students and teachers in this course

**Calendar**
: the calendar page for this course

**Courses**
: the page showing a list of all courses and course categories on this Moodle site

**My Moodle**
: the &quot;My Moodle&quot; page, which shows links to the user\'s courses and activities within them, such as unread forum posts and upcoming assignments

**Front Page**
: the front page of this Moodle site - also known as the &quot;site page&quot;';
$string['tail'] = 'Tail';
$string['text'] = 'Text';
$string['text_help'] = 'Any text entered here will be added below the list title and above the list of links.';
$string['textlength'] = 'Text length';
$string['textlength_help'] = 'These settings specify how to format links whose text is too long to be displayed in a single line in the block.

If the length of the text exceeds the &quot;Total&quot; number of characters specified here, then it will be reformatted as HEAD ... TAIL, where HEAD is the &quot;Head&quot; number of characters from the beginning of the text, and TAIL is the &quot;Tail&quot; number of characters from the end of the text.

You can specify separate values for each of the languages used in this course. Note that a value of zero will effectively disable then setting.';
$string['title'] = 'Title';
$string['title_help'] = 'This is the string that will be displayed as the title of this block. If this field is blank, no title will be displayed for this block.';
$string['total'] = 'Total';
$string['validimportfile'] = 'Configuration settings were successfully imported';
