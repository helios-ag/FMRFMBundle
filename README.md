FMRFMBundle
================

[RichfileManager](https://github.com/servocoder/RichFilemanager) integration in Symfony

### Code Quality Assurance ###

| TravisCI | License | Version | Downloads |
|----------| --------|---------|----------|
|[![Build Status](https://travis-ci.org/helios-ag/FMRFMBundle.svg?branch=master)](http://travis-ci.org/helios-ag/FMElfinderBundle)|[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)|[![Latest Stable Version](https://poser.pugx.org/helios-ag/fm-elfinder-bundle/v/stable.svg)](https://packagist.org/packages/helios-ag/fm-rfm-bundle)|[![Total Downloads](https://poser.pugx.org/helios-ag/fm-elfinder-bundle/downloads.svg)](https://packagist.org/packages/helios-ag/fm-rfm-bundle)|


**RichfileManager** An open-source file manager. http://fm.devale.pro


**Table of contents**

- [Installation](#installation)
    - [Step 1: Installation](#step-1-installation)
    - [Step 2: Enable the bundle](#step-2-enable-the-bundle)
    - [Step 3: Import FMElfinderBundle routing file](#step-3-import-fmrfmbundle-routing-file)
    - [Step 4: Securing paths](#step-4-configure-your-applications-securityyml)
- [Basic configuration](#basic-configuration)
    - [Add configuration options to your config.yml](#add-configuration-options-to-your-configyml)
    - [Use multiple upload folder by instance](#use-multiple-upload-folder-by-instance)
- [RichfileManager Form Type](/Resources/doc/elfinder-form-type.md)
- [CKEditor integration](/Resources/doc/ckeditor-integration.md)
    - [Installation](/Resources/doc/ckeditor-integration.md#step-1-installation)
    - [Configuration](/Resources/doc/ckeditor-integration.md#step-2-configure-ckeditor-setting-via-settingsyml-or-through-form-builder)


## Installation

### Step 1: Installation

Add FMRFMBundle to your composer.json

```json
{
    "require": {
        "helios-ag/fm-rfm-bundle": "~1"
    }
}
```

also add **component-dir** under config node of composer.json

```json
{
    "config": {
        "component-dir": "web/assets"
    }
}
```

Now tell composer to download the bundle by running the command:


```sh
composer update helios-ag/fm-rfm-bundle
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FM\RFMBundle\FMRFMBundle(),
    );
}
```

### Step 3: Import FMElfinderBundle routing file

``` yaml
# app/config/routing.yml
rfm:
     resource: "@FMRFMBundle/Resources/config/routing.yml"
```

### Step 4: Configure your application's security.yml

Secure RFM with access_control:
``` yaml
# app/config/security.yml
security:

    //....
    access_control:
        - { path: ^/rfm_show, role: ROLE_USER }
        - { path: ^/rfm_run, role: ROLE_USER }
        - { path: ^/rfm_config.json, role: ROLE_USER }
        - { path: ^/rfm_config_default.json, role: ROLE_USER }

```

## Basic configuration

### Add configuration options to your config.yml

Example below (assumed that richfilemanager assets placed in `web/assets/richfilemanager` directory) 

```yaml
fm_rfm:
    instances:
        default:
            options:
                serverRoot: true
                fileRoot: /uploads
```

You can see the full list of roots options [here](https://github.com/servocoder/RichFilemanager/wiki). To use them,
convert camelCased option name to under_scored name.

Bundle provides custom form type, RFMType (provides same functionality as in [FMElfinderBundle](https://github.com/helios-ag/FMElfinderBundle))
and integration with CKEditor out of the box. 
