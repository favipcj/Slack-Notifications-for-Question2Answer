<?php
/*
	Plugin Name: Slack Notifications
	Plugin URI: https://www.nioya.com/
	Plugin Description: Sends Slack notifications of new questions.
	Plugin Version: 0.2
	Plugin Date: 2019-04-07
	Plugin Author: nioya
	Plugin Author URI: https://github.com/nioya
	Plugin License: MIT
	Plugin Minimum Question2Answer Version: 1.5
*/
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}
qa_register_plugin_module('event', 'qa-slack-notifications-event.php', 'qa_slack_notifications_event', 'Slack Notifications');
qa_register_plugin_module('page', 'qa-slack-notifications-page.php', 'qa_slack_notifications_page', 'Slack Notifications Configuration');
