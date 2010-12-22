<?php

use ActiveEntity\Entity;

class DashboardPresenter extends BasePresenter {
  /**************   Basic renderers    *****************/

  /**
   * Show list of entities
   * @param string $what Entity alias
   */
  public function renderList($what) {
    $this->setTemplateFactory('code');

    $tbl = $this->getTable($this->getEntityName($what), $_GET);
    $this->template->content = $tbl->render();
    $this->template->title = $what;
  }

  /**
   * Show form for adding new item
   * @param string $what
   */
  public function renderAdd($what) {
    $this->template->what = $what;

    $frm = $this->getForm($what);
    $frm['action']->setValue('add');
    $frm['save']->caption = 'Add!';
    $frm->setDefaults($_GET);
  }

  /**
   * Shows form for editing entity
   * @throws BadRequestException
   * @param string $what Entity alias
   * @param int $id Entity ID
   * @return void
   */
  public function renderEdit($what, $id) {
    $this->template->what = $what;

    // Set-up form
    $frm = $this->getForm($what);
    if(!$frm->isSubmitted()) {
      $frm['action']->setValue('edit');

      // Find entity
      $cls = $this->getEntityName($what);
      $row = Entity::find($id, $cls);
      if(!$row) throw new Nette\Application\BadRequestException('Record not found');

      // Set item's index
      $indexField = Entity::getClassMetadata($cls)->getSingleIdentifierFieldName();
      $frm['index']->setValue($row->$indexField);

      $frm->setDefaults($row->toArray());
    }
  }

  /**
   * Shows form for cloning entity
   * @throws BadRequestException
   * @param string $what Entity alias
   * @param int $id Entity ID
   * @return void
   */
  public function renderClone($what, $id) {
    $this->template->what = $what;

    // Set-up form
    $frm = $this->getForm($what);
    if(!$frm->isSubmitted()) {
      $frm['action']->setValue('clone');
      $frm['save']->caption = 'Clone!';

      // Find entity
      $cls = $this->getEntityName($what);
      $row = Entity::find($id, $cls);
      if(!$row) throw new Nette\Application\BadRequestException('Record not found');

      // Set item's index
      $indexField = Entity::getClassMetadata($cls)->getSingleIdentifierFieldName();
      $frm['index']->setValue($row->$indexField);

      //\Nette\Debug::dump($row->toArray());
      $frm->setDefaults($row->toArray());
    }
  }

  /**
   * Show form for confirmation of deleting an entity
   * @param string $what Entity alias
   * @param int $id Entity ID
   * @return void
   */
  public function renderDelete($what, $id) {
    $this->template->what = $what;
    $this->template->index = $id;

    $cls = $this->getEntityName($what);
    $row = Entity::find($id, $cls);
    if(!$row) throw new Nette\Application\BadRequestException('Record not found');

    $fieldName = Entity::getClassMetadata($cls)->getNameField();
    $this->template->name = $row->$fieldName;
  }

  /**
   * Perform deleting on an entity
   * @throws BadRequestException
   * @param string $what
   * @param id $id
   * @return void
   */
  public function handleDelete($what, $id) {
    $cls = $this->getEntityName($what);
    $row = Entity::find($id, $cls);
    if(!$row) throw new Nette\Application\BadRequestException('Record not found');

    $row->remove();
    $row->flush();

    $this->flashMessage("Deleted!");
    $this->redirect('default');
  }


  /**************   Working with forms  ****************/
  /**
   * Creates new component by name
   * @param string $name Name of component
   * @return AppForm|IComponent
   */
  protected function createComponent($name) {
    if(preg_match('/^([a-zA-Z]+)Form$/', $name, $match)) return $this->createForm($match[1]);
    else return parent::createComponent($name);
  }

  /**
   * @param  $alias
   * @return \DoctrineForm
   */
  function getForm($alias) {
    return $this->getComponent($alias . 'Form');
  }

  /**
   * Creates new form for a given entity
   * @param string $alias Name of entity
   * @return DoctrineForm Created form
   */
  protected function createForm($alias, $addSubmitAction = true) {
    $entity = $this->getEntityName($alias);
    $frm = new DoctrineForm($entity);

    // Add buttons
    $frm->addSubmit('save', 'Save')->setAttribute('class', 'default');
    $frm->addSubmit('cancel', 'Cancel')->setValidationScope(NULL);

    // Add protection
    $frm->addProtection('Please submit this form again (security token has expired).');

    if($addSubmitAction) {
      $frm->onSubmit[] = callback($this, 'onFormSubmitted');
    }

    return $frm;
  }

  /**
   * Callback when form is submitted
   */
  public function onFormSubmitted(DoctrineForm $frm) {
    if($frm['save']->isSubmittedBy()) {
      $frm->saveForm();
      $this->flashMessage('Saved!');
      $this->redirect('default');
    }
  }
}
