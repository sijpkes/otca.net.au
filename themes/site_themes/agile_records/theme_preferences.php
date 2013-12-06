<?php /**
*The MIT License (MIT)
*
*Copyright (c) 2013 Paul Sijpkes.
*
*Permission is hereby granted, free of charge, to any person obtaining a copy
*of this software and associated documentation files (the "Software"), to deal
*in the Software without restriction, including without limitation the rights
*to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
*copies of the Software, and to permit persons to whom the Software is
*furnished to do so, subject to the following conditions:
*
*The above copyright notice and this permission notice shall be included in
*all copies or substantial portions of the Software.
*
*THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
*THE SOFTWARE.
*/ ?>
<?php

// --------------------------------------------------------------------

/**
 * Required modules
 */

// 'channel', 'member', 'stats' are already required by the system
$required_modules = array('email', 'rss', 'comment', 'search');

// --------------------------------------------------------------------

/**
 * Optional Values, Used for a Fresh Site
 */

$default_group = 'news';

// --------------------------------------------------------------------

/**
 * Default Preferences and Access Permissions for all Templates
 */

$default_template_preferences = array('caching'			=> 'n',
									  'cache_refresh'	=> 0,
									  'php_parsing'		=> 'none', // none, input, output
									  );

// Uses the Labels of the default four groups, as it is easier than the Group IDs, let's be honest
$default_template_access = array('Banned' 	=> 'n',
								 'Guests'	=> 'y',
								 'Pending'	=> 'y');

// --------------------------------------------------------------------

/**
 * Template Specific Preferences and Settings
 */

// $template_preferences['news']['index'] = array('caching' => 'y', 'cache_refresh' => 60);

$no_access = array(
					'Banned'	=> 'n',
					'Guests'	=> 'n',
					'Members'	=> 'n',
					'Pending'	=> 'n'
					);

$template_access['global_embeds']['index'] = $no_access;
$template_access['news_embeds']['index'] = $no_access;
$template_access['search']['index'] = $no_access;
				

/* End of file theme_preferences.php */
/* Location: ./themes/site_themes/agile_records
* /theme_preferences.php */
