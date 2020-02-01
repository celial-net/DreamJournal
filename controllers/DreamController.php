<?php

namespace app\controllers;

use app\components\gui\ActionItem;
use app\components\gui\Breadcrumb;
use app\components\gui\flash\Flash;
use app\components\gui\js\Script;
use app\models\dj\DreamCategory;
use app\models\dj\DreamComment;
use app\models\dj\DreamType;
use app\models\search\DreamForm;
use Rhumsaa\Uuid\Uuid;
use Yii;
use app\models\dj\Dream;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * DreamController implements the CRUD actions for Dream model.
 */
class DreamController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => ['manageDream'],
						'roleParams' => function() {
							$dream = NULL;
							$dreamId = Yii::$app->getRequest()->get('id');
							if($dreamId)
							{
								$dreamId = Uuid::fromString($dreamId)->getBytes();
								$dream = Dream::findOne(['id' => $dreamId]);
							}
							return ['dream' => $dream];
						},
					]
				],
			],
		];
	}

    public function beforeAction($action)
	{
		//Register scripts needed for dreams
		$this->getScriptRegistrar()->registerScript(
			new Script('tagsinput/tagsinput-typeahead.js')
		);

		//Register Vue for dream comments
		$this->getScriptRegistrar()->registerScript(
			new Script('vue/vue.js')
		);

		$this->addBreadcrumb(new Breadcrumb('Dream Journal', '/'));

		return parent::beforeAction($action);
	}

	/**
     * Lists all Dream models.
     * @return mixed
     */
    public function actionIndex(string $period = '', string $search = '')
    {
		$this->addActionItem(new ActionItem('New', '/dream/new', 'primary'));
		$this->addBreadcrumb(new Breadcrumb('Overview', '', true));

    	$dreams = Dream::find()->orderBy('dreamt_at DESC')->whereUserId(Yii::$app->getUser()->getId());


		$allActive = '';
		$weekActive = '';
		$monthActive = '';
		$yearActive = '';

    	if($search)
		{
			//Limit to searched for dreams
			$dreamSearchForm = new DreamForm();
			$dreamSearchForm->user_id = $this->getUser()->getId();
			$dreamSearchForm->search = $search;
			$searchResults = $dreamSearchForm->getDreams();
			$dreams->andWhere(['in' ,'id', array_column($searchResults, 'id')]);
		}
		else
		{
			$period = $period ?: $this->getUser()->loadAccountSettings()->default_dream_period;

			$allActive = $period == 'all' ? 'active' : '';
			$weekActive = $period == 'week' ? 'active' : '';
			$monthActive = $period == 'month' ? 'active' : '';
			$yearActive = $period == 'year' ? 'active' : '';

			//Limit by period
			$startDate = NULL;
			if($weekActive)
			{
				$startDate = strtotime("-1 week");
			}
			else if($monthActive)
			{
				$startDate = strtotime("-1 month", time());
			}
			else if($yearActive)
			{
				$startDate = strtotime("-1 year", time());
			}

			if($startDate)
			{
				$dreams->dreamtBetween($startDate, time());
			}
		}

		$dreams = $dreams->all();

		$dreamsByDay = [];

		if(count($dreams))
		{
			$currentDay = $dreams[0]->getFormattedDate() ?? '';
			foreach($dreams as $dream)
			{
				$dreamDay = $dream->getFormattedDate();
				if($dreamDay != $currentDay)
				{
					$currentDay = $dreamDay;
					$dreamsByDay[] = NULL;
				}
				$dreamsByDay[] = $dream;
			}
		}

        return $this->render('index', [
            'dreams' => $dreamsByDay,
			'weekActive' => $weekActive,
			'monthActive' => $monthActive,
			'yearActive' => $yearActive,
			'allActive' => $allActive
        ]);
    }

	/**
	 * Renders a simple dream list which can filter.
	 *
	 * @return string
	 */
    public function actionList()
	{
		$this->getScriptRegistrar()->registerScript(new Script('dream/dream-list.js'));
		return $this->render('dream-list', [
			'canFilter' => true,
			'formAction' => '/search/list'
		]);
	}

	/**
	 * Renders the related dreams.
	 *
	 * @param string $id
	 * @return string
	 */
	public function actionRelated(string $id)
	{
		$this->getScriptRegistrar()->registerScript(new Script('dream/dream-list.js'));
		return $this->render('related', [
			'canFilter' => true,
			'formAction' => '/search/related/' . $id,
			'searchOnLoad' => 0,
			'resultsPerPage' => $this->getUser()->loadAccountSettings()->results_per_page
		]);
	}

    /**
     * Displays a single Dream model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
		$this->getScriptRegistrar()->registerScript(new Script('dream/dream-list.js'));

		//Register dream comments
		$this->getScriptRegistrar()->registerScript(
			new Script('dream/comments.js')
		);

		$this->getView()->title = 'View Dream';

		$this->addActionItem(new ActionItem('New', '/dream/new', 'primary'));
		$this->addActionItem(new ActionItem('Edit', '/dream/edit/' . $id, 'secondary'));
		$this->addActionItem(new ActionItem('Delete', '/dream/delete/' . $id, 'danger'));

		$this->addBreadcrumb(new Breadcrumb('View', '', true));

        return $this->render('view', [
            'dream' => $this->findModel($id),
			'dreamTypes' => DreamType::find()->excludeNormal()->all(),
			'dreamTypesDisabled' => true,
			'resultsPerPage' => $this->getUser()->loadAccountSettings()->results_per_page
        ]);
    }

    /**
     * Creates a new Dream model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionNew()
    {
		//Register dream comments
		$this->getScriptRegistrar()->registerScript(
			new Script('dream/comments.js')
		);

		$this->getView()->title = 'New Dream';
		$this->addBreadcrumb(new Breadcrumb('New', '', true));

		$dream = new Dream();
		$dream->user_id = Yii::$app->getUser()->getId();

		$request = Yii::$app->request;
		if($request->getIsPost())
		{
			$postData = $request->post($dream->formName(), []);
			$categoryIdString = $postData['categories'] ?? "";
			$categories = explode(',', $categoryIdString);
			$types = $postData['types'] ?? [];
			$dreamComments = $postData['comment'] ?? [];

			if($postData)
			{
				$dream->attributes = $postData;
				if($dream->save())
				{
					//Add new categories
					if($categories)
					{
						foreach($categories as $category)
						{
							$dreamCategory = DreamCategory::find()->andWhere(['id' => $category])->one();
							if($dreamCategory)
							{
								$dream->link('categories', $dreamCategory);
							}
						}
						$dream->save();
					}

					//Add types
					if($types)
					{
						foreach($types as $typeId => $on)
						{
							$dreamType = DreamType::find()->andWhere(['id' => $typeId])->one();
							if($dreamType)
							{
								$dream->link('types', $dreamType);
							}
						}
						$dream->save();
					}

					if($dreamComments)
					{
						//Add new comments
						$dreamComments = $postData['comment'] ?? [];
						foreach($dreamComments as $commentId => $commentText)
						{
							$dreamComment = new DreamComment();
							$dreamComment->setId(Uuid::uuid1()->toString());
							$dreamComment->description = $commentText;
							$dreamComment->setUserId($this->getUser()->getId());
							$dreamComment->setDreamId($dream->getId());
							$dream->link('comments', $dreamComment);
						}
						$dream->save();
					}


					$this->addFlash(new Flash('Dream saved.', Flash::SUCCESS));
					return $this->redirect(['view', 'id' => $dream->getId()]);
				}
				else
				{
					$this->addFlash(new Flash('Dream failed to save.', Flash::FAILURE));
				}
			}
		}

        return $this->render('_form', [
            'dream' => $dream,
			'dreamTypes' => DreamType::find()->excludeNormal()->all(),
			'dreamTypesDisabled' => false,
			'categoryIdString' => ''
        ]);
    }

    /**
     * Updates an existing Dream model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEdit($id)
    {
		//Register dream comments
		$this->getScriptRegistrar()->registerScript(
			new Script('dream/comments.js')
		);

		$this->getView()->title = 'Edit Dream';

		$this->addActionItem(new ActionItem('New', '/dream/new', 'primary'));
		$this->addActionItem(new ActionItem('Cancel', '/dream/view/' . $id, 'secondary'));

		$this->addBreadcrumb(new Breadcrumb('Edit', '', true));

        $dream = $this->findModel($id);

		//Add dream to dream categories
		$categories = [];
		foreach($dream->categories as $category)
		{
			$categories[] = $category->getId();
		}
		$categoryIdString = implode(',', $categories);

		$request = Yii::$app->request;
		if($request->getIsPost())
		{
			$postData = $request->post($dream->formName(), []);

			$categoryIdString = $postData['categories'] ?? "";
			$categories = explode(',', $categoryIdString);
			$types = $postData['types'] ?? [];

			if($postData)
			{
				//Remove all previous categories
				$dream->unlinkAll('categories', true);

				//Add new categories
				foreach($categories as $category)
				{
					$dreamCategory = DreamCategory::find()->andWhere(['id' => $category])->one();
					if($dreamCategory)
					{
						$dream->link('categories', $dreamCategory);
					}
				}
				$dream->attributes = $postData;

				//Remove all previous types
				$dream->unlinkAll('types', true);

				//Add new types
				foreach($types as $typeId => $on)
				{
					$dreamType = DreamType::find()->andWhere(['id' => $typeId])->one();
					if($dreamType)
					{
						$dream->link('types', $dreamType);
					}
				}

				//Add new comments
				$dreamComments = $postData['comment'] ?? [];
				$deletedComments = $dreamComments['deleted'] ?? [];
				unset($dreamComments['deleted']);

				//Delete comments
				foreach($deletedComments as $commentId => $value)
				{
					$dreamComment = DreamComment::find()->id($commentId)->one();
					if($dreamComment)
					{
						$dreamComment->delete();
					}
				}

				//Create or edit
				foreach($dreamComments as $commentId => $commentText)
				{
					$dreamComment = DreamComment::find()->id($commentId)->one();
					if(!$dreamComment)
					{
						$dreamComment = new DreamComment();
						$dreamComment->setId(Uuid::uuid1()->toString());
						$dreamComment->setUserId($this->getUser()->getId());
						$dreamComment->setDreamId($dream->getId());
					}
					$dreamComment->setDescription($commentText);
					$dream->link('comments', $dreamComment);
				}

				if($dream->save())
				{
					$this->addFlash(new Flash('Dream updated.', Flash::SUCCESS));
					return $this->redirect(['view', 'id' => $dream->getId()]);
				}
				else
				{
					$this->addFlash(new Flash('Dream failed to update.', Flash::FAILURE));
				}
			}
		}

        return $this->render('_form', [
            'dream' => $dream,
			'dreamTypes' => DreamType::find()->excludeNormal()->all(),
			'dreamTypesDisabled' => false,
			'categoryIdString' => $categoryIdString
        ]);
    }

    /**
     * Deletes an existing Dream model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if($this->findModel($id)->delete())
		{
			$this->addFlash(new Flash('Dream deleted.', Flash::SUCCESS));
			return $this->redirect(['index']);
		}
		else
		{
			$this->addFlash(new Flash('Failed to delete dream.', Flash::FAILURE));
			return $this->redirect(['view', 'id' => $id]);
		}
    }

    /**
     * Finds the Dream model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Dream the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Dream
    {
    	$id = Uuid::fromString($id)->getBytes();
        if (($model = Dream::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
