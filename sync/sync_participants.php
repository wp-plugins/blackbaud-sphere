<?php

require_once('../../../../wp-load.php');

include_once("lib/kennect.php");
include_once('lib/webrequest.php');
include_once('lib/simple_html_dom.php');
require_once('lib/phpQuery/phpQuery.php');
include_once("lib/ez_sql.php");

global $wpdb;

set_time_limit(3600);
ini_set('memory_limit', '512M');

// delete cookie file
if (file_exists('lib/update.txt')) {
	unlink('lib/update.txt');
}

// get blog lists
if (is_multisite()) {
	$blog_lists = $wpdb->get_results("SELECT * FROM $wpdb->blogs");
	foreach ($blog_lists as $blog) {
		switch_to_blog($blog->blog_id);

		printf('Blog: %s<br /><hr />', $blog->blog_id);

		// reinit sphere options
		new Redux_Framework_Sphere();

		// start sync
		if (sphere_option('event_id') > 0) {
			sphere_sync_participants();
		}

		restore_current_blog();
	}
} else {
	// start sync
	sphere_sync_participants();
}

function sphere_sync_participants() {
	global $wpdb;

	// get all reports
	$reports = sphere_option('reports');
	$report_tabs = array();
	if (!empty($reports)) {
		foreach ((array) $reports['name'] as $k => $v) {
			$report_tabs[$k] = array(
				'name' => $reports['name'][$k],
				'field' => $reports['field'][$k],
				'condition' => $reports['condition'][$k],
				'criteria' => $reports['criteria'][$k],
				'id' => $reports['id'][$k],
			);
		}
	}

	// get event info first 
	$req = new WebRequest("update", "");
	$response = $req->Get("http://www.kintera.org/faf/json/event.asp?ievent=" . sphere_option('event_id'));
	$event_data = json_decode(trim($response), true);
	update_option('sphere_event_data_' . sphere_option('event_id'), $event_data);

	// Logging to CMS
	$req = new WebRequest("update", "");
	$response = $req->Post("https://www.kintera.com/Kintera_Sphere/login/asp/Login.aspx", "LoginName=" . sphere_option('username') . "&Password=" . sphere_option('password') . "&DomainCode=&x=74&y=16");
	$response = $req->Post("https://www.kintera.com/kintera_sphere/admin/privilege/VirtualAccountSetup/ChooseVirtualAccount.aspx", "EveSrc=VirtualAccountSelected&EveArgs=0&BtnVAccount=Login&LR1_CP_choosecolumns_flag=&__EVENTARGUMENT=&__EVENTTARGET=");

	// get report data
	foreach ((array) $report_tabs as $report) {

		printf('%s<br /><hr />', $report['name']);

		// Reset response data first
		$response = "";

		// load report page
		$response = $req->Get("https://www.kintera.com/kintera_sphere/reports/asp/individual_report.asp?id={$report['id']}&con=true");

		//  post cust. step 1
		$response = $req->Post("https://www.kintera.com/kintera_sphere/reports/asp/customize_b_adv.asp?prerun=1", "newselectedIDs=&submit_type=finish&popupflag=&__rangename=%2Fkintera_sphere%2Freports%2Fasp%2Fcustomize_b_adv.asp&__rangetype=TODATE&__begindate=&__enddate=&__group_countW=1&__group_sizeW1=1&__crit_fieldW1_1={$report['field']}&__operatorW1_1={$report['condition']}&__opvalueW1_1={$report['criteria']}&__using_enumW1_1=0&__group_countH=1&__group_sizeH1=1&__crit_fieldH1_1=-1&__operatorH1_1=&__using_enumH1_1=0");

		// Go to report page
		$response = $req->Post("https://www.kintera.com/kintera_sphere/reports/asp/customize_prepare.asp", "submit_type=finish&user_report_name=&user_email=dave%40cmscode.com&user_message=&user_report_format=XLS&choose_offline=no");

		// submit form and goto report screen
		$url = "https://www.kintera.com/kintera_sphere/reports/asp/individual_report.asp";
		$data = "__allcolumns=&__allfields=&__allflags=&__checked_recIds=&__deforderby=last_name&__export_or_printable=3&__isasc=desc&__listname=/kintera_sphere/reports/asp/individual_report.asp&__newcolumns=&__orderby=donation_amount&__pagenum=1&__pagesize=100&__persist=&__ren_submit_type=&eveSrc=&exporttitle=&graph_report_type=Registration&graph_where_clause=&mailing_save_name=&saveName=&strAccounts=&strAll=&strAllSingle=&strOrgs=&submit_type=";

		$response = $req->Post($url, $data);

		// parse file
		$headers = array();
		$dom = phpQuery::newDocument($response);
		$report_table = pq('table:first td:first table:first');
		foreach (pq('tr:first td font', $report_table) as $header) {
			$headers[] = str_replace(array("&nbsp;", " "), array("", "_"), pq($header)->text());
		}

		$line = 0;
		foreach (pq('tr:gt(0)', $report_table) as $row) {
			$data = null;
			$data = array();
			$cellindex = 0;
			foreach (pq('td', $row) as $cell) {
				if (trim(pq($cell)->text()) == "" && $cellindex == 0) break;
				$data[$headers[$cellindex]] = pq($cell)->text();
				if ($headers[$cellindex] == 'Total_Amount' || $headers[$cellindex] == 'Donation_Amount') {
					$data[$headers[$cellindex]] = str_replace(array("&nbsp;", " ", ",", "$"), array("", "_", "", ""), strip_tags(pq($cell)->text()));
				} else {
					$data[$headers[$cellindex]] = str_replace(array("&nbsp;"), array(" "), strip_tags(trim(pq($cell)->text())));
				}
				$cellindex++;
			}

			if (isset($data['Supporter_ID'])) {
				// insert record into db
				$member = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}supporters WHERE supporter_id = '" . $data['Supporter_ID'] . "' && event_id = '" . $data['Event_ID'] . "' AND type = '{$report['name']}'", ARRAY_A);

				if (empty($member)) {
					$wpdb->query("INSERT INTO {$wpdb->prefix}supporters (type,supporter_id,fname,lname,event_id,event_name,amount_raised,team_name,team_id,date_created,last_modified) VALUES (
					'" . $report['name'] . "',
					'" . $data['Supporter_ID'] . "',
					'" . mysql_real_escape_string($data['First_Name']) . "',
					'" . mysql_real_escape_string($data['Last_Name']) . "',
					'" . $data['Event_ID'] . "',
					'" . mysql_real_escape_string($data['Initiative_Name']) . "',
					'" . str_replace('$', '', $data['Donation_Amount']) . "',
					'" . mysql_real_escape_string($data['Team_Name']) . "',
					'" . mysql_real_escape_string($data['Team_ID']) . "',
					'" . time() . "',
					'" . time() . "'
				)");
					echo "SID: {$data['Supporter_ID']} Inserted<br/>";
					$s++;
				} else {
					$wpdb->query("UPDATE {$wpdb->prefix}supporters SET
					fname = '" . mysql_real_escape_string($data['First_Name']) . "',
					lname = '" . mysql_real_escape_string($data['Last_Name']) . "',
					event_name = '" . mysql_real_escape_string($data['Initiative_Name']) . "',
					amount_raised = '" . str_replace('$', '', $data['Donation_Amount']) . "',
					team_name = '" . mysql_real_escape_string($data['Team_Name']) . "',
					team_id = '" . mysql_real_escape_string($data['Team_ID']) . "',
					last_modified = '" . time() . "'
					WHERE supporter_id = '" . $data['Supporter_ID'] . "' && event_id = '" . $data['Event_ID'] . "'
				");
					echo "SID: {$data['Supporter_ID']} Already in DB. Updated<br/>";
					$u++;
				}
			}
			$line++;
		}
	}

}
exit;