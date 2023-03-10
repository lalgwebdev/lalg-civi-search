[
  [
    "SavedSearch",
    "save",
    {
      "records": [
        {
          "name": "Cancel_Members",
          "label": "Cancel/Delete Members",
          "form_values": null,
          "mapping_id": null,
          "search_custom_id": null,
          "api_entity": "Contact",
          "api_params": {
            "version": 4,
            "select": [
              "id",
              "display_name",
              "Contact_Email_contact_id_01.email",
              "Contact_Address_contact_id_01.street_address",
              "Contact_Address_contact_id_01.postal_code"
            ],
            "orderBy": [],
            "where": [
              [
                "contact_type:name",
                "IN",
                [
                  "Individual",
                  "Household"
                ]
              ],
              [
                "is_deleted",
                "=",
                false
              ],
              [
                "id",
                "CONTAINS",
                ""
              ],
              [
                "display_name",
                "CONTAINS",
                ""
              ],
              [
                "OR",
                [
                  [
                    "email_primary.email",
                    "CONTAINS",
                    ""
                  ],
                  [
                    "ISNULL(email_primary.email)",
                    "=",
                    true
                  ]
                ]
              ]
            ],
            "groupBy": [],
            "join": [
              [
                "Address AS Contact_Address_contact_id_01",
                "LEFT",
                [
                  "id",
                  "=",
                  "Contact_Address_contact_id_01.contact_id"
                ],
                [
                  "Contact_Address_contact_id_01.is_primary",
                  "=",
                  true
                ]
              ],
              [
                "Email AS Contact_Email_contact_id_01",
                "LEFT",
                [
                  "id",
                  "=",
                  "Contact_Email_contact_id_01.contact_id"
                ],
                [
                  "Contact_Email_contact_id_01.is_primary",
                  "=",
                  true
                ]
              ]
            ],
            "having": []
          },
          "expires_date": null,
          "description": null
        }
      ],
      "match": [
        "name"
      ]
    }
  ],
  [
    "SearchDisplay",
    "save",
    {
      "records": [
        {
          "name": "Delete_Cancel_Members_Table_1",
          "label": "Cancel/Delete Members Table 1",
          "saved_search_id.name": "Cancel_Members",
          "type": "table",
          "settings": {
            "description": "",
            "sort": [
              [
                "sort_name",
                "ASC"
              ]
            ],
            "limit": 25,
            "pager": [],
            "placeholder": 5,
            "columns": [
              {
                "type": "field",
                "key": "id",
                "dataType": "Integer",
                "label": "Membership No.",
                "sortable": false
              },
              {
                "type": "field",
                "key": "display_name",
                "dataType": "String",
                "label": "Name",
                "sortable": true
              },
              {
                "type": "field",
                "key": "Contact_Email_contact_id_01.email",
                "dataType": "String",
                "label": "Email",
                "sortable": true
              },
              {
                "type": "field",
                "key": "Contact_Address_contact_id_01.street_address",
                "dataType": "String",
                "label": "Street Address",
                "sortable": false
              },
              {
                "type": "field",
                "key": "Contact_Address_contact_id_01.postal_code",
                "dataType": "String",
                "label": "PostCode",
                "sortable": false
              },
              {
                "links": [
                  {
                    "entity": "Contact",
                    "action": "view",
                    "join": "",
                    "target": "_blank",
                    "icon": "fa-external-link",
                    "text": "View Contact",
                    "style": "default",
                    "path": "",
                    "condition": []
                  }
                ],
                "type": "links",
                "alignment": "text-right"
              }
            ],
            "actions": true,
            "classes": [
              "table",
              "table-striped"
            ],
            "button": null,
            "headerCount": true,
            "noResultsText": "No Contacts were found for those filters"
          },
          "acl_bypass": false
        }
      ],
      "match": [
        "name",
        "saved_search_id"
      ]
    }
  ],
  [
    "Afform",
    "save",
    {
      "records": [
        {
          "name": "afsearchCancelDeleteMembers1",
          "requires": [],
          "title": "Cancel/Delete Members",
          "description": "",
          "is_dashlet": false,
          "is_public": false,
          "is_token": false,
          "permission": "access CiviCRM",
          "type": "search",
          "entity_type": null,
          "join_entity": null,
          "contact_summary": null,
          "summary_contact_type": null,
          "icon": "fa-trash",
          "server_route": "civicrm/cancelmembers",
          "redirect": null,
          "create_submission": false,
          "navigation": {
            "parent": "Contacts",
            "label": "Cancel/Delete Members",
            "weight": 99
          },
          "layout": "<div class=\"af-markup\">\n  \n  \n  \n  \n  <p><strong>This form will Delete the selected Contacts to the Recycle Bin, and then:</strong></p>\n\n  <ul><li>Cancel their membership(s),</li>\n\t<li>Delete their Drupal Login, if any,</li>\n\t<li>Delete remaining members of any Households that have been deleted.</li>\n\t<li>Delete any&nbsp;Households left empty by deleting all their members,</li>\n  </ul><p>&nbsp;</p>\n\n\n\n\n\n</div>\n<div af-fieldset=\"\">\n  <div class=\"af-container af-layout-inline\">\n    <af-field name=\"id\" defn=\"{label: 'Membership No.', input_attrs: {}, input_type: 'Text', required: false}\" />\n    <af-field name=\"display_name\" defn=\"{label: 'Name (contains)', input_attrs: {}}\" />\n    <af-field name=\"Contact_Email_contact_id_01.email\" defn=\"{label: 'Email (contains)', input_attrs: {}}\" />\n  </div>\n  <div class=\"af-markup\">\n    <p>&nbsp; &nbsp;</p>\n\n  </div>\n  <crm-search-display-table search-name=\"Cancel_Members\" display-name=\"Delete_Cancel_Members_Table_1\"></crm-search-display-table>\n</div>\n"
        }
      ]
    }
  ]
]
