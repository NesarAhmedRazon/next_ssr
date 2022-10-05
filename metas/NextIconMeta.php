<?php

namespace NEXTSSR;

if (!defined('ABSPATH')) {
    exit;
}

class NextIconMeta extends next_ssr
{
    public function __construct()
    {
        add_action('wp_nav_menu_item_custom_fields', [$this, 'next_ssr_nextIcon'], 10, 2);
        add_action('wp_update_nav_menu_item', [$this, 'next_ssr_nextIcon_update'], 10, 2);
    }
    public function next_ssr_nextIcon($id, $item)
    {
        if ($item->menu_item_parent != 0) {
            wp_nonce_field('nextIconMeta', 'nextIconMeta_nonce');
            $level = get_post_meta($id, 'level', true);
            if ($level == 3) {
                $icon = get_post_meta($id, 'nextIcon', true);
                var_dump($icon);
                $this->nextIcon_html($icon, $id);
            }
        }
    }
    public function next_ssr_nextIcon_update($menu_id, $item_id)
    {
        $nonce_name   = isset($_POST['nextIconMeta_nonce']) ? $_POST['nextIconMeta_nonce'] : '';
        $nonce_action = 'nextIconMeta';

        // Check if our nonce is set.
        if (!wp_verify_nonce($nonce_name, $nonce_action)) {
            return;
        }
        $level = get_post_meta($item_id, 'level', true);
        if ($level == 3) {
            if (isset($_POST['nextIcon'])) {
                update_post_meta($item_id, 'nextIcon', sanitize_text_field($_POST['nextIcon'][$item_id]));
            }
        }
    }
    public function nextIcon_html($icon_data, $id)
    {
        $icons = [
            [
                'name' => 'Users',
                'file' => 'assets/nextIcons/Users.svg',
            ],
            [
                'name' => 'GraduationCap',
                'file' => 'assets/nextIcons/GraduationCap.svg',
            ],
            [
                'name' => 'Boxes',
                'file' => 'assets/nextIcons/Boxes.svg',
            ],
            [
                'name' => 'HeadSet',
                'file' => 'assets/nextIcons/HeadSet.svg',
            ]
        ]
?>
<div class="field-description next_ssr_metabox">
    <div class="label">NextJs Icon</div>

    <div class="options">

        <?php
                $i = 0;
                foreach ($icons as $icon) {

                    echo '<div class="option">';
                    echo '<input class="radio_input" type="radio" hidden name="nextIcon[' . $id . ']" value="' . $icon['name'] . '" id="nextIcon-' . $i . '-' . $id . '" ' . checked($icon_data, $icon['name'], '', false) . '/>';
                    echo '<label for="nextIcon-' . $i . '-' . $id . '"><i class="icon-' . $icon['name'] . '"></i></label>';
                    echo '</div>';
                    $i++;
                } ?>
    </div>
</div>
<?php
    }
}
new NextIconMeta();
