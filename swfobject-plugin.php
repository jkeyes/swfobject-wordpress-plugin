<?php
/*
Plugin Name: SWFObject Plugin
Plugin URI:  http://keyes.ie/wordpress/swfobject-plugin/
Description: Replaces a simple tag syntax with SWFObject JavaScript to embed Flash video. 
Version: 0.1
Author: John Keyes 
Author URI: http://keyes.ie
*/

/*  Copyright 2009  John Keyes

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// YouTube 
define("VIDEO_CONFIG", 
	serialize(
		array(
			"youtube" => array(
				"width" => 425,
				"height" => 344,
				"regexp" => "/\[youtube ([[:print:]]+)\]/",
				"code" => "<script type=\"text/javascript\">swfobject.embedSWF(\"http://www.youtube.com/v/VID_ID\", \"youtube-VID_ID\", \"WIDTH\", \"HEIGHT\", \"9.0.0\",\"expressInstall.swf\", {}, {}, {});</script><div id=\"youtube-VID_ID\"></div>"
        	),
			"vimeo" => array(
				"width" => 400,
				"height" => 300,
				"regexp" => "/\[vimeo ([[:print:]]+)\]/",
				"code" => "<script type=\"text/javascript\">var flashvars = {};var params = {};var attributes = {};swfobject.embedSWF(\"http://vimeo.com/moogaloop.swf?clip_id=VID_ID&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1\", \"vimeo-VID_ID\", \"WIDTH\", \"HEIGHT\", \"9.0.0\",\"expressInstall.swf\", flashvars, params, attributes);</script><div id=\"vimeo-VID_ID\"></div>"
			)
    	)
	)
);

function video_plugin_callback($match)
{
	$parts = explode(" ", trim(substr($match[0], 1, -1))); 
	$config = unserialize(VIDEO_CONFIG);
	$video_type = $config[$parts[0]];
	if (count($parts) > 2) {
		$width = ( $parts[2] == 0 ) ? $video_type['width'] : $parts[2];
		$height = ( $parts[3] == 0 ) ? $video_type['height'] : $parts[3];
	} else {
		$width = $video_type['width'];
		$height = $video_type['height'];
	}
	$tokens = array( 'VID_ID', 'WIDTH', 'HEIGHT');
	$values = array( $parts[1], $width, $height); 
	$output = str_replace($tokens, $values, $video_type['code']);
	return ($output);
}
function video_plugin($content)
{
	$plugins = array();
	$config = unserialize(VIDEO_CONFIG);
	foreach ($config as $key => $value) {
		$content = (preg_replace_callback($config[$key]["regexp"], 'video_plugin_callback', $content));
	}
	return $content; 
}

add_filter('the_content', 'video_plugin',1);
add_filter('the_content_rss', 'video_plugin',1);
add_filter('comment_text', 'video_plugin');
?>
