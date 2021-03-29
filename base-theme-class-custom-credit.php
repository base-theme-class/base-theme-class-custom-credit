<?php
/*
+----------------------------------------------------------------------
| Copyright (c) 2018,2019,2020 Genome Research Ltd.
| This is part of the Wellcome Sanger Institute extensions to
| wordpress.
+----------------------------------------------------------------------
| This extension to Worpdress is free software: you can redistribute
| it and/or modify it under the terms of the GNU Lesser General Public
| License as published by the Free Software Foundation; either version
| 3 of the License, or (at your option) any later version.
|
| This program is distributed in the hope that it will be useful, but
| WITHOUT ANY WARRANTY; without even the implied warranty of
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
| Lesser General Public License for more details.
|
| You should have received a copy of the GNU Lesser General Public
| License along with this program. If not, see:
|     <http://www.gnu.org/licenses/>.
+----------------------------------------------------------------------

# Author         : js5
# Maintainer     : js5
# Created        : 2018-02-09
# Last modified  : 2018-02-12

 * @package   BaseThemeClass/CustomCredit
 * @author    JamesSmith james@jamessmith.me.uk
 * @license   GLPL-3.0+
 * @link      https://jamessmith.me.uk/base-theme-class/
 * @copyright 2018 James Smith
 *
 * @wordpress-plugin
 * Plugin Name: Website Base Theme Class - Custom Credit
 * Plugin URI:  https://jamessmith.me.uk/base-theme-class/
 * Description: Adds a custom credit to images.
 * Version:     0.1.0
 * Author:      James Smith
 * Author URI:  https://jamessmith.me.uk
 * Text Domain: base-theme-class-locale
 * License:     GNU Lesser General Public v3
 * License URI: https://www.gnu.org/licenses/lgpl.txt
 * Domain Path: /lang
*/

namespace BaseThemeClass;

class CustomCredit {
  var $self;
  function __construct( $self ) {
    $this->self = $self;
    add_filter( 'attachment_fields_to_edit',           [ $this, 'custom_media_add_credit'          ], null, 2 );
    add_action( 'edit_attachment',                     [ $this, 'custom_media_save_attachment'     ] );
    add_action( 'wp_ajax_save-attachment-compat',      [ $this, 'custom_media_save_attachment'     ], PHP_INT_MAX );
    add_filter( 'get_image_tag',                       [ $this, 'include_credit_as_data_attribute' ], 0, 4);
//  add_filter( 'wp_get_attachment_image_attributes',  [ $this, 'include_credit_as_data_attribute' ], 0, 4);

  }
  function custom_media_add_credit( $form_fields, $post ) {
    $field_value = get_post_meta( $post->ID, 'custom_credit', true );
    $form_fields['custom_credit'] = array(
        'value' => $field_value ? $field_value : '',
        'label' => __( 'Credit' ),
        'helps' => __( 'Enter credit details for image' ),
        'input'  => 'text'
    );
    return $form_fields;
  }

  function include_credit_as_data_attribute( $html, $id, $alt, $title ) {
    $t = get_post_meta( $id );
    $credit = $t['custom_credit'];
    if( is_array( $credit ) ) {
      $credit = $credit[0];
    }
//  $size = $t['image_size'];  wp_get_attachment_image_src
    return $credit ? preg_replace( '/<img /','<img data-credit="'.HTMLentities($credit).'" ', $html ) : $html;
  }

  function custom_media_save_attachment( $attachment_id ) {
    if ( isset( $_REQUEST['attachments'][ $attachment_id ]['custom_credit'] ) ) {
      $custom_credit = $_REQUEST['attachments'][ $attachment_id ]['custom_credit'];
      update_post_meta( $attachment_id, 'custom_credit', $custom_credit );
//      $image_size    = $_REQUEST['attachments'][ $attachment_id ]['image_size'];
//      update_post_meta( $attachment_id, 'image_size', $image_size );
    }
  }

}