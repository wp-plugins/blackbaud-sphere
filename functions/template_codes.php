<?php

function sphere($action, $param_1 = '', $param_2 = '', $param_3 = 0) {
	switch ($action) {
		case "participants":
			echo do_shortcode("[sphere_participants amount={$param_1} show_amount_raised={$param_2}]");
			break;
		case "report_participants":
			echo do_shortcode("[sphere_report_participants report=\"{$param_1}\" amount={$param_2} show_amount_raised={$param_3}]");
			break;
		case "participantId":
			echo do_shortcode("[sphere_participantId id=\"{$param_1}\" show_amount_raised={$param_2}]");
			break;
		case "teams":
			echo do_shortcode("[sphere_teams amount={$param_1} show_amount_raised={$param_2}]");
			break;
		case "report_teams":
			echo do_shortcode("[sphere_report_teams report=\"{$param_1}\" amount={$param_2} show_amount_raised={$param_3}]");
			break;
		case "teamId":
			echo do_shortcode("[sphere_teamId id=\"{$param_1}\" show_amount_raised={$param_2}]");
			break;
		case "donate":
			echo do_shortcode("[sphere_donate]");
			break;
		case "register":
			echo do_shortcode("[sphere_register]");
			break;
		case "eventId":
			echo do_shortcode("[sphere_eventId]");
			break;
		case "eventName":
			echo do_shortcode("[sphere_eventName]");
			break;
		case "eventGoal":
			echo do_shortcode("[sphere_eventGoal]");
			break;
		case "eventRaised":
			echo do_shortcode("[sphere_eventRaised]");
			break;
		case "participantsSearch":
			echo do_shortcode("[sphere_participantsSearch]");
			break;
		case "teamSearch":
			echo do_shortcode("[sphere_teamSearch]");
			break;
	}
}