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
                $icon = get_post_meta($id, 'childlayout_image', true);
                $this->childlayout_radio_html($childlayout, $id);
                $this->childlayout_listType_html($listType, $id);
                $this->childlayout_image_html($icon, $id);
            }
            if ($level == 3) {
                $icon = get_post_meta($id, 'childlayout_image', true);
                $this->childlayout_image_html($icon, $id);
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

        if (isset($_POST['childlayout_image'])) {
            if (isset($_POST['childlayout_image'][$item_id])) {
                update_post_meta($item_id, 'childlayout_image', sanitize_text_field($_POST['childlayout_image'][$item_id])); // Input var okay.
            } else {
                delete_post_meta($item_id, 'childlayout_image');
            }
        } else {
            delete_post_meta($item_id, 'childlayout_image');
        }
    }

    // Html For Meta Boxes
    public function childlayout_radio_html($value, $id)
    {
?>

<div class="field-description half next_ssr_metabox">
    <div class="label">Children Layout</div>
    <div class="next_ssr_container">
        <input type="radio" id="childlayout-one-<?php echo $id; ?>" name="childlayout[<?php echo $id; ?>]" value="list"
            checked <?php checked($value, "list"); ?> />
        <label for="childlayout-one-<?php echo $id; ?>"><i class="fa fa-list-alt" aria-hidden="true"></i></label>
        <input type="radio" id="childlayout-two-<?php echo $id; ?>" name="childlayout[<?php echo $id; ?>]" value="image"
            <?php checked($value, "image"); ?> />
        <label for="childlayout-two-<?php echo $id; ?>"><i class="fa fa-id-card-o" aria-hidden="true"></i></label>
        <input type="radio" id="childlayout-three-<?php echo $id; ?>" name="childlayout[<?php echo $id; ?>]"
            value="testimonial" <?php checked($value, "testimonial"); ?> />
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
    <div class="next_ssr_container">
        <input type="radio" id="childlayout_listtype-one-<?php echo $id; ?>"
            name="childlayout_listtype[<?php echo $id; ?>]" value="unordered" checked
            <?php checked($value, "unordered"); ?> />
        <label title="Un-Ordered" for="childlayout_listtype-one-<?php echo $id; ?>"><i class="fa fa-list-ul"
                aria-hidden="true"></i></label>
        <input type="radio" id="childlayout_listtype-two-<?php echo $id; ?>"
            name="childlayout_listtype[<?php echo $id; ?>]" value="ordered" <?php checked($value, "ordered"); ?> />
        <label title="Ordered" for="childlayout_listtype-two-<?php echo $id; ?>"><i class="fa fa-list-ol"
                aria-hidden="true"></i></label>
        <input type="radio" id="childlayout_listtype-three-<?php echo $id; ?>"
            name="childlayout_listtype[<?php echo $id; ?>]" value="icon" <?php checked($value, "icon"); ?> />
        <label title="Icon" for="childlayout_listtype-three-<?php echo $id; ?>"><i class="fa fa-th-list"
                aria-hidden="true"></i></label>
        <input type="radio" id="childlayout_listtype-four-<?php echo $id; ?>"
            name="childlayout_listtype[<?php echo $id; ?>]" value="none" <?php checked($value, "none"); ?> />
        <label title="Icon" for="childlayout_listtype-four-<?php echo $id; ?>"><i class="fa fa-bars"
                aria-hidden="true"></i></label>
    </div>
</div>
<?php
    }

    public function childlayout_image_html($icon, $id)
    {
        $x = get_the_title($icon);
        var_dump(html_entity_decode($x));
        $img = "";
        if (!empty($icon)) {
            $imgurl    = wp_get_attachment_image_src($icon, 'full');
            $hide = '';
            $img = '<img class="icon" src="' . $imgurl[0] . '"/>';
        } else {
            $img = 'Choose Icon/Image';
            $hide = 'hide';
        }

    ?>
<script>
jQuery(document).ready(function() {
    var $ = jQuery;
    if ($('.imgPrev').length > 0) {
        if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
            $('.imgPrev').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                wp.media.editor.send.attachment = function(props, attachment) {
                    button.parent().children('.childlayout_image').val(attachment.id);
                    button.html('<img class="icon" src="' + attachment
                        .url + '"/>');
                    button.parent().children('.icondel').removeClass('hide');

                };
                wp.media.editor.open(button);
                return false;
            });

        }
    }

    $('.icondel').on('click', function(s) {
        s.preventDefault();
        $(this).parent().children('.imgPrev').html('Choose Icon/Image');
        $(this).parent().children('.childlayout_image').val('');
        $(this).addClass('hide');
    });
});
</script>

<div class="field-description next_ssr_metabox">
    <div class="label">Icon</div>
    <div class='next_ssr_container set_custom_images' id="container_<?php echo $id; ?>">
        <input hidden type="number" value="<?php echo $icon; ?>" class="childlayout_image imageField"
            name="childlayout_image[<?php echo $id; ?>]" id="childlayout_image-<?php echo $id; ?>">
        <div class='imgPrev btn'><?php echo $img; ?></div>
        <div class='icondel btn <?php echo $hide; ?>' id="<?php echo $id; ?>"><i class="fa fa-trash-o"
                aria-hidden="true"></i></div>
    </div>


</div>
<?php
    }
}

new MetaChildItemLayout();
