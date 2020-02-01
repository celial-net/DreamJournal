<?php

namespace app\controllers;

use app\components\gui\ActionItem;
use app\components\gui\Breadcrumb;
use app\components\gui\js\PackageStore;
use app\components\gui\js\Registrar;
use app\components\gui\flash\FlashContainer;
use app\components\gui\flash\IFlash;
use app\models\dj\User;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Request;

class BaseController extends Controller
{
	/** @var ActionItem[] $actionItems */
	protected $actionItems = NULL;

	/** @var Breadcrumb[] $breadcrumbs */
	protected $breadcrumbs = NULL;

	/** @var Registrar $scriptRegistrar */
	protected $scriptRegistrar = NULL;

	/** @var FlashContainer $flashContainer  */
	protected $flashContainer = NULL;

	public function __construct($id, $module, $config = [])
	{
		parent::__construct($id, $module, $config);

		$this->breadcrumbs = new \ArrayObject();
		$this->actionItems = new \ArrayObject();

		//Setup scripts
		$this->scriptRegistrar = new Registrar();
		$packageStore = new PackageStore();
		$this->getScriptRegistrar()->registerPackage(
			$packageStore->getBootstrapPackage()
		);

		//Load the flash container from the session
		$session = \Yii::$app->getSession();
		$flashContainer = $session->get('flashContainer') ?? new FlashContainer();
		$this->flashContainer = $flashContainer;
		$session->set('flashContainer', $flashContainer);
	}

	public function getScriptRegistrar(): Registrar
	{
		return $this->scriptRegistrar;
	}

	public function addBreadcrumb(Breadcrumb $breadcrumb)
	{
		$this->breadcrumbs[] = $breadcrumb;
	}

	public function addActionItem(ActionItem $actionItem)
	{
		$this->actionItems[] = $actionItem;
	}

	public function addFlashes(array $flashes)
	{
		foreach($flashes as $flash)
		{
			$this->addFlash($flash);
		}
	}

	public function addFlash(IFlash $flash)
	{
		$this->flashContainer->addFlash($flash);
	}

	/**
	 * Gets the default controller access which limits the interface
	 * to logged in users.
	 *
	 * @return array
	 */
	public function getDefaultAccess(): array
	{
		return $this->getAccessByPermission(['@']);
	}

	/**
	 * Gets access rules which lock down to a set of permissions.
	 *
	 * @param array $permissions
	 * @return array
	 */
	public function getAccessByPermission(array $permissions): array
	{
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => $permissions,
					]
				]
			]
		];
	}

	/**
	 * Gets the logged in user or NULL if there is none.
	 *
	 * @return User|null
	 */
	public function getUser(): ?User
	{
		return \Yii::$app->getUser()->getIdentity();
	}

	/**
	 * Gets the request if there is one.
	 *
	 * @return null|Request
	 */
	public function getRequest(): ?Request
	{
		return \Yii::$app->getRequest();
	}

	public function render($view, $params = [])
	{
		//Set scripts
		$this->view->params['headerScripts'] = $this->getScriptRegistrar()->getHeaderScripts();
		$this->view->params['bodyScripts'] = $this->getScriptRegistrar()->getBodyScripts();
		$this->view->params['breadcrumbs'] = $this->breadcrumbs;
		$this->view->params['actionItems'] = $this->actionItems;
		$this->view->params['flashContainer'] = $this->flashContainer;

		return parent::render($view, $params);
	}

	public function pre($data, bool $die = false)
	{
		print "<pre>";
		print_r($data);
		print "</pre>";
		if($die) die();
	}
}