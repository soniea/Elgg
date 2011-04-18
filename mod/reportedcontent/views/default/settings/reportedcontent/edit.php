<?php
/**
 * Reported Content plugin settings
 */

// set default value
if (!isset($vars['entity']->send_notification)) {
	$vars['entity']->send_notification = 'no';
}

echo '<div>';
echo ' ';
echo elgg_view('input/dropdown', array(
	'name' => 'params[send_notification]',
	'options_values' => array(
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
	),
	'value' => $vars['entity']->send_notification,
));
echo '</div>';
