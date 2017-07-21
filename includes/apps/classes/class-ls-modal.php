<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Modal
{
    private $modal_additional_class = null;
    private $content_style = null;
    private $html_message = null;
    private $close_icon = 'hide';

    public function __construct($option)
    {
        if(isset($option['additional_class'])){
            $this->set_additional_class($option['additional_class']);
        }

        if(isset($option['content_style'])){
            $this->set_content_style($option['content_style']);
        }

        if(isset($option['default_html_message'])){
            $this->set_html_message($option['default_html_message']);
        }

        if(isset($option['close_icon']) && 'hide' != $option['close_icon']){
            $this->close_icon = $option['close_icon'];
        }
    }

    public function set_additional_class($additional_class)
    {
        if (is_array($additional_class)) {
            $this->modal_additional_class = implode(" ", $additional_class);;
        } else {
            $this->modal_additional_class = $additional_class;
        }
    }

    public function get_additional_class()
    {
        return $this->modal_additional_class;
    }

    public function set_content_style($style = null)
    {
        if (is_array($style)) {
            $temp_style = '';
            foreach ($style as $style_name => $style_value) {
                $temp_style .= $style_name . ':' . $style_value . ';';
            }
            $this->content_style = $temp_style;
        } else {
            $this->content_style = $style;
        }
    }

    public function get_content_style()
    {
        return $this->content_style;
    }


    public function set_html_message($html_message)
    {
        $this->html_message = $html_message;
    }

    public function get_html_message()
    {
        return $this->html_message;
    }

    public function show()
    {
        ?>
        <div class="ls-sync-modal <?php echo $this->get_additional_class(); ?>">
            <div class="ls-pop-ups ls-modal-content"
                 style="<?php echo $this->get_content_style(); ?>">
                <?php
                if('hide' != $this->close_icon){
                    ?>
                    <div style="float: right;">
                        <div class="ui-icon ui-icon-close close-reveal-modal btn-no"
                             style="width: 16px !important;height: 17px;"></div>
                    </div>
                    <?php
                }
                ?>

                <center>
                    <br/>
                    <?php echo $this->get_html_message(); ?>
                </center>

            </div>
            <div class="ls-modal-backdrop close"></div>
        </div>
        <?php
    }
}