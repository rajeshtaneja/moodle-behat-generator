@_performance_toolkit_generator
Feature: Create groups and grouing in test site
  In order to create groups in test site
  As a teacher
  I need to create groups and grouping and create group members

  Scenario: Create groups in each course
    Given the following "groups" instances exist:
      | name                     | idnumber                          | course             | instances                      | repeat                            | referencecount                 |
      | group in misc #count#    | group_c#repeatcount#_#refcount#   | TC_#repeatcount#   | #!numberofgroupsineachcourse!# | #!courseinstancesinmisccategory!# | #!numberofgroupsineachcourse!# |
      | group in cat #count#     | group_cc#repeatcount#_#refcount#  | TCC_#repeatcount#  | #!numberofgroupsineachcourse!# | #!courseinstancesincategory!#     | #!numberofgroupsineachcourse!# |
      | group in sub cat #count# | group_csc#repeatcount#_#refcount# | TCSC_#repeatcount# | #!numberofgroupsineachcourse!# | #!courseinstancesinsubcategory!#  | #!numberofgroupsineachcourse!# |

  Scenario: Create groupings in each course
    Given the following "groupings" instances exist:
      | name                         | idnumber                              | course             | instances                         | repeat                            | referencecount                 |
      | groupings in misc #count#    | groupings_c#repeatcount#_#refcount#   | TC_#repeatcount#   | #!numberofgroupingsineachcourse!# | #!courseinstancesinmisccategory!# | #!numberofgroupsineachcourse!# |
      | groupings in cat #count#     | groupings_cc#repeatcount#_#refcount#  | TCC_#repeatcount#  | #!numberofgroupingsineachcourse!# | #!courseinstancesincategory!#     | #!numberofgroupsineachcourse!# |
      | groupings in sub cat #count# | groupings_csc#repeatcount#_#refcount# | TCSC_#repeatcount# | #!numberofgroupingsineachcourse!# | #!courseinstancesinsubcategory!#  | #!numberofgroupsineachcourse!# |

  Scenario Outline: Create groupings groups for each grouping
    Given the following "grouping groups" instances exist:
      | grouping                         | group                           | instances                         | repeat                         | referencecount                 |
      | groupings_c<course>_#refcount#   | group_c<course>_#repeatcount#   | #!numberofgroupingsineachcourse!# | #!numberofgroupsineachcourse!# | #!numberofgroupsineachcourse!# |
      | groupings_cc<course>_#refcount#  | group_cc<course>_#repeatcount#  | #!numberofgroupingsineachcourse!# | #!numberofgroupsineachcourse!# | #!numberofgroupsineachcourse!# |
      | groupings_csc<course>_#refcount# | group_csc<course>_#repeatcount# | #!numberofgroupingsineachcourse!# | #!numberofgroupsineachcourse!# | #!numberofgroupsineachcourse!# |
