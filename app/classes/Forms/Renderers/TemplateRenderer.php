<?php

namespace Nette\Forms;

use Nette\Templates\FileTemplate;

class TemplateRenderer implements IFormRenderer {
  protected $renderer;
  protected $template;

  public function __construct($file = null) {
    if(is_array($file) && sizeof($file) >= 1) {
      $files = $file;
      foreach($files as $file) {
        if($file = realpath($file)) break;
      }

      if(!$file) throw new \InvalidArgumentException("Template {$files[0]} not found");
    }

    $this->template = new FileTemplate($file);
  }

  public function setFile($file) {
    $this->template->setFile($file);
    return $this;
  }

  public function getTemplate() {
    return $this->template;
  }

  public function setRenderer(IFormRenderer $renderer) {
    $this->renderer = $renderer;
  }

  public function getRenderer() {
    if(!isset($this->renderer)) {
      $this->renderer = new DefaultFormRenderer;
    }

    return $this->renderer;
  }

  /**
   * Render this form
   * @param Form $frm
   * @return string
   */
  function render(Form $frm) {
    $oldRenderer = $frm->getRenderer();

    // Change renderer and draw
    $frm->setRenderer($this->getRenderer());
    $this->template->form = $frm;
    $ret = $this->template->__toString();

    // Return old renderer
    $frm->setRenderer($oldRenderer);

    return $ret;
  }
}
