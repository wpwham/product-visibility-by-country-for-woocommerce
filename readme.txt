=== Product Visibility by Country for WooCommerce ===
Contributors: wpwham
Tags: woocommerce, product, visibility, country, woo commerce
Requires at least: 4.4
Tested up to: 5.3
Stable tag: 1.3.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Display WooCommerce products by customer's country.

== Description ==

**Product Visibility by Country for WooCommerce** plugin lets you show/hide WooCommerce products depending on customer's country. Customer's country is detected automatically by IP.

There are options in plugin to hide products by:

* Hiding **catalog visibility** - will hide selected products in shop and search results. However product still will be accessible via direct link.
* Making products **non-purchasable** - will make selected products non-purchasable (i.e. product can't be added to the cart).
* Modifying products **query** - will hide selected products completely (including direct link).
* Hiding products **prices** - will hide prices for selected products (i.e. will make products non-purchasable). Also you can optionally replace price with your own custom message.
* Outputting customizable "**product is not available in your country**" message on single product and/or archives pages.
* Hiding in **WooCommerce Blocks** - will hide selected products in blocks created with "WooCommerce Blocks" plugin.

= Premium Version =

[Pro version](https://wpfactory.com/item/product-visibility-by-country-for-woocommerce/) has the options to:

* Hide **product terms** (i.e. product categories and tags) by country.
* Customize **redirect URL** for hidden products (i.e. set it different from 404 page).

= Feedback =

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/product-visibility-by-country-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Product Visibility by Country".

== Changelog ==

= 1.3.3 - 30/01/2020 =
* Dev - Puerto Rico added to the countries list.

= 1.3.2 - 24/01/2020 =
* Dev - Experimental `geolocate_via_api()` function added (can geolocate city, region, zip etc. by IP).
* Dev - Product Terms - Hide products - Minor code refactoring.
* WC tested up to: 3.9.

= 1.3.1 - 20/01/2020 =
* Dev - Advanced Options - "Disable URL" option added.
* Dev - Code refactoring.

= 1.3.0 - 31/12/2019 =
* Dev - Product Terms - "Hide products" option added.
* Dev - All admin settings input is sanitized now.
* Dev - Code refactoring.

= 1.2.1 - 25/11/2019 =
* Dev - General Options - 'Hide visibility in "WooCommerce Blocks"' option added.
* Dev - Advanced Options - Modify query - "Check main query only" (checkbox) option renamed to "Queries" (select) and "Check main and search queries only" value added.
* Dev - Advanced Options - Modify query - "Use simple redirect" option added.
* Dev - Plugin's fields added to WooCommerce export and import products tools.
* Tested up to: 5.3.

= 1.2.0 - 12/11/2019 =
* Dev - Optimization - `product_by_country_pre_get_posts()` - Checking for main query now (can be disabled via "Advanced Options > Check main query only").
* Dev - Optimization - `product_by_country_pre_get_posts()` - Checking for product query now (can be disabled via "Advanced Options > Check post type").
* Dev - Optimization - `get_country_by_ip()` - Saving country by IP now.
* Dev - Optimization - `is_product_visible()` - Saving products visibility in array now.
* Dev - Advanced Options - "Debug mode" option added.
* Dev - Admin Options - Select box type - Standard - "Currently selected" countries list added to the meta boxes.
* Dev - Admin settings split into sections and restyled.
* Dev - Code refactoring.
* WC tested up to: 3.8.

= 1.1.7 - 25/07/2019 =
* Tested up to: 5.2.

= 1.1.6 - 25/04/2019 =
* Dev - Extra safety checks added (for compatibility with "Popup Builder WooCommerce").
* Dev - "WC tested up to" updated.

= 1.1.5 - 12/04/2019 =
* Dev - General Options - "Hide price" options added.
* Dev - Admin settings descriptions updated.

= 1.1.4 - 09/04/2019 =
* Dev - General Options - "Info on single product page" and "Info on archives" options (and `[alg_wc_pvbc_translate]` shortcode) added.
* Dev - Admin settings minor restyling.

= 1.1.3 - 10/02/2019 =
* Dev - Modify query - "Redirect URL" option added.

= 1.1.2 - 08/02/2019 =
* Fix - Product Terms - "European Union" option fixed.
* Dev - Code refactoring.

= 1.1.1 - 04/01/2019 =
* Dev - "Invisible in countries" option moved to the free version.
* Dev - "Hide product terms" option added to Pro version.

= 1.1.0 - 12/11/2018 =
* Fix - Modify query - Possible pagination issue fixed.
* Dev - Modify query - "Modify widget query" option added.
* Dev - Admin Options - "Select box type" option added.
* Dev - "European Union" added as country selection.
* Dev - Admin settings restyled.
* Dev - Code refactoring.
* Dev - Plugin URI updated.

= 1.0.0 - 30/08/2017 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
