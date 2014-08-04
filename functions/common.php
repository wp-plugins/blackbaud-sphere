<?php

function sphere_option($name, $field = '') {
	global $sphere_data;
	$value = $sphere_data[$name];
	if (!empty($field)) {
		$value = $value[$field];
	}
	return $value;
}

function sphere_get_participants($params = array()) {
	global $wpdb;

	$condition = $order_by = $limit = "";
	if (!empty($params['is_team']) && $params['is_team'] == 'Y') {
		$condition .= $wpdb->prepare(" AND team_id > %d", 0);
	}
	if (!empty($params['type'])) {
		$condition .= $wpdb->prepare(" AND type = %s", $params['type']);
	}
	if (!empty($params['supporter_id'])) {
		$condition .= $wpdb->prepare(" AND supporter_id = %s", $params['supporter_id']);
	}
	if (!empty($params['team_id'])) {
		$condition .= $wpdb->prepare(" AND team_id = %s", $params['team_id']);
	}

	$order_by = " ORDER BY amount_raised_pending DESC ";
	if (!empty($params['amount'])) {
		$limit = $wpdb->prepare(" LIMIT 0, %d", $params['amount']);
	}
	$participants = $wpdb->get_results("SELECT *, (amount_raised+pending_donations) as amount_raised_pending FROM {$wpdb->prefix}supporters WHERE 1 {$condition} {$order_by} {$limit}");

	return $participants;
}

function sphere_get_teams($params = array()) {
	global $wpdb;

	$condition = $order_by = $limit = "";
	if (!empty($params['is_team']) && $params['is_team'] == 'Y') {
		$condition .= $wpdb->prepare(" AND team_id > %d", 0);
	}
	if (!empty($params['type'])) {
		$condition .= $wpdb->prepare(" AND type = %s", $params['type']);
	}
	if (!empty($params['supporter_id'])) {
		$condition .= $wpdb->prepare(" AND supporter_id = %s", $params['supporter_id']);
	}
	if (!empty($params['team_id'])) {
		$condition .= $wpdb->prepare(" AND team_id = %s", $params['team_id']);
	}

	$order_by = " ORDER BY team_amount_raised_pending DESC ";
	if (!empty($params['amount'])) {
		$limit = $wpdb->prepare(" LIMIT 0, %d", $params['amount']);
	}
	$participants = $wpdb->get_results("SELECT *, SUM(amount_raised+pending_donations) as team_amount_raised_pending FROM {$wpdb->prefix}supporters WHERE 1 {$condition} GROUP BY team_id {$order_by}  {$limit}");

	return $participants;
}

function sphere_get_event_name($event_id) {
	$event_data = get_option('sphere_event_data_' . $event_id);
	return $event_data['event']['name'];
}

function sphere_get_amount_raised($event_id) {
	$event_data = get_option('sphere_event_data_' . $event_id);
	return $event_data['event']['raised'];
}

function sphere_get_goal($event_id) {
	$event_data = get_option('sphere_event_data_' . $event_id);
	return $event_data['event']['goal'];
}

function sphere_slug($string) {
	return sanitize_title($string);
}

add_action( 'wp_ajax_empty_supporters', 'sphere_empty_supporters_action_callback' );
add_action( 'wp_ajax_nopriv_empty_supporters', 'sphere_empty_supporters_action_callback' );
function sphere_empty_supporters_action_callback() {
	global $wpdb;
	$empty = intval( $_POST['empty'] );
	if ($empty) {
		$result = $wpdb->query("truncate table {$wpdb->prefix}supporters;");
		if ($result) {
			echo "Emptied successfully!";
		} else {
			echo "Query error!";
		}
	} else {
		echo "Cheating?!";
	}
	die(); // this is required to return a proper result
}

//This function is copied from sync/sync_participants.php so be sure to keep it updated
function sphere_sync_participants_in_plugin() {
	include_once(dirname(dirname(__FILE__))."/sync/lib/kennect.php");
	include_once(dirname(dirname(__FILE__)).'/sync/lib/webrequest.php');
	include_once(dirname(dirname(__FILE__)).'/sync/lib/simple_html_dom.php');
	require_once(dirname(dirname(__FILE__)).'/sync/lib/phpQuery/phpQuery.php');
	include_once(dirname(dirname(__FILE__))."/sync/lib/ez_sql.php");

	set_time_limit(3600);
	ini_set('memory_limit', '512M');

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

		//printf('%s<br /><hr />', $report['name']);

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
				if ($headers[$cellindex] == 'Total_Amount' || $headers[$cellindex] == 'Donation_Amount' || $headers[$cellindex] == 'Pending_Donations') {
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
					$wpdb->query("INSERT INTO {$wpdb->prefix}supporters (type,supporter_id,fname,lname,event_id,event_name,amount_raised,pending_donations,team_name,team_id,date_created,last_modified) VALUES (
					'" . $report['name'] . "',
					'" . $data['Supporter_ID'] . "',
					'" . mysql_real_escape_string($data['First_Name']) . "',
					'" . mysql_real_escape_string($data['Last_Name']) . "',
					'" . $data['Event_ID'] . "',
					'" . mysql_real_escape_string($data['Initiative_Name']) . "',
					'" . str_replace('$', '', $data['Donation_Amount']) . "',
					'" . str_replace('$', '', $data['Pending_Donations']) . "',
					'" . mysql_real_escape_string($data['Team_Name']) . "',
					'" . mysql_real_escape_string($data['Team_ID']) . "',
					'" . time() . "',
					'" . time() . "'
				)");
					//echo "SID: {$data['Supporter_ID']} Inserted<br/>";
					$s++;
				} else {
					$wpdb->query("UPDATE {$wpdb->prefix}supporters SET
					fname = '" . mysql_real_escape_string($data['First_Name']) . "',
					lname = '" . mysql_real_escape_string($data['Last_Name']) . "',
					event_name = '" . mysql_real_escape_string($data['Initiative_Name']) . "',
					amount_raised = '" . str_replace('$', '', $data['Donation_Amount']) . "',
					pending_donations = '" . str_replace('$', '', $data['Pending_Donations']) . "',
					team_name = '" . mysql_real_escape_string($data['Team_Name']) . "',
					team_id = '" . mysql_real_escape_string($data['Team_ID']) . "',
					last_modified = '" . time() . "'
					WHERE supporter_id = '" . $data['Supporter_ID'] . "' && event_id = '" . $data['Event_ID'] . "'
				");
					//echo "SID: {$data['Supporter_ID']} Already in DB. Updated<br/>";
					$u++;
				}
			}
			$line++;
		}
	}

}

add_action( 'wp_ajax_sync_manually', 'sphere_sync_manually_action_callback' );
add_action( 'wp_ajax_nopriv_sync_manually', 'sphere_sync_manually_action_callback' );
function sphere_sync_manually_action_callback() {
	global $wpdb;
	$sync = intval( $_POST['sync'] );
	if ($sync) {
		// reinit sphere options
		new Redux_Framework_Sphere();

		// start sync
		if (sphere_option('event_id') > 0) {
			sphere_sync_participants_in_plugin();
		}
		echo "Synced successfully!";
	} else {
		echo "Cheating?!";
	}
	die(); // this is required to return a proper result
}