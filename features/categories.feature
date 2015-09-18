@_performance_toolkit_generator
Feature: Create categories in test site
  In order to create categories in test site
  As an admin
  I need to generate categories and sub-categories.

  Scenario: Create following categories
    Given the following "categories" instances exist:
      | category | idnumber             | name                  | instances        |
      | 0        | TestCategory_#count# | Test Category #count# | #!catinstances!# |

  # Instances - number of instances to create
  # reference - refcount will be incremented as per example referencecount.
  Scenario: Create following sub-categories
    Given the following "categories" instances exist:
      | category             | idnumber                   | name                         | instances           | referencecount  |
      | TestCategory_#count# | TestSubCategory_#refcount# | Test Sub Category #refcount# | #!subcatinstances!# | #!catwithsubcat!# |
