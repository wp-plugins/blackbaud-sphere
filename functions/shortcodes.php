<?php



add_shortcode("sphere_participants", 'sphere_participants');

add_shortcode("sphere_report_participants", 'sphere_report_participants');

add_shortcode("sphere_participantId", "sphere_participant_id");

add_shortcode("sphere_teams", 'sphere_teams');

add_shortcode("sphere_report_teams", 'sphere_report_teams');

add_shortcode("sphere_teamId", "sphere_team_id");

add_shortcode("sphere_donate", "sphere_donate");

add_shortcode("sphere_register", "sphere_register");

add_shortcode("sphere_eventId", "sphere_event_id");

add_shortcode("sphere_eventName", "sphere_event_name");

add_shortcode("sphere_eventGoal", "sphere_event_goal");

add_shortcode("sphere_eventRaised", "sphere_event_raised");

add_shortcode("sphere_participantsSearch", "sphere_participants_search");

add_shortcode("sphere_teamSearch", "sphere_team_search");



function sphere_participants($atts)

{

	extract(shortcode_atts(array(

		'amount' => 0,

		'show_amount_raised' => 0,

	), $atts));



	$participants = sphere_get_participants(array(

		'amount' => $amount,

	));



	ob_start();

	?>

	<?php if (!empty($participants)) : ?>

	<dl class="top-participants">

		<?php foreach ($participants as $participant) : ?>

			<dt><?php echo $show_amount_raised ? "$" . $participant->amount_raised_pending . " - " : ''; ?></dt>
			<dd><a href="http://www.kintera.org/faf/donorReg/donorPledge.asp?ievent=<?php echo $participant->event_id; ?>&supId=<?php echo $participant->supporter_id; ?>"><?php echo $participant->fname; ?> <?php echo $participant->lname; ?></a></dd>

		<?php endforeach; ?>

	</dl>

	<?php endif; ?>

	<?php

	return ob_get_clean();

}



function sphere_report_participants($atts)

{

	extract(shortcode_atts(array(

		'report' => '',

		'amount' => 0,

		'show_amount_raised' => 0,

	), $atts));



	$participants = sphere_get_participants(array(

		'amount' => $amount,

		'type' => $report,

	));



	ob_start();

	?>

	<?php if (!empty($participants)) : ?>

	<dl class="<?php echo sphere_slug($report); ?>-top-participants">

		<?php foreach ($participants as $participant) : ?>

			<dt><?php echo $show_amount_raised ? "$" . $participant->amount_raised_pending . " - " : ''; ?></dt>
			<dd><a href="http://www.kintera.org/faf/donorReg/donorPledge.asp?ievent=<?php echo $participant->event_id; ?>&supId=<?php echo $participant->supporter_id; ?>"><?php echo $participant->fname; ?> <?php echo $participant->lname; ?></a></dd>

		<?php endforeach; ?>

	</dl>

	<?php endif; ?>

	<?php

	return ob_get_clean();

}



function sphere_participant_id($atts)

{

	extract(shortcode_atts(array(

		'id' => 0,

		'show_amount_raised' => 0,

	), $atts));



	$participants = sphere_get_participants(array(

		'supporter_id' => $id,

	));



	ob_start();

	?>

	<?php if (!empty($participants)) : ?>

		<?php foreach ($participants as $participant) : ?>

			<a href="http://www.kintera.org/faf/donorReg/donorPledge.asp?ievent=<?php echo $participant->event_id; ?>&supId=<?php echo $participant->supporter_id; ?>"><?php echo $show_amount_raised ? "$" . $participant->amount_raised_pending . " - " : ''; ?><?php echo $participant->fname; ?> <?php echo $participant->lname; ?></a>

		<?php endforeach; ?>

	<?php endif; ?>

	<?php

	return ob_get_clean();

}



function sphere_teams($atts)

{

	extract(shortcode_atts(array(

		'amount' => 0,

		'show_amount_raised' => 0,

	), $atts));



	$participants = sphere_get_teams(array(

		'is_team' => 'Y',

		'amount' => $amount,

	));



	ob_start();

	?>

	<?php if (!empty($participants)) : ?>

	<dl class="top-teams">

		<?php foreach ($participants as $participant) : ?>

			<dt><?php echo $show_amount_raised ? "$" . $participant->team_amount_raised_pending . " - " : ''; ?></dt>
			<dd><a href="http://theprouty.kintera.org/faf/search/searchTeamPart.asp?ievent=<?php echo $participant->event_id; ?>&lis=1&team=<?php echo $participant->team_id; ?>"><?php echo $participant->team_name; ?></a></dd>

		<?php endforeach; ?>

	</dl>

	<?php endif; ?>

	<?php

	return ob_get_clean();

}



function sphere_report_teams($atts)

{

	extract(shortcode_atts(array(

		'report' => '',

		'amount' => 0,

		'show_amount_raised' => 0,

	), $atts));



	$participants = sphere_get_teams(array(

		'is_team' => 'Y',

		'amount' => $amount,

		'type' => $report,

	));



	ob_start();

	?>

	<?php if (!empty($participants)) : ?>

	<dl class="<?php echo sphere_slug($report); ?>-top-teams">

		<?php foreach ($participants as $participant) : ?>

			<dt><?php echo $show_amount_raised ? "$" . $participant->team_amount_raised_pending . " - " : ''; ?></dt>
			<dd><a href="http://theprouty.kintera.org/faf/search/searchTeamPart.asp?ievent=<?php echo $participant->event_id; ?>&lis=1&team=<?php echo $participant->team_id; ?>"><?php echo $participant->team_name; ?></a></dd>

		<?php endforeach; ?>

	</dl>

	<?php endif; ?>

	<?php

	return ob_get_clean();

}



function sphere_team_id($atts)

{

	extract(shortcode_atts(array(

		'id' => 0,

		'show_amount_raised' => 0,

	), $atts));



	$participants = sphere_get_teams(array(

		'team_id' => $id,

	));



	ob_start();

	?>

	<?php if (!empty($participants)) : ?>

		<?php foreach ($participants as $participant) : ?>

		<a href="http://theprouty.kintera.org/faf/search/searchTeamPart.asp?ievent=<?php echo $participant->event_id; ?>&lis=1&team=<?php echo $participant->team_id; ?>" class="team teamId<?php echo $participant->team_id; ?>"><?php echo $show_amount_raised ? "$" . $participant->team_amount_raised_pending . " - " : ''; ?><?php echo $participant->team_name; ?></a>

		<?php endforeach; ?>

	<?php endif; ?>

	<?php

	return ob_get_clean();

}



function sphere_donate()

{

	ob_start();

	?>

	<a href="https://www.kintera.org/faf/donorReg/donorPledge.asp?supId=0&ievent=<?php echo sphere_option('event_id'); ?>&lis=1&givenow=y" class="faf-donate">Donate</a>

	<?php

	return ob_get_clean();

}



function sphere_register()

{

	ob_start();

	?>

	<a href="https://www.kintera.org/faf/r/default.asp?ievent=<?php echo sphere_option('event_id'); ?>&lis=1" class="faf-register">Register</a>

	<?php

	return ob_get_clean();

}



function sphere_event_id()

{

	return sphere_option('event_id');

}



function sphere_event_name()

{

	return sphere_get_event_name(sphere_option('event_id'));

}



function sphere_event_goal()

{

	return sphere_option('event_goal') ? sphere_option('event_goal') : sphere_get_goal(sphere_option('event_id'));

}



function sphere_event_raised()

{

	return sphere_option('event_raised') ? sphere_option('event_raised') : sphere_get_amount_raised(sphere_option('event_id'));

}



function sphere_participants_search()

{

	ob_start();

	?>

	<a href="http://www.kintera.org/faf/search/searchParticipants.asp?ievent=<?php echo sphere_option('event_id'); ?>&lis=1" class="faf-participant-search">Participant Search &raquo;</a>

	<?php

	return ob_get_clean();

}



function sphere_team_search()

{

	ob_start();

	?>

	<a href="http://theprouty.kintera.org/faf/search/searchTeam.asp?ievent=<?php echo sphere_option('event_id'); ?>&lis=1" class="faf-team-search">Team Search &raquo;</a>

	<?php

	return ob_get_clean();

}