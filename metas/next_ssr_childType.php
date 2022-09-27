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
        register_graphql_object_type(
            'ChildLayout',
            [
                'description' => __("2nd Level Column's Item Layout", 'next_ssr'),
                'fields' => [
                    'type' => [
                        'type' => 'String',
                        'description' => __('Item Type', 'next_ssr'),
                    ],
                    'prop' => [
                        'type' => 'String',
                        'description' => __('Item Style', 'next_ssr'),
                    ],
                ]
            ]
        );
        register_graphql_field('MenuItem', 'childlayout', [
            /**
             * expand meta key Added to WP GraphQl
             */
            'type' => 'ChildLayout',
            'description' => __('Set Layout for NEXT SSR Menu\'s 3rd Level Items', 'next_ssr'),
            'resolve' => function (\WPGraphQL\Model\MenuItem $menu_item, $args, $context, $info) {
                if (!isset($menu_item->databaseId)) {
                    return null;
                }
                $data['type'] = $childlayout = get_post_meta($menu_item->databaseId, 'childlayout', true);
                if ($childlayout == 'list') {
                    $data['prop'] = get_post_meta($menu_item->databaseId, 'childlayout_listtype', true);
                    $data['icon'] = get_post_meta($menu_item->databaseId, 'childlayout_listIcon', true);
                } elseif ($childlayout == 'image') {
                    $data['prop'] = get_post_meta($menu_item->databaseId, 'childlayout_image', true);
                } elseif ($childlayout == 'testimonial') {
                    $data['prop'] = get_post_meta($menu_item->databaseId, 'childlayout_testimonial', true);
                } else {
                    $data['prop'] = "";
                }


                return $data;
            }
        ]);
    }
    public function next_ssr_levelthree_type($id, $item)
    {
        if ($item->menu_item_parent != 0) {
            wp_nonce_field('l3meta', 'l3meta_nonce');
            $children = get_post_meta($id, 'children', true);
            $level = get_post_meta($id, 'level', true);
            if ($level == 2) {
                $childlayout = get_post_meta($id, 'childlayout', true);
                $listType = get_post_meta($id, 'childlayout_listtype', true);
                $this->childlayout_radio_html($childlayout, $id);
                $this->childlayout_listType_html($listType, $id);
            }
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

        if (isset($_POST['childlayout_listtype'])) {
            if (isset($_POST['childlayout_listtype'][$item_id])) {
                update_post_meta($item_id, 'childlayout_listtype', sanitize_text_field($_POST['childlayout_listtype'][$item_id])); // Input var okay.
            } else {
                delete_post_meta($item_id, 'childlayout_listtype');
            }
        } else {
            delete_post_meta($item_id, 'childlayout_listtype');
        }
    }

    // Html For Meta Boxes
    public function childlayout_radio_html($value, $id)
    {
        ?>

        <div class="field-description half next_ssr_metabox">
            <div class="label">Children Layout</div>
            <div class="radio_field">
                <input type="radio" id="childlayout-one-<?php echo $id; ?>" name="childlayout[<?php echo $id; ?>]" value="list" checked <?php checked($value, "list"); ?> />
                <label for="childlayout-one-<?php echo $id; ?>"><i class="fa fa-list-alt" aria-hidden="true"></i></label>
                <input type="radio" id="childlayout-two-<?php echo $id; ?>" name="childlayout[<?php echo $id; ?>]" value="image" <?php checked($value, "image"); ?> />
                <label for="childlayout-two-<?php echo $id; ?>"><i class="fa fa-id-card-o" aria-hidden="true"></i></label>
                <input type="radio" id="childlayout-three-<?php echo $id; ?>" name="childlayout[<?php echo $id; ?>]" value="testimonial" <?php checked($value, "testimonial"); ?> />
                <label for="childlayout-three-<?php echo $id; ?>"><i class="fa fa-commenting-o" aria-hidden="true"></i></label>
            </div>
        </div>
    <?php
    }

    public function childlayout_listType_html($value, $id)
    {
        ?>

        <div class="field-description half next_ssr_metabox">
            <div class="label">List Type</div>
            <div class="radio_field">
                <input type="radio" id="childlayout_listtype-one-<?php echo $id; ?>" name="childlayout_listtype[<?php echo $id; ?>]" value="unordered" checked <?php checked($value, "unordered"); ?> />
                <label title="Un-Ordered" for="childlayout_listtype-one-<?php echo $id; ?>"><i class="fa fa-list-ul" aria-hidden="true"></i></label>
                <input type="radio" id="childlayout_listtype-two-<?php echo $id; ?>" name="childlayout_listtype[<?php echo $id; ?>]" value="ordered" <?php checked($value, "ordered"); ?> />
                <label title="Ordered" for="childlayout_listtype-two-<?php echo $id; ?>"><i class="fa fa-list-ol" aria-hidden="true"></i></label>
                <input type="radio" id="childlayout_listtype-three-<?php echo $id; ?>" name="childlayout_listtype[<?php echo $id; ?>]" value="icon" <?php checked($value, "icon"); ?> />
                <label title="Icon" for="childlayout_listtype-three-<?php echo $id; ?>"><i class="fa fa-th-list" aria-hidden="true"></i></label>
                <input type="radio" id="childlayout_listtype-four-<?php echo $id; ?>" name="childlayout_listtype[<?php echo $id; ?>]" value="none" <?php checked($value, "none"); ?> />
                <label title="Icon" for="childlayout_listtype-four-<?php echo $id; ?>"><i class="fa fa-bars" aria-hidden="true"></i></label>
            </div>
        </div>
<?php
    }
}

new MetaChildItemLayout();
