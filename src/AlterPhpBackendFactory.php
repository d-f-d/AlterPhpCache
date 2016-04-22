<?php

namespace dfd\AlterPhpCache;

use Drupal\Core\Cache\PhpBackendFactory;

class AlterPhpBackendFactory extends PhpBackendFactory {
  function get($bin) {  
    return new AlterPhpBackend($bin, $this->checksumProvider);
  }
}