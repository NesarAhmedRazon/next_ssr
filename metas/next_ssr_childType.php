<?php

namespace NEXTSSR;

if (!defined('ABSPATH')) {
    // Exit if accessed directly.
    exit;
}

final class MetaChildItemLayout extends next_ssr
{
    public function __construct()
    {

        add_action('wp_nav_menu_item_custom_fields', [$this, 'next_ssr_levelthree_type'], 10, 2);
        add_action('wp_update_nav_menu_item', [$this, 'next_ssr_levelthree_type_update'], 10, 2);
        add_action('graphql_register_types', [$this, 'addToGraphQl']);
    }

    public function addToGraphQl()
    {
        $dataset = [
            'metakey' => 'childlayout', 'gQlKey' => 'childlayout', 'gQlType' => 'Int'
        ];
        register_graphql_field('MenuItem', 'childlayout', [
            /**
             * expand meta key Added to WP GraphQl
             */
            'type' => 'Int',
            'description' => __('Set Layout for NEXT SSR Menu\'s 3rd Level Items', 'next_ssr'),
            'resolve' => function (\WPGraphQL\Model\MenuItem $menu_item, $args, $context, $info) {
                if (!isset($menu_item->databaseId)) {
                    return null;
                }
                $data   = (int)get_post_meta($menu_item->databaseId, 'childlayout', true);
                return $data;
            }
        ]);
    }
    public function next_ssr_levelthree_type($id, $item)
    {

        if ($item->menu_item_parent != 0) {
            wp_nonce_field('l3meta', 'l3meta_nonce');
            $value = get_post_meta($id, 'childlayout', true);
            $this->radio_html($value, $id);
        }
    }
    public function next_ssr_levelthree_type_update($menu_id, $item_id)
    {
        $nonce_name   = isset($_POST['l3meta_nonce']) ? $_POST['l3meta_nonce'] : '';
        $nonce_action = 'l3meta';

        // Check if our nonce is set.
        if (!wp_verify_nonce($nonce_name, $nonce_action)) {
            return;
        }

        // Input var okay.

        if (isset($_POST['childlayout'])) {
            if (isset($_POST['childlayout'][$item_id])) {
                update_post_meta($item_id, 'childlayout', sanitize_text_field($_POST['childlayout'][$item_id])); // Input var okay.
            } else {
                delete_post_meta($item_id, 'childlayout');
            }
        } else {
            delete_post_meta($item_id, 'childlayout');
        }
    }
    public function radio_html($value, $id)
    {
?>

<div class="field-description half next_ssr_metabox">
    <div class="label">Children Layout</div>
    <div class="radio_field">
        <input type="radio" id="childlayout-one-<?php echo $id; ?>" name="childlayout[<?php echo $id; ?>]" value="1"
            checked <?php checked($value, 1); ?> />
        <label for="childlayout-one-<?php echo $id; ?>"><i class="fa fa-list-alt" aria-hidden="true"></i></label>
        <input type="radio" id="childlayout-two-<?php echo $id; ?>" name="childlayout[<?php echo $id; ?>]" value="2"
            <?php checked($value, 2); ?> />
        <label for="childlayout-two-<?php echo $id; ?>"><i class="fa fa-file-image-o" aria-hidden="true"></i></label>
        <input type="radio" id="childlayout-three-<?php echo $id; ?>" name="childlayout[<?php echo $id; ?>]" value="3"
            <?php checked($value, 3); ?> />
        <label for="childlayout-three-<?php echo $id; ?>"><i class="fa fa-commenting-o" aria-hidden="true"></i></label>
    </div>
</div>
<?php
    }
}

new MetaChildItemLayout();
