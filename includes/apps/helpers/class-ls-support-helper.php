<?php

class LS_Support_Helper
{
    public function __construct()
    {
        add_action('wp_ajax_linksync_new_ticket_support', array($this, 'send_new_ticket'));
    }

    public function send_new_ticket()
    {

        $to = 'support@linksync.com';
        if (!empty($_POST['summary']) && !empty($_POST['description'])) {
            $subject = $_POST['summary'];
            $body = $_POST['description'];
            $site_url = get_site_url();
            $admin_email = get_option('admin_email');
            $headers[] = 'From: Ticket From ' . $site_url . ' <' . $admin_email . '>';
            $send_email = wp_mail($to, $subject, $body, $headers);

            wp_send_json('' . $send_email . '');
        }

        wp_send_json('0');
        die();
    }

    public static function supportScripts()
    {
        add_action('admin_footer', array('LS_Support_Helper', 'zendeskScripts'));
    }

    public static function zendeskScripts()
    {
        ?>
        <!-- Start of linksync Zendesk Widget script -->
        <script>/*<![CDATA[*/
            window.zEmbed || function (e, t) {
                var n, o, d, i, s, a = [], r = document.createElement("iframe");
                window.zEmbed = function () {
                    a.push(arguments)
                }, window.zE = window.zE || window.zEmbed, r.src = "javascript:false", r.title = "", r.role = "presentation", (r.frameElement || r).style.cssText = "display: none", d = document.getElementsByTagName("script"), d = d[d.length - 1], d.parentNode.insertBefore(r, d), i = r.contentWindow, s = i.document;
                try {
                    o = s
                } catch (e) {
                    n = document.domain, r.src = 'javascript:var d=document.open();d.domain="' + n + '";void(0);', o = s
                }
                o.open()._l = function () {
                    var e = this.createElement("script");
                    n && (this.domain = n), e.id = "js-iframe-async", e.src = "https://assets.zendesk.com/embeddable_framework/main.js", this.t = +new Date, this.zendeskHost = "linksync.zendesk.com", this.zEQueue = a, this.body.appendChild(e)
                }, o.write('<body onload="document._l();">'), o.close()
            }();
            /*]]>*/</script>
        <!-- End of linksync Zendesk Widget script -->
        <script type="text/javascript">
            window.zESettings = {
                webWidget: {
                    position: {
                        horizontal: 'right',
                        vertical: 'bottom'
                    },
                    color: {
                        theme: '#347bc1'
                    }
                }
            };
        </script>
        <?php
    }

    public static function renderFormForSupportTab()
    {
        ?>
        <table class="form-table">
            <tr>
                <th class="titledesc"><b>Summary</b><br/><br/></th>
                <td align="left">
                    <input type="text" id="support_summary" name="linksync_support[summary]" value=""
                           style="width: 630px;">
                </td>
            </tr>
            <tr>
                <td class="titledesc"><b>Description</b></td>
                <td>
                    <textarea id="support_description" name="linksync_support[description]" cols="100"
                              rows="10"></textarea>
                </td>
            </tr>
            <tr>
                <td><br/><br/></td>
                <td><input id="submit-support-button" class="button button-primary button-large" type="submit"
                           name="submit_ticket" value="Submit the ticket "></td>
            </tr>

        </table>
        <script>
            jQuery(document).ready(function ($) {

                $('#submit-support-button').click(function (e) {
                    e.preventDefault();
                    var selector_summary = $('#support_summary');
                    var selector_description = $('#support_description');
                    var summary = selector_summary.val().trim(),
                        description = selector_description.val().trim();

                    if (summary == '' || description == '') {
                        alert("Summary and Descripton is required fields")
                    }

                    if (summary != '' && description != '') {
                        $.post(
                            ajaxurl, {
                                action: 'linksync_new_ticket_support',
                                summary: summary,
                                description: description
                            },
                            function (response) {
                                response = JSON.parse(response);

                                if ('1' == response) {
                                    console.log(response);
                                    alert("Ticket successfully submitted");
                                    selector_summary.val('');
                                    selector_description.val('');
                                } else {
                                    alert("Ticket submission failed");
                                }
                            });
                    }
                });
            });
        </script>
        <?php
    }
}

new LS_Support_Helper();