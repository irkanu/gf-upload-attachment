<?php
/*
Plugin Name: Gravity Form Upload as Attachment
Plugin URI:
Description: Files uploaded will be sent as attachments.
Version: 1.0.0
Author: Dylan Ryan
Author URI:
License: GPL2
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

function gfuaa_admin_init() {
    add_option('gfuaa-form-id', '');
    add_option('gfuaa-all-forms', '1');
    register_setting('gfuaa-settings-group', 'gfuaa-form-id');
    register_setting('gfuaa-settings-group', 'gfuaa-all-forms');

    // TODO
    // add_option('gfuaa-notification-name', 'Admin Notification');
    // register_setting('gfuaa-settings-group', 'gfuaa-notification-name');

    add_settings_field('gfuaa-form-id', 'Gravity Form ID', 'gfuaa_form_id_callback', 'gfuaa-settings', 'gfuaa_section-one');
}
add_action('admin_init', 'gfuaa_admin_init');

function gfuaa_section_one_callback(){};
function gfuaa_form_id_callback(){};

add_action('admin_menu', 'gfuaa_menu_page');
function gfuaa_menu_page() {
    add_submenu_page (
        'options-general.php',
        'Gravity Form File Upload Settings',
        'Gravity Form Upload as Attachment',
        'manage_options',
        'gfuaa-settings',
        'gfuaa_settings_page'
    );
}

function gfuaa_settings_page(){
    ?>
    <div class="wrap">
        <h2>Gravity Form Upload as Attachment</h2>
        <form action="options.php" method="post">
            <?php settings_fields('gfuaa-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="gfuaa-all-forms">Use on all forms:</label></th>
                    <td><input type="checkbox" id="gfuaa-all-forms" name="gfuaa-all-forms" value="1" <?php if ( 1 == get_option('gfuaa-all-forms')) echo 'checked="checked"'; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="gfuaa-form-id">Gravity Form ID:</label></th>
                    <td><input type="number" id="gfuaa-form-id" name="gfuaa-form-id" value="<?php echo get_option('gfuaa-form-id'); ?>" /></td>
                </tr>
                <!-- TODO
                <tr valign="top">
                    <th scope="row"><label for="gfuaa-notification-name">Notification name:</label></th>
                    <td><input type="number" id="gfuaa-notification-name" name="gfuaa-notification-name" value="<?php /*echo get_option('gfuaa-notification-name', 'Admin Notification'); */?>" /></td>
                </tr>
                -->
            </table>
            <?php submit_button(); ?>
        </form>
        <h2>Debug</h2>
        <p><?php echo gfuaa_get_form_id(); ?></p>
    </div>

<?php
}

function gfuaa_get_form_id(){
    if (get_option('gfuaa-all-forms') == '1') {

        $form_id = 'gform_notification';

        return $form_id;

    } else {

        $form_id = 'gform_notification_' . gfuaa_get_option_form_id();

        return $form_id;
    }
}

// gform_notification_FORMID, custom_function
add_filter(gfuaa_get_form_id(), 'gfuaa_upload_attachment', 10, 3);

// http://gravityformspdfextended.com/topic/file-upload-as-attachments/
function gfuaa_upload_attachment( $notification, $form, $entry ) {

	//There is no concept of user notifications anymore, so we will need to target notifications based on other criteria, such as name
	if($notification["name"] == gfuaa_get_option_notification_name()){

		$fileupload_fields = GFCommon::get_fields_by_type($form, array("fileupload"));

		if(!is_array($fileupload_fields))
			return $notification;

		$upload_root = RGFormsModel::get_upload_root();
		foreach($fileupload_fields as $field){
			$url = $entry[$field["id"]];
			$attachment = preg_replace('|^(.*?)/gravity_forms/|', $upload_root, $url);
			if($attachment){
				$notification["attachments"][] = $attachment;
			}
		}
	}
	return $notification;
}

function gfuaa_get_option_form_id() {
    return get_option('gfuaa-form-id');
}

/* TODO
 * function gfuaa_get_option_notification_name(){
    return (get_option('gfuaa-notification-name') != '') ? get_option('gfuaa-notification-name') : 'Admin Notification';
}*/