<?php

namespace Drupal\ai_interpolator_fireworksai\Plugin\AiInterPolatorFieldRules;

use Drupal\ai_interpolator_fireworksai\FireworksaiOptionsBase;

/**
 * The rules for a list_string field.
 *
 * @AiInterpolatorFieldRule(
 *   id = "ai_interpolator_fireworksai_list_string",
 *   title = @Translation("Fireworks AI List String"),
 *   field_rule = "list_string",
 * )
 */
class FireworksaiListString extends FireworksaiOptionsBase {

  /**
   * {@inheritDoc}
   */
  public $title = 'Fireworks AI List String';

}