<?php

namespace CustomerModule;

use Nette\Application\AppForm,
	Nette\Forms\Form,
	Doctrine;



class DashboardPresenter extends BasePresenter
{
	/********************* view default *********************/



	public function renderDefault()
	{
    $list = Doctrine::getTable('Zakaznik')->findAll();
    $this->template->customers = $list;
	}



	/********************* views add & edit *********************/



	public function renderAdd()
	{
		$this['customer']['save']->caption = 'Add';
	}



	public function renderEdit($id = 0)
	{
		$form = $this['customer'];
		if (!$form->isSubmitted()) {
			$row = Doctrine::getTable('Zakaznik')->findOneByPorCis($id);
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
