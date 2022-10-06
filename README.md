
# Module Amage Cms

[![installs on Packagist](https://img.shields.io/packagist/dt/amage/module-cms)](https://packagist.org/packages/amage/module-cms)

    ``amage/module-cms``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)


## Main Functionalities
Import CMS Blocks and Pages

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Amage`
 - Enable the module by running `php bin/magento module:enable Amage_Cms`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

## Configuration

## Specifications
 - Support 2.2.x 2.3.x 2.4.x
 - Controller
	- adminhtml > import/index/index
![Magento-Import-CMS](https://user-images.githubusercontent.com/16528097/122640558-d1db5080-d11d-11eb-8147-fee6de6b7b7d.png)
