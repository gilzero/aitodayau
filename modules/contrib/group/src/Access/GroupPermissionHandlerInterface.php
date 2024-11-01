<?php

namespace Drupal\group\Access;

use Drupal\group\Entity\GroupTypeInterface;

/**
 * Defines an interface to list available permissions.
 */
interface GroupPermissionHandlerInterface {

  /**
   * Gets all defined group permissions.
   *
   * @param bool $include_plugins
   *   (optional) Whether to also include the permissions defined by all
   *   installed group relations. Defaults to FALSE.
   *
   * @return array
   *   An array whose keys are permission names and whose corresponding values
   *   are arrays containing the following key-value pairs:
   *   - title: The untranslated human-readable name of the permission, to be
   *     shown on the permission administration page. You may use placeholders
   *     as you would in t().
   *   - title_args: (optional) The placeholder values for the title.
   *   - description: (optional) An untranslated description of what the
   *     permission does. You may use placeholders as you would in t().
   *   - description_args: (optional) The placeholders for the description.
   *   - restrict access: (optional) A boolean which can be set to TRUE to
   *     indicate that site administrators should restrict access to this
   *     permission to trusted users. This should be used for permissions that
   *     have inherent security risks across a variety of potential use cases.
   *     When set to TRUE, a standard warning message will be displayed with the
   *     permission on the permission administration page. Defaults to FALSE.
   *   - warning: (optional) An untranslated warning message to display for this
   *     permission on the permission administration page. This warning
   *     overrides the automatic warning generated by 'restrict access' being
   *     set to TRUE. This should rarely be used, since it is important for all
   *     permissions to have a clear, consistent security warning that is the
   *     same across the site. Use the 'description' key instead to provide any
   *     information that is specific to the permission you are defining. You
   *     may use placeholders as you would in t().
   *   - warning_args: (optional) The placeholder values for the warning.
   *   - allowed for: (optional) An array of strings that define which
   *     membership types can use this permission. Possible values are:
   *     'anonymous', 'outsider', 'member'. Will default to all three when left
   *     empty.
   *   - provider: (optional) The provider name of the permission. Defaults to
   *     the module providing the permission. You may set this to another
   *     module's name to make it appear as if the permission was provided by
   *     that module.
   *   - section: (optional) The untranslated section name of the permission.
   *     This is used to maintain a clear overview on the permissions form.
   *     Defaults to the plugin name for plugin provided permissions and to
   *     "General" for all other permissions.
   *   - section_args: (optional) The placeholder values for the section name.
   *   - section_id: (optional) The machine name to identify the section by,
   *     defaults to the plugin ID for plugin provided permissions and to
   *     "general" for all other permissions. This is not a great solution and
   *     should be refactored in 3.0.0.
   *
   * @todo Refactor before 3.0.0.
   */
  public function getPermissions($include_plugins = FALSE);

  /**
   * Gets all defined group permissions for a group type.
   *
   * Unlike ::getPermissions(), this also includes the group permissions that
   * were defined by the plugins installed on the group type.
   *
   * @param \Drupal\group\Entity\GroupTypeInterface $group_type
   *   The group type to retrieve the permission list for.
   *
   * @return array
   *   The full permission list, structured like ::getPermissions().
   *
   * @see \Drupal\group\Access\GroupPermissionHandlerInterface::getPermissions()
   */
  public function getPermissionsByGroupType(GroupTypeInterface $group_type);

}
