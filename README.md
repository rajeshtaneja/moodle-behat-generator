Moodle behat generator
==========================
This is part of the Moodle performance toolkit, used for generating site data using behat. This tool can be used by
itself for generating test site data. This tool generate site with specified number of:
    - Categories
    - Courses
    - Activities and resources
    - Users

## Requirements:
* MySQL or PostgresSQL or Mariadb or MSSQL or oracle
* git
* curl
* PHP 5.4.4+
* moodle site
* Directory where data files can be saved (different from moodle dataroot)

## Generate test site:
Tool supports 5 sizes, according to specified size appropriate data will be generated.
* [xs] - Extra small
* [s] - Small
* [m] - Medium
* [l] - Large
* [xl] - Extra large

This tool comes with following templates, but you can add your own behat feature template.
* categories - Create caretories and sub-catgories.
* courses - Create courses in each category and sub-category
* activities - Create all core activities in each course
* users - Create users and enrol them as students/teachers/managers/course creators in each course.
* custom_roles - Create custom roles.
* groups - Create groups and groupings
* groups_members - add users to groups
* role_override - Create role overrides
* cohorts - Create cohorts
* main test course - Course with all above, used by performance toolkit.

```sh
cd bin & ./moodle_behat_generator --install --testdata s --moodlepath {PATH_TO_MOODLE_SOURCE} --datapath {PATH_OF_DIR_TO_STORE_TEST_DATA_FILES}
```
> If you already have a site installed, then it will generate data depending on template chosen provided --force option is passed.

## Before you use this tool,
* Get composer
* Install dependencies
```
php composer.phar install
```
* If config.php is not present for moodle, then the above install will ask user for details and write config.php

## Script options:
#### Save and restore site state
Often we need to backup and restore site state after and before data is generated. This can be done by
```sh
vendor/bin/moodle_behat_generator --backup StateName
vendor/bin/moodle_behat_generator --restore StateName
```
Above commands will backup dataroot directory and database state and will restore the same from the backedup state.

#### Drop site
There are 2 options
* drop - Drops moodle site db and dataroot, and also drop any test data.
* dropsite - Drop only moodle site db and dataroot.

## Default tool structure
#### Define order in which data is generated (behatgenerator.json)
Site data use behat feature and steps to generate data and it's order is defined by behatgenerator.json-dist
```
moodle-behat-generator/behatgenerator.json-dist
```
Order in which fetaures are defined in json file will be respected. You should only pass custom values which are needed with respect to site size. Rest should be handled by feature and custom steps.
```
{
  "categories": {
    "scenario": {
      "catinstances": {"xs": 1, "s": 10, "m": 100, "l": 10000, "xl": 100000},
      "subcatinstances": {"xs": 1, "s": 10, "m": 100, "l": 1000, "xl": 10000},
      "maxcategory": ["categories","scenario","catinstances"] // Reference 
    }
  }
}
```
If you are using scenario outline, then passing count and reference will update your feature file with Example. In the following code Example will be added to your feature file for the specified count, replacing <catname> and <catnewname> values with the specified values.
```
{
  "categories": {
    "scenario": {
      "catinstances": {"xs": 1, "s": 10, "m": 100, "l": 10000, "xl": 100000},
      "subcatinstances": {"xs": 1, "s": 10, "m": 100, "l": 1000, "xl": 10000},
      "maxcategory": ["categories","scenario","catinstances"] // Reference 
    },
    "scenario-ouline": {
      "count": {"xs": 1, "s": 10, "m": 100, "l": 10000, "xl": 100000},
      "catname": "TC#count#",
      "catnewname": "Test Course #count#"
    }
  }
}
```

#### Default feature are placed in
```
moodle-behat-generator/fixtures
```
#### Default behat Classes are placed in
Moodle naming convention is observered while naming these classes. File name should be behat_CLASSNAME.php
```
moodle-behat-generator/classes
```

## Adding custom feature and steps.
* Create moodle-behat-generator/behatgenerator.json
* Add contextpath and featurepath for every generator set.
```
"categories": {
    "featurepath": "ABSOLUTE_PATH_TO_FEATURE",
    "contextpath": "ABSOLUTE_PATH_TO_CONTEXT_FILE", // This can be an array or string.
    "scenario": {
      "catinstances": {"xs": 1, "s": 10, "m": 100, "l": 10000, "xl": 100000},
      "subcatinstances": {"xs": 1, "s": 10, "m": 100, "l": 1000, "xl": 10000},
      "maxcategory": ["categories","scenario","catinstances"]
    }
  },
```
