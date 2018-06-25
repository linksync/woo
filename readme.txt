=== linksync for WooCommerce and Vend ===
Contributors: linksync, jameshwartlopez, jaxolis, jguillano
Tags: linksync, woocommerce, download, downloadable, Vend
Requires at least: 4.4
Tested up to: 4.8
Stable tag: 2.5.9
License: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)

WooCommerce extension for syncing inventory and order data with Vend.

== Description ==
linksync for WooCommerce and Vend is a plugin that works in the backend to automatically sync products and orders between your Wordpress WooCommerce online store and Vend with REAL-TIME UPDATES.

**Product Syncing**
Add a new product, create new variants or make changes to an existing product in your online store, and see the change in the Vend moments later. Or make changes in Vend and have them sync to WooCommerce. The product syncing capabilities are very flexible so that they can be tailored to your business needs.

**Order Syncing**
linksync works so whenever you have a sales order created in Wordpress WooCommerce, it will automatically sync to Vend to updating your contacts and inventory in the process.


== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* WooCommerce 2.6.10 or greater
* PHP version 5.2.4 or greater (PHP 5.6 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of linksync for WooCommerce and Vend, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “linksync for WooCommerce and Vend” and click Search Plugins. Once you’ve found our plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading our eCommerce plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).



== Frequently Asked Questions ==
= What are the minimum plugin and software requirements for link sync for WooCommerce? =
* WooCommerce 2.2.0 or greater
* PHP 5.2.4 or greater
* curl support must be enabled on your server (usually enabled by default with most hosting companies).

= How often is data synced between Vend and my WooCommerce store? =
Product changes and orders are synced immediately, so within moments a change in one store should be reflected in the other, depending on what syncing options you have in place.

= Can images be synced between Vend and WooCommerce? =
Yes and no. We can sync images from Vend to WooCommerce, so if you have product image in Vend then we can sync them to products in WooCommerce, but Vend does not support images uploading at this time, so we are not able to sync or push images from WooCommerce to Vend.

= Do you support the latest version for WooCommerce and Vend? =
Yes, we support the latest version on both apps.

= Do I need to do a manual sync to get inventory for orders? =
No, you don’t. linksync automatically syncs your orders and products.

= What sort of customer service and support can I expect from linksync? =
Glad you asked. We provide support via chat, phone and email, and every person working at linksync is committed to providing first-rate customer service, so we’ll do what everything in our earthly powers to answer any questions or resolve any issues you might have.


== Screenshots ==

1. You'll see linksync settings in the admin menu.

== Changelog ==

= Version 2.5.18 - 25 June 2018 Release =

* Order settings - User can select either sync by order date or current date.
* Duplicate order issue when sync vend to woo

= Version 2.5.9 - 25 September 2017 Release =

* LV-171] - Woocommerce products is defaulting to out of stock when manages stock is disable and no product quantity is entered.
* [LV-175] - Plugin did not sync because product type was change to Simple in which woocommerce only gives simple
* [LV-177] - Main/Parent product is set to out of stock even though variations has stock

= Version 2.5.8 - 18 September 2017 Release =

**Bug**

* Fix order syncing vend where tax details is not set properly which causes incorrect order total when synced to vend
* Enhance image syncing and avoid using file_get_contents which will cause issue if server do not enable allow_url_fopen

**New Feature**

* Added option for woocommerce syncable product base on its product status
* Added coupon handling in order syncing for bundled products
* Increase or reduce stock when is on order page and clicks Reduce stock or Increase stock for the selected order item in WooCommerce
* Syncing vend to woo where since can be specified to a certain date. Helpful in case of integration halt and you need to get vend product changes you made yesterday or from the other day.
* You can select a date and import vend updates to your WooCommerce products.
* Syncing woocommerce products with what was the selected status, types, categories and tags

= Version 2.5.7 - 30 August 2017 Release =

**Bug**

* [LV-154] - Fixed blank WP admin content area with new Impreza theme version 4.6.2

**New Feature**

* [LV-140] - Please add search function on Connected Products and Orders page
* [LV-157] - Vend plugin support for WooCommerce Simple Subscription plugin
* [LV-158] - Vend plugin support for WooCommerce Product Bundles plugin
* Prevent product that has been set to allow backorders but notify customers from being set to draft


= Version 2.5.6 - 10 August 2017 Release =

**Bug**
* [LV-153] - Fixed product quantity syncing in Vend and WooCommerce so that it will update once the product quantity becomes zero after WooCommerce purchase.
* Fix clearing woocommerce attribute if attribute option is off
* Inactive Vend products should no longer sync to WooCommerce.

**Improvement**
* [LV-152] - Apply UI fix for theme elements / Woo elements that is messing the connected products / orders listings page
* Added Icon identifier in woocommerce product and order list if it synced to vend

= Version 2.5.5 - 20 July 2017 Release =

**Bug**
* [LV-143] - After updating the API Key via `Edit API Key` button in Configuration page, the linksync status is not refreshed
* [LV-145] - _visibility meta key is not set on simple and main product
* [LV-147] - Vend Setup wizard wrongfully indicates a valid API key from an unlimited plan as invalid or expired
* [LV-148] - Product variation and attributes gets wiped out in Woo after editing the variant prices in Vend UI
* [LV-149] - Modified Product Price on Woo order not sync to Vend

**New Feature**

* [LV-116] - To prevent items that allow backorders from being set to draft
* [LV-126] - Improve plugin look and feel for vend.
* [LV-141] - Add a Duplicate SKU detection and fixing feature

= Version 2.5.4 - 04 July 2017 Release =

Version 2.5.4 is an immediate release to fix the following bugs.

**Bugs**

* [LV-142] - Delete function should now work with product variation. When you delete a variant from WooCommerce, it deletes as well on Vend. Delete option should be enabled and product syncing type should not be "Vend to WooCommerce" for this function to work.
* Fixed Product variation attachment being synced to Vend that caused the creation of a new variation in Vend and unconfigured variation cloned in WooCommerce variable product.
* Fixed issue where the Connected Product and Connected Order list pagination is not working properly.

= Version 2.5.3 - 29 June 2017 Release =

Our latest version 2.5.3 improved the performance load in Configuration settings, Product Syncing settings and Order syncing settings. With this applied enhancement it is expected that load time is quicker than the previous versions.

We've also fixed reported bugs listed below:

**Bugs**

* [LV-133] - Fixed issue where customer records are duplicated in Vend.
* [LV-136] - Fixed issue where price updated in Woo is overwritten with recent price updated in Vend when product syncing type is set to Two Way.
* [LV-137] - Fixed issue where a variable product is not synced automatically to Vend
* [LV-139] - Fixed sorting issues on Connected Products and Orders page
* Fixed order syncing error when user email and name already exists in WooCommerce (order syncing type is Vend to WooCommerce)
* Fixed product inventory deducted twice if order syncing settings is WooCommerce to Vend.
* Fixed fatal error: Uncaught TypeError: Argument 1 passed to LS_Product_Helper::__construct() must be an instance of LS_Woo_Product.

**New Features**

* [LV-112] - Reflect the Vend's variants' UI order in WooCommerce.
* [LV-120] - Ajaxify Product Syncing Loading and Order Syncing settings.
* [LV-121] - Save Product Syncing and Order Syncing Settings using ajax.

= Version 2.5.2 - 16 June 2017 Release =

**Bug**

* [LV-130] - Fix not syncing all products
* [LV-131] - Fix unable to complete syncing
* [LV-132] - Fix inventory Syncing Issue

**New Feature**

* [LV-118] - Sync of Total Amount Woo Order after Refund
* [LV-119] - Trigger a call to the remote URL via the API
* [LV-123] - Create menu for linksync plugin
* [LV-124] - Show Connected product in vend and woocommerce
* [LV-125] - Show Connected order or order that was syncing both in vend or woocomerce.

= Version 2.5.1 - 01 June 2017 Release =

* Improve quantity syncing when product syncing type is Woo to vend with the quantity enabled and order syncing type is Vend to Woo.
* Added and used base Method that handles every product syncing from Vend to Woocommerce
* Use wordpress ajax in syncing products from vend and to vend and added progress bar.
* Fix Incorrect set up of product tax in the order if the product tax class is custom made. Thus results into incorrect order total in vend when synced.
* Added Hooks after sync. For more information [click here](https://help.linksync.com/hc/en-us/articles/115000699004).

= Version 2.4.20 - 03 May 2017 Release =

* Fix incorrect message for the remaining days of trial
* Fix HTTP ERROR 500 when syncing order to vend on WooCommerce 2.6.14
* Fix Product quantity not syncing properly in vend when an order is proccessed in woocommerce with order syncing type was set to "Vend to WooCommerce" and quantity option is enabled in product syncing settings.
* Added wizard

= Version 2.4.19 - 20 April 2017 Release =

* Update plugin updater
* Fix duplicate linksync menu item under WooCommerce
* Added zendesk chat support
* Added Support tab for submitting ticket support
* Updated api key validation to avoid saving invalid api key
* Added trial message account status

= Version 2.4.18 - 17 April 2017 Release =

* Fix tax is applied twice when order import is to vend due to WooCommerce Update(3.0.1)
* Update webhook info on linksync page load
* Remove Product Syncing deprecated errors due to WooCommerce 3.0.1

= Version 2.4.17 - 30 March 2017 Release =

* Fix Attribute not updating via WooCommerce user interface
* Fix product becoming draft if "Change product status in WooCommerce based on stock quantity" is disable

= Version 2.4.16 - 27 March 2017 Release =

* Fix "API Key is already exists" error on adding api key
* Fix Pending product not staying as pending when sync happens  from Vend to WooCommerce
* Fix Variant Attribute syncing from WooCommerce To Vend
* Fix redirection error on "Click here for more information" link under category option
* Fix Order syncing shipping price if shipping is not taxable
* Refactor product syncing from WooCommerce to Vend

= Version 2.4.15 - 08 March 2017 Release =

* Fix conflict in the vend selected outlets between 'two way' and 'vend to woocommerce' syncing option
* Fix admin email being used when order is synced to vend

= Version 2.4.14 - 16 February 2017 Release =

* Fix completed order export not exporting to vend
* Added new option to use Woocommerce Billing Address as Vend Physical Address
* Added new option to use Woocommerce Shipping Address as Vend Postal Address

= Version 2.4.13 - 13 February 2017 Release =

* Fix order syncing not triggering on some cases
* Fix duplicate order created in Vend when triggering order syncing to vend via Wordpress admin

= Version 2.4.12 - 02 February 2017 Release =

* Fix plugin updater error on php 7
* Fix vend outlet issue where it autoselects the first option(outlet) every plugin page reload
* Fix Vend to Woocommerce order import setting to 'no' even though the selected order syncing type is 'Vend to Woocommerce'

= Version 2.4.11 - 8 December 2016 Release =

* Fix Duplication of order to vend
* Added Notes on successfully created order to vend
* Added Logs on linksync triggered a sync

= Version 2.4.10 - 8 November 2016 Release =

* [LWE-411] - Fix Quantity in Vend becoming infinite when an order is made via woocommerce and the product losses all its quantity

= Version 2.4.9 - 18 October 2016 Release =

* Fix Delete option issue when enabled
* Fix error T_PAAMAYIM_NEKUDOTAYIM in plugin activation on other server
* Fix improperly setup zero variation name from vend
* Fix minor error

= Version 2.4.8 - 7 September 2016 Release =

* Fix sync reset stopping.
* Fix variant attribute not setting properly if attribute name has ", < , >  and '.
* Fix memory exhausted on clearing raw-log.txt

= Version 2.4.7 - 17 August 2016 Release =

* Fix woocommerce sale price not showing in front end
* Fix broken description when woocommerce product is being synced to vend
* Set up woocommerce product attribute properly and fix empty attribute
* Fix minor error

= Version 2.4.6 - 20 July 2016 Release =

* Fix T_FUNCTION  error on plugin activated on php version lower 5.3
* Fix empty attribute for variant products
* Fix Vend outlets selection that selects all outlets on linksync plugin loaded.
* Fix tax becoming other tax or no tax if woocommerce to vend syncing triggers.

= Version 2.4.5 - 14 July 2016 Release =

* Fix Vend tag issue when tag is having single qoutes
* Fix error when brand is enable that prevent vend product creation
* Fix javascript null error
* Tweak vend tag syncing that have forward slash and doesn't have a space beside it. Ven tags that has '/' without space beside it will be treated as single woocommerce product category
* Minor fixes
* Code Refactor

= Version 2.4.4 - 4 July 2016 Release =

* Automatic api key checking and updating update url on linksync plugin loaded. This fixes expired api key issue.
* Main class instantiation if woocommerce has been loaded
* [LWE-409] - Determine Vend tags parent and child relationship.
* [LWE-408] - Fix javascript tooltip conflict for Tomoko Theme
* [LWE-407] - Fix Vend quantity not syncing to Woocommerce on Product syncing type as Woocommerce to Vend and order syncing settings as Vend to Woocommerce .
* Remove mysql connection that causes error in syncing to vend
* Code refactor for price option
* Minor fixes

= Version 2.4.3 - 9 June 2016 Release =

* Fix issue product name having "\"
* Fix attribute option still changes woocommerce attribute on disable
* fix some undefined variable error

= Version 2.4.2 - 27 May 2016 Release =

* Fixes some error
* Add fix for Constants variable and undefined variable
* Fix some SQL error

= Version 2.4.1 - 13 April 2016 Release =

* fix 'api key is empty' on update of api key

= Version 2.4.0 - 8 April 2016 Release =

* mysql_* functions was removed
* Manage API Tab was removed
* UI Update for API key's modal
* Updating API key was transferred to Configuration Tab
* Plugin file was restructured

= Version 2.3.2 - 29 March 2016 Release =

* Fix variable product becoming out of stock if it has one variants out of stock.

= Version 2.3.1 - 7 March 2016 Release =

* Product category with ampersand sign is now syncing properly
* Fix issue in syncing product tags in vend to woo
* Fix Internal connection error

= Version 2.3.0 - 5 January 2016 Release =

* New products created in Woo with ‘published’ status not showing until the product is re-saved via WooCommerce.
* Orders in Woo not updating the correct Outlet in Vend in some instances.
* Product description not updating correctly in some instances.
* Updates to the linksync plugin can be done with a single click via the WordPress plugin page.

= Version 2.2.9 - 17 Dec 2015 Release =

* [LWE-402] - Internal Connection Error
* [LWE-397] - Sync incorrect product image
* [LWE-394] - Sync Removing product variants
* [LWE-390] - Extra Variant is created on linksync in some instances
* [LWE-388] - Some products not showing on WooCommerce site
* [LWE-387] - Update URL not accessible
* [LWE-380] - Product Price Not Displaying in some instances
* Images are syncing into year/month folders when this options is disabled.
* Performance improvements with image syncing
* Product description and images added in Woo getting removed on sync.

= Version 2.2.8 - 18 Sep 2015 Release =

* [LWE-385] - Incorrect Tax rate pushing to Vend
* [LWE-384] - Woo to Vend Product syncs incorrect product price in some cases
* [LWE-383] - Prices not syncing for Woo to Vend in some cases
* [LWE-382] - Disabled ' Change product status' in WooCommerce based on stock quantity not working
* [LWE-377] - Product is getting deleted in Woo when full sync is happening from Vend to Woo
* [LWE-376] - Draft Products with Variants in WooCommerce when set to Publish creates a duplicate product variant in Vend
* [LWE-375] - triggering of update URL will only process 50 products in some instances
* [LWE-374] - Product Image Sync - if Vend products do not have product image, then image in Woo is being removed
* [LWE-372] - Duplicate Product Images in Media Library

= Version 2.2.7 - 30 Jul 2015 Release =

* [LWE-370] - syncs stuck on 'starting....' for some users.

= Version 2.2.6 - 18 Jul 2015 Release =

* [LWE-366] - Incorrect URL path used by linksync to update WooCommerce with changes from Vend, introduced in 2.2.5
* [LWE-363] - New product from Vend not creating in WooCommerce if 'sync title' disabled.

= Version 2.2.5 - 15 Jul 2015 Release =

* [LWE-362] - Attributes and values having issues with foreign language (eg Norwegian characters ø å and the like).
* [LWE-360] - Adding empty attribute to products in WooCommerce in some instances.
* [LWE-358] - change order "created" time when sending order data to Vend.
* [LWE-353] - resolved some minor page/console errors.
* [LWE-344] - sync stuck on 'starting....' in some cases.

= Version 2.2.4 - 30 Jun 2015 Release =

* [LWE-354] - in some instances two full syncs were required to update product variants in Woo.
* [LWE-352] - Under some circumstance, and when a variable product had no SKU, that product was being deleted from Woo.
* [LWE-350] - product 'Catalog Visibility' was being updated when 'change product status' option was disabled.
* [LWE-341] - order POST from Woo to Vend getting json error and failing in some instances.
* [LWE-313] - improved image handling for sites with many and/or large sized images.

= Version 2.2.3 - 12 Jun 2015 Release =

* [LWE-333] - new orders in WooCommerce not updating inventory to Vend due to recent changes to WooCommerce
* [LWE-329] - new logic to determine if Vend prices include tax or not, depending on the country a Vend store is associated with
* [LWE-327] - sync says 'starting' but never starts
* [LWE-326] - resolved error messages on settings pages. eg. 'Outlet data not found'
* [LWE-325] - changes to Product Syncing UI to make option settings simpler
* [LWE-324] - wrong logic for order sync for outlet settings Woo to Vend
* [LWE-323] - Change product tax logic and UI settings to now default to WooCommerce tax settings for including or excluding tax in prices
* [LWE-319] - changed logic for discounts on order sync Woo to Vend
* [LWE-318] - Tax mapping issues for orders Woo to Vend
* [LWE-317] - add 'waiting/loading' gif to settings pages
* [LWE-316] - tax on shipping not included in orders synced to Vend
* [LWE-310] - Changes not syncing to Vend when using product "Quick Edit" in Woo
* [LWE-309] - price being deleted from Vend
* [LWE-308] - payment mapping for orders Woo to Vend updated
* [LWE-307] - Sync not completing
* [LWE-300] - error when attempting to disable quantity sync on Product Sync Settings
* [LWE-297] - Check permissions are correct on plugin folder (wp-content/plugins/linksync) and displaying message if they are incorrect
* [LWE-293] - Time offset in log is odd
* [LWE-292] - add Vend order number to WooCommerce orders synced from Vend
* [LWE-291] - improved logic for 'Change product status in WooCommerce based on stock quantity' option
* [LWE-289] - issue with underscores and non-standard characters in sku field in WooCommerce
* [LWE-286] - Orders are not syncing from Vend to WooCommerce in some instances
* [LWE-285] - issue with product 'disappearing' from Woo after sync from Vend
* [LWE-284] - log truncation logic to prevent linksync log getting too big
* [LWE-283] - fatal error on enabling extension in some instances
* [LWS-126] - wrong price being set when syncing from Vend to Woo in some instances when multiple pricebooks existed for the same product in Vend.
* [LWS-107] - incomplete tag data being retrieved from Vend in some instances

= Version 2.2.1 - 9 Apr 2015 Release =

* [LWE-168] - One Order in vend is displaying as two Orders in WooCommerce
* [LWE-249] - resolved issue where some products in WooCommerce were failing to sync to Vend
* [LWE-250] - No option to add new API key after we delete it
* [LWE-255] - Created option to not remove attribute types or values in WooCommerce when syncing from Vend.
* [LWE-256] - vend price incorrect on order export
* [LWE-259] - Client issues with attributes, and tax on order export
* [LWE-262] - Multiple Random Images from Vend to Woo
* [LWE-263] - Total order value is displaying as 0 in vend when discount is applied in woo
* [LWE-264] - custom product field being wiped on sync
* [LWE-266] - admin option to set attributes as 'visible on product page'
* [LWE-267] - Order export off by 1c
* [LWE-272] - remove forward slash and unsupported characters from WooCommerce sku field to ensure syncing to Vend will not fail.
* [LWE-277] - Displaying blank pop up box when user clicks on sync all product to vend
* [LWE-278] - Attributes are not syncing to vend from woocommerce
* [LWE-279] - no access to linksycn API causes site performance issues

= Version 2.1.9 beta - 19 Feb 2015 Release =

* [LWE-165] - order POST - discount applied twice
* [LWE-200] - 'Import as guest' is importing the billing address also
* [LWE-212] - Product prices not being set correctly in the database for 'on sale'
* [LWE-222] - Price is displaying even though it is disabled while syncing
* [LWE-224] - add actions/outcomes to log
* [LWE-228] - register_id not being populated on order POST
* [LWE-229] - Save changes button disabled
* [LWE-231] - Order number in order POST is not correct
* [LWE-232] - add 'settings' link to plugin page
* [LWE-233] - variant values still being modified on sync
* [LWE-235] - Add comment to order generated by Order GET
* [LWE-240] - Issue where attributes and values where getting out of sync.
* [LWE-243] - Option added to let user choose direction of initial product sync - from Vend to WooCommerce, or to Vend from WooCommerce.
* [LWE-245] - issue with 'copy to short description' option not saving.
* [LWE-246] - Add additional product images from Vend if more than one exists.
* Minor bug fixes.

= Version 2.1.8 beta - 28 Jan 2015 Release =

* Performance improvements
* [LWS-44] - Support for international characters in product description.
* [LWE-188] - Initial sync of product from Vend to WooCommerce no longer times out, and now includes a progress indicator.
* [LWE-199] - order POST to Vend not using payment ID from WooCommerce.
* [LWE-202] - Hide linksync admin settings if using less than WooCommerce 2.2.x.
* [LWE-203] - update order import to include 'shipping details' as well as billing address.
* [LWE-207] - sync to Vend - auto-add sku to product WooCommerce if none exists, using product ID.
* [LWE-208] - Euro decimal separator causing product updates to Vend to fail.
* [LWE-210] - Product sync settings - if more than one outlet, then outlet is now mandatory.
* [LWE-211] - keep settings after deactivation of module.
* [LWE-212] - 'Sale price' for WooCommerce product being overwritten on sync from Vend.
* [LWE-213] - Attribute values aren't keeping their cases.
* [LWE-214] - New admin option - sync Vend price to 'Regular' or 'Sale Price' field in WooCommerce.
* [LWE-217] - attribute name getting modified on sync from Vend to WooCommerce.
* [LWE-218] - Order POST primary_email for 'guest' checkout.

= Version 2.1.7 beta - 10 Jan 2015 Release =

* Added support for non-inventory products.
* Added a time offset if host/server time is different to linksync server time.
* Introduced a 'lock' to the update process to prevent simultaneous linksync updates happening at the same time in WooCommerce, and causing duplicate product and/or product mis-configuration.
* Fixes to 'Sync product to Vend' function, and now includes status of sync. eg. syncing product 118 of 354.
* Fixes to order import and export operations.
* [LWE-52] - Product images from Vend are sometimes not saved correctly.
* [LWE-170] - linksync API Key must be valid for Vend and WooCommerce.
* [LWE-196] - Product categories in WooCommerce removed from product, even if linksync category sync option not enabled.
* Minor bug fixes.

= Version 2.1.5 beta - 18 Dec 2014 Release =

* Image import refactoring to improve performance.
* [LWE-132] - Fix creation of duplicate orders in Vend on change of order status in WooCommerce.
* [LWE-129] - Create/update of orders in WooCommerce now updating inventory in Vend when Order Syncing 'WooCommerce to Vend' is disabled.

= Version 2.1.4 beta - 17 Dec 2014 Release =

* Order syncing enabled.
* [LWE-130] - Added an option 'Set new product to Pending' so that new product imported from Vend aren't automatically published.
* Minor bug fixes.

= Version 2.1.2 beta - 11 Dec 2014 - initial beta release =

* Initial beta release



