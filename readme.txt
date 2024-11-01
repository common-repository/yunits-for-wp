=== Yunits for WP ===
Author: Yard Digital Agency
Author URI: https://www.yard.nl
Contributors: yarddigitalagency
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 6.0
Requires PHP: 8.1
Stable tag: 1.12.0
Tags: yunits, community, integrate community
Tested up to: 6.6.2

Integrate a Yunits Community with WordPress and vice versa.

== Description ==

Integrate your [Yunits Community](https://www.yunits.com/en/) with WordPress and combine the best of both worlds.

## News & Agendas on Your WordPress Site

This plugin enables you to retrieve News and Agenda items from your Yunits Community and display them on your WordPress site. These items include metadata, allowing you to create gated content using the Yunits roles specified in the metadata.

If you decide to also synchronise Yunits Theme's (see Knowledge Bases below) it will also automatically connect them as taxonomy terms which you can use to create filters in FacetWP for example.

## Knowledge Bases

If you use FacetWP, you can retrieve Yunits Themes from your Yunits Community and synchronize them as taxonomies to your chosen Post Types. This allows you to create Knowledge Base posts in WordPress, enrich them with themes, and ensure your Yunits Community can read the FacetWP API to synchronize those items.

## Single Sign On via OpenID Connect

While not part of this plugin, we provide recommendations and instructions on setting up Single Sign-On using OpenID Connect.

== Installation ==

1. Upload `yunits-for-wp` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to your settings in the WordPress admin: Settings > Yunits For WP and insert your credentials

== Screenshots ==

== Frequently Asked Questions ==

== Changelog ==

= 1.12.0: Oct 28, 2024 =

* Add: set roles and item_roles on user meta if role scope available on OIDC
* Add: add type meta field on theme taxonomy

= 1.11.1: Oct 22, 2024 =

* Change: performance optimizations post_update
* Change: performance optimizations avoid redundant database call
* Change: increase news and agenda import limit to 100

= 1.11.0: Oct 16, 2024 =

* Add: yfw_featured_image to knowledge base endpoints to circumvent an additional api call
* Add: assign which post types need to be synchronized back to Yunits from the settings page
* Change: scale down import batch size since the recurrence can be set

= 1.10.0: Oct 15, 2024 =

* Add: yfw_manage_settings capability for managing the settings page
* Add: settings field to settings page for selecting different cron intervals
* Add: register more cron intervals

= 1.9.0: Sep 23, 2024 =

* Add: setting to configure the timeout of API requests

= 1.8.0: Sep 23, 2024 =

* Add: shortcode to display the api base url on the front-end

= 1.7.1: Sep 04, 2024 =

* Fix: check if isEnabled is set on settings array before usage

= 1.7.0: Aug 21, 2024 =

* Change: deprecate houseNumber, streetName and addressAddition with street in venue metadata

= 1.6.1: Aug 20, 2024 =

* Fix: base64 encoded images in post_content

= 1.6.0: Aug 20, 2024 =

* Add: allow inline images in agenda and news items imported from Yunits
* Add: web-detail-comments as metadata property to agenda and news as a direct link to comments
* Add: themes as metadata property on news items
* Add: automatically synchronise Yunits themes as taxonomy terms if enabled as knowledge base
* Change: improve code formatting following code standards on the exists trait
* Change: improve import performance and pause / restart indexers for FacetWP and SearchWP after importing

= 1.5.0: Aug 14, 2024 =

* Add: venue metadata to agenda cpt

= 1.4.0: Aug 13, 2024 =

* Add: update term if it already exists (allows for changing names)
* Add: add custom capabilities to agenda and news cpts (to hide by default)
* Add: typeName and themes as metadata properties on agenda items
* Change: increase batch size from 20 to 100
* Fix: broken import due to invalid to_post_array check

= 1.3.0: Aug 08, 2024 =

* Add: lead text as a metadata field to news items
* Change: use published date instead of created date as the post_data for news and agendas
* Change: logging text: import finished to import started

= 1.2.2: Aug 07, 2024 =

* Fix: PHP warning

= 1.2.1: Aug 07, 2024 =

* Fix: missed tag

= 1.2.0: Aug 07, 2024 =

* Add: wp crons schedule and unschedule based on activated modules (imports run twice daily)
* Change: improved importer code, imports can be run via wp cli, wp cron and via triggers on the plugin settings
* Change: update readme

= 1.1.0: Aug 06, 2024 =

* Change: keep action scheduler as a vendor dependency
* Change: rename the oidc scopes

= 1.0.1: Aug 05, 2024 =

* Change: improve deployment
* Fix: include required dist files

= 1.0.0: Aug 05, 2024 =

* Init: first release!
