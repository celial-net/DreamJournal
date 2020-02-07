<?php

namespace app\controllers;

use app\components\gui\Breadcrumb;
use app\models\dj\Dream;
use app\models\dj\DreamType;
use app\models\search\DreamForm;
use app\models\search\DreamQuery\Condition;
use app\models\search\DreamQuery\DreamQuery;
use app\models\search\DreamQuery\QueryCondition;
use app\models\search\DreamQuery\TypeCondition;
use Rhumsaa\Uuid\Uuid;
use yii\web\NotFoundHttpException;

class SearchController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		$access = $this->getDefaultAccess();
		return $access;
	}

	public function beforeAction($action)
	{
		$this->addBreadcrumb(new Breadcrumb('Dream Journal', '/'));
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		$this->getView()->title = 'Dream Search';
		$this->addBreadcrumb(new Breadcrumb('Search', '', true));
		$dreamForm = new DreamForm();

		return $this->render('index', [
			'dreamForm' => $dreamForm
		]);
	}

	public function actionList()
	{
		$dreamForm = new DreamForm();
		$dreamForm->user_id = $this->getUser()->getId();
		$dreamForm->load(\Yii::$app->request->get());
		$dreamSearchResponse = $dreamForm->performSearch();
		$data = [
			'total' => 0,
			'results' => []
		];
		if($dreamSearchResponse->isSuccess())
		{
			$dreamData = [];
			foreach($dreamSearchResponse->getDreams() as $dream)
			{
				$dreamData[] = [
					'id' => $dream->getId(),
					'title' => $dream->getTitle(),
					'date' => $dream->getFormattedDate()
				];
			}
			$data['total'] = $dreamSearchResponse->total;
			$data['results'] = $dreamData;
		}

		return $this->asJson($data);
	}

	public function actionRelated(string $id)
	{
		$dream = $this->findDream($id);

		$dreamForm = new DreamForm();
		$dreamForm->user_id = $this->getUser()->getId();
		$dreamForm->load(\Yii::$app->request->get());

		//Get all of the searched for results and filter by their IDs in the related dreams
		$limit = $dreamForm->limit;
		$page = $dreamForm->page;
		$dreamForm->limit = 0;

		$searchedDreams = $dreamForm->getDreams();
		$searchedDreamIds = [];
		foreach($searchedDreams as $searchedDream)
		{
			$searchedDreamIds[] = $searchedDream->getId();
		}

		//Find all of the related dreams
		$filteredDreams = [];
		foreach($dream->findRelated() as $relatedDream)
		{
			if(in_array($relatedDream->getId(), $searchedDreamIds))
			{
				$filteredDreams[] = $relatedDream;
			}
		}

		$data = [
			'total' => count($filteredDreams),
			'results' => []
		];

		$dreamData = [];

		//Do the limit/offset in PHP...:(
		if($limit > 0 && $limit < count($filteredDreams))
		{
			$page = intval($page);
			$filteredDreams = array_slice($filteredDreams, ($page - 1) * $limit, $limit);
		}

		foreach($filteredDreams as $dream)
		{
			$dreamData[] = [
				'id' => $dream->getId(),
				'title' => $dream->getTitle(),
				'date' => $dream->getFormattedDate()
			];
		}
		$data['results'] = $dreamData;

		return $this->asJson($data);
	}

	public function actionSearch()
	{
		$dreamForm = new DreamForm();
		$dreamForm->user_id = $this->getUser()->getId();
		$dreamForm->load(\Yii::$app->request->get());

		$this->getView()->title = 'Search Results';
		$this->addBreadcrumb(new Breadcrumb('Search', '/search'));
		$this->addBreadcrumb(new Breadcrumb('Results for "' . $dreamForm->search . '"', '', true));

		$dreams = $dreamForm->getDreams();

		return $this->render('search', [
			'dreams' => $dreams
		]);
	}

	public function actionDreamquery()
	{
		$dreamQuery = new DreamQuery();

		$lucidType = DreamType::find()->where(['name' => 'Lucid'])->one();
		$typeCondition = new TypeCondition($lucidType->getId(), QueryCondition::OPERATOR_EQUALS);
		$typeCondition->setOperator(QueryCondition::CONDITION_AND);
		$dreamQuery->addCondition($typeCondition);

		$dreams = $dreamQuery->query();
		print "<b>Dreams</b>";
		print "<pre>";
		foreach($dreams as $dream)
		{
			print $dream->getId() . "\n";
		}
		print "</pre>";
	}

	protected function findDream($id): Dream
	{
		$id = Uuid::fromString($id)->getBytes();
		if (($model = Dream::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}