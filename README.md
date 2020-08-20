<h1><p align="center">PaiementPro for Give</p></h1>

<p align="center">This plugin is an integration of PaiementPro payment gateway with GiveWP, the popular donations plugin for WordPress. You can easily start accepting donations via MTN Money, Mobile Money, and Orange Money using PaiementPro</p>

---

👉 Not a developer? Running WordPress? [Download PaimentPro for Give](https://wordpress.org/plugins/paiementpro-for-give/) on WordPress.org.

![WordPress version](https://img.shields.io/wordpress/plugin/v/paiementpro-for-give.svg) ![WordPress Rating](https://img.shields.io/wordpress/plugin/r/paiementpro-for-give.svg) ![WordPress Downloads](https://img.shields.io/wordpress/plugin/dt/paiementpro-for-give.svg) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kohp-ministries/paiementpro-for-give/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kohp-ministries/paiementpro-for-give/?branch=master) [![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](https://github.com/kohp-ministries/paiementpro-for-give/blob/master/license.txt) 

Welcome to the **PaiementPro for Give** GitHub repository. This is the core repository and heart of an ecosystem of active development. Here you can browse the source, look at open issues, and contribute to the project. 

Happy Coding!

 ## 🙋 Support
 
 This repository is not suitable for support. Please don't use GitHub issues for non-development related support requests. Don't get us wrong, we're more than happy to help you! However, to get the support you need please use the following channels:

* [WP.org Support Forums](https://wordpress.org/support/plugin/paiementpro-for-give) - for all **free** users.
* [GiveWP Documentation](https://givewp.com/documentation/) - for all GiveWP related questions. 
 
## 🌱 Getting Started 

If you're looking to contribute or actively develop on **PaiementPro for Give**, welcome! We're glad you're here. Please ⭐️ this repository and fork it to begin local development. 

Most of us are using [Local by Flywheel](https://localbyflywheel.com/) to develop on WordPress, which makes set up quick and easy. If you prefer [Docker](https://www.docker.com/), [VVV](https://github.com/Varying-Vagrant-Vagrants/VVV), or another flavor of local development that's cool too!

## ✅ Prerequisites
* [Node.js](https://nodejs.org/en/) as JavaScript engine
* [NPM](https://docs.npmjs.com/) npm command globally available in CLI
* [Composer](https://getcomposer.org/) composer command globally available in CLI

**Development Notes**

* Ensure that you have `SCRIPT_DEBUG` enabled within your wp-config.php file. Here's a good example of wp-config.php for debugging:
    ```
     // Enable WP_DEBUG mode
    define( 'WP_DEBUG', true );
    
    // Enable Debug logging to the /wp-content/debug.log file
    define( 'WP_DEBUG_LOG', true );
   
    // Loads unminified core files
    define( 'SCRIPT_DEBUG', true );
    ```
* Commit the `package.lock` file. Read more about why [here](https://docs.npmjs.com/files/package-lock.json). 
* Your editor should recognize the `.eslintrc` and `.editorconfig` files within the Repo's root directory. Please only submit PRs following those coding style rulesets. 
