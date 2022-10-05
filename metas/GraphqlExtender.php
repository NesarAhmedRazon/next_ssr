<?php

namespace NEXTSSR;

if (!defined('ABSPATH')) {
    // Exit if accessed directly.
    exit;
}
final class GraphqlExtender extends next_ssr
{
    public function __construct()
    {
        add_action('graphql_register_types', [$this, 'addToGraphQl_object_type']);
        add_action('graphql_register_types', [$this, 'addToGraphQl_field']);
    }
    public function addToGraphQl_object_type()
    {
        register_graphql_object_type('ImgIcon', [
            'description' => __("Image Icon Detais", 'next_ssr'),
            'fields' => [
                'url' => [
                    'type' => 'String',
                    'description' => __('Icon Url', 'next_ssr'),
                ],
                'alt' => [
                    'type' => 'String',
                    'description' => __('Icon Alt text', 'next_ssr'),
                ],
                'title' => [
                    'type' => 'String',
                    'description' => __('Icon Title', 'next_ssr'),
                ],
                'height' => [
                    'type' => 'Integer',
                    'description' => __('Height', 'next_ssr'),
                ],
                'width' => [
                    'type' => 'Integer',
                    'description' => __('Width', 'next_ssr'),
                ]
            ]
        ]);
        register_graphql_object_type(
            'Layout',
            [
                'description' => __("2nd Level Column's Item Layout", 'next_ssr'),
                'fields' => [
                    'type' => [
                        'type' => 'String',
                        'description' => __('Item Type', 'next_ssr'),
                    ],
                    'display' => [
                        'type' => 'String',
                        'description' => __('Item Style', 'next_ssr'),
                    ],

                    'imgicon' => [
                        'type' => 'ImgIcon',
                        'description' => __('Icon URL', 'next_ssr'),
                    ],
                ]
            ]
        );
    }
    public function addToGraphQl_field()
    {
        register_graphql_field('MenuItem', 'layout', [
            /**
             * expand meta key Added to WP GraphQl
             */
            'type' => 'Layout',
            'description' => __('Set Layout for NEXT SSR Menu\'s 3rd Level Items', 'next_ssr'),
            'resolve' => function (\WPGraphQL\Model\MenuItem $menu_item, $args, $context, $info) {
                if (!isset($menu_item->databaseId)) {
                    return null;
                }
                $data['type'] = $childlayout = get_post_meta($menu_item->databaseId, 'childlayout', true);
                if ($childlayout == 'list') {
                    $data['display'] = get_post_meta($menu_item->databaseId, 'childlayout_listtype', true);
                } elseif ($childlayout == 'testimonial') {
                    $data['display'] = get_post_meta($menu_item->databaseId, 'childlayout_testimonial', true);
                } else {
                    $data['display'] = "";
                }
                $icon = get_post_meta($menu_item->databaseId, 'childlayout_image', true);
                if (!empty($icon)) {

                    $imgurl = $this->imageData($icon);
                    $data['imgicon'] = $imgurl;
                }

                return $data;
            }
        ]);
    }
    public function imageData($id)
    {
        $img_alt = get_post_meta($id, '_wp_attachment_image_alt', true);
        $img_data = get_post_meta($id, '_wp_attachment_metadata')[0];
        $img_url = wp_get_attachment_image_src($id, 'full')[0];
        $img = [
            'alt' => $img_alt,
            'title' => html_entity_decode(get_the_title($id)),
            'url' => $img_url,
            'height' => $img_data['height'],
            'width' => $img_data['width']
        ];
        return $img;
    }
}

new GraphqlExtender();
