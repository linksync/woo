<?php

class LS_User_Helper
{
    public function __construct()
    {
        add_action('admin_notices', array($this, 'setUpLaidInfoMessage'));
    }

    public static function setUpLaidInfoMessage()
    {
        $message = LS_ApiController::get_current_laid_info();
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
            $service_status = $message_data[1];
            $registrationDate = $message_data[0];
        } else if (count($message_data) <= 1 && isset($message['userMessage']) && is_string($message['userMessage'])) {
            $user_message = ucfirst($message['userMessage']);
        }

        $update_button = '<a  href="https://my.linksync.com/index.php?m=dashboard" 
                              target="_blank" 
                              class="button button-primary" style="margin-top: -4px;">Click here to upgrade now</a>';

        if (true == $isFreeTrial && isset($registrationDate)) {
            $registrationDate = trim($registrationDate);
            $service_status = trim($service_status);
            $remaining_days = self::getRemainingDaysOfTrial($registrationDate, $api_time);

            if ('Terminated' == $service_status) {
                $user_message = 'Hey, sorry to say but your linksync free trial has ended! '.$update_button;
            } elseif ('Suspended' == $service_status) {
                $user_message = 'Hey, sorry to say but your linksync account was Suspended!'.$update_button;
            } elseif ('Cancelled' == $service_status) {
                $user_message = 'Hey, sorry to say but your linksync account was Cancelled!'.$update_button;
            } elseif ('1' == $remaining_days && 'Terminated' != $service_status) {
                $user_message = 'Your linksync FREE  trial ends tomorrow! '.$update_button;
            } elseif ('0' == $remaining_days && 'Terminated' != $service_status) {
                $user_message = 'Your linksync FREE trial ends today! '.$update_button;
            } else {
                $user_message = 'Your linksync FREE trial ends in ' . $remaining_days . ' days! '.$update_button;
            }

        } else if ('Terminated' == $service_status) {
            $user_message = 'Hey, sorry to say but your linksync account was Terminated!';
        } else if ('Suspended' == $service_status) {
            $user_message = 'Hey, sorry to say but your linksync account was Suspended!';
        }


        if (!empty($user_message)) {
            ?>
            <div class="error notice">
                <h3><?php echo $user_message; ?></h3>
            </div>
            <?php

        }

    }

    public static function getRemainingDaysOfTrial($productRegistrationDate, DateTime $current_api_time)
    {
        $next_due_date = date('Y-m-d', strtotime($productRegistrationDate . "+13 days"));
        $today = $current_api_time;
        $expiry_date = new DateTime($next_due_date);
        $trialDaysRemaining = $today->diff($expiry_date);
        return $trialDaysRemaining->days;

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

new LS_User_Helper();