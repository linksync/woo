<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Message_Builder
{
    public static function notice($message, $class = 'error', $dismissible = false)
    {
        if (true == $dismissible) {
            $dismissible = 'is-dismissible';
        }
        ?>
        <div class="notice notice-<?php echo $class . ' ' . $dismissible; ?>">
            <p><?php echo $message; ?></p>
        </div>
        <?php
    }

    public static function error($message, $dismissible = false)
    {
        self::notice($message, 'error', $dismissible);
    }

    public static function warning($message, $dismissible = false)
    {
        self::notice($message, 'warning', $dismissible);
    }

    public static function success($message, $dismissible = false)
    {
        self::notice($message, 'success', $dismissible);
    }

    public static function info($message, $dismissible = false)
    {
        self::notice($message, 'info', $dismissible);
    }

}