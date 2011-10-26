<?php
/*
Plugin Name: Wordpress Guest Post Plugin
Plugin URI: http://lisaangelettieblog.com/wordpress-guest-post-plugin/
Description: Review posts submitted by the users to approval...
Author: Lisa Angelettie
Version: 2.5
Author URI: http://lisaangelettieblog.com/wordpress-guest-post-plugin/
*/

require_once(dirname(__FILE__).'/../../../wp-config.php');
require_once(dirname(__FILE__).'/../../../wp-admin/upgrade-functions.php');

register_activation_hook(__FILE__, 'wppostreviewbyadmin_install');
register_deactivation_hook(__FILE__, 'wppostreviewbyadmin_uninstall');

add_action('admin_menu', 'wppostreviewbyadmin_admin_menus');
//add_action('init', 'wppostreviewbyadmin_install');

add_action('init', 'wppostreviewbyadmin_init');
add_action('wp_head', 'wppostreviewbyadmin_styling');
//session_start();
global $wpdb;
define("WPUSER_POSTS_TABLE",$wpdb->prefix."usersposts");

wp_enqueue_script('postreviewbyadmin_script',get_bloginfo('wpurl').'/wp-content/plugins/wordpressguestpost/js/postreviewbyadmin_script.js');
wp_enqueue_script('inneditor_script',get_bloginfo('wpurl').'/wp-content/plugins/wordpressguestpost/jscripts/tiny_mce/tiny_mce.js');

function wppostreviewbyadmin_init()	{
	session_start();
}

function wppostreviewbyadmin_install()	{
	global $wpdb, $wp_rewrite;
	$table_userposts	=	WPUSER_POSTS_TABLE;
	$getTable			=	mysql_query("SHOW TABLES LIKE '".WPUSER_POSTS_TABLE."'");
	$Version			=	get_option('wppostreviewbyadmin_version');
	$Now			=	time();
	$createqry = "CREATE TABLE $table_userposts(
				`ID` bigint(20) NOT NULL auto_increment,
				`post_author` bigint(20) NOT NULL,
				`post_author_name` varchar(255) NOT NULL,
				`post_author_fname` varchar(255) NOT NULL,
				`post_author_lname` varchar(255) NOT NULL,
				`post_author_email` varchar(100) NOT NULL,
				`post_date` datetime NOT NULL,
				`post_date_gmt` datetime NOT NULL,
				`post_content` longtext NOT NULL,
				`post_title` text NOT NULL,
				`post_excerpt` text NOT NULL,
				`post_status` varchar(20) NOT NULL,
				`post_type` varchar(20) NOT NULL,
				`category_ids` varchar(500) NOT NULL,
				`post_tags` varchar(500) NOT NULL,
				`approval` int(11) NOT NULL, PRIMARY KEY  (`ID`) )";
	$wpdb->query($createqry);
	add_option('wppostreviewbyadmin_version','1.8');
	add_option('wppostreviewbyadmin_guestpage', '');
	add_option('wppostreviewbyadmin_adminmailid',get_option('admin_email'));
	add_option('wppostreviewbyadmin_paginationno',25);
	add_option('wppostreviewbyadmin_postsubmsg', "Thank you for your recent article submission. We have received it and it's going through the editorial process. Once your article is approved, it will be published to our site and you will receive a confirmation email.");
	add_option('wppostreviewbyadmin_postappmsg', 'Your Post "%postapp_title%" has got reviewed and approved by the admin.');
	add_option('wppostreviewbyadmin_enableeditor', '');

/*	
	$post_date				=	date("Y-m-d H:i:s");
	$post_date_gmt			=	gmdate("Y-m-d H:i:s");
	$num					=	0;
	$pages[$num]['name']	=	'add-posts';
	$pages[$num]['title']	=	'Add Post';
	$pages[$num]['tag']		=	'[addposts]';
	$pages[$num]['option']	=	'addposts_url';
	$num++;
	$newpages = false;
	$i = 0;
	$post_parent = 0;
	foreach($pages as $page)	{
		$check_page = $wpdb->get_row("SELECT * FROM `".$wpdb->posts."` WHERE `post_content` LIKE '%".$page['tag']."%'  AND `post_type` NOT IN('revision') LIMIT 1",ARRAY_A);
		if($check_page == null)	{
			if($i == 0) {
				$post_parent	=	0;
			}
			else	{
				$post_parent	=	$first_id;
			}
			if($wp_version >= 2.1)	{
				$sql ="INSERT INTO ".$wpdb->posts."(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_type) VALUES ('1', '$post_date', '$post_date_gmt', '".$page['tag']."', '', '".$page['title']."', '', 'publish', 'open', 'open', '', '".$page['name']."', '', '', '$post_date', '$post_date_gmt', '$post_parent', '0', 'page')";
			}
			else	{
				$sql ="INSERT INTO ".$wpdb->posts."(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_type) VALUES ('1', '$post_date', '$post_date_gmt', '".$page['tag']."', '', '".$page['title']."', '', 'publish', 'open', 'open', '', '".$page['name']."', '', '', '$post_date', '$post_date_gmt', '$post_parent', '0', 'page')";
			}
			$wpdb->query($sql);
			$post_id = $wpdb->insert_id;
			if($i == 0)	{
				$first_id = $post_id;
			}
			$wpdb->query("UPDATE $wpdb->posts SET guid = '" . get_permalink($post_id) . "' WHERE ID = '$post_id'");
			update_option($page['option'],  get_permalink($post_id));
			$newpages = true;
			$i++;
		}
	}
	*/
	add_option('wppostreviewbyadmin_style','original.css');
}

function wppostreviewbyadmin_uninstall()	{
	// The uninstall function is no longer used as of v1.2 but if you wish to do a clean uninstall and remove everything
	// Then uncomment the line '// register_deactivation_hook(__FILE__, 'wpmenudir_uninstall');' above then deactivate the plugin.
	global $wpdb;
	$table_links = WPUSER_POSTS_TABLE;
	$wpdb->query("DROP TABLE {$table_links}");
	delete_option('wppostreviewbyadmin_version');
	delete_option('wppostreviewbyadmin_guestpage');
	delete_option('wppostreviewbyadmin_adminmailid');
	delete_option('wppostreviewbyadmin_paginationno');
	delete_option('wppostreviewbyadmin_postsubmsg');
	delete_option('wppostreviewbyadmin_postappmsg');
	delete_option('wppostreviewbyadmin_style');
	delete_option('wppostreviewbyadmin_enableeditor');
}

function wppostreviewbyadmin_styling($Style='original.css')	{
	if($Style=='')	{
		$Style=get_option('wppostreviewbyadmin_style');
	}
	$StyleFile=dirname(__FILE__).'/styles/'.$Style;
	if(file_exists($StyleFile) && $Style)	{
		include_once($StyleFile);
	}
//	session_start();
}

function wppostreviewbyadmin_admin_menus()	{
	add_menu_page('Wordpress Guest Post', 'Wordpress Guest Post', 8, __FILE__, 'wppostreviewbyadmin_reviewposts');
	add_submenu_page(__FILE__, 'Settings', 'Settings', 8, 'sub-page', 'wppostreviewbyadmin_settings');
}

function wp_delete_userpost($spl_id)	{
	global $wpdb;
	$query = "DELETE FROM ".$wpdb->prefix."usersposts WHERE ID=$spl_id ";
	$wpdb->query($query);
}

function wp_select_userpost($postid)	{
	global $wpdb;
	if($postid != "")	{
		$selpostdetails_query = "SELECT * FROM ".$wpdb->prefix."usersposts where ID = ".$postid;
		$selpostdetails = $wpdb->get_results($selpostdetails_query, ARRAY_A);
	}
	return $selpostdetails;
}

function wppostreviewbyadmin_reviewposts()	{
	global $wpdb, $wp_rewrite;
	wppostreviewbyadmin_styling();
	if(($_POST["submit"]) && ($_POST["postid"]))	{
		$addpostval = wp_select_userpost($_POST["postid"]);
		$newpost = array();
		$newpost["post_author"] = 1;
		$newpost["post_category"][0] = $_POST["cat"];
		$newpost["post_date"] = $addpostval[0]["post_date"];
		$newpost["post_title"] = $_POST["post_title"];
		$newpost["post_content"] = htmlspecialchars(trim($_POST['post_content'], "\t\n "), ENT_QUOTES);
		$newpost["post_excerpt"] = $_POST["excerpt"];
		$newpost["post_status"] = 'publish';
		$newpost["post_type"] = 'post';
		$newpost["tags_input"] = $_POST["tags"];
		$pid = wp_insert_post( $newpost, $wp_error );
		if($pid != "")	{
			$StrMailcontent1 = '
				<html>
					<head><title>Your Post has been approved</title></head>
					<body>
						<div style="border:10px solid #3AABE3;float:left;width:610px;">
							<table align="center" width="610px" border="0" cellpadding="4" cellspacing="4" align="center">
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td style="color:#4E6E8E;"><font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Hi '.$addpostval[0]['post_author_name'].'</strong></font></td>
								</tr>
								<tr>
									<td width="130" valign="top" style="color:#4E6E8E;">
										<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>'.wppostreviewbyadmin_replace_posttitle($addpostval[0]["post_title"]).'</strong></font>
									</td>
								</tr>';
			$StrMailcontent1 .= '<tr><td>&nbsp;</td></tr>
								<tr>
									<td style="color:#4E6E8E;">
										<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">Thanks &amp; Regards <br>Admin</font>
									</td>
								</tr>
								<tr><td style="border-bottom:1px dotted #cccccc;"></td></tr>
							</table>
						</div>
					</body>
				</html>';
			$headers1  = 'MIME-Version: 1.0' . "\r\n";
			$headers1 .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers1 .= 'From: Admin Sample Website<'.get_option('wppostreviewbyadmin_adminmailid').'>'.'' . "\r\n";
			$subject1 = "Your Post has been published in our site";
			$ToEmail1 = $addpostval[0]['post_author_email'];
			mail($ToEmail1, $subject1, $StrMailcontent1, $headers1);
			wp_delete_userpost($addpostval[0]["ID"]);
			$_REQUEST["sns_reviewposts_view"] = "";
			$_REQUEST["sns_reviewposts_id"] = "";

			$appmsg = "The selected post has been approve successfully";
			echo "<div id=\"message\" class=\"updated fade\"><p>$appmsg</p></div>";
			
		}
	}
	
	if(($_REQUEST["sns_reviewposts_view"] == "approve") && ($_REQUEST["sns_reviewposts_id"] != "") )	{
		$unapost = wp_select_userpost($_REQUEST["sns_reviewposts_id"]);
		$expcatid = explode(",", $unapost[0]["category_ids"]);
		$catnames = "";
		for($i=0;$i<count($expcatid);$i++)	{
			$unapost[0]["category_names"] .= get_cat_name($expcatid[$i]).", ";
		}
		if ((current_user_can('level_10')))	{
?>
<script type="text/javascript">
	tinyMCE.init({
		mode : "exact",
		elements : "post_content",
		theme : "advanced",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist",
		theme_advanced_buttons2 : "undo,redo,|,outdent,indent,blockquote,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "styleselect,formatselect,fontselect,fontsizeselect,|,hr,removeformat,visualaid",
		theme_advanced_buttons4 : "tablecontrols,|,sub,sup,|,charmap,emotions,iespell,media,advhr",
		theme_advanced_buttons5 : "print,|,ltr,rtl,|,fullscreen,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,insertlayer,moveforward,movebackward,absolute,|,visualchars,nonbreaking",
		theme_advanced_buttons6 : "template,pagebreak,restoredraft",		
						
		
		
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		content_css : "css/content.css",

		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
	<div class="wrap">
		<h2>Review Posts Submitted by Users For Approval</h2>
		<p></p>

	<form id="frmaddpost" name="frmaddpost" method="post" action="" enctype="multipart/form-data">
		<input type="hidden" value="<?php echo $unapost[0]["ID"]; ?>" name="postid" id="postid" />
			<div style="float:left;width:600px;">
				<div style="float:left; width:580px;">
					<div style="float:left;width:175px;"><p>Title Of Article : </p></div>
						<div style="float:left;width:400px;">
							<p><input type="text" id="title" class="txtbg" value="<?php echo $unapost[0]["post_title"]; ?>" size="30" name="post_title" style="width:90%;"><br /><span class="hint">For Ex:- (100 characters max)</span></p>
						</div>
					</div>
					<div style="float:left; width:580px;">
						<div style="float:left;width:175px;"><p>Author First Name : </p></div>
						<div style="float:left;width:400px;">
							<p><input type="text" id="fname" class="txtbg" value="<?php echo $unapost[0]["post_author_fname"]; ?>" size="30" name="posted_fname" style="width:90%;"></p>
						</div>
					</div>
					<div style="float:left; width:580px;">
						<div style="float:left;width:175px;"><p>Author Last Name : </p></div>
						<div style="float:left;width:400px;">
							<p><input type="text" id="lname" class="txtbg" value="<?php echo $unapost[0]["post_author_lname"]; ?>" size="30" name="posted_lname" style="width:90%;"></p>
						</div>
					</div>
					<div style="float:left; width:580px;">
						<div style="float:left;width:175px;"><p>Author Email : </p></div>
						<div style="float:left;width:400px;">
							<p><input type="text" id="aemail" class="txtbg" value="<?php echo $unapost[0]["post_author_email"]; ?>" size="30" name="posted_aemail" style="width:90%;"></p>
						</div>
					</div>
					<div style="float:left; width:580px;">
						<div style="float:left;width:175px;"><p>Article Summary : </p></div>
						<div style="float:left;width:400px;">
							<p><textarea id="excerpt" name="excerpt" class="txtareabgsum" cols="40" rows="5" style="width:90%;"><?php echo $unapost[0]["post_excerpt"]; ?></textarea>
                  <br />
                  <span class="hint">(2-5 sentences, no paragraphs please)</span></p>
						</div>
					</div>
					<div style="float:left; width:580px;">
						<div style="float:left;width:175px;"><p>Article Content : </p></div>
						<div style="float:left;width:400px;">
							<p><textarea id="post_content" name="post_content" class="txtareabgcont" cols="40" rows="20" style="width:90%;"><?php echo $unapost[0]["post_content"]; ?></textarea></p>
						</div>
					</div>
					<div style="float:left;width:580px;">
						<div style="float:left;width:175px;"><p>Category : </p></div>
						<div style="float:left;width:400px;">
							<?php
								$pcatsary = array();
								$pcatsary["selected"] = $unapost[0]["category_ids"];
								$pcatsary["show_option_none"] = "Select Category";
								$pcatsary["hierarchical"] = true;
								$pcatsary["hide_empty"] = 0;
							?>
							<p><?php wp_dropdown_categories($pcatsary); ?></p>
						</div>
					</div>
					<div style="float:left;width:580px;"><div style="float:left;width:450px;"><p>&nbsp;</p></div></div>
					<div style="float:left; width:580px;">
						<div style="float:left;width:450px;">
							<p><input type="submit" value="Approve Post" id="submit" name="submit" style="cursor:pointer;"></p>
						</div>
					</div>
				</div>
			</form>
		</div>

<?php } else { ?>
	<div class="wrap">
	<p>Sorry, you are not allowed to view this page.</p>
	</div>
<?php } ?>		
		
		
<?php 		
	}
	
	elseif(($_REQUEST["sns_reviewposts_view"] == "disapprove") && ($_REQUEST["sns_reviewposts_id"] != "") )	{
		wp_delete_userpost($_REQUEST["sns_reviewposts_id"]);
		$delmsg = "The selected post has been disapprove successfully";
		echo "<div id=\"message\" class=\"updated fade\"><p>$delmsg</p></div>";
	}
	else {
	
	$selpostdetails_query	=	"SELECT * FROM ".$wpdb->prefix."usersposts";
	$selpostdetails			=	$wpdb->get_results($selpostdetails_query);
	$paginno = get_option('wppostreviewbyadmin_paginationno');
	if($_REQUEST['pno'])
		$pgno=$_REQUEST['pno']-1;
	else
		$pgno=0;
	$pno= $_REQUEST['pno'];
//	$limit=$pgno*25;
	$limit=$pgno*$paginno;
	
//	$getpostdetails_query	=	"SELECT * FROM ".$wpdb->prefix."usersposts ORDER BY ID ASC LIMIT $limit, 25";
	$getpostdetails_query	=	"SELECT * FROM ".$wpdb->prefix."usersposts ORDER BY ID ASC LIMIT $limit, $paginno";
	$getpostdetails			=	$wpdb->get_results($getpostdetails_query);
	
	$totalselpostdetails = count($selpostdetails);
	if(empty($getpostdetails))	{
		echo '<div class="error"><p>There are 0 posts for review.</p></div>';
	}
	else {
		echo '<div class="error"><p>There are ';
		echo count($getpostdetails);
		echo ' posts for review.</p></div>';	
	}
	?>
	<div class="wrap">
		<h2>Review Posts Submitted by Users For Approval</h2>
		<p></p>
		<table class="widefat">
			<thead>
				<tr>
					<th>Id</th>
					<th>Post Name</th>
					<th>Posted by</th>
					<th>Posted on</th>
					<th colspan="3">Actions</th>
				</tr>
			</thead>
			<tbody>
			<?php if(empty($getpostdetails))	{ ?>
				<tr>
					<td colspan="7">You currently have no post for approval.</td>
				</tr>
			<?php
				}
				else	{ ?>
				<tr>
					<td colspan="7">You currently have <?php echo count($getpostdetails); ?> posts for approval.</td>
				</tr>
<?php 			
					$class = "";
					$homesss = get_settings('home');
					$i=1;
					foreach($getpostdetails as $hh)	{
						echo "<tr id=\"search-{$hh->ID}\" class=\"$class\">";
						echo "<td>". $hh->ID."</td>\n";
						echo "<td>". $hh->post_title."</td>\n";
						echo "<td>". $hh->post_author_name."</td>\n";
						echo "<td>". $hh->post_date."</td>\n";
						echo "<td><a class='view' href='admin.php?page=wordpressguestpost/wordpressguestpost.php&amp;sns_reviewposts_view=approve&amp;sns_reviewposts_id={$hh->ID}'>Approve</a></td>\n";
						echo "<td><a class='view' href='admin.php?page=wordpressguestpost/wordpressguestpost.php&amp;sns_reviewposts_view=disapprove&amp;sns_reviewposts_id={$hh->ID}' onclick=\"return confirm('" . js_escape(sprintf( __("You are about to delete the slider image '%s'.\n'OK' to delete, 'Cancel' to stop.", 'slider_images'), $hh->id)) . "' );\">Disapprove</a></td>\n";						
						echo "</tr>";
						$class = empty($class)?"alternate":"";
						$i++;
					}
				}
			?>
			</tbody>
		</table>
		<?php //echo wppostreviewbyadmin_perpagefun($totalselpostdetails,25,$pno,$getcatlist); ?>
		<?php echo wppostreviewbyadmin_perpagefun($totalselpostdetails,$paginno,$pno,$getcatlist); ?>		
	</div>
	<?php
	}
}
function wppostreviewbyadmin_get_pages() {
	global $post;
//	$nonce= wp_create_nonce('kgp_nonce');
	$args = array('post_type' => 'page', 'numberposts' => -1, 'order'=> 'ASC', 'orderby' => 'menu_order'); 
//	$myposts = get_posts('post_type=page');
	$myposts = get_posts($args); ?>
	<select id="wppostreviewbyadmin_guestpage" name="wppostreviewbyadmin_guestpage">
	<option value="">Select Wordpress Guest Post Page</option>
	<?php foreach($myposts as $post) :
	setup_postdata($post);
	if(get_option('wppostreviewbyadmin_guestpage')==$post->ID) : ?>
	<option SELECTED value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
	<?php else : ?>
	<option value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
	<?php endif; ?>
	<?php endforeach; ?>
	</select>
<?php }
function wppostreviewbyadmin_get_adminemailid()	{
	if(get_option('wppostreviewbyadmin_adminmailid') == "")	{
		$adminemail = get_option('admin_email');
	}
	else	{
		$adminemail = get_option('wppostreviewbyadmin_adminmailid');
	}
	?>
	<input type="text" value="<?php echo $adminemail; ?>" name="adminmailid" id="adminmailid" size="50" />
<?php	
}
function wppostreviewbyadmin_get_paginationno()	{
	if(get_option('wppostreviewbyadmin_paginationno') == "")	{
		$postsno = 25;
	}
	else	{
		$postsno = get_option('wppostreviewbyadmin_paginationno');
	}
	?>
	<input type="text" value="<?php echo $postsno; ?>" name="paginationno" id="paginationno" size="50" />
<?php	
}
function wppostreviewbyadmin_get_enableeditor()	{
	if(get_option('wppostreviewbyadmin_enableeditor') == "")	{
		$enableeditor = "";
	}
	else	{
		$enableeditor = get_option('wppostreviewbyadmin_enableeditor');
	}
	?>
	<input type="checkbox" value="Y" name="enableeditor" id="enableeditor" <?php if($enableeditor == "Y") { ?> checked="checked" <?php } ?>  />
<?php	
}

function wppostreviewbyadmin_replace_posttitle($posttitle) {
	$msg=stripslashes(get_option('wppostreviewbyadmin_postappmsg'));
	$msg=str_replace('%postapp_title%', $posttitle, $msg);
	return $msg;
}
function wppostreviewbyadmin_get_postsubmsg()	{
	if(get_option('wppostreviewbyadmin_postsubmsg') == "")	{
		$postssubmsg = "Thank you for your recent article submission. We have received it and it's going through the editorial process. Once your article is approved, it will be published to our site and you will receive a confirmation email.";
	}
	else	{
		$postssubmsg = get_option('wppostreviewbyadmin_postsubmsg');
	}
	?>
	<textarea name="postssubmsg" id="postssubmsg" style="height:200px;" cols="50"><?php echo $postssubmsg; ?></textarea>
	<?php 
}
function wppostreviewbyadmin_get_postappmsg()	{
	if(get_option('wppostreviewbyadmin_postappmsg') == "")	{
		$postsappmsg = 'Your Post "%postapp_title%" has got reviewed and approved by the admin.';
	}
	else	{
		$postsappmsg = get_option('wppostreviewbyadmin_postappmsg');
	}
	?>
	<textarea name="postsappmsg" id="postsappmsg" style="height:200px;" cols="50"><?php echo $postsappmsg; ?></textarea>
	<?php 
}

function wppostreviewbyadmin_settings()	{
	echo '<div class="wrap">
			<h2>Settings</h2>
			<p></p>';
	if(get_option('wppostreviewbyadmin_guestpage') == "")	{
		echo '<div class="error"><p>Please select the page from the dropdown next to "Wordpress Guest Post Page"</p></div>';
	}
	if($_POST)	{
		update_option('wppostreviewbyadmin_guestpage', $_POST['wppostreviewbyadmin_guestpage']);
		update_option('wppostreviewbyadmin_adminmailid', $_POST['adminmailid']);
		update_option('wppostreviewbyadmin_paginationno', $_POST['paginationno']);
		update_option('wppostreviewbyadmin_postsubmsg', stripslashes($_POST['postssubmsg']));
		update_option('wppostreviewbyadmin_postappmsg', stripslashes($_POST['postsappmsg']));
		update_option('wppostreviewbyadmin_enableeditor', $_POST['enableeditor']);
		$wppostreviewbyadmin_guestpage = get_option('wppostreviewbyadmin_guestpage');		
	}
	//	echo "<div id=\"message\" class=\"updated fade\"><p>Setting will be available on upgraded version</p></div>";
	?>
	<form id="frmsettings" name="frmsettings" method="post" action="" enctype="multipart/form-data">
		<div style="float:left;margin: 5px 0 15px;">
			<div style="float:left; width:800px;">
				<div style="float:left;width:345px;"><p>Wordpress Guest Post Page : </p></div>
				<div style="float:left;width:450px;">
					<p><?php wppostreviewbyadmin_get_pages(); ?></p>
				</div>
			</div>
			<div style="float:left; width:800px;">
				<div style="float:left;width:345px;"><p>Wordpress Guest Post Admin Email : </p></div>
				<div style="float:left;width:450px;">
					<p><?php wppostreviewbyadmin_get_adminemailid(); ?></p>
				</div>
			</div>
			<div style="float:left; width:800px;">
				<div style="float:left;width:345px;"><p>Post Submission Message: </p></div>
				<div style="float:left;width:450px;">
					<p><?php wppostreviewbyadmin_get_postsubmsg(); ?></p>
				</div>
			</div>
			<div style="float:left; width:800px;">
				<div style="float:left;width:345px;"><p>Post Approval Message: <br /><small>%postapp_title% keyword has to specified for displying Post Title.</small></p></div>
				<div style="float:left;width:450px;">
					<p><?php wppostreviewbyadmin_get_postappmsg(); ?></p>
				</div>
			</div>
			<div style="float:left; width:800px;">
				<div style="float:left;width:345px;"><p>No. of Posts in a Page : <br /><small>(Pagination for the number of posts in the "Admin section")</small></p></div>
				<div style="float:left;width:450px;">
					<p><?php wppostreviewbyadmin_get_paginationno(); ?></p>
				</div>
			</div>
			<div style="float:left; width:800px;">
				<div style="float:left;width:345px;"><p>Enable the Editor for post content : <br /><small>(Checking this option will enable the editor for the post content)</small></p></div>
				<div style="float:left;width:450px;">
					<p><?php wppostreviewbyadmin_get_enableeditor(); ?></p>
				</div>
			</div>
			<div style="float:left;width:580px;"><div style="float:left;width:450px;"><p>&nbsp;</p></div></div>
				<div style="float:left; width:580px;">
					<div style="float:left;width:450px;">
						<p><input type="submit" value="Save Settings" id="submit" name="submit" style="cursor:pointer;"></p>
					</div>
				</div>
			</div>
		</form>
	<?php
}


function addpostbyuser_func()	{
	$plugin_dir_path = dirname(__FILE__);
	$plugin_directory = plugin_basename(__FILE__);
	include_once $plugin_dir_path . '/securimage/securimage.php';
	$securimage = new Securimage();
	if($_POST['submit']=='Submit Article')	{
		if(!$_POST['post_title'])	{
			$error = "Please enter the Title Of Article.";
		}
		elseif(!is_email($_POST['posted_aemail'], true))	{
			$error = "Please enter a valid email address.";
		}
		elseif($securimage->check($_POST['captcha_code']) == false) {
			$error = "The security code entered was incorrect.";
		}
		else	{
			$spl_id = wp_insert_userpost($_POST);
		}
	}
	
	if($_POST['Preview']=='Preview')	{
		$post_title = $_POST["post_title"];
		$post_author_name = $_POST["posted_aname"].''.$_POST["posted_lname"];
		$post_author_email = $_POST["posted_aemail"];
		$post_content = $_POST["post_content"];
//		$post_content = htmlspecialchars(trim($_POST['post_content'], "\t\n "), ENT_QUOTES);
		$excerpt = $_POST["excerpt"];
		$post_tags = $_POST["newtag"]["post_tag"];
		$post_status = "publish";
		$post_type = "post";
		$post_date	=	date("Y-m-d H:i:s");
		
		echo '
			<table border="0" cellspacing="10" cellpadding="10" class="previewtable">
				<tr><td valign="top" nowrap="nowrap" colspan="3"><h2>Preview the post</h2></td></tr>
				<tr>
					<td valign="top" nowrap="nowrap">Post Title </td>
					<td valign="top" nowrap="nowrap"> : </td>
					<td valign="top" nowrap="nowrap">'.$post_title.'</td>
				</tr>
				<tr>
					<td valign="top" nowrap="nowrap">Post Author Name</td>
					<td valign="top" nowrap="nowrap"> : </td>
					<td valign="top">'.$post_author_name.'</td>
				</tr>
				<tr>
					<td valign="top" nowrap="nowrap">Post Email Address </td>
					<td valign="top" nowrap="nowrap"> : </td>
					<td valign="top" nowrap="nowrap">'.$post_author_email.'</td>
				</tr>
				<tr>
					<td valign="top" nowrap="nowrap">Post Content </td>
					<td valign="top" nowrap="nowrap"></td>
					<td valign="top"></td>
				</tr>
				<tr>
					<td valign="top" colspan="3">'.$post_content.'</td>
				</tr>
				<tr>
					<td valign="top" nowrap="nowrap">Post Date </td>
					<td valign="top" nowrap="nowrap"> : </td>
					<td valign="top" nowrap="nowrap">'.$post_date.'</td>
				</tr>
			</table>
			<br/><br/><br/>';
	}
	if($spl_id)	{
		echo "<p class='addpostsuccess'>Thank you for your recent article submission. We have received it and it's going through the editorial process. Once your article is approved, it will be published to our site and you will receive a confirmation email.</p>";
	}
	else {
?>	
<?php if(get_option('wppostreviewbyadmin_enableeditor') != "")	{ ?>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "exact",
//		mode : "textareas",		
		elements : "post_content",
		theme : "advanced",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist",
		theme_advanced_buttons2 : "undo,redo,|,outdent,indent,blockquote,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "styleselect,formatselect,fontselect,fontsizeselect,|,hr,removeformat,visualaid",
		theme_advanced_buttons4 : "tablecontrols,|,sub,sup,|,print,|,ltr,rtl",
		theme_advanced_buttons5 : "charmap,emotions,iespell,media,advhr,|,fullscreen,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,insertlayer,moveforward,movebackward,absolute",
		theme_advanced_buttons6 : "visualchars,nonbreaking,|,template,pagebreak,restoredraft",		
						
		
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
<?php } ?>
<?php if($error)	{ ?>
<p style="color:#FF0000; font-weight:bold;"><?php echo $error; ?></p>
<?php } ?>
	<form id="frmaddpost" name="frmaddpost" method="post" action="" enctype="multipart/form-data">
		<div class="addpostmaindiv">
			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p>Title Of Article : </p></div>
				<div class="addpostctrl">
					<p><input type="text" id="title" class="txtbg" value="<?php echo $_POST['post_title']; ?>" size="30" name="post_title" /><br /><span class="hint">(100 characters max)</span>
					</p>
				</div>
			</div>
			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p>Author First Name : </p></div>
				<div class="addpostctrl">
					<p><input type="text" id="aname" class="txtbg" value="<?php echo $_POST['posted_aname']; ?>" size="30" name="posted_aname"></p>
				</div>
			</div>
			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p>Author Last Name : </p></div>
				<div class="addpostctrl">
					<p><input type="text" id="lname" class="txtbg" value="<?php echo $_POST['posted_lname']; ?>" size="30" name="posted_lname"></p>
				</div>
			</div>
			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p>Author Email : </p></div>
				<div class="addpostctrl">
					<p><input type="text" id="aemail" class="txtbg" value="<?php echo $_POST['posted_aemail']; ?>" size="30" name="posted_aemail"></p>
				</div>
			</div>
			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p>Article Summary : </p></div>
				<div class="addpostctrl">
					<p><textarea id="excerpt" name="excerpt" class="txtareabgsum" cols="40" rows="5"><?php echo $_POST['excerpt']; ?></textarea><br /><span class="hint">(2-5 sentences, no paragraphs please)</span></p>
				</div>
			</div>
			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p>Article Content : </p></div>
				<div class="addpostctrl">
					<p><textarea id="post_content" name="post_content" class="txtareabgcont" cols="40" rows="20" style="width: 90%" <?php if(get_option('wppostreviewbyadmin_enableeditor') == "")	{ ?> onkeyup="limiter()" <?php } ?> ><?php echo $_POST['post_content']; ?></textarea>
					</p>
					<?php if(get_option('wppostreviewbyadmin_enableeditor') == "")	{ ?>
                	<p>
                  <script type="text/javascript">
									document.write("<input style='width:30px;' type=text name=limit size=4 readonly value="+count+">");
								</script><small style="padding:0px 0px 0px 10px;">Word Count</small>
                </p>
					<?php } ?>
				</div>
			</div>
			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p>Category : </p></div>
				<div class="addpostctrl">
					<p>
						<select id="kategorienselect" name="cat">
							<option value="">-- Please Select</option>
							<?php
								$categories = get_categories('hierarchical=0&hide_empty=0');
								foreach($categories as $category)	{
									$selected = (is_category($category->cat_ID)) ? 'selected' : '';
									echo '<option '.$selected.' value="'.$category->cat_ID.'">'.$category->cat_name.'</option>';
								}
							?>
						</select>
					</p>
				</div>
			</div>

			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p>Security Code : </p></div>
				<div class="addpostctrl">
					<p><img id="captcha" src="<?php echo WP_PLUGIN_URL; ?>/wordpressguestpost/securimage/securimage_show.php" alt="CAPTCHA Image" border="0" /></p>
				</div>
			</div>

			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p></p></div>
				<div class="addpostctrl">
					<p>
<input type="text" name="captcha_code" size="10" maxlength="6" class="txtbg" style="width:130px;" />
<a href="#" onclick="document.getElementById('captcha').src = '<?php echo WP_PLUGIN_URL; ?>/wordpressguestpost/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>					
					</p>
				</div>
			</div>
			

			

			<div class="addpostinnerdiv"><div style="float:left;width:450px;"><p>&nbsp;</p></div></div>
			<div class="addpostinnerdiv">
				<div class="addpostlbl"><p>&nbsp;</p></div>
				<div class="addpostctrl">
					<p>
						<input type="submit" value="Submit Article" id="submit" name="submit" style="cursor:pointer;width:120px;height:35px;">&nbsp;
						<input type="submit" id="Preview" name="Preview"  value="Preview" style="cursor:pointer;width:110px;height:35px;">
					</p>
				</div>
			</div>
		</div>
	</form>
	<div style="float:left;width:100%;margin:10px 0px 10px 0px;padding:10px 0px 10px 0px;">
		<p align="center">
			<!--Plugin by <a href="http://lisaangelettieblog.com/wordpress-guest-post-plugin/" target="_blank">WordpressGuestPost</a>-->
		</p>
	</div>
<?php 
}	
}

add_action('the_content', 'callpostcnt');

function callpostcnt($content) {
	global $post;
	$theme_name = get_current_theme();
	$wppostreviewbyadmin_guestpage = get_option("wppostreviewbyadmin_guestpage");
//	if(is_page(get_option('wppostreviewbyadmin_guestpage'))) {
	if($post->ID == get_option('wppostreviewbyadmin_guestpage')) {
		if($theme_name == "Thesis")	{
			add_action('thesis_hook_before_post', 'addpostbyuser_func');
		}
		else	{
			add_action('the_content', 'addpostbyuser_func');
			return $content = addpostbyuser_func();
		}
	}
	else {
		return $content;
	}
}

	function wp_insert_userpost($postarr)	{
	global $wpdb;
	$post_title			=	$postarr["post_title"];
	$post_author_name	=	$postarr["posted_aname"].''.$postarr["posted_lname"];
	$post_author_fname	=	$postarr["posted_aname"];
	$post_author_lname	=	$postarr["posted_lname"];
	$post_author_email	=	$postarr["posted_aemail"];
	$post_content		=	$postarr["post_content"];
	$excerpt			=	$postarr["excerpt"];
	$post_tags			=	$postarr["tags"];
	$cat_ids			=	$postarr["cat"];
	$post_status		=	"publish";
	$post_type			=	"post";
	$post_date			=	date("Y-m-d H:i:s");
	$query				=	"INSERT IGNORE INTO ".$wpdb->prefix."usersposts(post_author_name, post_author_fname, post_author_lname, post_author_email, post_date, post_content, post_title, post_excerpt, post_status, post_type, category_ids, post_tags) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)";
	$query				=	$wpdb->prepare($query, $post_author_name, $post_author_fname, $post_author_lname, $post_author_email, $post_date, $post_content, $post_title, $excerpt, $post_status, $post_type, $cat_ids, $post_tags);
	$wpdb->query($query);
	$spl_id				=	$wpdb->insert_id;
	if($spl_id != "")	{
		$inspostdetails_query	=	"SELECT * FROM ".$wpdb->prefix."usersposts where ID = ".$spl_id;
		$inspostdetails			=	$wpdb->get_results($inspostdetails_query, ARRAY_A);
	}
	$inspostdetails[0]["category_names"] .= get_cat_name($inspostdetails[0]["category_ids"]);
	$StrMailcontent = '
		<html>
			<head><title>New Post Added in Temp Table</title></head>
			<body>
		<div style="border:10px solid #3AABE3;float:left;width:610px;">
			<table align="center" width="610px" border="0" cellpadding="4" cellspacing="4" align="center">
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr>
					<td style="color:#4E6E8E;" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Hi Admin</strong></font></td>
				</tr>
				<tr>
					<td width="130" valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Title Of Article</strong></font>
					</td>
					<td width="8" valign="top">:</td>
					<td width="332" style="color:#4E6E8E;" valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">'.$inspostdetails[0]['post_title'].' </font>
					</td>
				</tr>';
				$StrMailcontent .= '
				<tr>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Author First Name</strong></font>
					</td>
					<td width="8" valign="top">:</td>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">'.$inspostdetails[0]['post_author_fname'].'</font>
					</td>
				</tr>
				<tr>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Author Last Name</strong></font>
					</td>
					<td width="8" valign="top">:</td>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">'.$inspostdetails[0]['post_author_lname'].'</font>
					</td>
				</tr>';
				$StrMailcontent .= '
				<tr>
					<td style="color:#4E6E8E;" valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Article Summary</strong></font>
					</td>
					<td width="8" valign="top">:</td>
					<td style="color:#4E6E8E;" valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">'.$inspostdetails[0]['post_excerpt'].'</font>
					</td>
				</tr>
				<tr>
					<td style="color:#4E6E8E;" valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Article Content</strong></font>
					</td>
					<td width="8" valign="top">:</td>
					<td style="color:#4E6E8E;" valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">'.$inspostdetails[0]['post_content'].'</font>
					</td>
				</tr>
				<tr>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Category</strong></font>
					</td>
					<td width="8" valign="top">:</td>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">'.$inspostdetails[0]['category_names'].'</font>
					</td>
				</tr>
				<tr>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Tags</strong></font>
					</td>
					<td width="8" valign="top">:</td>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">'.$inspostdetails[0]['post_tags'].'</font>
					</td>
				</tr>
				<tr>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Posted on</strong></font>
					</td>
					<td width="8" valign="top">:</td>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">'.$inspostdetails[0]['post_date'].'</font>
					</td>
				</tr>				
				<tr>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>To review and approve the post</strong></font>
					</td>
					<td width="8" valign="top">:</td>
					<td valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><a href=" '.get_option('home').'/wp-admin/admin.php?page=wordpressguestpost/wordpressguestpost.php&sns_reviewposts_view=approve&sns_reviewposts_id='.$inspostdetails[0]['ID'].'">Click the link to review and approve posts</a></font>
					</td>
				</tr>';
				$StrMailcontent .= '<tr><td colspan="3">&nbsp;</td></tr>
				<tr>
					<td style="color:#4E6E8E;" colspan="3">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">Thanks &amp; Regards <br>';
							$StrMailcontent .= $inspostdetails[0]['post_author_fname'].' '.$inspostdetails[0]['post_author_lname'];
							$StrMailcontent .='</font>
					</td>
				</tr>
				<tr><td style="border-bottom:1px dotted #cccccc;" colspan="3"></td></tr>
			</table>
		</div>
		</body>
	</html>';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$inspostdetails[0]['post_author_name'].'<'.$inspostdetails[0]['post_author_email'].'>'.'' . "\r\n";
	$subject = "Sample Website | New Post Added on samplesite.com for Approval";
	$ToEmail = get_option('wppostreviewbyadmin_adminmailid');
	mail($ToEmail, $subject, $StrMailcontent, $headers);
	$StrMailcontent1 = '
	<html>
		<head><title>Reply for the New Post Added</title></head>
		<body>
		<div style="border:10px solid #3AABE3;float:left;width:610px;">
			<table align="center" width="610px" border="0" cellpadding="4" cellspacing="4" align="center">
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td style="color:#4E6E8E;"><font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>Hi '.$inspostdetails[0]['post_author_fname'].'</strong></font></td>
				</tr>
				<tr>
					<td width="130" valign="top" style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;"><strong>';
						
	//$StrMailcontent1 .= "Thank you for your recent article submission. We have received it and it's going through the editorial process. Once your article is approved, it will be published to our site and you will receive a confirmation email.";
	$StrMailcontent1 .= get_option('wppostreviewbyadmin_postsubmsg');
	$StrMailcontent1 .= '</strong></font>
					</td>
				</tr>';
				$StrMailcontent1 .= '<tr><td>&nbsp;</td></tr>
				<tr>
					<td style="color:#4E6E8E;">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif;">Thank You, <br>admin</font>
					</td>
				</tr>
				<tr><td style="border-bottom:1px dotted #cccccc;"></td></tr>
			</table>
		</div>
		</body>
	</html>';
	$headers1  = 'MIME-Version: 1.0' . "\r\n";
	$headers1 .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers1 .= 'From: Admin Sample Website<'.get_option('wppostreviewbyadmin_adminmailid').'>'.'' . "\r\n";
	$subject1 = "Reply for the New Post Added";
	$ToEmail1 = $inspostdetails[0]['post_author_email'];
		
	mail($ToEmail1, $subject1, $StrMailcontent1, $headers1);
	
	return $spl_id;
}


function wppostreviewbyadmin_perpagefun($total,$limit,$page='',$Search='')	{
	global $_REQUEST,$global_config,$_SESSION;
	$Searchval=$Search;
	$totalrecords  = $total;
	if($limit=='')
		$limit=5;
	//$pagenumber = intval(($offset + $limit) / $limit);
	$totalpages = intval($totalrecords/$limit);
	if ($totalrecords%$limit > 0)	{ // partial page
		$lastpage = $totalpages * $limit;
		$totalpages++;
	}
	else	{
		$lastpage = ($totalpages - 1) * $limit;
	}
	$navstring  = "
		<script type='text/javascript'>
			function perpages(pno)	{
				document.getElementById('pno').value=pno;
				document.perpage.submit();
			}
		</script>
		<div class='perpage' align='right' >";
	if ($totalrecords > $limit)	{ // only show <<PREV NEXT>> row if $totalrecords is greater than $limit
		$navstring .= "
			<div><form name='perpage' method='post'>
			<input type='hidden' name='pno' id='pno' value='' />
			<input type='hidden' name='Search' id='Search' value='$Searchval'/>";
		$navstring .= "<div align='right' style='padding-right:20px;padding-top:10px;'>";
		if($page=='')
			$page=0;
		$blocksize=6;
		if($pagenumber=='')
			$pagenumber=$page;
		if ($totalpages < $blocksize)	{
			$blocksize = $totalpages;
			$firstpage = 1;
		}
		elseif($pagenumber > $blocksize)	{
			$firstpage = ($pagenumber-$blocksize) + 2;
		}
		elseif ($pagenumber == $blocksize)	{
			$firstpage = 2;
		}
		else	{
			$firstpage = 1;
		}
		$blocklimit = $blocksize + $firstpage;
		for ($i=$firstpage;$i<$blocklimit;$i++)	{ 
			if ($i == $pagenumber)	{
				$navstring .= "&nbsp;<span class='NavPageFont'>$i</span> ";
			}
			else	{
				if ($i <= $totalpages)	{
					$nextoffset = $limit * ($i-1);
					//$navstring .= "&nbsp;<a title='Page ". $i ." of ". $totalpages ."' class='WhiteFont' onmouseover=this.className='WhiteFont-Over'  onmouseout=this.className='WhiteFont' href='".$page_url."?pno=".$i."&Search=".$Search."'>$i</a> ";
					$navstring .= "&nbsp;<a title='Page ". $i ." of ". $totalpages ."' class='WhiteFont' onmouseover=this.className='WhiteFont-Over'  onmouseout=this.className='WhiteFont'  onclick=perpages('$i') >$i</a> ";
				}
			}
		}
		$navstring .= "</div></div></form>";
	}
	return $navstring .= "</div>";
}
?>