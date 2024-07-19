<?php
// Admin menu
function language_dropdown_admin_menu() {
   add_options_page(
      'Language Dropdown Settings',
      'Language Dropdown',
      'manage_options',
      'language-dropdown-settings',
      'language_dropdown_settings_page'
   );
}
add_action('admin_menu', 'language_dropdown_admin_menu');

// Admin settings page
function language_dropdown_settings_page() {
   if (!current_user_can('manage_options')) {
      return;
   }

   if (isset($_POST['submit'])) {
      $languages = array();
      $order = isset($_POST['language_order']) ? explode(',', $_POST['language_order']) : array();
      
      foreach ($order as $index) {
         if (isset($_POST['language_name'][$index]) && !empty($_POST['language_name'][$index])) {
               $languages[] = array(
                  'name' => sanitize_text_field($_POST['language_name'][$index]),
                  'code' => sanitize_text_field($_POST['language_code'][$index]),
                  'flag' => absint($_POST['language_flag'][$index]),
                  'url' => esc_url_raw($_POST['language_url'][$index])
               );
         }
      }
      
      update_option('language_dropdown_entries', $languages);
      echo '<div class="updated"><p>Settings saved.</p></div>';
   }

   $languages = get_option('language_dropdown_entries', array());
   
   wp_enqueue_media();
   wp_enqueue_script('jquery-ui-sortable');
   ?>
   <div class="wrap">
      <h1>Language Dropdown Settings</h1>
      <form method="post" action="">
         <table class="wp-list-table widefat fixed striped" id="sortable-table">
            <colgroup>
               <col style="width: 5%;">
               <col style="width: 20%;">
               <col style="width: 10%;">
               <col style="width: 25%;">
               <col style="width: 30%;">
               <col style="width: 10%;">
            </colgroup>
            <thead>
               <tr>
                     <th style="width: 5%;">Order</th>
                     <th style="width: 20%;">Language Name</th>
                     <th style="width: 10%;">Language Code</th>
                     <th style="width: 25%;">Flag Image</th>
                     <th style="width: 30%;">Language URL</th>
                     <th style="width: 10%;">Actions</th>
               </tr>
            </thead>
            <tbody id="language-entries">
               <?php
               if (empty($languages)) {
                  $languages[] = array('name' => '', 'code' => '', 'flag' => '', 'url' => '');
               }
               foreach ($languages as $index => $lang):
               ?>
               <tr data-index="<?php echo $index; ?>">
                  <td><span class="dashicons dashicons-move"></span></td>
                  <td><input type="text" name="language_name[<?php echo $index; ?>]" value="<?php echo esc_attr($lang['name']); ?>" required></td>
                  <td><input type="text" name="language_code[<?php echo $index; ?>]" value="<?php echo esc_attr($lang['code']); ?>" required></td>
                  <td class="flag-image-wrapper">
                        <div class="flag-image-container">
                           <?php 
                           $image_url = wp_get_attachment_image_url($lang['flag'], 'thumbnail');
                           if ($image_url) {
                              echo '<img src="' . esc_url($image_url) . '" alt="Flag" style="max-width: 50px; max-height: 50px;">';
                           }
                           ?>
                        </div>
                        <input type="hidden" name="language_flag[<?php echo $index; ?>]" value="<?php echo esc_attr($lang['flag']); ?>" class="flag-image-id">
                        <button type="button" class="button select-flag-image">Select Flag Image</button>
                  </td>
                  <td><input type="url" name="language_url[<?php echo $index; ?>]" value="<?php echo esc_url($lang['url']); ?>" required></td>
                  <td>
                        <button type="button" class="remove-language button">Remove</button>
                  </td>
               </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
         <input type="hidden" name="language_order" id="language-order" value="">
         <button type="button" id="add-language" class="button button-secondary">Add Language</button>
         <p class="submit">
               <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
         </p>
      </form>
   </div>
   <script>
   jQuery(document).ready(function($) {
      var frame;
      var nextIndex = <?php echo count($languages); ?>;

      function updateOrder() {
         var order = [];
         $('#language-entries tr').each(function() {
               order.push($(this).data('index'));
         });
         $('#language-order').val(order.join(','));
      }

      $('#sortable-table tbody').sortable({
         handle: '.dashicons-move',
         update: updateOrder,
         helper: function(e, ui) {
               ui.children().each(function() {
                  $(this).width($(this).width());
               });
               return ui;
         },
         start: function(e, ui) {
               ui.placeholder.height(ui.item.height());
         }
      });

      $('#add-language').on('click', function() {
         var newRow = `
               <tr data-index="${nextIndex}">
                  <td><span class="dashicons dashicons-move"></span></td>
                  <td><input type="text" name="language_name[${nextIndex}]" required></td>
                  <td><input type="text" name="language_code[${nextIndex}]" required></td>
                  <td>
                     <div class="flag-image-container"></div>
                     <input type="hidden" name="language_flag[${nextIndex}]" class="flag-image-id">
                     <button type="button" class="button select-flag-image">Select Flag Image</button>
                  </td>
                  <td><input type="url" name="language_url[${nextIndex}]" required></td>
                  <td><button type="button" class="remove-language button">Remove</button></td>
               </tr>
         `;
         $('#language-entries').append(newRow);
         nextIndex++;
         updateOrder();
      });

      $(document).on('click', '.remove-language', function() {
         $(this).closest('tr').remove();
         updateOrder();
      });

      $(document).on('click', '.select-flag-image', function() {
         var button = $(this);
         var imageContainer = button.siblings('.flag-image-container');
         var imageIdInput = button.siblings('.flag-image-id');

         if (frame) {
               frame.open();
               return;
         }

         frame = wp.media({
               title: 'Select Flag Image',
               button: {
                  text: 'Use this image'
               },
               multiple: false
         });

         frame.on('select', function() {
               var attachment = frame.state().get('selection').first().toJSON();
               imageContainer.html('<img src="' + attachment.url + '" alt="Flag" style="max-width: 50px; max-height: 50px;">');
               imageIdInput.val(attachment.id);
         });

         frame.open();
      });

      updateOrder();
   });
   </script>
   <?php
}

// Enqueue admin scripts and styles
function language_dropdown_admin_enqueue_scripts($hook) {
   if ($hook != 'settings_page_language-dropdown-settings') {
      return;
   }
   wp_enqueue_style('language-dropdown-admin-style', plugin_dir_url(__FILE__) . 'css/admin-style.css');
   wp_enqueue_style('dashicons');
}
add_action('admin_enqueue_scripts', 'language_dropdown_admin_enqueue_scripts');