Behat's extension for testing Magento based projects
====================================================

[![Build Status](https://travis-ci.org/irs/behat-magento-extension.png?branch=master)](https://travis-ci.org/irs/behat-magento-extension)

This extension defines Behat hooks that can be used into feature's context that allows restore 
Magento in certain state.

Installation
------------

To install this extension with Composer add folowing lines into your `composer.json:`

```javascript
{
    "require": {
        "irs/behat-magento-extension": "dev-master"
    }
}
```

and run `composer install.` After that to enable extension add following lines to `behat.yml:`

```
default:
  extensions:
    Irs\BehatMagentoExtension\Extension:
      magento: /path/to/magento
      target: /path/to/target
      store: store_code        # default: empty
      scope: scope_code        # default: store
      database:
        host: test_db_host          # default: localhost
        user: test_db_user_name     # default: root
        password: test_db_password  # default: empty
        schema: test_db_schema    
```

Usage
-----

To activate hooks you need to use `Irs\BehatMagentoExtension\Context\MagentoHooks` trait into you features' context.

On first run Magento will be installed into target (by `Irs\MagentoInitializer\Installer\GenericInstaller`) and 
after that deafult state will be saved into `states/default.state.` Magento will be restored from this state 
on _before suite_ event.

To restore Magento to certain state before feature or before scenario you need to add tag `@state:state_name` to feature 
or scenario (correspondingly). For example, following code restores Magento to default state (that's saved into 
`states/default.state`) before "Successfully describing scenario":

```
Feature: Your first feature
  In order to start using Behat
  As a manager or developer
  I need to try

  @state:default
  Scenario: Successfully describing scenario
    Given there is something
    When I do something
    Then I should see something
```

Current Magento state from target can be saved with `bin\magento save-state` command.

```
d:\opensource-workspace\behat-magento-extension>vendor\bin\magento help save-state
Usage:
 save-state [-c|--config="..."] [-p|--config-profile="..."] [-s|--name="..."]

Options:
 --config (-c)         Path to Behat's config (default: "behat.yml")
 --config-profile (-p) Profile of Behat's config (default: "default")
 --name (-s)           State name (default: "states/2013-04-03-06-11-10.state")
 --help (-h)           Display this help message.
 --quiet (-q)          Do not output any message.
 --verbose (-v)        Increase verbosity of messages.
 --version (-V)        Display this application version.
 --ansi                Force ANSI output.
 --no-ansi             Disable ANSI output.
 --no-interaction (-n) Do not ask any interactive question.
```
