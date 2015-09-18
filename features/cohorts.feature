@_performance_toolkit_generator
Feature: Create cohorts and cohort members in test site
  In order to create cohorts and cohort members in test site
  As an admin
  I need to create cohorts and assign members.

  Scenario: Add cohorts and cohort members with data generator
    Given the following "cohorts" instances exist:
      | name                       | idnumber    | contextlevel | reference               | instances   | referencecount                    |
      | System cohort #count#      | CHS#count#  | System       |                         | #!cohorts!# | #!courseinstancesinmisccategory!# |
      | Cohort in category #count# | CHC#count#  | Category     | TestCategory_#refcount# | #!cohorts!# | #!courseinstancesinmisccategory!# |
      | Empty cohort #count#       | CHE#count#  | Category     | TestCategory_#refcount# | #!cohorts!# | #!courseinstancesinmisccategory!# |
    And the following "cohort members" instances exist:
      | user     | cohort | instances             |
      | s#count# | CHS1   | #!studentspercohort!# |
      | s#count# | CHC1   | #!studentspercohort!# |
      | s#count# | CHE1   | #!studentspercohort!# |
