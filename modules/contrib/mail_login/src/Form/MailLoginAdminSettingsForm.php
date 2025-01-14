<?php

namespace Drupal\mail_login\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Mail Login settings.
 */
class MailLoginAdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mail_login_form_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mail_login.settings'];
  }

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * MailLoginAdminSettingsForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('mail_login.settings');

    $form['general'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('General Configurations'),
      '#open' => TRUE,
    ];

    $form['general']['mail_login_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable login by email address'),
      '#default_value' => $config->get('mail_login_enabled'),
      '#description' => $this->t('This option enables login by email address.'),
    ];

    $form['general']['mail_login_case_sensitive'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Email addresses are case-sensitive'),
      '#default_value' => $config->get('mail_login_case_sensitive'),
      '#description' => $this->t('Disable this option to ignore upper/lower-case differences in email addresses during login, provided there is only one possible match.  If more than one email address would match then case-sensitivity is respected in order to guarantee uniqueness.  This is because RFC&nbsp;5321 permits case-sensitivity and therefore, while email addresses are <em>commonly</em> case-insensitive in practice, conflicts are possible in principle.'),
    ];

    $form['general']['mail_login_email_only'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Log in by email address only'),
      '#default_value' => $config->get('mail_login_email_only'),
      '#states' => [
        'visible' => [
          ':input[name="mail_login_enabled"]' => ['checked' => TRUE],
        ],
      ],
      '#description' => $this->t('This option disables logging in by username and forces logging in by email address only.'),
    ];

    $form['general']['mail_login_override_login_labels'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Override login form'),
      '#default_value' => $config->get('mail_login_override_login_labels'),
      '#states' => [
        'visible' => [
          ':input[name="mail_login_enabled"]' => ['checked' => TRUE],
        ],
      ],
      '#description' => $this->t('This option allows you to override the login form username title/description.'),
    ];

    $form['general']['mail_login_username_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Login form username/email address label'),
      '#default_value' => $config->get('mail_login_username_title') ?: $this->t('Log in by username/email address'),
      '#states' => [
        'required' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
        ],
        'visible' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
          ':input[name="mail_login_email_only"]' => ['checked' => FALSE],
        ],
      ],
      '#description' => $this->t('Override the username field title.'),
    ];

    $form['general']['mail_login_username_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Login form username/email address description'),
      '#default_value' => $config->get('mail_login_username_description') ?: $this->t('You can use your username or email address to login.'),
      '#states' => [
        'required' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
        ],
        'visible' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
          ':input[name="mail_login_email_only"]' => ['checked' => FALSE],
        ],
      ],
      '#description' => $this->t('Override the username field description.'),
    ];

    $form['general']['mail_login_email_only_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Login form email address only label'),
      '#default_value' => $config->get('mail_login_email_only_title') ?: $this->t('Login by email address'),
      '#states' => [
        'required' => [
          ':input[name="mail_login_email_only"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
        ],
        'visible' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
          ':input[name="mail_login_email_only"]' => ['checked' => TRUE],
        ],
      ],
      '#description' => $this->t('Override the username field title.'),
    ];

    $form['general']['mail_login_email_only_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Login form email address only description'),
      '#default_value' => $config->get('mail_login_email_only_description') ?: $this->t('You can use your email address only to login.'),
      '#states' => [
        'required' => [
          ':input[name="mail_login_email_only"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
        ],
        'visible' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
          ':input[name="mail_login_email_only"]' => ['checked' => TRUE],
        ],
      ],
      '#description' => $this->t('Override the username field description.'),
    ];

    $form['general']['mail_login_password_only_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Login form password only description'),
      '#default_value' => $config->get('mail_login_password_only_description') ?: $this->t('Enter the password that accompanies your email address.'),
      '#states' => [
        'required' => [
          ':input[name="mail_login_email_only"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
        ],
        'visible' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
          ':input[name="mail_login_email_only"]' => ['checked' => TRUE],
        ],
      ],
      '#description' => $this->t('Override the password field description.'),
    ];

    $form['general']['mail_login_password_reset_username_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Password reset form username/email address label'),
      '#default_value' => $config->get('mail_login_password_reset_username_title') ?: $this->t('Username or email address'),
      '#states' => [
        'required' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
        ],
        'visible' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
          ':input[name="mail_login_email_only"]' => ['checked' => FALSE],
        ],
      ],
      '#description' => $this->t('Override the username field title.'),
    ];

    $form['general']['mail_login_password_reset_username_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Password reset form username/email address description'),
      '#default_value' => $config->get('mail_login_password_reset_username_description') ?: $this->t('Password reset instructions will be sent to your registered email address.'),
      '#states' => [
        'required' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
        ],
        'visible' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
          ':input[name="mail_login_email_only"]' => ['checked' => FALSE],
        ],
      ],
      '#description' => $this->t('Override the username field description.'),
    ];

    $form['general']['mail_login_password_reset_email_only_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Password reset form email address only label'),
      '#default_value' => $config->get('mail_login_password_reset_email_only_title') ?: $this->t('Email address'),
      '#states' => [
        'required' => [
          ':input[name="mail_login_email_only"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
        ],
        'visible' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
          ':input[name="mail_login_email_only"]' => ['checked' => TRUE],
        ],
      ],
      '#description' => $this->t('Override the username field title.'),
    ];

    $form['general']['mail_login_password_reset_email_only_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Password reset form email address only description'),
      '#default_value' => $config->get('mail_login_password_reset_email_only_description') ?: $this->t('Password reset instructions will be sent to your registered email address.'),
      '#states' => [
        'required' => [
          ':input[name="mail_login_email_only"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
        ],
        'visible' => [
          ':input[name="mail_login_override_login_labels"]' => [
            'checked' => TRUE,
            'visible' => TRUE,
          ],
          ':input[name="mail_login_email_only"]' => ['checked' => TRUE],
        ],
      ],
      '#description' => $this->t('Override the username field description.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('mail_login.settings');
    $config
      ->set('mail_login_enabled', $form_state->getValue('mail_login_enabled'))
      ->set('mail_login_case_sensitive', $form_state->getValue('mail_login_case_sensitive'))
      ->set('mail_login_email_only', $form_state->getValue('mail_login_email_only'))
      ->set('mail_login_override_login_labels', $form_state->getValue('mail_login_override_login_labels'))
      ->set('mail_login_username_title', $form_state->getValue('mail_login_username_title'))
      ->set('mail_login_username_description', $form_state->getValue('mail_login_username_description'))
      ->set('mail_login_email_only_title', $form_state->getValue('mail_login_email_only_title'))
      ->set('mail_login_email_only_description', $form_state->getValue('mail_login_email_only_description'))
      ->set('mail_login_password_only_description', $form_state->getValue('mail_login_password_only_description'))
      ->set('mail_login_password_reset_username_title', $form_state->getValue('mail_login_password_reset_username_title'))
      ->set('mail_login_password_reset_username_description', $form_state->getValue('mail_login_password_reset_username_description'))
      ->set('mail_login_password_reset_email_only_title', $form_state->getValue('mail_login_password_reset_email_only_title'))
      ->set('mail_login_password_reset_email_only_description', $form_state->getValue('mail_login_password_reset_email_only_description'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
