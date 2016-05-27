# WP-resmio-API 
Contributors: Renaldo Weiser (resmio GmbH)

Tags: shortcode, resmio, usability, button, widget, reservation, online, restaurant, social, page, posts
 
Tested up to: 4.5.2

Stable tag: 1.2


**With this API, you can add relevant information about your restaurant in your WordPress website. If you use the widget or button, your guests can make online reservations.**


### Description
resmio provides you with an online reservation system software for your restaurant that allows you to manage all reservations received in your restaurant quickly and easily. In order to recieve reservations from your website you have to integrate the codesnippet provided by resmio either for the widget itself or for a reservation button (which then loads the widget).

With this template you do not have to manually add these codesnippets to your site. Instead you can type shortcodes directly into the wysiwyg-editor and or insert the shortcodes through a menu button in the toolbar of the TinyMCE editor (only in visual mode).


**The shortcodes are:**

- **[resmio-button]** > for the resmio button
- **[resmio-widget]** > for the resmio widget
- **[resmio-name]** > for the name of the restaurant
- **[resmio-street]** > for the street of the restaurant
- **[resmio-zipcode]** > for the zip code of the restaurant
- **[resmio-city]** > for the city of the restaurant
- **[resmio-address]** > for the address (street, zipcode & city) of the restaurant
- **[resmio-phone]** > for the phone of the restaurant
- **[resmio-email]** > for the email of the restaurant
- **[resmio-contact]** > for the contact (phone & email with icon) of the restaurant
- **[resmio-facebook]** > for the Facebook URL of the restaurant
- **[resmio-googleplus]** > for the Google+ URL of the restaurant
- **[resmio-social]** > for the social informations (Facebook & Google+ with icon) of the restaurant
- **[resmio-description]** > for the description of the restaurant
- **[resmio-openinghours]** > for the opening hours of the restaurant


### Why Use It?
Instead of adding whole codesnippets for your resmio button/widget and informations for restaurant to your website you can use shortcodes.

### Getting Started
#### You have 2 options to use the WordPress resmio API.
#### First option: Integration in own Theme
1. Copy the following folder and files in your theme:
 - `/resmio-api/` to `/yourTheme/`
 - `languages/en_US.mo` to `/yourTheme/languages/`
 - `languages/en_US.po` to `/yourTheme/languages/`
 - `languages/resmio_i18n.pot` to `/yourTheme/languages/`

2. Add the following Code in the `functions.php` of your theme
```php
<?php
require_once ('resmio-api/resmio-api.php');
?>
```
3. Add the following Code in the `index.php` outside the PHP Code of your theme
```html
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/resmio-api/css/resmio-style.css">
```
####Second option: If you don't use a theme, then use these instructions.
1. Add the **wp-resmio-api** folder to your **wp-content/themes** folder.
2. Activate the theme. **WP-Admin -> Appearance -> Themes**


### How To Use
1. Go to **settings > resmio api** and insert your resmio ID and confirm with **Import data**. 

2. You can add or edit the fields about your restaurant.

3. Confirm the settings with **Save data** for your WordPress Website.

4. Go to your **pages** or **posts** and insert the shortcode you want to use.

 **There are two ways to insert the shortcodes:**

4.1. Type the shortcodes `[resmio-button]` and/or `[resmio-widget]` directly into the wysiwyg editor.

4.2. Use the menu button **resmio** in the toolbar of your wysiwyg-editor (only in visual mode).


### Compatibility
Tested on 4.5.1 and 4.5.2


### Support
If you encounter any problem with this template please contact resmio GmbH via email support@resmio.com


### Disclaimer
I do not accept any responsibility for any damages or losses, direct or indirect, that may arise from using the plugin or these instructions. This software is provided as is, with absolutely no warranty.


### Changelog

* **1.2 (16 May. 2016)** Add new layout for settings page and add install instructions to ReadMe
* **1.1 (26 Apr. 2016)** fix english/german translation
* **1.0 (08 Apr. 2016)** Add shortcode preview page for frontend
* **0.12 (02 Mar. 2015)** Add WP resmio options page for backend
* **0.1 (24 Sep. 2015)** Initial Release 