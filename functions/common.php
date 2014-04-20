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

	$order_by = " ORDER BY amount_raised DESC ";
	if (!empty($params['amount'])) {
		$limit = $wpdb->prepare(" LIMIT 0, %d", $params['amount']);
	}
	$participants = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}supporters WHERE 1 {$condition} {$order_by} {$limit}");

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