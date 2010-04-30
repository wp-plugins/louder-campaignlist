<?php
 /*
Plugin Name: Louder latest campaign
Plugin URI: http://www.louder.org.uk/plugins_wp.php
Description: Display latest campaigns from Louder.org.uk
Version: 1.0 beta
Author: Adam Sargant
Author URI: http://www.adamsargant.net
License: GPL2

Copyright 2010  Adam Sargant  (email : adam@sargant.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
function widget_louderlatestcampaigns_register(){
	function widget_louderlatestcampaigns($args) {
		extract($args);
		$louderlatestcampaigns_displaycampaignlogos=get_option('louderlatestcampaigns_displaycampaignlogos');
		$louderlatestcampaigns_categoryID=get_option('louderlatestcampaigns_categoryID');
		$louderlatestcampaigns_displaycount=get_option('louderlatestcampaigns_displaycount');
		$loudercampaignsurl="http://www.louder.org.uk/api/listcampaigns?key=FZWsTqAUKJgMAzuZVzASwGYe&categoryID=$louderlatestcampaigns_categoryID&displaycount=$louderlatestcampaigns_displaycount";
		if(louderlatestcampaigns_curl_file_exists($loudercampaignsurl)){
			$loudercampaignxml=simplexml_load_file($loudercampaignsurl);
			if(count($loudercampaignxml->campaign)){
				echo $before_widget;
				echo $before_title
				."Louder.org.uk Campaigns"
				. $after_title;
				$output="<div style=\"width: 100%; margin: 0 auto; text-align: center; \">";
				foreach($loudercampaignxml->campaign as $campaign){
					if($louderlatestcampaigns_displaycampaignlogos){
						if($campaign->campaignlogo<>"0"){
							$output.= "<a href=\"http://www.louder.org.uk/$campaign->campaignslug/\"><img src=\"http://www.louder.org.uk/campaignlogodisplay.php?id=$campaign->campaignID\" alt=\"$campaign->campaignlogo\" /></a><br />";
						}
						else{
							$output.= "----";
						}
					}
					$output.="<p><a href=\"http://www.louder.org.uk/$campaign->campaignslug/\">$campaign->campaignname</a></p>";
				}
				$output.="</div>";
				echo $output;
				echo $after_widget;
			}
		}
	}

	register_sidebar_widget('Louder Display Latest Campaigns','widget_louderlatestcampaigns');
	function widget_louderlatestcampaigns_options() {
		$louderdirectoryurl="http://www.louder.org.uk/api/getdirectory?key=FZWsTqAUKJgMAzuZVzASwGYe";
		if(louderlatestcampaigns_curl_file_exists($louderdirectoryurl)){
			if(!function_exists(recursexml)){
				function recursexml($currentcategory, $depth, $selectedID){
					foreach ($currentcategory->category as $newcategory) {
						echo "<option value=\"".$newcategory->categoryID."\"";
						if($newcategory->categoryID==$selectedID){echo " selected=\"selected\"";}
						echo ">".str_repeat("-",$depth).$newcategory->categoryname."</option>";
						if($newcategory){recursexml($newcategory, $depth+1, $selectedID);}
					}	
				}
			}
			if(isset($_POST['update_louderlatestcampaigns'])){		
				$louderlatestcampaigns_categoryID=$_POST['louderlatestcampaigns_categoryID'];
				update_option('louderlatestcampaigns_categoryID',$louderlatestcampaigns_categoryID);
				$louderlatestcampaigns_displaycount=$_POST['louderlatestcampaigns_displaycount'];
				update_option('louderlatestcampaigns_displaycount',$louderlatestcampaigns_displaycount);
				if (isset($_POST['louderlatestcampaigns_displaycampaignlogos'])) {
					$louderlatestcampaigns_displaycampaignlogos=1;
					update_option('louderlatestcampaigns_displaycampaignlogos',$louderlatestcampaigns_displaycampaignlogos);
				}
				else{
					$louderlatestcampaigns_displaycampaignlogos=0;
					update_option('louderlatestcampaigns_displaycampaignlogos',$louderlatestcampaigns_displaycampaignlogos);			
				}
			}
			$louderlatestcampaigns_displaycampaignlogos=get_option('louderlatestcampaigns_displaycampaignlogos');
			$louderlatestcampaigns_categoryID=get_option('louderlatestcampaigns_categoryID');
			$louderlatestcampaigns_displaycount=get_option('louderlatestcampaigns_displaycount');
			$louderdirectoryxml=simplexml_load_file($louderdirectoryurl);
			$loudercampaignsurl="http://www.louder.org.uk/api/listcampaigns?key=FZWsTqAUKJgMAzuZVzASwGYe&categoryID=$louderlatestcampaigns_categoryID&displaycount=$louderlatestcampaigns_displaycount";
			
			$loudercampaignxml=simplexml_load_file($loudercampaignsurl);
			if(!count($loudercampaignxml->campaign)){
				echo "<p style=\"color:red;\">$loudercampaignsurl<br />That configuration has resulted in no campaigns to be listed. The widget will not display.</p>";
			}
			echo "<p><label for=\"louderlatestcampaigns_displaycount\">No. of Campaigns to display : <select name=\"louderlatestcampaigns_displaycount\">";
			$counter=1;
			while($counter<=5){
				echo "<option value=\"$counter\"";
				if($louderlatestcampaigns_displaycount==$counter){echo " selected=\"selected\"";}
				echo ">$counter</option>";
				$counter++;
			}
			echo "</select></label></p>";
			
			echo "<p><label for=\"louderlatestcampaigns_categoryID\">Select Campaign Category :<br />
			<select name=\"louderlatestcampaigns_categoryID\">";
			echo "<option value=\"0\"";
			if($louderlatestcampaigns_categoryID==0){echo " selected=\"selected\"";}
			echo ">All Campaigns</option>";
			recursexml($louderdirectoryxml,0, $louderlatestcampaigns_categoryID);
			echo "</select></label></p>";
			echo "<p><label for='louderlatestcampaigns_displaycampaignlogos'>Display Campaign Logos : <input id='louderlatestcampaigns_displaycampaignlogos' name='louderlatestcampaigns_displaycampaignlogos' type='checkbox' ";
			if($louderlatestcampaigns_displaycampaignlogos){echo "checked='checked' ";}
			echo "/></label></p><input type=\"hidden\" name=\"update_louderlatestcampaigns\" value=\"1\" />";
		}
		else{
			echo "<p style=\"color:red;\">The API could not be retrieved. This may be because the site is unavailable. Please notify the plugin author on <a href=\"mailto:adamsargant@gmail.com\">adamsargant@gmail.com</a></p>";
		}
	}
	register_widget_control('Louder Display Latest Campaigns',  'widget_louderlatestcampaigns_options');

}
add_action('init', widget_louderlatestcampaigns_register);

function louderlatestcampaigns_curl_file_exists($url){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	// $retcode > 400 -> not found, $retcode = 200, found.
	curl_close($ch);
	if($retcode==200){
		return TRUE;
	}
	else{
		return FALSE;
	}
}

?>
