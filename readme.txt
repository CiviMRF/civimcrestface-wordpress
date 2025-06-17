=== Connector to CiviCRM with CiviMcRestFace ===
Contributors: jaapjansma, artfulrobot, kainuk
Donate link: https://github.com/CiviMRF/civimcrestface-wordpress
Tags: CiviCRM, api, connector, rest
Requires at least: 5.2
Tested up to: 6.4
Requires PHP: 7.2
Stable tag: 1.0.11
License: AGPL-3.0

Provides an API connector to a local or remote CiviCRM installation. This connector could be used by other plugins. Funded by Artfulrobot, CiviCoop, civiservice.de, Bundesverband Soziokultur e.V., Article 19

== Description ==

This plugin provides a connector to connect to a local or remote CiviCRM. This connector can then be reused by other plugins such as the [Integration of CiviCRM's Form Processor with Caldera Forms](https://wordpress.org/plugins/cf-civicrm-formprocessor/)

**Configuration**

Configuration can be done under **Settings > CiviCRM McRestFace Connections**.

**Plugins using the CiviCRM McRestFace Connector**

* [Integration of CiviCRM's Form Processor with Caldera Forms](https://wordpress.org/plugins/cf-civicrm-formprocessor/)

**Funded by**

* [Artfulrobot](https://artfulrobot.uk)
* [CiviCooP](https://www.civicoop.org)
* [Civiservice.de GmbH](https://civiservice.de/)
* [Bundesverband Soziokultur e.V.](https://www.soziokultur.de/)
* [Article 19](https://www.article19.org/)

== FAQ ==

= How can I report busg? =

You can report bugs at https://github.com/CiviMRF/civimcrestface-wordpress

If you want to notify a (confidential) security bug you can do so by sending an e-mail to jaap.jansma@civicoop.org

= How can I contribute? =

You can contribute to the project by submitting Pull Requests at https://github.com/CiviMRF/civimcrestface-wordpress

= How can I report security bugs? =

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team helps validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/4f50a738-600d-4e04-8bd1-b28312abae76)


== Changelog ==

1.0.11: Added check for api4 and added permission check for clearing caches
1.0.10: Added FAQ to readme
1.0.9: Fixed input sanitization
1.0.8: Compatibility with Wordpress 6.4
1.0.7: Fixed notice in CiviMRF Abstract Core
1.0.6: Fixed regression bug
1.0.5: Added CurlAuthX connector.
1.0.4: Added multi site activation.
1.0.3: Added multi site activation.
1.0.2: Added multi site activation.
1.0.1: Added InnoDB to the database tables. This gave sometimes an installation error.
1.0.0: First version.
