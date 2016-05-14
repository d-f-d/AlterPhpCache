<?php

namespace dfd\AlterPhpCache;

use Drupal\Core\Cache\PhpBackend;

class AlterPhpBackend extends PhpBackend {
  /**
   * Writes a cache item to PhpStorage.
   *
   * @param string $cidhash
   *   The hashed version of the original cache ID after being normalized.
   * @param \stdClass $item
   *   The cache item to store.
   */
  protected function writeItem($cidhash, \stdClass $item) {
    $content = '<?php return ' . $this->drupal_var_export($item) . ';';
    $this->storage()->save($cidhash, $content);
  }

  function drupal_var_export($var, $prefix = '') {
    if (is_array($var)) {
      $output = [];
      $export_keys = array_values($var) != $var;
      foreach ($var as $key => $value) {
        $output[] = ($export_keys ? var_export($key, TRUE) . ' => ' : '') . $this->drupal_var_export($value, $prefix . '  ');
      }
      if (strlen($output_imp = implode(', ', $output)) > 120) {
        return "[\n$prefix  " . implode(",\n  $prefix", $output) . "\n$prefix]";
      }
      else {
        return "[$output_imp]";
      }
    }
    if (is_object($var)) {
      if (get_class($var) === 'stdClass') {
        return '(object) ' . $this->drupal_var_export((array) $var, $prefix);
      }
      else {
        return 'unserialize(' . $this->drupal_var_export(serialize($var)) . ')';
      }
    }
    return var_export($var, TRUE);
  }
}