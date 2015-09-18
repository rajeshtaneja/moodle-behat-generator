@_performance_toolkit_generator
Feature: Create role override for users
  In order to create role override
  As an admin
  I need to override roles in each course.

  Scenario Outline: Role overrides
    Given the following "permission overrides" instances exist:
      | capability                                | permission  | role   | contextlevel | reference   | instances |
      | block/recent_activity:viewaddupdatemodule | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | block/recent_activity:viewdeletemodule    | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/category:config                     | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/cohort:config                       | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/database:config                     | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/database:unenrol                    | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/flatfile:manage                     | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/flatfile:unenrol                    | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/guest:config                        | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/imsenterprise:config                | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/ldap:manage                         | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/manual:config                       | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/manual:enrol                        | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/manual:manage                       | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/manual:unenrol                      | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/manual:unenrolself                  | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/meta:config                         | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/meta:selectaslinked                 | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/meta:unenrol                        | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/mnet:config                         | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/paypal:config                       | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/paypal:manage                       | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/paypal:unenrol                      | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | enrol/paypal:unenrolself                  | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/assign:addinstance                    | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/assignment:addinstance                | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/book:addinstance                      | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/chat:addinstance                      | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/choice:addinstance                    | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/data:addinstance                      | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/feedback:addinstance                  | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/folder:addinstance                    | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/forum:addinstance                     | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/glossary:addinstance                  | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/imscp:addinstance                     | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/label:addinstance                     | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/lesson:addinstance                    | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/lti:addcoursetool                     | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/lti:addinstance                       | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/lti:requesttooladd                    | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/page:addinstance                      | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/quiz:addinstance                      | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/resource:addinstance                  | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/scorm:addinstance                     | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/survey:addinstance                    | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/url:addinstance                       | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/wiki:addinstance                      | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | mod/workshop:addinstance                  | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
      | moodle/backup:anonymise                   | Allow       | <role> | Course       | TC_#count#  | #!courseinstancesinmisccategory!# |
    Examples:
      | role           |
      | manager        |
      | coursecreator  |
      | editingteacher |
      | teacher        |
