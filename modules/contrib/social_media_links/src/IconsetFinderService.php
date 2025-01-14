<?php

namespace Drupal\social_media_links;

use Drupal\Core\DrupalKernelInterface;
use Drupal\Core\Extension\ExtensionPathResolver;

/**
 * Service class to detect the files of the iconsets/ in various directories.
 */
class IconsetFinderService {

  /**
   * Directories that iconsets are installed to.
   *
   * @var string
   */
  protected $installDirs = [];

  /**
   * Directories to search for Iconsets.
   *
   * @var string
   */
  protected $searchDirs = [];

  /**
   * List of Iconsets.
   *
   * @var string
   */
  protected $iconsets = [];

  /**
   * The Drupal Kernel.
   *
   * @var \Drupal\Core\DrupalKernelInterface
   */
  protected $kernel;

  /**
   * The Path resolver for extensions (Themes, Profiles, Modules).
   *
   * @var \Drupal\Core\Extension\ExtensionPathResolver
   */
  protected $extensionPathResolver;

  /**
   * Constructor for the Iconset Finder Service.
   */
  public function __construct(DrupalKernelInterface $kernel, ExtensionPathResolver $extension_path_resolver) {
    $this->kernel = $kernel;
    $this->extensionPathResolver = $extension_path_resolver;

    $this->setSearchDirs();
    $this->setIconsets();
  }

  /**
   * Get the path to a iconset.
   *
   * @param string $id
   *   The id of the iconset.
   *
   * @return string|null
   *   The path to the iconset or NULL if the iconset is not installed.
   */
  public function getPath($id) {
    return $this->iconsets[$id] ?? NULL;
  }

  /**
   * Get the directories where to look for the iconsets.
   *
   * @return array
   *   The paths to the directories.
   */
  public function getSearchDirs() {
    return $this->searchDirs;
  }

  /**
   * Defines a list of the locations, where icon sets are searched.
   */
  protected function setSearchDirs() {
    // Similar to 'modules' and 'themes' directories inside an installation
    // profile, installation profiles may want to place libraries into a
    // 'libraries' directory.
    $install_profile = $this->kernel->getContainer()->getParameter('install_profile');
    if ($install_profile && strpos($install_profile, "core") === FALSE) {
      $profile_path = $this->extensionPathResolver->getPath('profile', $install_profile);
      $searchdirs[] = "$profile_path/libraries";
    }

    // Search sites/all/libraries for backwars-compatibility.
    $searchdirs[] = 'sites/all/libraries';

    // Always search the root 'libraries' directory.
    $searchdirs[] = 'libraries';

    // Also search sites/<domain>/*.
    $site_path = $this->kernel->getSitePath();
    $searchdirs[] = "$site_path/libraries";

    // Add the social_media_links module directory.
    $searchdirs[] = $this->extensionPathResolver->getPath('module', 'social_media_links') . '/libraries';

    $this->searchDirs = $searchdirs;
  }

  /**
   * Get the directories where iconsets should be saved.
   *
   * @return array
   *   The paths to the directories where iconsets should be saved.
   */
  public function getInstallDirs() {
    if (empty($this->installDirs)) {
      $this->setInstallDirs();
    }

    return $this->installDirs;
  }

  /**
   * Set the directores where iconsets should be saved.
   *
   * This differs to the "searchDirs" that if a icon should be printed,
   * we are searching for the iconset path in all "searchDirs". But the user
   * should only install new iconsets in the directories, that are defined in
   * "installDirs".
   */
  protected function setInstallDirs() {
    $searchdirs = $this->searchDirs;

    // Remove the core and social_media_links module directory from the possible
    // target directories for installation.
    foreach ($searchdirs as $key => $dir) {
      if (preg_match("/core|social_media_links/", $dir)) {
        unset($searchdirs[$key]);
      }
    }

    $this->installDirs = $searchdirs;
  }

  /**
   * Returns the iconsets that are installed.
   *
   * @return array
   *   The installed iconsets.
   */
  public function getIconsets() {
    return (array) $this->iconsets;
  }

  /**
   * Searches the directories for libraries (e.g. Icon Sets).
   *
   * Sets an array of library directories from the all-sites directory
   * (i.e. sites/all/libraries/), the profiles directory, and site-specific
   * directory (i.e. sites/somesite/libraries/). The returned array will be
   * keyed by the library name. Site-specific libraries are prioritized over
   * libraries in the default directories. That is, if a library with the same
   * name appears in both the site-wide directory and site-specific directory,
   * only the site-specific version will be listed.
   *
   * Most of the code in this function are borrowed from the libraries module
   * (http://drupal.org/project/libraries).
   */
  protected function setIconsets() {
    // Retrieve a list of directories.
    $directories = [];
    $nomask = ['CVS'];

    foreach ($this->searchDirs as $dir) {
      if (is_dir($dir) && $handle = opendir($dir)) {
        while (FALSE !== ($file = readdir($handle))) {
          if (!in_array($file, $nomask) && $file[0] != '.') {
            if (is_dir("$dir/$file")) {
              $directories[$file] = "$dir/$file";
            }
          }
        }
        closedir($handle);
      }
    }

    $this->iconsets = (array) $directories;
  }

}
