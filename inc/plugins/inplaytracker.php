<?php

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook("newthread_start", "inplaytracker_newthread");
$plugins->add_hook("newthread_do_newthread_end", "inplaytracker_do_newthread");
$plugins->add_hook("editpost_end", "inplaytracker_editpost");
$plugins->add_hook("editpost_do_editpost_end", "inplaytracker_do_editpost");
$plugins->add_hook("newreply_do_newreply_end", "inplaytracker_do_newreply");
$plugins->add_hook("forumdisplay_thread_end", "inplaytracker_forumdisplay");
$plugins->add_hook("misc_start", "inplaytracker_misc");
$plugins->add_hook("global_intermediate", "inplaytracker_global");
$plugins->add_hook("member_profile_end", "inplaytracker_profile");
$plugins->add_hook("usercp_do_options_end", "inplaytracker_usercp_options");
$plugins->add_hook("usercp_options_start", "inplaytracker_usercp");


function inplaytracker_info()
{
	return array(
		"name"			=> "Inplaytracker",
		"description"	=> "Eintragen von Postpartnern in eine Szene, PN-Benachrichtigung bei neuen Szenen/Posts & Komplette Übersicht offener Szenen.",
		"website"		=> "http://www.storming-gates.de",
		"author"		=> "sparks fly",
		"authorsite"	=> "http://www.storming-gates.de",
		"version"		=> "1.0",
		"compatibility" => "*"
	);
}

function inplaytracker_install()
{
  global $db, $mybb;

  // Tabellen erstellen
  $db->query("ALTER TABLE `".TABLE_PREFIX."threads` ADD `partners` VARCHAR(1155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `attachmentcount`;");
  $db->query("ALTER TABLE `".TABLE_PREFIX."posts` ADD `partners` VARCHAR(1155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `visible`;");
  $db->query("ALTER TABLE `".TABLE_PREFIX."threads` ADD `ipdate` VARCHAR(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `partners`;");
  $db->query("ALTER TABLE `".TABLE_PREFIX."posts` ADD `ipdate` VARCHAR(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `partners`;");
	$db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `inplaytrackerpm` int(11) NOT NULL DEFAULT '1';");

  // Einstellungen
  $setting_group = array(
      'name' => 'inplaytracker',
      'title' => 'Inplaytracker',
      'description' => 'Einstellungen für das Inplaytracker-Plugin',
      'disporder' => 5, // The order your setting group will display
      'isdefault' => 0
  );

  $gid = $db->insert_query("settinggroups", $setting_group);

  $setting_array = array(
      'inplaytracker_forum' => array(
          'title' => 'Inplay-Kategorie',
          'description' => 'Gib die ID deiner Inplay-Kategorie an.',
          'optionscode' => 'text',
          'value' => '998', // Default
          'disporder' => 1
      ),
      'inplaytracker_archiv' => array(
          'title' => 'Archiv-Forum',
          'description' => 'Gib die ID deines Inplay-Archivs an.',
          'optionscode' => 'text',
          'value' => '999', // Default
          'disporder' => 2
      ),
  );

  foreach($setting_array as $name => $setting)
  {
      $setting['name'] = $name;
      $setting['gid'] = $gid;

      $db->insert_query('settings', $setting);

  }

  $insert_array = array(
		'title'		=> 'newthread_ip_partners',
		'template'	=> $db->escape_string('<tr>
<td class="trow1" width="20%"><strong>Postpartner:</strong></td>
<td class="trow1"><span class="smalltext">Trenne deine Postpartner mit <strong>", "</strong> voneinander und füge deinen eigenen Namen <i>nicht</i> mit ein!<br /> <input type="text" class="textbox" name="partners" size="40" maxlength="1155" value="{$partners}" /> <br /> Für optimale Funktionsweise, fügst du bei mehreren Postpartnern die Namen in der entsprechenden Postreihenfolge ein.</span> </td>
</tr>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

  $insert_array = array(
		'title'		=> 'newthread_ip_date',
		'template'	=> $db->escape_string('<tr>
<td class="trow1" width="20%"><strong>Inplaydatum:</strong></td>
<td class="trow1"><span class="smalltext"><input type="text" class="textbox" name="ipdate" size="40" maxlength="155" value="{$ipdate}" /> <br /> Die Angabe des Inplaydatums muss die <i>volle</i> Angabe des Spielmonats ausgeben (deutsch)!</span> </td>
</tr>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

  $insert_array = array(
		'title'		=> 'misc_inplaytracker',
		'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Inplayszenen</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Inplayszenen</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
<h1><i>{$countgesamt}</i> Szenen insgesamt, <i>{$opengesamt}</i> davon offen!</h1>
{$scenes_user}
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

  $insert_array = array(
		'title'		=> 'misc_ip_user',
		'template'	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="5" class="tborder smalltext" style="width: 85%; margin: 10px;">
	<tr>
		<td class="tcat" colspan="3">
			Szenen von {$username} (insgesamt $countscenes Szenen, an der Reihe in $countactive Szenen)
		</td>
	</tr>
	<tr class="trow2">
	<td width="20%">
		<strong>Nächster Post</strong>
	</td>
	<td>
		<strong>Szenen-Informationen</strong>
	</td>
	<td style="width: 25%">
		<strong>Letzter Post</strong>
	</td>
</tr>
{$scenes_bit}
</table>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

  $insert_array = array(
		'title'		=> 'misc_ip_bit',
		'template'	=> $db->escape_string('<tr class="trow1">
	<td width="20%">
		{$status}
	</td>
	<td>
		<strong>Charaktere:</strong> {$szene[\'partners\']}<br />
		<strong>Inplaydatum:</strong> {$szene[\'ipdate\']}
	</td>
	<td style="width: 25%">
	<a href="showthread.php?tid={$szene[\'tid\']}&action=lastpost" target="blank">{$szene[\'subject\']}</a><br />
	{$szene[\'lastpost\']}<br />
	{$szene[\'lastposter\']}
	</td>
</tr>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

  $insert_array = array(
		'title'		=> 'header_inplaytracker',
		'template'	=> $db->escape_string('<li><a href="{$mybb->settings[\'bburl\']}/misc.php?action=scenes" class="search">Inplayszenen ({$opengesamt}/{$countgesamt})</a></li>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

  $insert_array = array(
		'title'		=> 'member_profile_inplaytracker',
		'template'	=> $db->escape_string('<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="5px" class="tborder tfixed">
<tr>
<td class="thead"><strong>Inplayszenen ({$inplayposts} Beiträge)</strong></td>
</tr>
<tr>
<td class="trow1">
	<div style="max-height: 200px; overflow: auto;">
		<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="5px" class="tborder tfixed">
{$inplaytracker_bit}
		</table>
	</div>
</td>
</table>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

  $insert_array = array(
		'title'		=> 'member_profile_inplaytracker_bit',
		'template'	=> $db->escape_string('<tr class="trow1">
	<td><a href="showthread.php?tid=$szenen[tid]" target="blank">$szenen[subject]</a><br />
		<span class="smalltext">{$szenen[\'ipdate\']}<br /><i>Mitspieler</i>: {$szenen[\'partners\']}</span>
	</td>
</tr>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'usercp_options_inplaytrackerpm',
		'template'	=> $db->escape_string('<tr>
	<td valign="top" width="1"><input type="checkbox" class="checkbox" name="inplaytrackerpm" id="inplaytrackerpm" value="1" {$inplaytrackerpmcheck} /></td>
	<td><span class="smalltext"><label for="inplaytrackerpm">PN-Benachrichtigung bei neuen Szenen/Antworten?</label></span></td>
	</tr>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);

	$db->insert_query("templates", $insert_array);

  rebuild_settings();

}

function inplaytracker_is_installed()
{
  global $db;
  if($db->field_exists("partners", "threads"))
  {
      return true;
  }
  return false;
}

function inplaytracker_uninstall()
{
  global $db;

  // Tabellen entfernen
	if($db->field_exists("partners", "threads"))
  {
    $db->drop_column("threads", "partners");
  }
  if($db->field_exists("partners", "posts"))
  {
    $db->drop_column("posts", "partners");
  }
  if($db->field_exists("ipdate", "threads"))
  {
    $db->drop_column("threads", "ipdate");
  }
  if($db->field_exists("ipdate", "posts"))
  {
    $db->drop_column("posts", "ipdate");
  }
	if($db->field_exists("inplaytrackerpm", "users"))
	{
		$db->drop_column("users", "inplaytrackerpm");
	}

  // Einstellungen entfernen
  $db->delete_query('settings', "name IN ('inplaytracker_forum', 'inplaytracker_archiv')");
  $db->delete_query('settinggroups', "name = 'inplaytracker'");

  rebuild_settings();

  // Templates entfernen
  $db->delete_query("templates", "title IN('newthread_ip_partners', 'newthread_ip_date', 'misc_inplaytracker', 'misc_ip_user', 'misc_ip_bit', 'header_inplaytracker', 'member_profile_inplaytracker', 'member_profile_inplaytracker_bit', 'usercp_options_inplaytrackerpm')");

}

function inplaytracker_activate()
{
  global $db, $mybb;

	// PM-Feld einfügen
	if(!$db->field_exists("inplaytrackerpm", "users"))
  {
    $db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `inplaytrackerpm` int(11) NOT NULL;");
  }

  // Variablen einfügen
  include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("newthread", "#".preg_quote('{$loginbox}')."#i", '{$loginbox} {$tracker_partners} {$tracker_date}');
	find_replace_templatesets("editpost", "#".preg_quote('{$loginbox}')."#i", '{$loginbox} {$tracker_partners} {$tracker_date}');
	find_replace_templatesets("header", "#".preg_quote('{$menu_calendar}')."#i", '{$menu_calendar} {$menu_inplaytracker} {$tracker_date}');
	find_replace_templatesets("member_profile", "#".preg_quote('</fieldset>')."#i", '</fieldset> {$inplaytracker}');
	find_replace_templatesets("usercp_options", "#".preg_quote('{$board_style}')."#i", '{$inplaytrackerpm}{$board_style}');
}

function inplaytracker_deactivate()
{
  global $db, $mybb;

  // Variablen entfernen
  include MYBB_ROOT."/inc/adminfunctions_templates.php";
  find_replace_templatesets("newthread", "#".preg_quote('{$tracker_partners} {$tracker_date}')."#i", '', 0);
  find_replace_templatesets("editpost", "#".preg_quote('{$tracker_partners} {$tracker_date}')."#i", '', 0);
  find_replace_templatesets("header", "#".preg_quote('{$menu_inplaytracker}')."#i", '', 0);
  find_replace_templatesets("member_profile", "#".preg_quote('{$inplaytracker}')."#i", '', 0);
	find_replace_templatesets("usercp_options", "#".preg_quote('{$inplaytrackerpm}')."#i", '', 0);
}

function inplaytracker_newthread()
{
  global $mybb, $templates, $post_errors, $forum, $thread, $tracker_partners, $tracker_date;

  $inplaykategorie = $mybb->settings['inplaytracker_forum'];

	$forum['parentlist'] = ",".$forum['parentlist'].",";
	if(preg_match("/,$inplaykategorie,/i", $forum['parentlist'])) {
	 if(isset($mybb->input['previewpost']) || $post_errors)
	 {
		  $partners = htmlspecialchars_uni($mybb->get_input('partners'));
		  $ipdate = htmlspecialchars_uni($mybb->get_input('ipdate'));
	 }
	 else
	 {
		  $partners = htmlspecialchars_uni($thread['partners']);
		  $ipdate = htmlspecialchars_uni($thread['ipdate']);
	 }
	 eval("\$tracker_partners = \"".$templates->get("newthread_ip_partners")."\";");
	 eval("\$tracker_date = \"".$templates->get("newthread_ip_date")."\";");
  }
}

function inplaytracker_do_newthread()
{
	global $db, $mybb, $tid, $pmhandler, $pm, $pminfo, $forum;

  $username = $mybb->user['username'];
  $ownuid = $mybb->user['uid'];
	$partners = $mybb->get_input('partners');
	$inplaykategorie = $mybb->settings['inplaytracker_forum'];

	$forum['parentlist'] = ",".$forum['parentlist'].",";
	if(preg_match("/,$inplaykategorie,/i", $forum['parentlist'])) {

	$new_record = array(
		"partners" => $username.", ".$db->escape_string($mybb->get_input('partners')),
    "ipdate" => $db->escape_string($mybb->input['ipdate'])
	);
	$db->update_query("threads", $new_record, "tid='{$tid}'");

  $tags = explode(", ", $partners);
	foreach($tags as $tag) {
		$partneruid = $db->fetch_field($db->query("SELECT uid FROM ".TABLE_PREFIX."users WHERE username = '$tag'"), "uid");
		$subject = "Neue Szene eröffnet!";
    $message = "Hi! Ich habe dir soeben eine neue Szene eröffnet! Du findest sie <a href=\"showthread.php?tid={$tid}\">hier</a>!";
    $fromid = $ownuid;

    require_once MYBB_ROOT . "inc/datahandlers/pm.php";
    $pmhandler = new PMDataHandler();

    $pm = array(
        "subject" => $subject,
        "message" => $message,
        "fromid" => $fromid,
        "toid" => $partneruid
    );

		$pmcheck = $db->fetch_field($db->query("SELECT inplaytrackerpm FROM ".TABLE_PREFIX."users where uid = '$partneruid'"), "inplaytrackerpm");

			if($pmcheck == "1") {

    $pmhandler->set_data($pm);

    // Now let the pm handler do all the hard work.
    if (!$pmhandler->validate_pm()) {
        $pm_errors = $pmhandler->get_friendly_errors();
    }

    else {
        $pminfo = $pmhandler->insert_pm();
		}

	}

	}

}

}

function inplaytracker_editpost()
{
  global $mybb, $templates, $post_errors, $forum, $thread, $pid, $tracker_partners, $tracker_date;

  $inplaykategorie = $mybb->settings['inplaytracker_forum'];

	$forum['parentlist'] = ",".$forum['parentlist'].",";
	if(preg_match("/,$inplaykategorie,/i", $forum['parentlist'])) {
    $pid = $mybb->get_input('pid', MyBB::INPUT_INT);
  	if($thread['firstpost'] == $pid)
    {
	   if(isset($mybb->input['previewpost']) || $post_errors)
	   {
		    $partners = htmlspecialchars_uni($mybb->get_input('partners'));
        $ipdate = htmlspecialchars_uni($mybb->get_input('ipdate'));
	   }
	   else
	   {
		    $partners = htmlspecialchars_uni($thread['partners']);
		    $ipdate = htmlspecialchars_uni($thread['ipdate']);
	   }
	   eval("\$tracker_partners = \"".$templates->get("newthread_ip_partners")."\";");
 	   eval("\$tracker_date = \"".$templates->get("newthread_ip_date")."\";");
    }
  }
}

function inplaytracker_do_editpost()
{
    global $db, $mybb, $tid, $pid, $thread;

    if ($pid != $thread['firstpost']) return;

    $new_record = array(
        "partners" => $db->escape_string($mybb->input['partners']),
        "ipdate" => $db->escape_string($mybb->input['ipdate'])
    );
    $db->update_query("threads", $new_record, "tid='{$tid}'");
}

function inplaytracker_do_newreply()
{
	global $db, $mybb, $username, $pmhandler, $pm, $pminfo, $thread, $forum;

  $username = $mybb->user['username'];
  $ownuid = $mybb->user['uid'];
	$partners = $thread['partners'];
	$inplaykategorie = $mybb->settings['inplaytracker_forum'];

	$forum['parentlist'] = ",".$forum['parentlist'].",";
	if(preg_match("/,$inplaykategorie,/i", $forum['parentlist'])) {

	$tags = explode(", ", $partners);
	foreach($tags as $tag) {
		$partneruid = $db->fetch_field($db->query("SELECT uid FROM ".TABLE_PREFIX."users WHERE username = '$tag'"), "uid");
		$subject = "Antwort auf $thread[subject]!";
		$message = "Hi! Ich habe so eben eine Antwort auf <a href=\"showthread.php?tid={$thread[tid]}\">unsere Szene</a> veröffentlicht!";
		$fromid = $ownuid;

		require_once MYBB_ROOT . "inc/datahandlers/pm.php";
		$pmhandler = new PMDataHandler();

		$pm = array(
				"subject" => $subject,
				"message" => $message,
				"fromid" => $fromid,
				"toid" => $partneruid
		);

		$pmcheck = $db->fetch_field($db->query("SELECT inplaytrackerpm FROM ".TABLE_PREFIX."users where uid = '$partneruid'"), "inplaytrackerpm");

			if($pmcheck == "1") {

		$pmhandler->set_data($pm);

		// Now let the pm handler do all the hard work.
		if (!$pmhandler->validate_pm()) {
				$pm_errors = $pmhandler->get_friendly_errors();
		}
		else {

				if ($tag != $username) {
				$pminfo = $pmhandler->insert_pm();
				}
		}

	}

}

}

}

function inplaytracker_forumdisplay(&$thread)
{
  global $db, $mybb, $thread, $foruminfo;

  $inplaykategorie = $mybb->settings['inplaytracker_forum'];
  $inplayarchiv = $mybb->settings['inplaytracker_archiv'];

	$foruminfo['parentlist'] = ",".$foruminfo['parentlist'].",";
	if(preg_match("/,$inplaykategorie,/i", $foruminfo['parentlist']) OR preg_match("/,$inplayarchiv,/i", $foruminfo['parentlist']) ) {
  $partners = explode(", ", $thread['partners']);
  $partnerusers = "";
  foreach ($partners as $partner) {
    $uid = $db->fetch_field($db->query("SELECT uid FROM ".TABLE_PREFIX."users WHERE username = '$partner'"), "uid");
    $taguser = build_profile_link($partner, $uid);
    $partnerusers .= " $taguser "."##";
  }
  $thread['profilelink'] =  "<b>Charaktere:</b> $partnerusers <br /> <b>Inplaydatum:</b> $thread[ipdate]";
  return $thread;
  }
}

function inplaytracker_misc()
{
  global $mybb, $db, $templates, $headerinclude, $header, $footer, $scenes_bit, $scenes_user;

  $mybb->input['action'] = $mybb->get_input('action');

  if($mybb->input['action'] == "scenes") {

    $ipforum = $mybb->settings['inplaytracker_forum'];
    $email = $mybb->user['email'];

		$query = $db->query("SELECT username FROM ".TABLE_PREFIX."users WHERE ".TABLE_PREFIX."users.email = '$email' ORDER By ".TABLE_PREFIX."users.username ASC");

		$countgesamt = 0;
		$opengesamt = 0;

		while($user = $db->fetch_array($query)) {

			$username = $user['username'];

			$query1 = $db->query("SELECT *, ".TABLE_PREFIX."threads.lastpost, ".TABLE_PREFIX."threads.partners, ".TABLE_PREFIX."threads.subject, ".TABLE_PREFIX."threads.lastposter, ".TABLE_PREFIX."threads.ipdate, ".TABLE_PREFIX."threads.lastposteruid FROM ".TABLE_PREFIX."threads
			LEFT JOIN ".TABLE_PREFIX."posts ON ".TABLE_PREFIX."threads.lastpost = ".TABLE_PREFIX."posts.dateline
      LEFT JOIN ".TABLE_PREFIX."forums ON ".TABLE_PREFIX."threads.fid = ".TABLE_PREFIX."forums.fid
			WHERE ".TABLE_PREFIX."threads.partners LIKE '%$username%'
      AND ".TABLE_PREFIX."forums.parentlist LIKE '$ipforum,%'
			ORDER by ".TABLE_PREFIX."threads.lastpost DESC");

					$scenes_bit = "";
					$countscenes = 0;
					$countactive = 0;

			while($szene = $db->fetch_array($query1)) {

				$countgesamt++;

				$tagged = explode(", ", $szene['partners']);
  			$szene['lastpost'] = my_date('relative', $szene['lastpost']);

				if(my_strlen($szene['subject']) > 35) {
			    $szene['subject'] = my_substr($szene['subject'], 0, 35)."...";
			  }

				$key = array_search($szene['lastposter'], $tagged);
	      $key = $key + 1;
	      $next = $tagged[$key];

	      if(!$tagged[$key]) {
	        $next = $tagged[0];
	      }

				if($next == $username) {
          $status = "<center><span style=\"text-transform: uppercase; font-size: 12px; font-weight: bold; color: #66B84B\">DU BIST DRAN!</span></center>";
				  $countactive++;
				  $opengesamt++;
				}

        else {
          $status = "<center><span style=\"text-transform: uppercase; font-size: 12px; font-weight: bold; color: #B8664B\">$next</span></center>";
        }

				$countscenes++;

				$szene['lastposter'] = build_profile_link($szene['lastposter'],$szene['lastposteruid']);

				eval("\$scenes_bit .= \"".$templates->get("misc_ip_bit")."\";");

			}

		eval("\$scenes_user .= \"".$templates->get("misc_ip_user")."\";");

		}

    eval("\$page = \"".$templates->get("misc_inplaytracker")."\";");
    output_page($page);
  }

}

function inplaytracker_global()
{
  global $mybb, $db, $templates, $menu_inplaytracker;

  $ipforum = $mybb->settings['inplaytracker_forum'];
  $email = $mybb->user['email'];

  $query = $db->query("SELECT username FROM ".TABLE_PREFIX."users WHERE ".TABLE_PREFIX."users.email = '$email' ORDER By ".TABLE_PREFIX."users.username ASC");

  $countgesamt = 0;
  $opengesamt = 0;

  while($user = $db->fetch_array($query)) {

    $username = $user['username'];

    $query1 = $db->query("SELECT *, ".TABLE_PREFIX."threads.partners, ".TABLE_PREFIX."threads.lastposter FROM ".TABLE_PREFIX."threads
    LEFT JOIN ".TABLE_PREFIX."posts ON ".TABLE_PREFIX."threads.lastpost = ".TABLE_PREFIX."posts.dateline
    LEFT JOIN ".TABLE_PREFIX."forums ON ".TABLE_PREFIX."threads.fid = ".TABLE_PREFIX."forums.fid
    WHERE ".TABLE_PREFIX."threads.partners LIKE '%$username%'
    AND ".TABLE_PREFIX."forums.parentlist LIKE '$ipforum,%'");

    while($szene = $db->fetch_array($query1)) {

      $countgesamt++;

      $tagged = explode(", ", $szene['partners']);

      $key = array_search($szene['lastposter'], $tagged);
      $key = $key + 1;
      $next = $tagged[$key];

      if(!$tagged[$key]) {
        $next = $tagged[0];
      }

      if($next == $username) {
        $opengesamt++;
      }

      $countscenes++;

    }

  }

	eval("\$menu_inplaytracker = \"".$templates->get("header_inplaytracker")."\";");
}

function inplaytracker_profile() {
  global $db, $mybb, $templates, $memprofile, $inplaytracker, $inplaytracker_bit;

  $ipforum = $mybb->settings['inplaytracker_forum'];
  $archiv = $mybb->settings['inplaytracker_archiv'];

  $inplayposts = $db->fetch_field($db->query("SELECT COUNT(*) AS inplayposts FROM ".TABLE_PREFIX."posts
	LEFT JOIN ".TABLE_PREFIX."threads on ".TABLE_PREFIX."threads.tid = ".TABLE_PREFIX."posts.tid
	LEFT JOIN ".TABLE_PREFIX."forums on ".TABLE_PREFIX."forums.fid = ".TABLE_PREFIX."threads.fid
	WHERE ".TABLE_PREFIX."posts.username = '$memprofile[username]'
  AND (".TABLE_PREFIX."forums.parentlist LIKE '$ipforum,%'
  OR ".TABLE_PREFIX."forums.parentlist LIKE '%,$archiv%')"), "inplayposts");

  if($inplayposts != "0") {

	$monate = array(
	"januar" => "januar",
	"februar" => "februar",
	"märz" => "märz",
	"april" => "april",
	"mai" => "mai",
	"juni" => "juni",
	"juli" => "juli",
	"august" => "august",
	"september" => "september",
	"oktober" => "oktober",
	"november" => "november",
	"dezember" => "dezember"
);
foreach($monate as $monat) {
$query = $db->query("SELECT * , ".TABLE_PREFIX."threads.partners, ".TABLE_PREFIX."threads.ipdate, ".TABLE_PREFIX."threads.subject FROM ".TABLE_PREFIX."threads
LEFT JOIN ".TABLE_PREFIX."posts ON ".TABLE_PREFIX."threads.tid = ".TABLE_PREFIX."posts.tid
LEFT JOIN ".TABLE_PREFIX."forums ON ".TABLE_PREFIX."forums.fid = ".TABLE_PREFIX."threads.fid
WHERE (".TABLE_PREFIX."forums.parentlist LIKE '%$ipforum%' OR ".TABLE_PREFIX."forums.parentlist LIKE '%,$archiv%')
AND ".TABLE_PREFIX."threads.ipdate LIKE '%$monat%'
AND ".TABLE_PREFIX."threads.partners LIKE '%$memprofile[username]%'
GROUP by ".TABLE_PREFIX."threads.tid
ORDER by ".TABLE_PREFIX."threads.ipdate
");

	while($szenen = $db->fetch_array($query)) {
    eval("\$inplaytracker_bit .= \"".$templates->get("member_profile_inplaytracker_bit")."\";");
	}
}

	eval("\$inplaytracker = \"".$templates->get("member_profile_inplaytracker")."\";");

}

}

function inplaytracker_usercp() {

	global $mybb, $user, $templates, $inplaytrackerpmcheck, $inplaytrackerpm;

	if(isset($mybb->user['inplaytrackerpm']) && $mybb->user['inplaytrackerpm'] == 1)
	{
		$inplaytrackerpmcheck = "checked=\"checked\"";
	}
	else
	{
		$inplaytrackerpmcheck = "";
	}

	eval("\$inplaytrackerpm = \"".$templates->get("usercp_options_inplaytrackerpm")."\";");

}

function inplaytracker_usercp_options()
{
	global $mybb, $db;

	$uid = $mybb->user['uid'];

	$new_record = array(
		"inplaytrackerpm" => $mybb->get_input('inplaytrackerpm', MyBB::INPUT_INT)
	);
		$db->update_query("users", $new_record, "uid = '$uid'");

}

?>
