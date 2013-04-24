<?php
    if($_POST['submit'])
    {
        $mChannelId = $_POST['cxtn_channel_id'];
        if(empty($mChannelId))
        {
            # delete channel ID if empty
            delete_option('cxtn_channel_id');
        }
        else{
            $sChannelUrl = "http://data.cxtn.net/data/channel/".$mChannelId;

            $sChannelOpt = @file_get_contents($sChannelUrl);
            //var_dump($sChannelOpt);

            $iPosOf = strpos($sChannelOpt, "{");

            if($iPosOf !== false && $iPosOf > 0)
            {
                # Trim text before/after json output
                $sChannelOpt = substr($sChannelOpt, $iPosOf, -2);
            }
            $output = json_decode($sChannelOpt);

            /* If valid json found, it will conver to Object containing valid Channel ID */
            if(is_object($output))
            {
                //echo "<pre>"; print_r($output); echo "</pre>";

                /* Update database to store valid Channel URL & Channel ID */
                //update_option('cxtn_channel_url' , $sChannelUrl);
                update_option('cxtn_channel_id' , $output->_id);

                $aMsg = array('msg_type' => 'updated', 'sMsg' => 'Channel ID validated. You have successfully activated the VidAnalytic Plugin.');
            }
            else
            {
                $aMsg = array('msg_type' => 'error', 'sMsg' => 'The Channel ID you have entered is either invalid or inactive.');
            }
        }
    }
?>
<div class="wrap">
    <div class="narrow">
        <h2>VidAnalytic</h2>
        <?php if(!empty($aMsg))
        {
        ?>
            <div class="<?php echo $aMsg['msg_type']; ?>" id="message">
                <p><strong><?php echo $aMsg['sMsg']; ?></strong></p>
            </div>
        <?php
        }
        ?>

        <form method="post" action="" name="option_form" class="validate" style="margin: auto; width:400px">
<p>
<a href="http://vidanalytic.com" target="_blank">VidAnalytic</a> is a free companion solution to Google Analytics for tracking embedded video usage on site. Get up to date audience video consumption data, discover what your users actually watch on your site, and boost visitor engagement by helping you program to their interests.
</p>

<p>
<a href="http://vidanalytic.com" target="_blank">VidAnalytic</a> is currently compatible with embedded YouTube videos.
</p>
        <p>To find out more, check out our website at  <a href="http://www.vidanalytic.com" target="_blank">VidAnalytic.com</a><p>
            <!--<div class="form-field form-required">
                <label for="cxtn_channel_url">Channel URL</label>
                <input type="text" name="cxtn_channel_url" id="cxtn_channel_url" value="<?php echo get_option('cxtn_channel_url'); ?>" size="90" />
                <p>Enter URL to grab valid channel ID from it.</p>
            </div>-->

        <h3>Channel ID</h3>
<p style="padding: 1em; background-color: #aa0; color: #fff; font-weight: bold;">Please enter your Channel ID.</p>
<p><h4>Don't have a Channel ID? <a href="http://app.vidanalytic.com/register?return=true" style="" target="_blank">Get Your Free Account and Channel ID at VidAnalytic.com.</a></h4></p>

            <div class="form-field">
                <input type="text" name="cxtn_channel_id" id="cxtn_channel_id" value="<?php echo get_option('cxtn_channel_id'); ?>" />
            </div>

            <p class="submit">
                <input type="submit" name="submit" value="Save Changes" class="button button-primary" />
            </p>
        </form>
        <div class="validate" style="margin: auto; width:400px">
            <h3>Control Panel</h3>
            <p style="padding: .5em; background-color: #4AB915; color: #fff; font-weight:bold;"><a style="padding: .5em; background-color: #4AB915; color: #fff; font-weight:bold;" href="http://app.vidanalytic.com/"  target="_blank">Access Your Account</a></p>
            <p><a href="http://app.vidanalytic.com/" target="_blank">Click Here</a> to access your <a href="http://www.vidanalytic.com" target="_blank">VidAnalytic</a> account</p>

        </div>
    </div>
</div>
