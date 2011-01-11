<?php

namespace ShapingModule;

/**
 * Queue creator takes a shaper and prepares list of queues for it.
 * Result will be array of queues, where each queue is array of parameters:
 *
 */
interface IQueueCreator {
  /**
   * Clean any state
   * @return void
   */
  function clean();

  /**
   * Create queues for given shaper
   * @param Shaper $shaper
   * @return void
   */
  function create(\Shaper $shaper);

  /**
   * Get list of prepared queues
   * @return array
   */
  function getQueues();
}
