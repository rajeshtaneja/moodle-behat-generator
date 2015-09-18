@_performance_toolkit_generator
Feature: Create system roles
  In order to create system roles
  As an admin
  I need to set system roles

  Scenario: Add role assigns
    Given the following "roles" instances exist:
      | name                          | shortname            | description                    | archetype      | instances             |
      | Custom editing teacher#count# | customteacher#count# | My custom teacher role #count# | editingteacher | #!customteacherrole!# |
      | Custom editing student#count# | customstudent#count# | My custom student role #count# | student        | #!customstudentrole!# |
