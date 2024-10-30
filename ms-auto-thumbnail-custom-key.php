<?php 
/*
Plugin Name: MS Auto Thumbnail Custom Key Generator
Plugin URI: http://shahidmau.blogspot.com
Description: Plugin to generate automatically custom key of the thumbnail of your post image
Author: M. Shahid (mshahid85@gmail.com)
Version: 1.0
Author URI: http://shahidmau.blogspot.com
*/

if($_REQUEST['ms_atckg_form']!='')
{
	update_option(ms_atckg_is_active,$_REQUEST['ms_atckg_is_active']);
	update_option(ms_atckg_keys,trim($_REQUEST['ms_atckg_keys']));
}
function ms_checkbox($now,$original)
{
	if($now==$original){ return "checked='checked'"; }
}
 
function ms_atckg_contents()
{
	echo '<form action="" method="post"><br /><br /><strong>MS Auto Thumbnail Custom Key Generator</strong><br /><br />
	<input name="ms_atckg_is_active" type="checkbox" '.ms_checkbox(1,get_option("ms_atckg_is_active")).' value="1" /> Enable Auto Thumbnail Custom Key
	<br /><br />
	Enter custom key name (one custom key per line)
	<br /><br /><b>e.g.</b> <br />Thumbnail<br />articleimg<br />postimg<br />
<textarea name="ms_atckg_keys" cols="60" rows="6">'.get_option("ms_atckg_keys").'</textarea>
 <p class="submit"><input name="" type="submit" value="    Save    "><input type="hidden" name="ms_atckg_form" value="1" ></p>
</form>

<b>usage:</b>
<br />
<p>use below code to show thumbnail in your template</p>
<p>$thumb = get_post_meta($post-&gt;ID, "Thumbnail", $single = true);<br>
  $thumb_alt = get_post_meta($post-&gt;ID, "Thumbnail-alt", $single = true);</p>
<p>(note: "<b>Thumbnail</b>" will be replaced by your own custom key)</p>

<br /><br />
for more details visit <a href="http://shahidmau.blogspot.com" target="_blank">http://shahidmau.blogspot.com</a>
';
} 

function ms_atckg() {
	add_options_page("MS Auto Thumbnail Custom Key Generator", "MS Auto Thumbnail Custom Key Generator", 8, "MS Auto Thumbnail Custom Key Generator", "ms_atckg_contents");
}

function ms_atckg_set_keys($id,$image_src,$image_alt)
{
	$arr=explode("\r",get_option("ms_atckg_keys"));
	for($i=0;$i<count($arr);$i++)
	{
		update_post_meta($id, $arr[$i], $image_src);
		update_post_meta($id, $arr[$i]."-alt", $image_alt);
	}
}

function ms_atckg_generate()
{
	if( (get_post_meta($post->ID, "Thumbnail", true)=="") and get_option("ms_atckg_is_active")=="1")
	{
		global $post;
		$pattern = '!<img.*?src="(.*?)"!';
		preg_match_all($pattern, $post->post_content, $matches);
		$image_src = $matches['1'][0];

		$pattern = '!<img.*?alt="(.*?)"!';
		preg_match_all($pattern, $post->post_content, $matches);
		$image_alt = $matches['1'][0];
		if($image_alt=="")
		{
			$pattern = '!<img.*?title="(.*?)"!';
			preg_match_all($pattern, $post->post_content, $matches);
			$image_alt = $matches['1'][0];
		}
		
		ms_atckg_set_keys($post->ID,$image_src,$image_alt);
	}
	return nl2br($post->post_content);
}

add_action('admin_menu', 'ms_atckg');
add_filter('the_content', 'ms_atckg_generate');
add_action('admin_head', 'ms_atckg_generate');
	 
?>
