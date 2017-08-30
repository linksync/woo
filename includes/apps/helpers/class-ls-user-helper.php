<?php

class LS_User_Helper
{
    public static function upgrade_url()
    {
        return 'https://my.linksync.com/index.php?m=dashboard&redirect_to=upgrade_downgrade';
    }
    public static function update_button($button_text = 'Click here to upgrade now', $style = 'margin-top: -4px;')
    {
        $update_button = '<a  href="'.self::upgrade_url().'" 
                              target="_blank" 
                              class="button button-primary" style="">'.$button_text.'</a>';

        return $update_button;
    }

    public static function why_limit_link($message = 'Why is this happening?')
    {
        return '<a href="https://help.linksync.com/hc/en-us/articles/115001095050" target="_blank">'.$message.'</a>';
    }

    public static function save_syncing_error_limit()
    {

        $html_error_message = 'Alert: You have reached your linksync syncing limit. Your free trial is limited to syncing of 50 products and 50 order. '.LS_User_Helper::why_limit_link('Learn more about limits with our free trial').' or view your <a target="_blank" href="'.LS_Vend_Menu::page_menu_url('connected_products').'">synced products</a> and <a target="_blank" href="'.LS_Vend_Menu::page_menu_url('connected_orders').'">synced orders</a>.  <br/><br/> Sync withour limits '.LS_User_Helper::update_button('Upgrade Now','');
        LS_Vend()->option()->update_option('capping_error_limit', $html_error_message);

        return $html_error_message;
    }

    public static function save_product_syncing_error_limit()
    {
        //$html_error_message = 'Product syncing halted! You have reached the '.LS_Constants::TRIAL_PRODUCT_SYNC_LIMIT;
        //$html_error_message .= ' product sync limit. <br/>If you\'d like to sync more '. LS_User_Helper::update_button('upgrade now','').'.';
        //$html_error_message .= '<br/> '.LS_User_Helper::why_limit_link();
        //LS_Vend()->option()->update_option('product_capping_error', $html_error_message);

        return self::save_syncing_error_limit();
    }

    public static function save_order_syncing_error_limit()
    {

        $html_error_message = 'Order syncing halted! You have reached the '.LS_Constants::TRIAL_PRODUCT_SYNC_LIMIT;
        $html_error_message.= ' order sync limit. <br/>If you\'d like to sync more '. LS_User_Helper::update_button('upgrade now','').'.';
        $html_error_message .= '<br/>'.LS_User_Helper::why_limit_link();

        LS_Vend()->option()->update_option('order_capping_error', $html_error_message);
        return $html_error_message;
    }

    public static function getUserPlan()
    {
        $currentLaidInfo = LS_Vend()->laid()->get_current_laid_info();
        $message_data = array();
        if (isset($currentLaidInfo['message'])) {
            $message_data = explode(',', trim($currentLaidInfo['message']));
        } elseif (isset($message['userMessage'])) {
            $message_data = explode(',', trim($currentLaidInfo['userMessage']));
        }

        if (!empty($message_data[2])) {
            $plainId = (int)trim($message_data[2]);
            $message_data['user_plan'] = self::getLinksyncPlansForLaid($plainId);
        }
        return $message_data;
    }

    /**
     *  Check if current laid info is on trial or not
     *
     * @param null $current_laid_info
     * @return bool
     */
    public static function is_laid_on_free_trial($current_laid_info = null)
    {
        if(null == $current_laid_info){
            $current_laid_info = LS_Vend()->laid()->get_current_laid_info();
        }

        $isFreeTrial = false;

        if (isset($current_laid_info['message'])) {
            $accountData = explode(',', $current_laid_info['message']);
        } elseif (isset($current_laid_info['userMessage'])) {
            $accountData = explode(',', $current_laid_info['userMessage']);
        }

        if (is_array($current_laid_info) && !empty($accountData[2])) {
            $isFreeTrial = self::isFreeTrial($accountData[2]);
        }

        return $isFreeTrial;
    }

    public static function reset_capping_error_message()
    {
        $vendOption = LS_Vend()->option();
        $vendOption->update_option('product_capping_error','');
        $vendOption->update_option('order_capping_error', '');
        $vendOption->update_option('product_vend_to_woo_count', '');
        $vendOption->update_option('capping_error_limit', '');
    }

    public static function setUpLaidInfoMessage()
    {
        $current_screen = get_current_screen();
        $GLOBALS['ls_vend_product_capping_error'] = LS_Vend()->option()->get_option('product_capping_error');
        $GLOBALS['ls_vend_order_capping_error'] = LS_Vend()->option()->get_option('order_capping_error');
        $GLOBALS['ls_vend_current_screen'] = $current_screen;
        $GLOBALS['ls_vend_capping_error_limit'] = LS_Vend()->option()->get_option('capping_error_limit');

        global $linksync_vend_laid,$ls_vend_product_capping_error, $ls_vend_order_capping_error, $ls_vend_current_screen, $ls_vend_capping_error_limit;

        if(!empty($linksync_vend_laid)){

            $laid_info = LS_Vend()->laid()->get_laid_info($linksync_vend_laid);
            if (!empty($laid_info)) {
                LS_Vend()->laid()->update_current_laid_info($laid_info);
            }

            $message = $laid_info;
            $isFreeTrial = false;
            $service_status = '';
            $message_data = '';
            $user_message = '';
            $api_time = isset($message['time']) ? new DateTime($message['time']) : new DateTime();

            if (isset($message['message'])) {
                $message_data = explode(',', $message['message']);
            } elseif (isset($message['userMessage'])) {
                $message_data = explode(',', $message['userMessage']);
            }

            if (is_array($message_data) && !empty($message_data[2]) && !empty($message_data[1]) && !empty($message_data[0])) {
                $isFreeTrial = self::isFreeTrial($message_data[2]);
                if(!$isFreeTrial){
                    //Remove capping error if not trial
                    self::reset_capping_error_message();

                    $ls_vend_capping_error_limit = '';
                }
                $service_status = $message_data[1];
                $registrationDate = $message_data[0];
            } else if (count($message_data) <= 1 && isset($message['userMessage']) && is_string($message['userMessage'])) {
                $user_message = ucfirst($message['userMessage']);
            }

            $update_button = '<a  href="https://my.linksync.com/index.php?m=dashboard" 
                              target="_blank" 
                              class="button button-primary" style="margin-top: -4px;">Click here to upgrade now</a>';

            $showTrialEnds = false;
            if (true == $isFreeTrial && isset($registrationDate)) {
                $registrationDate = trim($registrationDate);
                $service_status = trim($service_status);
                $remaining_days = self::getRemainingDaysOfTrial($registrationDate, $api_time);

                if ('Terminated' == $service_status) {
                    $user_message = 'Hey, sorry to say but your linksync free trial has ended! ' . $update_button;
                    $showTrialEnds = true;
                } elseif ('Suspended' == $service_status) {
                    $user_message = 'Hey, sorry to say but your linksync account was Suspended!' . $update_button;
                } elseif ('Cancelled' == $service_status) {
                    $user_message = 'Hey, sorry to say but your linksync account was Cancelled!' . $update_button;
                } elseif ('1' == $remaining_days && 'Terminated' != $service_status) {
                    $user_message = 'Your linksync FREE  trial ends tomorrow! ' . $update_button;
                    $showTrialEnds = true;
                } elseif ('0' == $remaining_days && 'Terminated' != $service_status) {
                    $user_message = 'Your linksync FREE trial ends today! ' . $update_button;
                    $showTrialEnds = true;
                } else {
                    $user_message = 'Your linksync FREE trial ends in ' . $remaining_days . ' days! ' . $update_button;
                }


            } else if ('Terminated' == $service_status) {
                $user_message = 'Hey, sorry to say but your linksync account was Terminated!';
                $showTrialEnds = true;
            } else if ('Suspended' == $service_status) {
                $user_message = 'Hey, sorry to say but your linksync account was Suspended!';
                $showTrialEnds = true;
            } else {
                if(isset($laid_info['errorCode'])){
                    if('invalid or expired API Key' == $laid_info['userMessage']){
                        $user_message = 'Hey, you are using an invalid linksync api key. <a href="'.LS_Vend_Menu::settings_page_menu_url('support').'">Contact support for assistance</a> ';
                        $showTrialEnds = true;
                    }
                }
            }


            if(!empty($ls_vend_capping_error_limit) && false == $showTrialEnds){

                LS_Message_Builder::notice($ls_vend_capping_error_limit, 'error product-capping-error capping-error-limit');
                $user_message = '';
            }

            if (!empty($user_message)) {
                ?>
                <div class="error notice ls-trial-message">
                    <h3><?php echo $user_message; ?></h3>
                </div>
                <?php

            }

        }

    }

    public static function getRemainingDaysOfTrial($productRegistrationDate, DateTime $current_api_time)
    {
        $duedate = new DateTime($productRegistrationDate);

        $next_due_date = $duedate->add(new DateInterval('P14D'));

        $dueDateEnds = new DateTime($next_due_date->format('Y-m-d'));

        $today = new DateTime($current_api_time->format('Y-m-d'));

        $trialDaysRemaining = $today->diff($dueDateEnds);

        return $trialDaysRemaining->format("%d");
    }

    public static function isFreeTrial($package_id)
    {
        $package_id = trim($package_id);
        $package = self::getLinksyncPlansForLaid($package_id);
        if ('14 Days Free Trial' == $package) {
            return true;
        }
        return false;
    }

    public static function getLinksyncPlansForLaid($planId)
    {
        $plans = array(
            1 => 'Basic',
            2 => 'Business',
            3 => 'Premium',
            4 => '14 Days Free Trial',
            5 => 'Unlimited',
        );


        if (!empty($planId) && isset($plans[$planId])) {
            return $plans[$planId];
        }

        return $planId;

    }

}