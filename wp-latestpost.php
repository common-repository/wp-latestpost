<?php 

/*
Plugin Name: WP-LatestPost
Version: 1.0
Plugin URI: http://darkx-studios.com/?p=700
Description: This plugins notifys the online users about a new post ( works live )
Author: Neacsu Alexandru
Author URI: http://darkx-studios.com

Copyright (c) 2009
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt
*/

// Settings =>

$LP['CheckInterval'] = "20"; // The delay between new post checks ( in seconds )
$LP['Countdown'] = "20"; // If 0, the countdown will not display and the message wil stay on forever! ( in seconds )
$LP['Link_Title'] = "Read >>"; // The post link title (ex: <a href="http://mysite.com/post-name">Title</a> )
$LP['Minutes'] = "minutes"; // The translation for seconds ( ex: $LP['Minutes'] = "Minutos" for Spanish )
$LP['Seconds'] = "seconds"; // The translation for seconds ( ex: $LP['Seconds'] = "Segundos" for Spanish )
$LP['Animated'] = "true"; // Is the div animated?
$LP['Developer'] = "true"; // The plugin will display the latest post so you can modify the template.html file.

// Do not edit below thisw line

define('PluginDir', dirname(plugin_basename(__FILE__)));
define('PluginPath', 'wp-content/plugins/' . PluginDir .'/');
define('PluginFullUrl', get_option('siteurl') . '/wp-content/plugins/' . PluginDir .'/');


 function add_notify_js(){
	 
	 global $LP;
	 $PostCount = wp_count_posts();
	 $PostCount = trim($PostCount->publish);
	 
    if(!IsHotlink()){
		
	   if($_GET['lpCommand'] == "getMiniPost"){
		 GetMiniPosts();
      }
	  
	  if($_GET['lpCommand'] == "getPostCount"){
         exit($PostCount);
	  }
	 
	  if($_GET['lpCommand'] == "getBaseSettings"){
	   if($LP['Developer'] == "true"){
	   $PCnt = 0;
	   } else {
	   $PCnt = $PostCount;
	   }
	   exit("var LP_ChkTime={$LP['CheckInterval']}000; var LP_PostCount=$PCnt; var LP_ServerCount={$LP['Countdown']}; var LP_Animated={$LP['Animated']};");
	  }
	 }
	 
	 if(!strstr($_SERVER['REQUEST_URI'],"wp-admin")){
	 wp_enqueue_script('LatestPosts',PluginFullUrl . "wp_latestpost.js", array('jquery'));
	 }
	
 }
 
 function GetMiniPosts(){
  global $post, $LP;
  $myposts = get_posts('numberposts=1&order=DESC&orderby=date');
  $template = file_get_contents(PluginFullUrl . "template.html");
  foreach($myposts as $post){
   
   $PostCount = wp_count_posts();
   $MiniPost['Count'] = $PostCount->publish;
   $MiniPost['Title'] = $post->post_title;
   $UserInfo = get_userdata($post->post_author);
   $MiniPost['Link'] = '<a href="'.get_permalink($post->ID).'">'.$LP['Link_Title'].'</a>';
   $MiniPost['Countdown'] = '<span id="LP_Countdown"></span>';
   $MiniPost['Username'] = $UserInfo->display_name;
   $MiniPost['PublishTime'] = $post->post_date;
   $MiniPost['TimeAgo'] = GetTimeAgo($post->post_date);
   
   foreach($MiniPost as $Prop => $value){
	  $template = str_replace("%$Prop%",$value,$template);
   }
   
   exit($template);
   
  }
  
 }
 
 // Functii Diverse
 
 function IsHotlink(){
  $domain = explode("/",get_option('siteurl'));
  $isFromDomain = strstr($_SERVER['HTTP_REFERER'],$domain[2]);
  $isFromAjax = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
  $isHotlink = !$isFromDomain && !$isFromAjax;
  return $isHotlink;
 }

 function GetTimeAgo($postedTime){
	 
	 global $LP;
	
  $postedTime = strtotime($postedTime);
  $now = strtotime(current_time('mysql'));
  $diferenta = $now - $postedTime; 
  
   if($diferenta > 60){
     return round($diferenta/60) . " " . $LP['Minutes'];
   } else {
     return $diferenta . " " . $LP['Seconds'];
   }
   
 }

  add_action ( 'init', 'add_notify_js');

?>