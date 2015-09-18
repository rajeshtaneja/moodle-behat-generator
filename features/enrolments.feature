@_performance_toolkit_generator
Feature: Enrol users in test site
  In order to enrol users in test site
  As an admin
  I need to enrol users in each course.

  # Instances - number of instances to create
  # reference - refcount will be incremented as per example referencecount.
  # repeat - repeatcount will be used to repeat for each course user enrolment.
  Scenario: Enrol users in each course
    Given the following "course enrolments" instances exist:
      | user         | course             | role          | instances                | referencecount     | repeat                            |
      | s#refcount#  | TC_#repeatcount#   | student       | #!maxstudentspercourse!# | #!students!#       | #!courseinstancesinmisccategory!# |
      | s#refcount#  | TCC_#repeatcount#  | customstudent1| #!maxstudentspercourse!# | #!students!#       | #!courseinstancesincategory!#     |
      | s#refcount#  | TCSC_#repeatcount# | student       | #!maxstudentspercourse!# | #!students!#       | #!courseinstancesinsubcategory!#  |
      | t#refcount#  | TC_#repeatcount#   | teacher       | #!maxteacherspercourse!# | #!teachers!#       | #!courseinstancesinmisccategory!# |
      | t#refcount#  | TCC_#repeatcount#  | customstudent1| #!maxteacherspercourse!# | #!teachers!#       | #!courseinstancesincategory!#     |
      | t#refcount#  | TCSC_#repeatcount# | teacher       | #!maxteacherspercourse!# | #!teachers!#       | #!courseinstancesinsubcategory!#  |
      | m#refcount#  | TC_#repeatcount#   | manager       | #!maxmanagerspercourse!# | #!managers!#       | #!courseinstancesinmisccategory!# |
      | m#refcount#  | TCC_#repeatcount#  | manager       | #!maxmanagerspercourse!# | #!managers!#       | #!courseinstancesincategory!#     |
      | m#refcount#  | TCSC_#repeatcount# | manager       | #!maxmanagerspercourse!# | #!managers!#       | #!courseinstancesinsubcategory!#  |
      | cc#refcount# | TC_#repeatcount#   | coursecreator | #!maxmanagerspercourse!# | #!coursecreators!# | #!courseinstancesinmisccategory!# |
      | cc#refcount# | TCC_#repeatcount#  | coursecreator | #!maxmanagerspercourse!# | #!coursecreators!# | #!courseinstancesincategory!#     |
      | cc#refcount# | TCSC_#repeatcount# | coursecreator | #!maxmanagerspercourse!# | #!coursecreators!# | #!courseinstancesinsubcategory!#  |
