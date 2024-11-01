<?php

namespace Drupal\ai_provider_azure;

use Drupal\ai\OperationType\Chat\StreamedChatMessage;
use Drupal\ai\OperationType\Chat\StreamedChatMessageIterator;

/**
 * Azure Chat message iterator.
 */
class AzureChatMessageIterator extends StreamedChatMessageIterator {

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \Generator {
    foreach ($this->iterator->getIterator() as $data) {
      yield new StreamedChatMessage(
        $data->choices[0]->delta->role ?? '',
        $data->choices[0]->delta->content ?? '',
        $data->usage ? $data->usage->toArray() : []
      );
    }
  }

}
