<style>
   .radio-class{
       width:auto !important;
   }

</style>
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

            $resp = wp_remote_get($sChannelUrl);
            if ( 200 == $resp['response']['code'] ) {
                $body = $resp['body'];
                //var_dump($body);
            }

            $sChannelOpt = $body;
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

                $default_options= array();
                global $wp_roles;

                $default_options['cxtn_adv_track_opt'] = $_POST['cxtn_adv_track_opt'];

                foreach ( $wp_roles->role_names as $role => $name ){
                    $default_options['cxtn_adv_track_role_'.$role] = $_POST['cxtn_adv_track_role_'.$role];
                }
                update_option('cxtn_track_permission', $default_options);



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
<a href="http://vidanalytic.com" target="_blank">VidAnalytic</a> is currently compatible with embedded YouTube, Vimeo, and Dailymotion videos.
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

            <!-- Customized section -->

            <div class="form-field">
                <?php  $cxtnOptions = get_option('cxtn_track_permission');?>
                <p style="padding: 1em; background-color: #aa0; color: #fff; font-weight: bold;">Detailed Tracking Permissions</p>
                <em id="cxtn_adv_track_msg" style="color:#FF0000;display:none;">Enter a valid Channel ID to enable detailed tracking permissions.<br /></em>
                <table width="100%">
                 <tr>
                 <td>
                    Enable Detailed Tracking Permissions :
                  </td>
                  <td width="15%">
                      <input type="radio" class="radio-class" name="cxtn_adv_track_opt" id="cxtn_adv_track_opt1" value="1" <?php if( $cxtnOptions['cxtn_adv_track_opt']==1){echo ' checked';}?>/> Yes
                  </td>
                     <td width="15%">
                         <input type="radio" class="radio-class" name="cxtn_adv_track_opt" id="cxtn_adv_track_opt2" value="0" <?php if( !isset($cxtnOptions['cxtn_adv_track_opt']) ||$cxtnOptions['cxtn_adv_track_opt']==0 ){ echo ' checked';}?>/> No
                   </td>
                 </tr>
                </table>
            </div>

            <div class="form-field" id="ctn_track_roles" style="display: none;">
                <table class="form-table" width="100%">
                <?php
                global $wp_roles;

                $i=0;
                foreach ( $wp_roles->role_names as $role => $name ){
                    $optName = 'cxtn_adv_track_role_'.$role;
                    ?>

                    <tr>
                    <td width="50%"><strong><?php echo $name;?></strong></td>

                    <td width="30%">
                        <input type="radio" class="radio-class" name="<?php echo $optName;?>" id="cxtn_adv_track_role<?php echo $i;?>" value="1" checked="checked" /> Yes
                    </td>
                    <td width="30%">
                        <input type="radio" class="radio-class" name="<?php echo $optName;?>" id="cxtn_adv_track_role<?php echo $i;?>" value="0" <?php if( isset($cxtnOptions[$optName]) && $cxtnOptions[$optName]==0){echo ' checked';}?>/> No
                    </td>
                    </tr>

                    <?php
                    $i++;
                }
                ?>

                </table>
            </div>

            <!-- Customized section ends -->

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

<script>
    var cxtn_custom_display= false;
    jQuery(document).ready(function(){

        <?php if( $cxtnOptions['cxtn_adv_track_opt']==1){?>
            cxtn_custom_display= true;
        <?php }?>

        if(jQuery('#cxtn_channel_id').attr('value')==null || jQuery('#cxtn_channel_id').attr('value')==''){
            jQuery('input[name="cxtn_adv_track_opt"]').attr('disabled',true);
            jQuery('#ctn_track_roles').css('display','none');
            jQuery('#cxtn_adv_track_msg').css('display','block');

        }else if(cxtn_custom_display){
            jQuery('#ctn_track_roles').css('display','block');
        }

        jQuery('#cxtn_adv_track_opt1').click(function(){

            if( jQuery(this).is(':checked') ){
                jQuery('#ctn_track_roles').css('display','block');
            }
        })

        jQuery('#cxtn_adv_track_opt2').click(function(){

            if( jQuery(this).is(':checked') ){
                jQuery('#ctn_track_roles').css('display','none');
            }
        })


    })


</script>
