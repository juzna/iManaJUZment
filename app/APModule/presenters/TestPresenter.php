<?php

namespace APModule;

use Nette\Application\AppForm,
	Nette\Forms\Form,
	Doctrine;



class TestPresenter extends BasePresenter
{
  // List of entity aliases (for listing, adding, editing, removing)
  private $entityAliases = array(
    'ap'    => 'AP',
    'ip'    => 'APIP',
    'swif'  => 'APSwIf',
  );
  
  /**
   * Renders Access point's detail page
   * @param int $id ID of access point
   */
  public function renderDetail($id) {
    $this->template->AP = \AP::find($id);
  }
  
  /**
   * Show list of entities
   * @param string $what Entity alias
   */
  public function renderList($what)
  {
    $this->template->what = $what;
    $this->template->list = Doctrine::getTable($this->getEntityName($what))->findAll();
  }
  
  public function renderTable($what) {
    $this->setTemplateFactory('code');

    $tbl = $this->getTable($this->getEntityName($what), $_GET);
    $this->template->content = $tbl->render();
    $this->template->title = 'Voda';
  }
  

  /**
   * Form for adding new entity
   * @param string $what Entity alias
   */
  public function renderAdd($what)
  {
    $l = (object) null;
    $l->blocks['content'][] = function() { echo "PEPEK jede"; };
  
    $this->template->what = $what;
    $this->template->_l = $l;
    
    // Change button
    $wg = $this->getWidget($what);
    $wg['save']->caption = 'Add';
  }



	public function renderEdit($what, $id)
	{
    $row = Doctrine::getTable($what)->findOneByID($id);
    return;
	
		$form = $this['ap'];
		if (!$form->isSubmitted()) {
			$row = Doctrine::getTable('AP')->findOneByID($id);
			if (!$row) {
				throw new Nette\Application\BadRequestException('Record not found');
			}
			$form->setDefaults($row);
		}
	}



	/********************* view delete *********************/



	public function renderDelete($id = 0)
	{
		$album = new Albums;
		$this->template->customer = $row = Doctrine::getTable('Zakaznik')->findOneByPorCis($id);
		if (!$this->template->customer) {
			throw new Nette\Application\BadRequestException('Record not found');
		}
	}



  /********************* component factories *********************/
  protected function createComponentAp() {
    $form = new AppForm;
    $form->addText('nazev', 'Name')->addRule(Form::FILLED, 'Enter AP name');
    $form->addText('popis', 'Description')->addRule(Form::FILLED, 'Enter description');

    $form->addSubmit('save', 'Save')->setAttribute('class', 'default');
    $form->addSubmit('cancel', 'Cancel')->setValidationScope(NULL);
    
    return $form;
  }



	/**
	 * Album edit form component factory.
	 * @return mixed
	 */
	protected function createComponentCustomer()
	{
		$form = new AppForm;
		$form->addText('PorCis', 'Internal ID:')
			->addRule(Form::FILLED, 'Please enter an artist.');

		$form->addText('cisloSmlouvy', 'Contract no:')
			->addRule(Form::FILLED, 'Please enter contract number.');

		$form->addSubmit('save', 'Save')->setAttribute('class', 'default');
		$form->addSubmit('cancel', 'Cancel')->setValidationScope(NULL);
		$form->onSubmit[] = callback($this, 'customerFormSubmitted');

		$form->addProtection('Please submit this form again (security token has expired).');
		return $form;
	}



	public function customerFormSubmitted(AppForm $form)
	{
		if ($form['save']->isSubmittedBy()) {
			$id = (int) $this->getParam('id');
			//$album = new Albums;
			if ($id > 0) {
				//$album->update($id, $form->values);
				$this->flashMessage('The album has been updated.');
			} else {
				//$album->insert($form->values);
				$this->flashMessage('The album has been added.');
			}
		}

		$this->redirect('default');
	}

}
