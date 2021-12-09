<?php
/*
	Slack Notifications
	File: qa-plugin/slack-notifications/qa-slack-notifications-event.php
	Version: 0.5
	Date: 2019-07-06
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
		if($event == 'q_post'){
            $this->send_slack_notification($event, $userid, $handle, $params);
		}
    }
    
    private function send_slack_notification($event, $userid, $handle, $params)
    {
        $title        = $params['title'];

        $tags         = $params['tags'];
        $tags         = str_replace(' ', ',', $tags);
        $tags_array   = explode(',', $tags);

        $channel_name = '';
        $team_name    = '';
        foreach($tags_array as $key => $value) {
            if (strpos($value, '#') === 0) {
                $channel_name = trim($value);
                break;
             }
        }

        foreach($tags_array as $key => $value) {
             if (strpos($value, '@') === 0) {
                $team_name = trim($value);
                break;
             }
        }


        if ($channel_name !== ''){
            error_log("channel found: ".$channel_name);

            $title_link   = qa_q_path($params['postid'], $params['title'], true);
            $new_question = qa_opt('slack_notifications_title');
            $hide_user    = qa_opt('slack_notifications_is_show_user');

            $content      = '';
            if ($new_question !== '') {
                $content .= $new_question.' '.$team_name;
            } else {
                $content .= 'New Question '.$team_name;
            }

            $content .= ": [".$title."](".$title_link.")";

            // $content .= ' <' . $title_link . '|' .$title .'>';
            if (!$hide_user) {
                $site_url     = qa_opt('site_url');
                $username     = $this->get_user_full_name($handle);
                $user_link    = $site_url . "user/" . $username;
                $content .= ' _(by <' . $user_link . '|' . $username . '>)_';
            }

            $this->send_slack_command($content, $channel_name);
        }
        
    }
    
    private function send_slack_command($content, $channel_name)
    {
        $boot_url = qa_opt('slack_notifications_bot_url');
        $slack_token = qa_opt('slack_notifications_token');

        $data        = array(
            "channel" => $channel_name,
            "message" => $content
        );
        $data_string = json_encode($data);

        error_log($boot_url." boot_url");
        error_log($slack_token." slack_token");
        error_log($data_string ." data");
        
        $ch = curl_init($boot_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Accept: application/json",
            "Authorization: Bearer ".$slack_token
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);
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
