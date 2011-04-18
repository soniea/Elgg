<?php
/**
 * Elgg report action
 * 
 * @package ElggReportContent
 */
$title = get_input('title');
$description = get_input('description');
$address = get_input('address');
$access = ACCESS_PRIVATE; //this is private and only admins can see it


if ($title && $address) {

	$report = new ElggObject;
	$report->subtype = "reported_content";
	$report->owner_guid = elgg_get_logged_in_user_guid();
	$report->title = $title;
	$report->address = $address;
	$report->description = $description;
	$report->access_id = $access;
        $email_subject = "REPORTED CONTENT " . $report->title;
        $email_body = $report->description . "\n" . "Reported URL" . $report->address;
       
	if ($report->save()) {
		if (!elgg_trigger_plugin_hook('reportedcontent:add', 'system', array('report' => $report), true)) {
			$report->delete();
			register_error(elgg_echo('reportedcontent:failed'));
		} else {
                        $query = "SELECT guid FROM elgg_users_entity` WHERE admin = 1";
                        $admin_details = get_data($query);
                        if (elgg_get_plugin_setting('send_notification', 'reportedcontent') == 'yes'){
                            foreach($admin_details as $admin_guid){
                                notify_user($admin_guid->guid,$report->owner_guid,$email_subject, $email_body);
                                    //  system_message(elgg_echo('reportedcontent:success'));
                            }
                        }
			system_message(elgg_echo('reportedcontent:success'));
			$report->state = "active";
		}
		forward($address);
	} else {
		register_error(elgg_echo('reportedcontent:failed'));
		forward($address);
	}
} else {

	register_error(elgg_echo('reportedcontent:failed'));
	forward($address);
}
