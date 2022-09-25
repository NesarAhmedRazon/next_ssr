<?php

/*
*
* @package NextSSR
*
* Plugin Name: Next SSR
* Plugin URI: https://github.com/NesarAhmedRazon/next_ssr
* Description: This plugin will add Features for NextJs Server Side Randaring.
* Author: Nesar Ahmed
* Version: 0.0.1
* Author URI: https://github.com/NesarAhmedRazon/
* Text Domain: next_ssr
*/

namespace NEXTSSR;

if (!defined('ABSPATH')) {
    // Exit if accessed directly.
    exit;
}
define(
    'NEXT_SSR',
    __FILE__
);

if (!class_exists('next_ssr')) {
    /**
     * Class next_ssr
     */
    class next_ssr
    {
        public function __construct()
        {
            add_action('init', [$this, 'registe_menu_locations']);
            add_action('admin_init', [$this, 'style_toAdmin']);
            add_action('wp_nav_menu_item_custom_fields', [$this, 'next_ssr_leveltwo_column'], 10, 2);
            add_action('wp_update_nav_menu_item', [$this, 'next_ssr_leveltwo_column_update'], 10, 2);
            add_action('graphql_register_types', [$this, 'addToGraphQl']);
        }

        public function registe_menu_locations()
        {

            $locs = [
                'cta' => 'CTA Buttons',
                'copyright' => 'Copyright Menu',
                'megaFooter' => 'SubMenu Footer Menu',
                'next_ssr_nav' => 'Next.js Menu',
            ];
            register_nav_menus(
                $locs
            );
        }
        /**
         * 
         * Add FontAwsome Icon to Admin Panel
         */
        public function style_toAdmin()
        {
            wp_enqueue_style('next_ssr_fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', '', '', 'all');

            wp_enqueue_style('next_ssr_tailwind', plugin_dir_url(__FILE__) . 'assets/styles/tailwindext.css', '', '', 'all');
        }

        /**
         * Add custom fields to menu item
         *
         * This will allow us to play nicely with any other plugin that is adding the same hook
         *
         * @param  int $id 
         * @params obj $item - the menu item
         * @params array $args
         */

        public function next_ssr_leveltwo_column($id, $item)
        {

            if ($item->menu_item_parent != 0) {
                wp_nonce_field('l2meta', 'l2meta_nonce');
                $value = get_post_meta($id, 'expand', true);
                $this->radio_html($value, $id);
            }
        }

        /**
         * Update meta Data
         */

        public function next_ssr_leveltwo_column_update($menu_id, $item_id)
        {
            $nonce_name   = isset($_POST['l2meta_nonce']) ? $_POST['l2meta_nonce'] : '';
            $nonce_action = 'l2meta';

            // Check if our nonce is set.
            if (!wp_verify_nonce($nonce_name, $nonce_action)) {
                return;
            }

            // Input var okay.

            if (isset($_POST['expand'])) {
                if (isset($_POST['expand'][$item_id])) {
                    update_post_meta($item_id, 'expand', sanitize_text_field($_POST['expand'][$item_id])); // Input var okay.
                } else {
                    delete_post_meta($item_id, 'expand');
                }
            } else {
                delete_post_meta($item_id, 'expand');
            }
        }
        public function addToGraphQl()
        {
            register_graphql_field('MenuItem', 'colexpand', [
                /**
                 * expand meta key Added to WP GraphQl
                 */
                'type' => 'String',
                'description' => __('How the 2nd Level Nav Item Column will arranged', 'next_ssr'),
                'resolve' => function (\WPGraphQL\Model\MenuItem $menu_item, $args, $context, $info) {
                    if (!isset($menu_item->databaseId)) {
                        return null;
                    }
                    $data   = get_post_meta($menu_item->databaseId, 'expand', true);
                    $data == "" || $data == "yes" ? $data = "yes" : ($data == $data);
                    return $data;
                }
            ]);
        }


        /**
         * 
         * Html For Radio Button
         */
        public function radio_html($value, $id)
        {
?>

<div class="field-description half next_ssr_metabox">
    <div class="label">Expand Column</div>
    <div class="radio_field boolian">
        <input type="radio" id="radio-one-<?php echo $id; ?>" name="expand[<?php echo $id; ?>]" value="yes" checked
            <?php checked($value, 'yes'); ?> />
        <label for="radio-one-<?php echo $id; ?>">Yes</label>
        <input type="radio" id="radio-two-<?php echo $id; ?>" name="expand[<?php echo $id; ?>]" value="no"
            <?php checked($value, 'no'); ?> />
        <label for="radio-two-<?php echo $id; ?>">No</label>
    </div>
</div>
<?php
        }
    }
}


$next_ssr = new next_ssr();
require plugin_dir_path(NEXT_SSR) . 'metas/next_ssr_childType.php';
