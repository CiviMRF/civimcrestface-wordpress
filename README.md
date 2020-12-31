# Wordpress Connector to CiviCRM with CiviMcRestFace

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

## How to install

Open a terminal to your wordpress installation.

```
cd wp-content/plugins
git clone https://github.com/CiviMRF/civimcrestface-wordpress.git
cd wpcmrf
composer install
```

Then login in the admin of your wordpress installation and activate this plugin.

# Contributing

The code of this plugin is published and maintained at [Github](https://github.com/CiviMRF/civimcrestface-wordpress/).
The plugin is also published at [Wordpress.org](https://wordpress.org/plugins/cf-civicrm-formprocessor)
and this requires that we submit each release to the [Wordpress SVN](https://plugins.svn.wordpress.org/cf-civicrm-formprocessor)

**Workflow for development**

1. Fork the repository at Github
1. Create a new branch for the functionality you want to develop, or for the bug you want to fix.
1. Write your code and test it, once you are finished push it to your fork.
1. Create a Pull Request at Github to notify us to merge your changes.

**Workflow for creating a release**

Based on the instruction from [Learn with Daniel](https://learnwithdaniel.com/2019/09/publishing-your-first-wordpress-plugin-with-git-and-svn/)

1. Update `readme.txt` with the new version number (also update the Changelog section)
1. Update `wpcmrf.php` with the new version number
1. Create a new version at [Github](https://github.com/CiviMRF/civimcrestface-wordpress/).
1. To publish the release at Wordpress Plugin directory follow the following steps:
    1. Create a temp directory: `mkdir civimcrestface-wordpress-tmp`
    1. Go into this directory: `cd civimcrestface-wordpress-tmp`
    1. Do an SVN checkout into SVN directory: `svn checkout --depth immediates https://plugins.svn.wordpress.org/connector-civicrm-mcrestface svn`
    1. Clone the Github repository into Github directory: `git clone https://github.com/CiviMRF/civimcrestface-wordpress.git github`
    1. Go into the Github directory: `cd github`
    1. Checkout the created release (in our example 1.0.0): `git checkout 1.0.0`
    1. Go into the svn directory: `cd ../svn`
    1. Copie the files from github to SVN: `rsync -rc --exclude-from="../github/.distignore" "../github/" trunk/ --delete --delete-excluded`
    1. Add the files to SVN: `svn add . --force`
    1. Tag the release in SVN (in our example 1.0.0): `svn cp "trunk" "tags/1.0.0"`
    1. Now submit to the Wordpress SVN with a message: `svn ci -m 'Adding 1.0.0'`

# License

The plugin is licensed under [AGPL-3.0](LICENSE.txt).

