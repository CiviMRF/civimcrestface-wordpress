# CiviCRM McRestFace (CMRF) connector for Wordpress

This provides a connector for Wordpress to access remote CiviCRM databases using [CMRF](https://github.com/CiviMRF)

This plugin does only provide a way to connect to a remote or a local CiviCRM.
After installing this plugin you can manage connections under Settings --> CiviCRM McRestFace Connections

Plugins usings the CiviCRM McRestFace connector:

* [Caldera Forms integration with CiviCRMs Form Processor extension](https://github.com/CiviMRF/cf-civicrm-formprocessor)

## How to install

Open a terminal to your wordpress installation.

```
cd wp-content/plugins
git clone https://github.com/CiviMRF/wpcmrf.git
cd wpcmrf
composer install
```

Then login in the admin of your wordpress installation and activate this plugin.

