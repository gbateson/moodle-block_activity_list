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

// roles strings
$string['activity_list:addinstance'] = 'Add a new Activity List block';

// more strings
$string['apply'] = 'Apply';
$string['applyselectedvalues'] = 'Apply selected values to the following courses';
$string['caseinsensitive'] = 'Case insensitive';
$string['casesensitive'] = 'Case sensitive';
$string['charcount'] = 'Prefix length';
$string['cmids'] = 'Specific activities';
$string['cmids_help'] = 'This setting allows you to easily add links to the activities you select from the activity list.';
$string['exclude'] = 'Exclude';
$string['exportsettings'] = 'Export settings';
$string['exportsettings_help'] = 'This link allows you export the configuration settings for this block to a file that you can import into a similar block in another course.';
$string['head'] = 'Head';
$string['ignorecase'] = 'Ignore upper/lower case';
$string['ignorecase_help'] = 'When comparing the prefixes and suffixes of items in this list, this setting specifies whether to ignore or detect differences between upper and lowercase letters.

**Yes**  
&nbsp; differences between upper and lowercase letters will be ignored

**No**  
&nbsp; differences between upper and lowercase letters will be detected';

$string['ignorechars'] = 'Ignore these characters';
$string['ignorechars_help'] = 'Any characters specified here will be removed from the text of each item in this list.';
$string['importsettings'] = 'Import settings';
$string['importsettings_help'] = 'This link takes you to a screen where you can import configuration settings from a configuration settings file exported from the same type of block in another course.';
$string['include'] = 'Include';
$string['index'] = 'Activity index pages';
$string['index_help'] = 'These checkboxes allow you to easily add links to the index pages of activity types listed here.';
$string['invalidblockname'] = 'Invalid block name in block instance record: id={$a->id}, blockname={$a->blockname}';
$string['invalidcontextid'] = 'Invalid parentcontextid in block instance record: id = {$a->id}, parentcontextid = {$a->parentcontextid}';
$string['invalidcourseid'] = 'Invalid instanceid in course context record: id={$a->id}, instanceid={$a->instanceid}';
$string['invalidimportfile'] = 'Import file was missing, empty or invalid';
$string['invalidinstanceid'] = 'Invalid block instance id: id = {$a}';
$string['limit'] = 'Limit';
$string['list'] = 'List ({$a})';
$string['listcount'] = 'Number of lists';
$string['listcount_help'] = 'This setting specifies how many lists will appear in this block. The maximum number of lists is five.';
$string['listtitle'] = 'List title';
$string['listtitle_help'] = 'This string will be displayed as the title for this list. If this field is blank, no title will be displayed for this list.';
$string['long'] = 'Long';
$string['modname'] = 'Activity type filter';
$string['modname_help'] = 'This setting specifies which type of activities are to be included in the activity list. Only activity types that occur in this course are included in the list.';
$string['mycourses'] = 'My courses';
$string['mycourses_help'] ='On this list you can specify other courses to which you wish to copy this block\'s settings. The list only includes courses where you are a teacher and which already have an Activity List block.';
$string['namedisplay'] = 'Name display';
$string['namedisplay_help'] = 'These settings allow you to modify the activity names when they are displayed in this activity list.

Any text in the activity name that matches the **search** string will be replaced by the content of the **replace** string.

* The search can be case sensitive or case insensitive.
* You can specify the number of matches to be replaced using the **limit** setting.

Note that the search and replace strings are PHP regular expressions so you can use
[meta-characters](http://www.php.net/manual/en/regexp.reference.meta.php),
[escape sequences](http://www.php.net/manual/en/regexp.reference.escape.php),
[anchors](http://www.php.net/manual/en/regexp.reference.anchors.php),
[subpatterns](http://www.php.net/manual/en/regexp.reference.subpatterns.php),
[repitition](http://www.php.net/manual/en/regexp.reference.repetition.php),
[back references](http://www.php.net/manual/en/regexp.reference.back-references.php),
[assertions](http://www.php.net/manual/en/regexp.reference.assertions.php)
and so on.';
$string['namefilter'] = 'Activity name filter';
$string['namefilter_help'] = 'These settings allow you to include or exclude activities depending on their name.

* If the **include** string is specified, then an activity will only be added to the activity list if its name matches the include string.
* If the **exclude** string is specified, then an activity will not be added to the activity list if its name matches the exclude string.';
$string['params'] = 'Link parameters';
$string['params_help'] = 'Any parameters specified here will be added to the links to the activities.

The parameters are specified as: *name=value*. If there are several parameters, they should be joined with an ampersand, &quot;&amp;&quot;, like this:

* *name1=value1&amp;name2=value2&amp;name3=value3*';
$string['prefixchars'] = 'Prefix delimiters';
$string['prefixchars_help'] = 'If any characters are specified here, they will be used to detect the end of the prefix.

&nbsp; For a "short" prefix, the prefix ends at the **first** of these characters that is detected.
&nbsp; For a "long" prefix, the prefix ends at the **last** of these characters that is detected.';
$string['prefixkeep'] = 'Keep or remove prefix';
$string['prefixkeep_help'] = '**Remove**  
&nbsp; the prefix will be removed and the rest of the name or title will be kept

**Keep**  
&nbsp; the prefix will be kept and the rest of the name or title will be removed';
$string['prefixlength'] = 'Fixed prefix length';
$string['prefixlength_help'] = 'This setting specifies the number of characters in a fixed-length prefix.';
$string['prefixlong'] = 'Long or short prefix';
$string['prefixlong_help'] = '**Short**  
&nbsp; the shortest possible prefix will be used

**Long**  
&nbsp; the longest prefix will be used';
$string['replace'] = 'Replace';
$string['save'] = 'Save';
$string['search'] = 'Search';
$string['selectallnone'] = 'Select';
$string['selectallnone_help'] = 'The checkboxes in this column allow you to select certain settings in this block and copy them to TaskChain navigation blocks in other Moodle courses on this site.

Settings can be selected individually, or you can use the "All" or "None" links to select all or none of the settings with one click.

To select the courses to which you wish copy this block\'s settings, use the course menu at the bottom of this block configuration page.

Note that you can only copy the settings to courses in which you are a teacher (or administrator) and which already have a TaskChain navigation block.

To copy these settings to blocks in other Moodle sites, use the "export" function on this page, and the "import" function of the block on the destination site.';
$string['separator'] = 'Separator';
$string['separator_help'] = 'If specified, this text or html is inserted between each list displayed in this block. However, the separator will not be inserted before lists that have a title.';
$string['settingsmenu'] = 'Settings menu';
$string['short'] = 'Short';
$string['shortentext'] = 'Shorten item text';
$string['shortentext_help'] = '**Yes**  
&nbsp; if the text every of list item shares a common prefix with the list title, then the prefix will be removed from each item

**No**  
&nbsp; the text of every item on the list will be displayed in this block';
$string['sort'] = 'Sort method';
$string['sort_help'] = 'This settting specifies how the items in the activity list are to be sorted.

**Keep same order as course sections**  
&nbsp; the activities will appear in the same order as they do in the main content column of the course page.

**Sort by original name**  
&nbsp; the activities will be sorted by the activity names that are used in the main content column of the course page

**Sort by display name**  
&nbsp; the activities will be sorted by the display names, i.e. the activity names after they have been modified by the search and replace strings in the &quot;Name display&quot; settings';
$string['sortdisplayname'] = 'Sort by display name';
$string['sortoriginalname'] = 'Sort by original name';
$string['sortsectionsequence'] = 'Keep same order as course sections';
$string['special'] = 'Special Moodle pages';
$string['special_help'] ='These checkboxes allow you to easily add links to the following special Moodle pages.

**Grades**  
&nbsp; the grades page for this course

**Participants**  
&nbsp; the participant list, showing the names of students and teachers in this course

**Calendar**  
&nbsp; the calendar page for this course

**Courses**  
&nbsp; the page showing a list of all courses and course categories on this Moodle site

**My Moodle**  
&nbsp; the &quot;My Moodle&quot; page, which shows links to the user\'s courses and activities within them, such as unread forum posts and upcoming assignments

**Front Page**  
&nbsp; the front page of this Moodle site - also known as the &quot;site page&quot;';
$string['suffixchars'] = 'Suffix delimiters';
$string['suffixchars_help'] = 'If any characters are specified here, they will be used to detect the beginning of the suffix.

For a "short" suffix, the suffix starts at the **last** of these characters that is detected.
For a "long" suffix, the suffix starts at the **first** of these characters that is detected.';
$string['suffixkeep'] = 'Keep or remove suffix';
$string['suffixkeep_help'] = '**Remove**  
&nbsp; the suffix will be removed and the rest of the name or title will be kept

**Keep**  
&nbsp; the suffix will be kept and the rest of the name or title will be removed';
$string['suffixlength'] = 'Fixed suffix length';
$string['suffixlength_help'] = 'This setting specifies the number of characters in a fixed-length suffix.';
$string['suffixlong'] = 'Long or short suffix';
$string['suffixlong_help'] = '**Short**  
&nbsp; the shortest possible suffix will be used

**Long**  
&nbsp; the longest suffix will be used';
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
