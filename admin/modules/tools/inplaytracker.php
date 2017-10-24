<?php
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item("Inplaytracker - Administration", "index.php?module=tools-inplaytracker");

// Inaktive Szenen seit x Tagen finden
if(!$mybb->input['action'])
{
	// Zeit-Filter erstelen
	$time = TIME_NOW;
	$time = date("d.m.Y", $time);
	$days = $mybb->get_input('days');
	if(empty($days)) {
		$days = 30;
	}

	// Szenen auslesen
	$ipforum = $mybb->settings['inplaytracker_forum'];
	$filter = strtotime("$time - $days days");
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."threads
	LEFT JOIN ".TABLE_PREFIX."forums ON ".TABLE_PREFIX."threads.fid = ".TABLE_PREFIX."forums.fid
	WHERE parentlist LIKE '%$ipforum%'
	AND ".TABLE_PREFIX."threads.lastpost < '$filter'
	ORDER BY ".TABLE_PREFIX."threads.lastpost ASC");

	// Navigation erstellen
	$page->output_header("Inplaytracker Administration");

	$sub_tabs['inplaytracker'] = array(
		'title' => "Inaktive Szenen",
		'link' => "index.php?module=tools-inplaytracker",
		'description' => "Finde Szenen die seit $days Tagen inaktiv sind."
	);

	$sub_tabs['inplaytracker_deleted'] = array(
		'title' => "Szenen mit inaktiven Mitgliedern",
		'link' => "index.php?module=tools-inplaytracker&action=find-deleted",
		'description' => "Finde Szenen mit inaktiven Charakteren."
	);

	$sub_tabs['inplaytracker_update'] = array(
		'title' => "Plugin updaten",
		'link' => "index.php?module=tools-inplaytracker&action=update",
		'description' => "Inplaytracker auf die neueste Version updaten."
	);

	$page->output_nav_tabs($sub_tabs, 'inplaytracker');

	// Formular & Tabelle generieren
	$form = new Form("index.php?module=tools-inplaytracker", "post");
	$form_container = new FormContainer("Inaktive Szenen");

	$form_container->output_row_header("Filter");
	$form_container->output_row_header("Suchen");

	$html_filters = "
		<div align=\"center\">
			<label for='sdate'>Inaktiv seit wie vielen Tagen? </label><br />
			<input type='text' name='days' id='days' value='{$days}' style='text-align:center;'/>
		</div>
	";
	$form_container->output_cell($html_filters);
	$form_container->output_cell("<center>".$form->generate_submit_button("Szenen suchen", array("name" => "filter_scenes"))."</center>");

	$form_container->construct_row();
	$form_container->end();

	// Szenen auflisten
	$form_container = new FormContainer(Szenen);
	$i = 0;
	while($scenes = $db->fetch_array($query)) {
		$parentlist = ",".$scenes['parentlist'].",";
		if(preg_match("/,$ipforum,/i", $parentlist)) {
			$form_container->output_cell("<a href=\"{$mybb->settings['bburl']}/showthread.php?tid={$scenes['tid']}\" target=\"blank\">{$scenes['subject']}</a>");
			$form_container->construct_row();
			$i++;
		}
	}
	// Keine inaktiven Szenen?
	if($i == 0) {
		$form_container->output_cell("Keine inaktiven Szenen gefunden!");
		$form_container->construct_row();
	}
	$form_container->end();
	$form->end();
	$page->output_footer();
}

// Szenen mit inaktiven Charakteren finden
if($mybb->input['action'] == "find-deleted")
{

	// Navigation erstellen
	$page->output_header("Inplaytracker Administration");

	$sub_tabs['inplaytracker'] = array(
		'title' => "Inaktive Szenen",
		'link' => "index.php?module=tools-inplaytracker",
		'description' => "Finde Szenen die seit $days Tagen inaktiv sind."
	);

	$sub_tabs['inplaytracker_deleted'] = array(
		'title' => "Szenen mit inaktiven Mitgliedern",
		'link' => "index.php?module=tools-inplaytracker&action=find-deleted",
		'description' => "Finde Szenen mit inaktiven Charakteren."
	);

	$sub_tabs['inplaytracker_update'] = array(
		'title' => "Plugin updaten",
		'link' => "index.php?module=tools-inplaytracker&action=update",
		'description' => "Inplaytracker auf die neueste Version updaten."
	);

	$page->output_nav_tabs($sub_tabs, 'inplaytracker_deleted');

	// Formular & Tabelle generieren
	$form = new Form("index.php?module=tools-inplaytracker&action=find-deleted", "post");
	$form_container = new FormContainer("Szenen mit inaktiven Mitgliedern");

	$form_container = new FormContainer(Szenen);
	$i = 0;
	// Szenen auslesen
	$ipforum = $mybb->settings['inplaytracker_forum'];
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."threads
	LEFT JOIN ".TABLE_PREFIX."forums ON ".TABLE_PREFIX."threads.fid = ".TABLE_PREFIX."forums.fid
	WHERE parentlist LIKE '%$ipforum%'
	ORDER BY tid ASC");
	// Szenen auflisten
	while($scenes = $db->fetch_array($query)) {
		$parentlist = ",".$scenes['parentlist'].",";
		if(preg_match("/,$ipforum,/i", $parentlist)) {
			$partners = explode(",", $scenes['partners']);
			foreach($partners as $partner) {
				$user = get_user($partner);
				if(empty($user)) {
					$form_container->output_cell("<a href=\"{$mybb->settings['bburl']}/showthread.php?tid={$scenes['tid']}\" target=\"blank\">{$scenes['subject']}</a>");
					$form_container->construct_row();
					$i++;
				}
			}
		}
	}
	// Keine inaktiven Szenen?
	if($i == 0) {
		$form_container->output_cell("Keine Szenen gefunden!");
		$form_container->construct_row();
	}
	$form_container->end();
	$form->end();
	$page->output_footer();
}

// Inplaytracker updaten
if($mybb->input['action'] == "update")
{

	if($mybb->request_method == "post") {
		// Datenbank aktualisieren
		if(isset($mybb->input['update_database'])) {
			if(!$db->field_exists("iport", "threads"))
			{
				$db->query("ALTER TABLE `".TABLE_PREFIX."threads` ADD `iport` VARCHAR(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `ipdate`;");
			}
			if(!$db->field_exists("iport", "posts"))
			{
				$db->query("ALTER TABLE `".TABLE_PREFIX."posts` ADD `iport` VARCHAR(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `ipdate`;");
			}
			if(!$db->field_exists("ipdaytime", "threads"))
			{
				$db->query("ALTER TABLE `".TABLE_PREFIX."threads` ADD `ipdaytime` VARCHAR(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `iport`;");
			}
			if(!$db->field_exists("ipdaytime", "posts"))
			{
				$db->query("ALTER TABLE `".TABLE_PREFIX."posts` ADD `ipdaytime` VARCHAR(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `iport`;");
			}
			if(!$db->field_exists("openscene", "threads"))
			{
				$db->query("ALTER TABLE `".TABLE_PREFIX."threads` ADD `openscene` int(11) NOT NULL DEFAULT '-1';");
			}
			if(!$db->field_exists("openscene", "posts"))
			{
				$db->query("ALTER TABLE `".TABLE_PREFIX."posts` ADD `openscene` int(11) NOT NULL DEFAULT '-1';");
			}
			if(!$db->field_exists("postorder", "threads"))
			{
				$db->query("ALTER TABLE `".TABLE_PREFIX."threads` ADD `postorder` int(11) NOT NULL DEFAULT '1';");
			}
			if(!$db->field_exists("postorder", "posts"))
			{
				$db->query("ALTER TABLE `".TABLE_PREFIX."posts` ADD `postorder` int(11) NOT NULL DEFAULT '1';");
			}
		}

		// ACP Einstellungen aktualisieren
		if(isset($mybb->input['update_settings'])) {
			$gid = $db->fetch_field($db->query("SELECT gid FROM ".TABLE_PREFIX."settinggroups WHERE name = 'inplaytracker'"), "gid");
			$setting_array =
				array(
					'inplaytracker_location' => array(
					'title' => 'Ort-Feld hinzufügen?',
					'description' => 'Sollen User ein Feld ausfüllen können, in dem siie zusätzlich den Spielort angeben können?',
					'optionscode' => 'yesno',
					'value' => 1,
					'disporder' => 3
				),
					'inplaytracker_daytime' => array(
					'title' => 'Tageszeit-Feld hinzufügen?',
					'description' => 'Sollen User ein Feld ausfüllen können, in dem siie zusätzlich die Tageszeit angeben können?',
					'optionscode' => 'yesno',
					'value' => 0,
					'disporder' => 4
				),
					'inplaytracker_timeformat' => array(
					'title' => 'Eigene Zeitrechnung nutzen?',
					'description' => 'Soll das Plugin eine Zeitrechnung abseits vom gregorianischen Kalender nutzen (auch ankreuzen, wenn euer Forum vor 1970 oder nach 2038 spielt!)?',
					'optionscode' => 'yesno',
					'value' => 0,
					'disporder' => 5
				),
					'inplaytracker_months' => array(
						'title' => 'Monatsnamen',
						'description' => '<b>Nur bei eigener Zeitrechnung:</b> Welche Monate sollen in deinem Forum auswählbar sein? Besonders interessant für Fantasy- und/oder Fandomforen!',
						'optionscode' => 'text',
						'value' => 'Januar, Februar, März, April, Mai, Juni, Juli, August, September, Oktober, November, Dezember',
						'disporder' => 6
				),
			);

			foreach($setting_array as $name => $setting)
			{
				$setting['name'] = $name;
				$setting['gid'] = $gid;
				$db->insert_query('settings', $setting);
			}

			rebuild_settings();
		}

		// Datenbankspalte partners aktualisieren
		if(isset($mybb->input['update_partner'])) {
			// Szenen auslesen
			$ipforum = $mybb->settings['inplaytracker_forum'];
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."threads
			WHERE partners != ''");
			while($scenes = $db->fetch_array($query)) {
				$partners = explode(", ", $scenes['partners']);
				$partners_new = array();
				foreach($partners as $partner) {
					$partner = $db->escape_string($partner);
					$user_id = $db->fetch_field($db->query("SELECT uid FROM ".TABLE_PREFIX."users WHERE username = '$partner'"), "uid");
					$partners_new[] = $user_id;
				}
				$partners_new = implode(",", $partners_new);
				$new_record = array(
					"partners" => $partners_new
				);
				$db->update_query("threads", $new_record, "tid = '$scenes[tid]'");
			}
		}

		// Datenbankspalte ipdate aktualisieren
		if(isset($mybb->input['update_date'])) {
			$ipforum = $mybb->settings['inplaytracker_forum'];
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."threads
			WHERE ipdate != ''");
			while($scenes = $db->fetch_array($query)) {
				$ipdate = explode(" ", $scenes['ipdate']);
				$months = array(
					"january" => "Januar",
					"february" => "Februar",
					"march" => "März",
					"april" => "April",
					"may" => "Mai",
					"june" => "Juni",
					"july" => "Juli",
					"august" => "August",
					"september" => "September",
					"october" => "Oktober",
					"november" => "November",
					"december" => "Dezember"
				);
				$ipdate['1'] = array_search($ipdate['1'], $months);
				$ipdate = implode(" ", $ipdate);
				$ipdate = strtotime($ipdate);
				$new_record = array(
					"ipdate" => $ipdate
				);
				$db->update_query("threads", $new_record, "tid = '$scenes[tid]'");
			}
		}

		if(isset($mybb->input['update_alerts'])) {
			if(class_exists('MybbStuff_MyAlerts_AlertTypeManager')) {
				$alertTypeManager = MybbStuff_MyAlerts_AlertTypeManager::getInstance();

				if (!$alertTypeManager) {
					$alertTypeManager = MybbStuff_MyAlerts_AlertTypeManager::createInstance($db, $cache);
				}

				$alertType = new MybbStuff_MyAlerts_Entity_AlertType();
				$alertType->setCode('inplaytracker_newthread'); // The codename for your alert type. Can be any unique string.
				$alertType->setEnabled(true);
				$alertType->setCanBeUserDisabled(true);

				$alertTypeManager->add($alertType);

				$alertType = new MybbStuff_MyAlerts_Entity_AlertType();
				$alertType->setCode('inplaytracker_newreply'); // The codename for your alert type. Can be any unique string.
				$alertType->setEnabled(true);
				$alertType->setCanBeUserDisabled(true);

				$alertTypeManager->add($alertType);
			}
		}
	}

	// Navigation erstellen
	$page->output_header("Inplaytracker Administration");

	$sub_tabs['inplaytracker'] = array(
		'title' => "Inaktive Szenen",
		'link' => "index.php?module=tools-inplaytracker",
		'description' => "Finde Szenen die seit $days Tagen inaktiv sind."
	);

	$sub_tabs['inplaytracker_deleted'] = array(
		'title' => "Szenen mit inaktiven Mitgliedern",
		'link' => "index.php?module=tools-inplaytracker&action=find-deleted",
		'description' => "Finde Szenen mit inaktiven Charakteren."
	);

	$sub_tabs['inplaytracker_update'] = array(
		'title' => "Plugin updaten",
		'link' => "index.php?module=tools-inplaytracker&action=update",
		'description' => "Inplaytracker auf die neueste Version updaten."
	);

	$page->output_nav_tabs($sub_tabs, 'inplaytracker_update');

	// Optionen generieren
	$form = new Form("index.php?module=tools-inplaytracker&action=update", "post");
	$form_container = new FormContainer("Inplaytracker updaten");
	$form_container = new FormContainer("Plugin aktualisieren");
	$form_container->output_row_header("Status");
	$form_container->output_row_header("Update");
	// Datenbank aktualisieren
	$form_container->output_cell("<b>Datenbankmodifizierungen</b>");
	$form_container->output_cell("<center>".$form->generate_submit_button("Datenbank updaten", array("name" => "update_database"))."</center>");
	$form_container->construct_row();
	// Admin CP Settings hinzufügen
	$form_container->output_cell("<b>Admin CP Einstellungen aktualisieren</b>");
	$form_container->output_cell("<center>".$form->generate_submit_button("Einstellungen aktualisieren", array("name" => "update_settings"))."</center>");
	$form_container->construct_row();
	// Datenbankspalte "partners" aktualisieren
	$form_container->output_cell("<b>Charakternamen in User-ID umwandeln</b>");
	$form_container->output_cell("<center>".$form->generate_submit_button("Postingpartner aktualisieren", array("name" => "update_partner"))."</center>");
	$form_container->construct_row();
	// Datenbankspalte "ipdate" aktualisieren
	$form_container->output_cell("<b>Datum in Zeitstempel umwandeln</b><br />Voraussetzung ist die Formatierung <em>Tag Monat Jahr</em> für das ursprüngliche Datum!");
	$form_container->output_cell("<center>".$form->generate_submit_button("Datum aktualisieren", array("name" => "update_date"))."</center>");
	$form_container->construct_row();
	// MyAlerts-Integration anlegen
	$form_container->output_cell("<b>Alert-Types anlegen</b><br /> Nur, wenn MyAlerts <em>nach</em> dem Tracker installiert wurde!");
	$form_container->output_cell("<center>".$form->generate_submit_button("Alert Types anlegen", array("name" => "update_alerts"))."</center>");
	$form_container->construct_row();
	$form_container->end();
	$form->end();
	$page->output_footer();
}

?>
