<?php

namespace Drupal\site_audit\Renderer;

use Drupal\site_audit\Plugin\SiteAuditCheckBase;
use Drupal\site_audit\Plugin\SiteAuditChecklistBase;
use Drupal\site_audit\Renderer;

/**
 *
 */
class Html extends Renderer {

  /**
   * The build array for the page.
   */
  public $build;

  /**
   * @inherit
   */
  public function __construct($checklist, $logger = NULL, $options = NULL, $output = NULL) {
    parent::__construct($checklist, $logger, $options, $output);
    $this->buildHeader();
  }

  /**
   * Get the CSS class associated with a percentage.
   *
   * @return string
   *   Twitter Bootstrap CSS class.
   */
  public function getPercentCssClass($percent) {
    if ($percent > 80) {
      return 'success';
    }
    if ($percent > 65) {
      return 'error';
    }
    if ($percent >= 0) {
      return 'caution';
    }
    return 'info';
  }

  /**
   * Get the CSS class associated with a score.
   *
   * @return string
   *   Name of the Twitter bootstrap class.
   */
  public function getScoreCssClass($score = NULL) {
    switch ($score) {
      case SiteAuditCheckBase::AUDIT_CHECK_SCORE_PASS:
        return 'success';

      case SiteAuditCheckBase::AUDIT_CHECK_SCORE_WARN:
        return 'warning';

      case SiteAuditCheckBase::AUDIT_CHECK_SCORE_INFO:
        return 'info';

      default:
        return 'danger';

    }
  }

  /**
   * Build the header of the page.
   */
  public function buildHeader() {
    $this->build = [
      // '#type' => 'page',.
      'container' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'class' => 'container',
        ],
        'page_header' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('<a href="@site-audit-uri">Site Audit</a> report for @site', [
            '@site-audit-uri' => 'https://drupal.org/project/site_audit',
            '@site' => $this->options['uri'],
          ]),
          '#attributes' => [
            'id' => 'page-header',
          ],
          'br' => [
            '#type' => 'html_tag',
            '#tag' => 'br',
          ],
          'sub_head' => [
            '#type' => 'html_tag',
            '#tag' => 'small',
            '#value' => $this->t('Generated on @date_time', ['@date_time' => \Drupal::service('date.formatter')->format(\Drupal::time()->getRequestTime())]),
          ],
        ],
      ],
    ];
    if (is_array($this->checklist)) {
      // There are multiple reports.
      $this->build['container']['summary'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'id' => 'summary',
        ],
      ];
      $this->build['container']['summary']['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Summary'),
      ];
      $this->build['container']['summary']['links'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
      ];
      foreach ($this->checklist as $checklist) {
        $this->build['container']['summary']['links'][$checklist->getPluginId()] = [
          '#type' => 'html_tag',
          '#tag' => 'a',
          '#value' => $checklist->getLabel() . ' (' . $checklist->getPercent() . '%)',
          '#attributes' => [
            'href' => '#' . $checklist->getPluginId(),
            'class' => $this->getPercentCssClass($checklist->getPercent()),
          ],
        ];
      }
    }
  }

  /**
   * Check to see if the bootstrap option was selected and wrap in HTMl and add
   * bootstrap derived styles is so.
   */
  protected function checkBootstrap() {
    if (isset($this->options['bootstrap']) && $this->options['bootstrap']) {
      $this->build = [
        '#type' => 'html_tag',
        '#tag' => 'html',
        'head' => [
          '#type' => 'html_tag',
          '#tag' => 'head',
          'title' => [
            '#type' => 'html_tag',
            '#tag' => 'title',
            '#value' => $this->t('Site Audit report for @site', [
              '@site' => $this->options['uri'],
            ]),
          ],
          'bootstrap' => [
            '#type' => 'html_tag',
            '#tag' => 'link',
            '#attributes' => [
              'href' => 'https://stackpath.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css',
              'rel' => 'stylesheet',
              'crossorigin' => 'anonymous',
            ],
          ],
          'styles' => [
            '#type' => 'html_tag',
            '#tag' => 'style',
            '#value' => $this->getStyles(),
            '#attributes' => [
              'type' => 'text/css',
            ],
          ],
        ],
        'body' => [
          '#type' => 'html_tag',
          '#tag' => 'body',
          $this->build,
        ],
      ];
    }
    elseif (isset($this->options['inline']) && $this->options['inline']) {
      $this->build['#attached']['library'][] = 'site_audit/bootstrap';
    }
  }

  /**
   * Render either one report, or multiple.
   */
  public function render($detail = FALSE) {
    if (is_array($this->checklist)) {
      // There are multiple reports.
      foreach ($this->checklist as $checklist) {
        $this->build['container'][$checklist->getPluginId()] = $this->renderReport($checklist);
        $this->build['container'][$checklist->getPluginId()]['top_link'] = [
          '#type' => 'html_tag',
          '#tag' => 'a',
          '#value' => $this->t('Back to top'),
          '#attributes' => [
            'href' => '#summary',
          ],
        ];
      }
    }
    else {
      $this->build['container'][$this->checklist->getPluginId()] = $this->renderReport($this->checklist);
    }

    $this->checkBootstrap();
    if ($this->options['inline']) {
      // This is being requested as a page, not through CLI.
      return $this->build;
    }
    $out = \Drupal::service('renderer')->renderRoot($this->build);
    return $out;
  }

  /**
   * Render a single report.
   * @param $checklist
   * @return array
   */
  public function renderReport(SiteAuditChecklistBase $checklist) {
    $build = [];
    // The report header.
    $build['report_label'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $checklist->getLabel() . ' ',
      '#attributes' => [
        'id' => $checklist->getPluginId(),
      ],
      'percent' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $checklist->getPercent() . '%',
        '#attributes' => [
          'class' => 'label label-' . $this->getPercentCssClass($checklist->getPercent()),
        ],
      ],
    ];

    $percent = $checklist->getPercent();

    if ($percent != SiteAuditCheckBase::AUDIT_CHECK_SCORE_INFO) {
      // Show percent.
      $build['report_label']['percent'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $percent . '%',
        '#prefix' => ' ',
        '#attributes' => [
          'class' => 'label label-' . $this->getPercentCssClass($percent),
        ],
      ];
    }
    else {
      $build['label']['info'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $this->t('Info'),
        '#attributes' => [
          'class' => 'label label-info',
        ],
      ];
    }

    if ($percent == 100) {
      $build['success'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => '<strong>' . $this->t('Well done!') . '</strong> ' . $this->t('No action required.'),
        '#attributes' => [
          'class' => 'text-success',
        ],
      ];
    }

    if ($this->options['detail'] || $percent != 100) {
      foreach ($checklist->getCheckObjects() as $check) {
        $checkBuild = [];
        $score = $check->getScore();
        if (
          $this->options['detail'] || // detail is required
          $score < SiteAuditCheckBase::AUDIT_CHECK_SCORE_PASS || // this check didn't pass
          $percent == SiteAuditCheckBase::AUDIT_CHECK_SCORE_INFO // info needs to be returned
        ) {
          // Heading.
          $checkBuild['panel']['panel_heading'] = [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#value' => '<strong>' . $check->getLabel() . '</strong>',
            '#attributes' => [
              'class' => 'panel-heading',
            ],
          ];

          if ($this->options['detail']) {
            $checkBuild['panel']['panel_heading']['description'] = [
              '#type' => 'html_tag',
              '#tag' => 'small',
              '#value' => '- ' . $check->getDescription(),
            ];
          }

          // Result.
          $checkBuild['#result'] = $check->getResult();
          if (is_array($check->getResult())) {
            $checkBuild['result'] = $check->getResult();
            $checkBuild['result']['#attributes']['class'] = 'well result';
          }
          else {
            $checkBuild['detail'] = [
              '#type' => 'html_tag',
              '#tag' => 'p',
              '#value' => $check->getResult(),
              '#attributes' => [
                'class' => 'well result',
              ],
            ];
          }

          // Action.
          if ($action = $check->renderAction()) {
            $checkBuild['action'] = [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#attributes' => [
                'class' => 'well action',
              ],
            ];
            if (!is_array($action)) {
              $checkBuild['action']['text'] = [
                '#type' => 'html_tag',
                '#tag' => 'p',
                '#value' => $action,
              ];
            }
            else {
              $checkBuild['action']['rendered'] = $action;
            }
          }
          $build[$check->getPluginId()] = [
            '#type' => 'html_tag',
            '#tag' => 'div',
            //'#value' => '<strong>' . $this->t('Well done!') . '</strong> ' . $this->t('No action required.'),
            '#attributes' => [
              'class' => 'panel panel-' . $this->getScoreCssClass($check->getScore()),
              'id' => 'check-' . $check->getPluginId(),
            ],
            $checkBuild,
          ];
        }
      }
    }
    return $build;
  }

  /**
   * Render the results as a table.
   */
  public function table($element) {
    return \Drupal::service('renderer')->render($element);
  }

  /**
   *
   */
  public static function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
  }

  /**
   * Provide the bootstrap derived styles.
   */
  private function getStyles() {
    $file = \Drupal::service('extension.list.module')->getPath('site_audit') . '/css/bootstrap-overrides.css';
    $styles = "/* $file */\n" . file_get_contents($file);
    return $styles;
  }

}
