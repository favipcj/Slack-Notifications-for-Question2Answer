<?php
/*
	Slack Notifications
	File: qa-plugin/slack-notifications/qa-slack-notifications-event.php
	Version: 0.5
	Date: 2017-12-10
	Description: Event module class for Slack notifications plugin
*/

class qa_slack_notifications_event
{
    private $plugindir;
    
	function load_module($directory, $urltoroot)
    {
        $this->plugindir = $directory;
    }
    public function process_event($event, $userid, $handle, $cookieid, $params)
    {
        $this->send_mattermost_notification($event, $userid, $handle, $params);
    }
    
    private function send_mattermost_notification($event, $userid, $handle, $params)
    {
        
        $title        = $params['title'];
        $title_link   = qa_q_path($params['postid'], $params['title'], true);
        $new_question = qa_opt('slack_notifications_title');
        $hide_user    = qa_opt('slack_notifications_is_show_user');
        $content      = '';
        $content      = '*';
        if ($new_question) {
            $content .= $new_question;
        } else {
            $content .= 'New Question';
        }
        $content .= ':* ' . $title . ' - ' . $title_link;
        if (!$hide_user) {
			$site_url     = qa_opt('site_url');
			$username     = $this->get_user_full_name($handle);
			$user_link    = $site_url . "user/" . $username;
            $content .= ' _(by <' . $user_link . '|' . $username . '>)_';
        }
        $this->send_slack_command($content);
        
    }
    
    private function send_slack_command($content)
    {
        
        $webhook_url = qa_opt('slack_notifications_webhook_url');
        $data        = array(
            "text" => $content
        );
        $data_string = json_encode($data);
        
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));
        
        $result = curl_exec($ch);
    }
    private function get_user_full_name($handle)
    {
        $is_user_id        = false;
        $userprofiles      = qa_db_select_with_pending(qa_db_user_profile_selectspec($handle, $is_user_id));
        $userdisplayhandle = $handle;
        
        if (!isset($handle)) {
            return qa_lang('main/anonymous');
        }
        
        if (isset($userprofiles['name']) && !empty($userprofiles['name'])) {
            if (@$userprofiles['name'] != '') {
                $userdisplayhandle = @$userprofiles['name'];
            }
        }
        
        return $userdisplayhandle;
    }
    
    
}
