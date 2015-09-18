@_performance_toolkit_generator
Feature: Create courses in test site
  In order to create courses in test site
  As an admin
  I need to generate courses in categories and sub-categories.

  Scenario: Create courses in misc category
    Given the following "courses" instances exist:
      | category      | fullname                     | shortname    | numsections    | instances                         |
      | 0             | Test course #count#          | TC_#count#   | #!numsection!# | #!courseinstancesinmisccategory!# |

  Scenario: Create courses in each created category
    Given the following "courses" instances exist:
      | category                | fullname                          | shortname       | numsections    | instances                        | referencecount      |
      | TestCategory_#count#    | Test course in cat #refcount#     | TCC_#refcount#  | #!numsection!# | #!courseinstancesincategory!#    | #!catinstances!#    |
      | TestSubCategory_#count# | Test course in sub cat #refcount# | TCSC_#refcount# | #!numsection!# | #!courseinstancesinsubcategory!# | #!subcatinstances!# |
