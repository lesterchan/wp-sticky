# WP-Sticky
Contributors: GamerZ  
Donate link: http://lesterchan.net/site/donation/  
Tags: sticky, post, announcement, sticky post, stick, important, adhesive  
Requires at least: 3.3  
Tested up to: 5.9  
Stable tag: trunk  

[DEPRECATED] Adds a sticky post feature to your WordPress's blog.

## Description
Adds a sticky post feature to your WordPress's blog. Modified from Adhesive by Owen Winkler.

### Build Status
[![Build Status](https://travis-ci.org/lesterchan/wp-sticky.svg?branch=master)](https://travis-ci.org/lesterchan/wp-sticky)

### Development Blog
[https://github.com/lesterchan/wp-sticky](https://github.com/lesterchan/wp-sticky "https://github.com/lesterchan/wp-sticky")

### Credits
* Plugin icon by [Freepik](http://www.freepik.com) from [Flaticon](http://www.flaticon.com)

### Donations
I spent most of my free time creating, updating, maintaining and supporting these plugins, if you really love my plugins and could spare me a couple of bucks, I will really appreciate it. If not feel free to use it without any obligations.

## Changelog
### Version 1.53
* FIXED: Not being able to sort Posts by date in WP-Admin

### Version 1.52
* NEW: Add option to display sticky status (Sticky or Announcement) before post title
* NEW: Supports WordPress Multisite Network Activation
* NEW: Uses WordPress native uninstall.php
* FIXED: Missing argument 2 for sticky_the_title()

### Version 1.51
* FIXED: Allow HTML in Announcement Banner
* FIXED: Post Stick Status not showing before title

### Version 1.50 (01-06-2009)
* FIXED: Ensure That Post Is Not A Revision
* FIXED: Uses $_SERVER['PHP_SELF'] With plugin_basename(__FILE__) Instead Of Just $_SERVER['REQUEST_URI']

### Version 1.40 (12-12-2008)
* NEW: Works For WordPress 2.7 Only
* FIXED: Replaced is_sticky() to is_stickied()

### Version 1.31 (16-07-2008)
* NEW: Works For WordPress 2.6
* NEW: Added in stick_post And announcement_post CSS class. Credits To imvain2
* FIXED: Renamed 'Sticky' Tab To 'WP-Sticky' Tab Because Of Translation Issues.

### Version 1.30 (01-06-2008)
* NEW: Works For WordPress 2.5 Only
* NEW: Uses /wp-sticky/ Folder Instead Of /sticky/
* NEW: Uses wp-sticky.php Instead Of sticky.php
* FIXED: Manage Pages Will Be In Reverse Order If WP-Sticky Is Activated

### Version 1.10 (01-10-2007)
* NEW: Added announcement_banner(); For Those Not Using the_date();
* NEW: Ability To Uninstall WP-Sticky
* NEW: Moved Sticky Options From sticky.php To sticky-options.php

### Version 1.00 (01-06-2007)
* NEW: Initial Release

## Screenshots
1. Admin - Assign Sticky Status To Post
2. Admin - Sticky Options
3. Announcement Post
4. Sticky Post
