<?php
/*
	Slack Notifications
	File: qa-plugin/slack-notifications/qa-slack-notifications-page.php
	Version: 0.1
	Date: 2019-07-04
	Description: Event module class for Slack notifications plugin
*/
class qa_slack_notifications_page
{
    
    function admin_form()
    {
        $saved = false;
        if (qa_clicked('general_save_button')) {
            // save the preferences
            qa_opt('slack_notifications_webhook_url', qa_post_text('slack_notifications_webhook_url'));
            qa_opt('slack_notifications_title', qa_post_text('slack_notifications_title'));
            qa_opt('slack_notifications_is_show_user', qa_post_text('slack_notifications_is_show_user'));
            
            $notify = qa_post_text('slack_notifications_is_show_user');
            qa_opt('slack_notifications_is_show_user', empty($notify) ? 0 : 1);
            $saved = true;
        }
        $form = array(
            'ok' => $saved ? 'Slack Notification preferences saved' : null,
            'fields' => array(
                array(
                    'label' => 'Slack Webhook URL',
                    'value' => qa_opt('slack_notifications_webhook_url'),
                    'tags' => 'NAME="slack_notifications_webhook_url"'
                ),
                array(
                    'label' => 'New Question title',
                    'value' => qa_opt('slack_notifications_title'),
                    'tags' => 'NAME="slack_notifications_title"'
                ),
                
                array(
                    'type' => 'checkbox',
                    'label' => 'Hide sender name and profile url',
                    'value' => qa_opt('slack_notifications_is_show_user') ? true : false,
                    'tags' => 'NAME="slack_notifications_is_show_user"'
                )
            ),
            'buttons' => array(
                array(
                    'label' => 'Save Changes',
                    'tags' => 'NAME="general_save_button"'
                )
            )
        );
        return $form;
    }
}
