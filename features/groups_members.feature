@_performance_toolkit_generator
Feature: Create groups members
  In order to create groups members
  As a teacher
  I need to create create group members

  Scenario Outline: Create group members in each group
    Given the following "group members" instances exist:
      | group                           | user        | instances                       | repeat                         | referencecount                  |
      | group_c<course>_#repeatcount#   | s#refcount# | #!numberofstudentsineachgroup!# | #!numberofgroupsineachcourse!# | #!numberofstudentsineachgroup!# |
      | group_cc<course>_#repeatcount#  | s#refcount# | #!numberofstudentsineachgroup!# | #!numberofgroupsineachcourse!# | #!numberofstudentsineachgroup!# |
      | group_csc<course>_#repeatcount# | s#refcount# | #!numberofstudentsineachgroup!# | #!numberofgroupsineachcourse!# | #!numberofstudentsineachgroup!# |
