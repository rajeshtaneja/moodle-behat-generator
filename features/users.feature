@_performance_toolkit_generator
Feature: Create users in test site
  In order to create users in test site
  As an admin
  I need to generate users in site.

  Scenario: Create students, teachers, managers, course creators.
    Given the following "users" instances exist:
      | username  | firstname      | lastname | email                 | instances          |
      | s#count#  | Student        | #count#  | s#count#@example.com  | #!students!#       |
      | t#count#  | Teacher        | #count#  | t#count#@example.com  | #!teachers!#       |
      | m#count#  | Manager        | #count#  | m#count#@example.com  | #!managers!#       |
      | cc#count# | Course Creator | #count#  | cc#count#@example.com | #!coursecreators!# |
