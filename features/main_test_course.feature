@_performance_toolkit_generator
Feature: Create a special test course which will be used in test plan.
  In order to create a special test course
  As an admin
  I need to generate create course, add activities and other things in the course.

  Scenario: Create following categories
    # Create a Test course.
    Given the following "courses" exist:
      | fullname    | shortname | category | numsections |
      | Test course | TC        | 0        | 20          |
    # Add all activities to the course in different section.
    And the following "activities" instances exist:
      | activity   | name                          | course | idnumber          | instances                             | section  |
      | assign     | Test assignment name #count#  | TC     | assign#count#     | #!numberofallactivitiesineachcourse!# | 1        |
      | assignment | Test assignment22 name #count#| TC     | assignment#count# | #!numberofallactivitiesineachcourse!# | 1        |
      | book       | Test book name #count#        | TC     | book#count#       | #!numberofallactivitiesineachcourse!# | 2        |
      | chat       | Test chat name #count#        | TC     | chat#count#       | #!numberofallactivitiesineachcourse!# | 3        |
      | choice     | Test choice name #count#      | TC     | choice#count#     | #!numberofallactivitiesineachcourse!# | 4        |
      | data       | Test database name #count#    | TC     | data#count#       | #!numberofallactivitiesineachcourse!# | 5        |
      | feedback   | Test feedback name #count#    | TC     | feedback#count#   | #!numberofallactivitiesineachcourse!# | 6        |
      | folder     | Test folder name #count#      | TC     | folder#count#     | #!numberofallactivitiesineachcourse!# | 6        |
      | forum      | Test forum name #count#       | TC     | forum#count#      | #!numberofallactivitiesineachcourse!# | 7        |
      | glossary   | Test glossary name #count#    | TC     | glossary#count#   | #!numberofallactivitiesineachcourse!# | 8        |
      | imscp      | Test imscp name #count#       | TC     | imscp#count#      | #!numberofallactivitiesineachcourse!# | 9        |
      | label      | Test label name #count#       | TC     | label#count#      | #!numberofallactivitiesineachcourse!# | 10       |
      | lesson     | Test lesson name #count#      | TC     | lesson#count#     | #!numberofallactivitiesineachcourse!# | 11       |
      | lti        | Test lti name #count#         | TC     | lti#count#        | #!numberofallactivitiesineachcourse!# | 12       |
      | page       | Test page name #count#        | TC     | page#count#       | #!numberofallactivitiesineachcourse!# | 13       |
      | quiz       | Test quiz name #count#        | TC     | quiz#count#       | #!numberofallactivitiesineachcourse!# | 14       |
      | resource   | Test resource name #count#    | TC     | resource#count#   | #!numberofallactivitiesineachcourse!# | 15       |
      | scorm      | Test scorm name #count#       | TC     | scorm#count#      | #!numberofallactivitiesineachcourse!# | 16       |
      | survey     | Test survey name #count#      | TC     | survey#count#     | #!numberofallactivitiesineachcourse!# | 17       |
      | url        | Test url name #count#         | TC     | url#count#        | #!numberofallactivitiesineachcourse!# | 18       |
      | wiki       | Test wiki name #count#        | TC     | wiki#count#       | #!numberofallactivitiesineachcourse!# | 19       |
      | workshop   | Test workshop name #count#    | TC     | workshop#count#   | #!numberofallactivitiesineachcourse!# | 20       |
    # Create groups and groupings.
    And the following "groups" instances exist:
      | name                         | idnumber     | course   | instances                      |
      | group in test course #count# | group#count# | TC       | #!numberofgroupsineachcourse!# |
    And the following "groupings" instances exist:
      | name             | course | idnumber  | instances                         |
      | Grouping #count# | TC     | grouping#count# | #!numberofgroupingsineachcourse!# |
    And the following "grouping groups" instances exist:
      | grouping           | group              | instances                         | repeat                         | referencecount                 |
      | grouping#refcount# | group#repeatcount# | #!numberofgroupingsineachcourse!# | #!numberofgroupsineachcourse!# | #!numberofgroupsineachcourse!# |
    # Enrol users and assign them to group.
    And the following "course enrolments" instances exist:
      | user         | course | role    | instances                | referencecount |
      | s#refcount#  | TC     | student | #!maxstudentspercourse!# | #!students!#   |
      | t#refcount#  | TC     | teacher | #!maxteacherspercourse!# | #!teachers!#   |
      | m#refcount#  | TC     | manager | #!maxmanagerspercourse!# | #!managers!#   |
    And the following "group members" instances exist:
      | group              | user        | instances                       | repeat                         | referencecount                  |
      | group#repeatcount# | s#refcount# | #!numberofstudentsineachgroup!# | #!numberofgroupsineachcourse!# | #!numberofstudentsineachgroup!# |
      | group#repeatcount# | t#refcount# | #!maxteacherspercourse!#        | #!numberofgroupsineachcourse!# | #!maxteacherspercourse!#        |
    # Some permission overrides.
    And the following "permission overrides" exist:
      | capability                                | permission | role          | contextlevel | reference |
      | enrol/category:config                     | Allow      | coursecreator | Course       | TC        |
      | enrol/cohort:config                       | Allow      | coursecreator | Course       | TC        |
      | enrol/database:config                     | Allow      | coursecreator | Course       | TC        |
      | enrol/database:unenrol                    | Allow      | coursecreator | Course       | TC        |
      | enrol/flatfile:manage                     | Allow      | coursecreator | Course       | TC        |
      | enrol/flatfile:unenrol                    | Allow      | coursecreator | Course       | TC        |
      | enrol/guest:config                        | Allow      | coursecreator | Course       | TC        |
      | enrol/imsenterprise:config                | Allow      | coursecreator | Course       | TC        |
      | enrol/ldap:manage                         | Allow      | coursecreator | Course       | TC        |
      | enrol/meta:config                         | Allow      | coursecreator | Course       | TC        |
      | enrol/meta:selectaslinked                 | Allow      | coursecreator | Course       | TC        |
      | enrol/meta:unenrol                        | Allow      | coursecreator | Course       | TC        |
      | enrol/mnet:config                         | Allow      | coursecreator | Course       | TC        |
      | enrol/paypal:config                       | Allow      | coursecreator | Course       | TC        |
      | enrol/paypal:manage                       | Allow      | coursecreator | Course       | TC        |
      | enrol/paypal:unenrol                      | Allow      | coursecreator | Course       | TC        |
      | enrol/paypal:unenrolself                  | Allow      | coursecreator | Course       | TC        |

  Scenario Outline: Create forum discussions
    Given I log in as "admin"
    And I am on site homepage
    And I follow "Test course"
    And I add a new discussion to "Test forum name 1" forum with:
      | Subject | Discussion <discussion> |
      | Message | Discussion contents <discussion>, first message |
    And I reply "Discussion <discussion>" post from "Test forum name" forum with:
      | Subject | Reply 1 to discussion <discussion> |
      | Message | Discussion contents <discussion>, second message |
    And I log out
