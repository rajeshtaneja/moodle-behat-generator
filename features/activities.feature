@_performance_toolkit_generator
Feature: Create categories in test site
  In order to create categories in test site
  As an admin
  I need to generate categories and sub-categories.

  Scenario: Create following categories
    Given the following "activities" instances exist:
      | activity   | name                            | course         | idnumber          | instances                             | repeat                            | referencecount                    | section    |
      | assign     | Test assignment name #count#  | TC_#repeatcount# | assign#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | assignment | Test assignment22 name #count#| TC_#repeatcount# | assignment#count# | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | book       | Test book name #count#        | TC_#repeatcount# | book#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | chat       | Test chat name #count#        | TC_#repeatcount# | chat#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | choice     | Test choice name #count#      | TC_#repeatcount# | choice#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | data       | Test database name #count#    | TC_#repeatcount# | data#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | feedback   | Test feedback name #count#    | TC_#repeatcount# | feedback#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | folder     | Test folder name #count#      | TC_#repeatcount# | folder#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | forum      | Test forum name #count#       | TC_#repeatcount# | forum#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | glossary   | Test glossary name #count#    | TC_#repeatcount# | glossary#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | imscp      | Test imscp name #count#       | TC_#repeatcount# | imscp#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | label      | Test label name #count#       | TC_#repeatcount# | label#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | lesson     | Test lesson name #count#      | TC_#repeatcount# | lesson#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | lti        | Test lti name #count#         | TC_#repeatcount# | lti#count#        | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | page       | Test page name #count#        | TC_#repeatcount# | page#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | quiz       | Test quiz name #count#        | TC_#repeatcount# | quiz#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | resource   | Test resource name #count#    | TC_#repeatcount# | resource#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | scorm      | Test scorm name #count#       | TC_#repeatcount# | scorm#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | survey     | Test survey name #count#      | TC_#repeatcount# | survey#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | url        | Test url name #count#         | TC_#repeatcount# | url#count#        | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | wiki       | Test wiki name #count#        | TC_#repeatcount# | wiki#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      | workshop   | Test workshop name #count#    | TC_#repeatcount# | workshop#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesinmisccategory!# | #!courseinstancesinmisccategory!# | #refcount# |
      # Activity in courses under category.
      | assign     | Test assignment name #count#  | TCC_#repeatcount# | assign#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | assignment | Test assignment22 name #count#| TCC_#repeatcount# | assignment#count# | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | book       | Test book name #count#        | TCC_#repeatcount# | book#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | chat       | Test chat name #count#        | TCC_#repeatcount# | chat#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | choice     | Test choice name #count#      | TCC_#repeatcount# | choice#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | data       | Test database name #count#    | TCC_#repeatcount# | data#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | feedback   | Test feedback name #count#    | TCC_#repeatcount# | feedback#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | folder     | Test folder name #count#      | TCC_#repeatcount# | folder#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | forum      | Test forum name #count#       | TCC_#repeatcount# | forum#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | glossary   | Test glossary name #count#    | TCC_#repeatcount# | glossary#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | imscp      | Test imscp name #count#       | TCC_#repeatcount# | imscp#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | label      | Test label name #count#       | TCC_#repeatcount# | label#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | lesson     | Test lesson name #count#      | TCC_#repeatcount# | lesson#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | lti        | Test lti name #count#         | TCC_#repeatcount# | lti#count#        | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | page       | Test page name #count#        | TCC_#repeatcount# | page#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | quiz       | Test quiz name #count#        | TCC_#repeatcount# | quiz#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | resource   | Test resource name #count#    | TCC_#repeatcount# | resource#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | scorm      | Test scorm name #count#       | TCC_#repeatcount# | scorm#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | survey     | Test survey name #count#      | TCC_#repeatcount# | survey#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | url        | Test url name #count#         | TCC_#repeatcount# | url#count#        | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | wiki       | Test wiki name #count#        | TCC_#repeatcount# | wiki#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      | workshop   | Test workshop name #count#    | TCC_#repeatcount# | workshop#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesincategory!#    | #!courseinstancesincategory!#     | #refcount# |
      # Activity in courses under sub-category.
      | assign     | Test assignment name #count#  | TCSC_#repeatcount# | assign#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | assignment | Test assignment22 name #count#| TCSC_#repeatcount# | assignment#count# | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | book       | Test book name #count#        | TCSC_#repeatcount# | book#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | chat       | Test chat name #count#        | TCSC_#repeatcount# | chat#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | choice     | Test choice name #count#      | TCSC_#repeatcount# | choice#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | data       | Test database name #count#    | TCSC_#repeatcount# | data#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | feedback   | Test feedback name #count#    | TCSC_#repeatcount# | feedback#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | folder     | Test folder name #count#      | TCSC_#repeatcount# | folder#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | forum      | Test forum name #count#       | TCSC_#repeatcount# | forum#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | glossary   | Test glossary name #count#    | TCSC_#repeatcount# | glossary#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | imscp      | Test imscp name #count#       | TCSC_#repeatcount# | imscp#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | label      | Test label name #count#       | TCSC_#repeatcount# | label#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | lesson     | Test lesson name #count#      | TCSC_#repeatcount# | lesson#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | lti        | Test lti name #count#         | TCSC_#repeatcount# | lti#count#        | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | page       | Test page name #count#        | TCSC_#repeatcount# | page#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | quiz       | Test quiz name #count#        | TCSC_#repeatcount# | quiz#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | resource   | Test resource name #count#    | TCSC_#repeatcount# | resource#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | scorm      | Test scorm name #count#       | TCSC_#repeatcount# | scorm#count#      | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | survey     | Test survey name #count#      | TCSC_#repeatcount# | survey#count#     | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | url        | Test url name #count#         | TCSC_#repeatcount# | url#count#        | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | wiki       | Test wiki name #count#        | TCSC_#repeatcount# | wiki#count#       | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
      | workshop   | Test workshop name #count#    | TCSC_#repeatcount# | workshop#count#   | #!numberofallactivitiesineachcourse!# | #!courseinstancesinsubcategory!# | #!courseinstancesinsubcategory!# | #refcount# |
