<?php
/*
Plugin Name: Forum Restrict
Plugin URI: http://www.rexgoode.com/
Description: Restrict certain forums to certain users
Author: Rex Goode
Version: 1.4
Author URI: http://www.rexgoode.com/
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 

function forum_restrict_css() {
?>
	<style type="text/css">

	.forum_restrict {
		font-size: 8pt;
	}

	.forum_application {
		margin: 8pt 160pt 8pt 160pt;
		font-size: 11pt;
		line-height: 12pt;
	}
	.forum_restrict a{
		text-decoration: none;
		font-size: 10pt;
		font-weight: bold;
		color: black;
		background-color: #fee;
		margin-right: 8pt;
	}

	.forum_restrict a:hover {
		background-color: #eef;
	}

	.member_table tr td {
		padding: 0pt 6pt 0pt 6pt;
	}

	.member_table tr th {
		font-size: 12pt;
		padding: 0pt 6pt 0pt 6pt;
	}

	.forum_restrict_info {
		font-size: 12pt;
		background-color: #cdd;
		text-align: center;
	}
	</style>
<?php
}


	$adminurl = get_option('siteurl').'/wp-admin';
	$sep = (strpos(__FILE__,'/')===false)?'\\':'/';
	$WPpluggable = substr( dirname(__FILE__),0,strpos(dirname(__FILE__),'wp-content')) . 'wp-includes'.$sep.'pluggable.php';
	if ( file_exists($WPpluggable) )
		require_once($WPpluggable);

if (!class_exists("fr_Forum")) {
	class fr_Forum {
		var $forum_id;
		var $forum_name;
		var $forum_slug;
		var $restricted;
		var $userRequestable;

		function __construct($forum_id) {
			$this->forum_id = $forum_id;
			$this->read();
		}

		function read() {
			global $wpdb;

			$row = $wpdb->get_row("SELECT post_title, post_name FROM {$wpdb->prefix}posts WHERE ID = $this->forum_id", OBJECT);
			$this->forum_name = $row->post_title;
			$this->forum_slug = $row->post_name;
			list($this->restricted) = get_post_meta($this->forum_id, "isForumRestricted");
			list($this->userRequestable) = get_post_meta($this->forum_id, "userRequestable");
			list($this->isForumNonmemberInvisible) = get_post_meta($this->forum_id, "isForumNonmemberInvisible");
		}
	}
} else {
	die("fr_Forum class already exists");
}

	//Set Up Plugin Class
if (!class_exists("Forum_Restrict")) {		

    	class Forum_Restrict {
		var $pluginurl;
		var $working_forum;
		var $search_term;
		var $bbpress;
		var $messages;

		function Forum_Restrict() { //constructor
			global $user_ID;
			add_action('admin_notices', array($this,'notices'));
			$this->pluginurl = get_option('siteurl').'/wp-content/plugins/forum_restrict';
			if (isset($_REQUEST['user_search_term'])) $this->search_term = $_REQUEST['user_search_term']; else $this->search_term = "";
			if (isset($_REQUEST['fr_action']))
			switch($_REQUEST['fr_action']) {
				case "Submit Application":
					$forum = new fr_Forum($_REQUEST['forum_id']);
					$forum->read();
					$slug = $forum->forum_slug;
					$reason = $_REQUEST['application_reason'];
					list($previous_status) = get_user_meta($_REQUEST['user_id'], $slug."-application-status");
					if ($previous_status == "") $previous_status = "Pending";
					switch($previous_status) {
						case "Pending":
							break;
						case "Denied":
							list($apply_attempts) = get_user_meta($_REQUEST['user_id'], $slug."-reapplication-attempts");
							if (!$apply_attempts) $apply_attempts = 0;
							$apply_attempts++;
							if ($apply_attempts <= 3) $new_status = "Pending"; else $new_status = "Denied";
							update_user_meta($_REQUEST['user_id'], $slug."-application-status", $new_status);
							update_user_meta($_REQUEST['user_id'], $slug."-reapplication-attempts", $apply_attempts);
							unset($_REQUEST['forum_id']);
							break;
					}
					update_user_meta($_REQUEST['user_id'], $slug."-application-reason", $reason);
					break;
			}
			$this->setmessages();
		}

		function setmessages() {
			include_once("setmessages.php");
		}

		function notices() {
			$this->bbpress = is_plugin_active("bbpress/bbpress.php");
			if (!$this->bbpress) {
				echo "<div class='error'>
				<h2>Forum Restrict is intended to work exclusively with sites with the bbpress plugin activited.
				You must install and activate bbpress plugin for this plugin to work.</h2>
				</div>";
				deactivate_plugins("forum_restrict/index.php");
			}
		}

		function Main() {
			global $wpdb, $adminurl;
		}

		function frout($str) {
			echo("$str\n");
		}

		function forum_prelim() {
			global $adminurl;
			include("forum_restrict.js");
		}

		function administer() {
			global $wpdb, $adminurl, $pluginurl;
			echo "<br /><h1 align=center>Forum Restrictions Administration</h1>\n";
			if ($_REQUEST['forum_id']) $this->set_working_forum($_REQUEST['forum_id']);
			switch($_REQUEST['fr_action']) {
				case "Potential Member Search":
					$this->forum_dropdown();
					echo "<h3 align=center>Search Results (potential members)</h3>\n";
					$this->user_search();
					break;
				default:
					$this->forum_dropdown();
				break;
			}
		}

		function user_applications() {
			global $userdata, $adminurl, $wpdb;
			echo "<br /><h1 align=center>Forum Application</h1>\n";
			$username = $userdata->data->display_name;
			$user_ID = $userdata->data->ID;
			echo "<h2 align=center>$username</h2>\n";
			if ($_REQUEST['forum_id'] && ($_REQUEST['fr_action'] == "" || $_REQUEST['fr_action'] == "User Manage")) {
				echo "<h3 align=center>$forum->forum_name</h3>\n";
				$apply_forum = new fr_Forum($_REQUEST['forum_id']);
				$apply_forum->read();
				$slug = $apply_forum->forum_slug;
				list($app_reason) = get_user_meta($user_ID, $slug."-application-reason");
				list($app_status) = get_user_meta($user_ID, $slug."-application-status");
				if ($app_status == "") $app_status = "Pending";
				list($apply_attempts) = get_user_meta($user_ID, $slug."-reapplication-attempts");
				if ($apply_attempts == "") $apply_attempts = 0;
				$apply_attempts++;
				if ($apply_attempts <= 3) {
					echo "<div class='forum_application'>\n";
					echo "<form method='post' id='apply_forum' action='$adminurl/users.php?page=forum_application'>\n";
					echo "<input type='hidden' name='forum_id' value='$_REQUEST[forum_id]' />\n";
					echo "<input type='hidden' name='user_id' value='$user_ID' />\n";
					echo "<p align=center>Your current status is <b>$app_status</b>.</p>\n";
					echo "<p align=center>In the box below, enter your reason for wanting to join this forum:</p>\n";
					echo "<p align=center><textarea rows='10' cols='50' name='application_reason'>$app_reason</textarea></p>\n";
					switch($app_status) {
						case 'Pending':
							break;
						case 'Denied':
							echo "<p>You are currently in <b>Denied</b> status. This is likely because a forum moderator
	                                                         did not feel your reason was adequate for joining the forum. Each user is given
	                                                         three attempts to successfully apply for membership to a forum. For this forum,
	                                                         you are on attempt number <em>$apply_attempts</em>.";
							if ($apply_attempts == "3") echo " This is your last attempt. If you want to be reconsidered,
									you must contact the forum administrator.";
							echo "</p>\n";
							break;
						default:
							break;
					}
					echo "<center><input type=submit name=fr_action value='Submit Application'></center>\n";
				} else {
					echo "<h2 align=center>You may no longer try to apply for membership to this forum. Please contact
						the administrator to try again.</h2>\n";
				}
				echo "</form>\n";
				echo "</div>\n";
			} else {
?>
<script type='text/javascript'>
function fr_delcheck(f) {
	var result = confirm('Are you sure?');
	if (result) {
		f.submit();
		return true;
	} else {
		return false;
	}
}
</script>
<?php
				$forum_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'forum'");
				$header = "<h3 align=center>Your Application(s)</h3>\n";
				echo "<form method='post' id='my_applications' action='$adminurl/users.php?page=forum_application'>\n";
				echo "<input type='hidden' name='fr_action' value='User Manage'>\n";
				$header .= "<table align=center class='member_table'>\n";
				$header .= "<tr><th>Forum</th><th>Reason</th><th>Status</th><th>Delete</th><th>Edit</th></tr>\n";
				foreach($forum_ids as $forum_id) {
					$forum = new fr_Forum($forum_id);
					$forum->read();
					$slug = $forum->forum_slug;
					list($app_reason) = get_user_meta($user_ID, $slug."-application-reason");
					list($app_status) = get_user_meta($user_ID, $slug."-application-status");
					list($apply_attempts) = get_user_meta($user_ID, $slug."-reapplication-attempts");
					if ($apply_attempts == "") $apply_attempts = 0;
					$apply_attempts++;
					if ($app_status == "" || $app_status == "Pending") {
						$app_status = "Pending";
						$action = "None";
					}
					if ($app_reason) {
						echo "<tr><td colspan>$header;</td></tr>\n";
						$header = "";
						echo "<tr>\n";
						echo "		<td>\n";
						echo "			$forum->forum_name\n";
						echo "		</td>\n";
						echo "		<td>\n";
						echo "			$app_reason\n";
						echo "		</td>\n";
						echo "		<td>\n";
						if ($app_status == "Denied" && $apply_attempts > 3) echo "Permanently";
						echo "			$app_status\n";

						echo "		</td>\n";
						echo "		<td align='center'>\n";
						echo "			<input type='checkbox' name='delete_appl[$forum_id][$user_ID]' onClick='return fr_delcheck(this.form);' />\n";
						echo "		</td>\n";
						echo "		<td align='center'>\n";
						if ($apply_attempts <= 3)
						echo "			<input type='checkbox' name='forum_id' value='$forum_id' onClick='return this.form.submit();' />\n";
						else echo "X";
						echo "		</td>\n";
						echo "</tr>\n";
					}
				}
				echo "</table>\n";
				echo "</form>\n";
			}
		}

		function ConfigureMenu() {
			add_submenu_page( "users.php", "Forum Restrictions Admin", "Forum Restrictions", "publish_forums", "forum_restrict", array($this, 'administer'));
			add_submenu_page( "users.php", "Forum Application", "Forum Application", "read", "forum_application", array($this, 'user_applications'));
		}

		function forum_dropdown() {
			global $wpdb;


			$forum_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'forum'");
			$forums = array();
			foreach($forum_ids as $forum_id) {
				$forum = new fr_Forum($forum_id);
				$forum->read();
				if ($forum->restricted) {
					array_push($forums, $forum);
				}
			}
			echo "<div id=\"SELECT_FORM\">\n";
			echo "<form>\n";
			echo "<p align='center'><b>Forum:</b> "; 
			echo "<input type='hidden' name='user_search_term' value=''>\n";
			echo "<select name='forum_id' onChange=\"try {WriteForum(this.form, ''); } catch(e) { alert(e); }\">\n";
			echo "<option value='0'>Choose a forum</option>\n";
			foreach($forums as $forum) {
				if ($_REQUEST['forum_id'] == $forum->forum_id) $sel = " selected"; else $sel = "";
				echo "<option value='$forum->forum_id'$sel>$forum->forum_name </option>\n";
			}
			echo "</select></p>\n";
			echo "</form>\n";
			echo "</div>\n";
			echo "<div id=\"Forum_Title\"></div>\n";
			echo "<div id=\"Search_Field\"></div>\n";
			echo "<div id=\"Member_List\"></div>\n";
			echo "<div id=\"Search_Results\"></div>\n";
			echo "<div id=\"Member_Approvals\"></div>\n";
			echo "<div id=\"AJAX_Error\"></div>\n";
		}

		function metabox($postID) {
			$forum = new fr_Forum($postID);
			$forum->read();
			echo "<table>\n";
			echo "<tr>\n";
			echo "	<td valign=top>\n";
			if ($forum->restricted) $checked = "checked"; else $checked = "";
			echo "		<input type='checkbox' name='isForumRestricted' $checked/>\n";
			echo "	</td>\n";
			echo "	<td>\n";
			echo "		Restrict access to this forum using Forum Restrict plugin.\n";
			if ($forum->userRequestable) $checked = "checked"; else $checked = "";
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td valign=top>\n";
			echo "		<input type='checkbox' name='isForumUserRequestable' $checked/>\n";
			echo "	</td>\n";
			echo "	<td>\n";
			echo "	Users can request to join this forum.\n";
			echo "	</td>\n";
			echo "</tr>\n";
			if ($forum->isForumNonmemberInvisible) $checked = "checked"; else $checked = "";
			echo "<tr>\n";
			echo "	<td valign=top>\n";
			echo "		<input type='checkbox' name='isForumNonmemberInvisible' $checked/>\n";
			echo "	</td>\n";
			echo "	<td>\n";
			echo "	Not visible to non-members.\n";
			echo "	</td>\n";
			echo "</table>\n";
		}

		function topic_permalink($lnk, $topic_id) {
			global $post, $user_ID;
			$forum_id = $post->post_parent;
			$forum = new fr_Forum($forum_id);
			list($ismember) = get_user_meta($user_ID, $forum->forum_slug."-member", '');
			if ($ismember) echo "$lnk"; else echo "";
		}

		function forum_title($ttitle) {
			global $post, $user_ID, $adminurl;

			$forum_id = $post->ID;
			$forum = new fr_Forum($forum_id);
			$forum->read();
			if ($user_ID) {
				list($ismember) = get_user_meta($user_ID, $forum->forum_slug."-member", '');
			} else {
				$ismember = 0;
			}

			if ($forum->restricted) {
				if ($forum->isForumNonmemberInvisible && !$ismember) return "";
				if ($forum->userRequestable) {
					$apply_link = " <a href=$adminurl/users.php?page=forum_application&forum_id=$forum_id>Apply</a>\n";
					if ($ismember) return "$ttitle"; else return "</a><em>You are not a member of $ttitle$apply_link.</em><a>";
				} else {
					return "$ttitle";
				}
			} else {
				return "$ttitle";
			}
		}

		function forum_permalink($lnk) {
			global $post, $user_ID;
			$forum_id = $post->post_parent;
			$forum = new fr_Forum($forum_id);
			list($ismember) = get_user_meta($user_ID, $forum->forum_slug."-member", '');
			if ($ismember) echo "$lnk"; else echo "";
		}

		function topic_title($ttitle) {
			global $post, $user_ID;
			$forum_id = $post->post_parent;
			$forum = new fr_Forum($forum_id);
			$forum->read();
			if ($forum->restricted) {
				list($ismember) = get_user_meta($user_ID, $forum->forum_slug."-member", '');
				if ($ismember) echo "$ttitle"; else echo "</a><em>Topic not available</em><a>";
			} else echo $ttitle;
		}

		function member_search_form($current_forum_id) {
			global $adminurl;

			$this->frout("		<center>");
			echo "		<form method='post' action='$adminurl/users.php?page=forum_restrict' name='fr'>\n";
			echo "		<input type='hidden' name='forum_id' value='$current_forum_id' />\n";
			echo "		<p><input name='user_search_term' /><input type='submit' name='fr_action' value='Potential Member Search' /></p>\n";
			echo "		</form>\n";
			echo "		</center>\n";
		}

		function set_working_forum($forum_id) {
			global $wpdb;
			if ($forum_id) {
				$this->working_forum[forum_id] = $forum_id;
				$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID = $forum_id", OBJECT);
				if ($row) {
					$this->working_forum[forum_name] = $row->post_title;
					$this->working_forum[metaslug] = "{$row->post_name}-member";
				}
				echo "		<FORUMNAME>$row->post_title</FORUMNAME>\n";
			}
		}

		function list_applications() {
			global $wpdb;
			$forum = new fr_Forum($_REQUEST['forum_id']);
			$applications = $wpdb->get_col("SELECT user_id FROM {$wpdb->prefix}usermeta WHERE meta_key = '$forum->forum_slug-application-reason'");
			foreach($applications as $u_id) {
				list($uname) = $wpdb->get_col("SELECT display_name FROM {$wpdb->prefix}users WHERE ID = $u_id");
				list($reason) = get_user_meta($u_id, $forum->forum_slug.'-application-reason');
				list($app_status) = get_user_meta($u_id, $forum->forum_slug.'-application-status');
				if ($app_status == "") $app_status = "Pending";
				if ($app_status == "Pending" || $app_status == "Denied") { // Don't show banned
					echo "		<APPLICATION user_name='$uname' appstatus='$app_status' user_id='$u_id'>$reason</APPLICATION>\n";
				}
			}
		}

		function list_forum_members() {
			global $wpdb;
			$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID = $_REQUEST[forum_id]", OBJECT);
			if ($row) {
//				$this->member_search_form($_REQUEST['forum_id']);
				$slug = $this->working_forum[metaslug];
				$sql = "SELECT user_id FROM {$wpdb->prefix}usermeta m, {$wpdb->prefix}users u WHERE m.user_id = u.ID AND meta_key = '$slug' AND meta_value = 'true'";
				$user_search_term = trim($_REQUEST['user_search_term']);
				if ($user_search_term != "") {
					
					$sql .= " AND (
						lower(user_login) LIKE '%$user_search_term%' OR
						lower(user_nicename) LIKE '%$user_search_term%' OR
						lower(user_email) LIKE '%$user_search_term%' OR
						lower(display_name) LIKE '%$user_search_term%'
					)";
				}
				$sql .= " LIMIT 0,50";
				$members = $wpdb->get_col($sql);
				foreach($members as $u) {
					$user = get_userdata($u);
					$this->frout("		<USER id='$user->ID'>");
					$this->frout("			<RESULT>$user->user_login</RESULT>");
					$this->frout("			<RESULT>$user->user_nicename</RESULT>");
					$this->frout("			<RESULT>$user->user_email</RESULT>");
					$this->frout("			<RESULT>$user->display_name</RESULT>");
					$this->frout("		</USER>");
				}
			}
//			return $sql; // return this for debug, otherwise don't return anything
		}

		function user_search() {
			global $wpdb;
			$this->forum_search_results($this->working_forum[forum_id], $_REQUEST['user_search_term']);
		}

		function forum_search_results($forum_id, $user_search_term) {
			global $wpdb;

			$sql = "SELECT DISTINCT ID FROM {$wpdb->prefix}users
				WHERE
					lower(user_login) LIKE '%$user_search_term%' OR
					lower(user_nicename) LIKE '%$user_search_term%' OR
					lower(user_email) LIKE '%$user_search_term%' OR
					lower(display_name) LIKE '%$user_search_term%'
				LIMIT 0,30";
			$potential_users = $wpdb->get_col($sql);
			$slug = $this->working_forum[metaslug];
			$current_members = $wpdb->get_col("SELECT DISTINCT user_id FROM {$wpdb->prefix}usermeta WHERE meta_key = '$slug' AND meta_value = 'true'");
			$true_potential = array_diff($potential_users, $current_members);
			foreach($true_potential as $potential) {
				$user = get_userdata($potential);
				$this->frout("		<USER id='$potential'>");
				$this->frout("			<RESULT>$user->user_login</RESULT>");
				$this->frout("			<RESULT>$user->user_nicename</RESULT>");
				$this->frout("			<RESULT>$user->user_email</RESULT>");
				$this->frout("			<RESULT>$user->display_name</RESULT>");
				$this->frout("		</USER>");
			}
		}	

		function make_message($status, $values) {
			$basemessage = $this->messages[$status];
			foreach($values as $tag => $value) {
				$pattern = "/\[$tag\]/";
				$basemessage = preg_replace( $pattern, $value, $basemessage);
			}
			return $basemessage; // Check for null before sending mail
		}

		function process_ajax_requests() {
			global $current_site;

                        $site_name = $current_site->site_name;
			// This next block is done here because the following block will create XML code and it would otherewise
			// be outside of the root element
			if (isset($_REQUEST['forum_id'])) $fid = $_REQUEST['forum_id']; else $fid = 0;
			$forum = new fr_Forum($fid);
			$forum->read();
			$slug = $forum->forum_slug."-member";

			if (isset($_REQUEST['ajax_fr_action'])) {
				$this->frout("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");
				$this->frout("<AJ>");
			}
			if (isset($_REQUEST['user_id'])) {
				$us = get_userdata($_REQUEST['user_id']);
				$user_name_list = ": $us->user_login AKA $us->user_nicename AKA $us->display_name";
			} else $user_name_list = "";
			if (isset($_REQUEST['fr_action']))
			switch($_REQUEST['fr_action']) {
				case "approval_actions":
					if ($_REQUEST['user_id'] && $_REQUEST['forum_id']) {
						$user_id = $_REQUEST['user_id'];
						$userdata = get_userdata($user_id);
						$eaddress = $userdata->user_email;
						switch($_REQUEST['subaction']) {
							case "deny":
								// wp_mail(address, subject, message);
								$act = "Denial";
								list($reapp) = get_user_meta($_REQUEST['user_id'], $forum->forum_slug."-reapplication-attempts");
								if ($reapp == "") $reapp = 0;
								$reapp++;
								$result = update_user_meta($_REQUEST['user_id'], $forum->forum_slug."-application-status", "Denied");
								$result = $result | update_user_meta($_REQUEST['user_id'], $forum->forum_slug."-reapplication-attempts", "$reapp");
								if ($result) {
									$message = $this->make_message("denied",
										array(
											"forum_name" => $forum->forum_name,
											"site_name" => $site_name,
											"num_attempts" => "$reapp"
										)
									);
									if ($message)
										wp_mail($userdata->user_email, "Your Request Has Been Denied", $message);
								}
								break;
							case "approve":
								$act = "Approval";
								$result = update_user_meta($_REQUEST['user_id'], $forum->forum_slug."-member", "true");
								if ($result) {
									$message = $this->make_message("approved",
										array(
											"forum_name" => $forum->forum_name,
											"site_name" => $site_name
										)
									);
									if ($message)
										wp_mail($userdata->user_email, "Your Request Has Been Approved", $message);
									delete_user_meta($_REQUEST['user_id'], $forum->forum_slug."-application-status");
									delete_user_meta($_REQUEST['user_id'], $forum->forum_slug."-application-reason");
									delete_user_meta($_REQUEST['user_id'], $forum->forum_slug."-reapplication-attempts");
								}
								break;
							case "reconsider":
								// wp_mail(address, subject, message);
								$act = "Reconsideration";
								$result = update_user_meta($_REQUEST['user_id'], $forum->forum_slug."-application-status", "Pending");
								if ($result) {
									$message = $this->make_message("reconsidered",
										array(
											"forum_name" => $forum->forum_name,
											"site_name" => $site_name
										)
									);
									if ($message)
										wp_mail($userdata->user_email, "Your Request Has Been Reconsidered", $message);
								}
								break;
							case "ban":
								$act = "Ban";
								$result = update_user_meta($_REQUEST['user_id'], $forum->forum_slug."-application-status", "Banned");
								break;
							default:
								break;
						}
					}
					if ($result) {
						echo "	<INFO>\n";
						echo "	 	$act succeeded\n";
						echo "	</INFO>\n";
					} else {
						echo "	<INFO>\n";
						echo "	 	$act failed\n";
						echo "	</INFO>\n";
					}
					break;
				case "add_member":
					if ($_REQUEST['forum_id']) $this->set_working_forum($_REQUEST['forum_id']);
					$result = add_user_meta($_REQUEST['user_id'], $slug , 'true');
					if ($result) {
						echo "	<INFO>\n";
						echo "		Added member$user_name_list.";
						echo "	</INFO>\n";
					} else {
						echo "	<INFO>Failed to add member$user_name_list.</INFO>\n";
					}
					break;
				case "remove_member":
					if ($_REQUEST['forum_id']) $this->set_working_forum($_REQUEST['forum_id']);
					$dresult = delete_user_meta($_REQUEST['user_id'], $slug);
					$result = update_user_meta($_REQUEST['user_id'], $forum->forum_slug."-application-status", "Pending");
					if ($dresult) {
						echo "	<INFO>\n";
						echo "		Deleted member$user_name_list.";
						echo "	</INFO>\n";
					} else {
						echo "	<INFO>Failed to delete member$user_name_list.</INFO>\n";
					}
					break;
				default:
					break;
			}

			if (isset($_REQUEST['ajax_fr_action'])) {
				$user_search_term = preg_replace("/[']/", "", trim($_REQUEST['user_search_term']));
				switch($_REQUEST['ajax_fr_action']) {
					case "list_forum_members":
						$this->frout("	<PAGEDIV divname='Forum_Title'>");
						$this->set_working_forum($_REQUEST['forum_id']);
						$this->frout("	</PAGEDIV>");
						$fid = $_REQUEST['forum_id'];
						$this->frout("	<PAGEDIV divname='Member_Approvals' forum_id='$fid'>");
						$this->list_applications();
						$this->frout("	</PAGEDIV>");
						if ($this->working_forum[forum_id]) {
							$this->frout("	<PAGEDIV divname='Search_Field'>$this->search_term</PAGEDIV>");
							if ($this->search_term) {
								$this->frout("	<PAGEDIV divname='Search_Results' header='Non-Members' checkname='add_member' search_term='$user_search_term'>");
								$this->forum_search_results($this->working_forum->forum_id, $this->search_term);
								$this->frout("	</PAGEDIV>");
							}
						}
						$this->frout("	<PAGEDIV divname='Member_List' header='Members' checkname='remove_member' search_term='$user_search_term'>");
						$retval = $this->list_forum_members();
						$this->frout("	</PAGEDIV>");
						if ($retval) echo "	<INFO>$retval</INFO>\n";
					break;
				}
				$this->frout("</AJ>");
				exit;  // Exit so that the entire page doesn't end up in individual divs
			}
		}

		function save_forum($postID) {
			$forum = new fr_Forum($postID);
			$forum->read();
			$result = update_post_meta($postID, "isForumRestricted", $_REQUEST['isForumRestricted']);
			$result = update_post_meta($postID, "userRequestable", $_REQUEST['isForumUserRequestable']);
			$result = update_post_meta($postID, "isForumNonmemberInvisible", $_REQUEST['isForumNonmemberInvisible']);
		}

       	}
}



//Create new instance of class
if (class_exists("Forum_Restrict")) {
	$forum_restrict = new Forum_Restrict();
	$forum_restrict->process_ajax_requests();
}

//Actions and Filters
if (isset($forum_restrict)) {
	add_action('admin_head', 'forum_restrict_css');
	add_action('admin_head', array($forum_restrict, 'forum_prelim'));
	add_action('admin_menu',       array($forum_restrict,'ConfigureMenu'));
	add_action('save_post', array($forum_restrict,'save_forum'));
	add_action('bbp_forum_metabox', array($forum_restrict,'metabox'));
	add_filter('bbp_get_topic_title', array($forum_restrict,'topic_title'));
	add_filter('bbp_topic_permalink', array($forum_restrict,'topic_permalink'), 10, 2);
	add_filter('bbp_get_forum_title', array($forum_restrict,'forum_title'));
	add_filter('bbp_forum_permalink', array($forum_restrict,'forum_permalink'), 10, 1);
}

?>
