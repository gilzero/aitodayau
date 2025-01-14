# Views Bulk Edit

Views Bulk Edit allows modifying field values of a selected list of entities
of any type. It works with Drupal core actions as well as the
Views Bulk Operations (VBO) module, offering all of its benefits like batching,
ability to select all view results or persistent selection across pages.

For a full description of the module, visit the
[project page] https://www.drupal.org/project/views_bulk_edit

Submit bug reports and feature suggestions, or track changes in the
[issue queue] https://www.drupal.org/project/views_bulk_edit


## Contents of this file

- Requirements
- Installation
- Configuration
- Maintainers


## Requirements

Views Bulk Edit works with Drupal core but can also be used with
the Views Bulk Operations module.
 
 
## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).
 
 
## Configurations

 1. Create or edit any view, the most convenient display type for end
      user in case of VBO views is Table.
 2. Add a "Views bulk operations" field (global), available on
      all entity types, if not already added.
 3. Check the "Modify field values" action.
 4. (Optional) Define a "bulk_edit" form mode for the entity bundles
      that you wish to be able to bulk edit. Under the "Form modes" page
      at /admin/structure/display-modes/form you can add a new form
      mode. If it uses the machine name "bulk_edit", the form for the
      "Modify field values" action will use that form mode to determine
      what fields are available for bulk editing.
