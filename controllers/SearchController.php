<?php

namespace app\controllers;

use app\components\gui\Breadcrumb;
use app\models\dj\Dream;
use app\models\dj\DreamCategory;
use app\models\dj\DreamType;
use app\models\freud\Concept;
use app\models\search\DreamForm;
use app\models\search\DreamQuery\CategoryCondition;
use app\models\search\DreamQuery\ConceptCondition;
use app\models\search\DreamQuery\Condition;
use app\models\search\DreamQuery\DreamQuery;
use app\models\search\DreamQuery\DreamTextCondition;
use app\models\search\DreamQuery\ListCondition;
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

		$dreamForm->limit = NULL;
		$dreamForm->page = NULL;

		$searchedDreams = $dreamForm->getDreams();
		$relatedDreams = $dream->findRelated(true);

		$searchedDreamIds = [];
		foreach($searchedDreams as $searchedDream)
		{
			$searchedDreamIds[] = $searchedDream->getId();
		}

		//Find all of the related dreams
		$filteredDreams = [];
		foreach($relatedDreams as $relatedDream)
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
			$filteredDreams = array_slice($filteredDreams, $page * $limit, $limit);
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
		try {
			$dreamQuery = new DreamQuery();

			$lucidType = DreamType::find()->where(['name' => 'Lucid'])->one();
			$typeCondition = new TypeCondition($lucidType->getId(), QueryCondition::OPERATOR_EQUALS);
			$typeCondition->setOperator(QueryCondition::CONDITION_AND);
			$dreamQuery->addCondition($typeCondition);

			$recurrentType = DreamType::find()->where(['name' => 'Recurrent'])->one();
			$typeCondition = new TypeCondition($recurrentType->getId(), QueryCondition::OPERATOR_EQUALS);
			$typeCondition->setOperator(QueryCondition::CONDITION_OR);
			//$dreamQuery->addCondition($typeCondition);

			$animalCat = DreamCategory::find()->where(['name' => 'Animals'])->one();
			$catCondition = new CategoryCondition($animalCat->getId(), QueryCondition::OPERATOR_EQUALS);
			$dreamQuery->addCondition($catCondition);

			$lucidWord = "Sheldon";
			$wordCondition = new DreamTextCondition($lucidWord, DreamTextCondition::OPERATOR_NOT_LIKE);
			$dreamQuery->addCondition($wordCondition);

			$lucidPhrase = "'Lucid Dream Test'";
			$wordCondition = new DreamTextCondition($lucidPhrase, DreamTextCondition::OPERATOR_NOT_LIKE);
			$dreamQuery->addCondition($wordCondition);

			$listCondition = $dreamQuery->getCondition()->toList();
			$sheldonCondition = new DreamTextCondition('Sheldon', DreamTextCondition::OPERATOR_LIKE);
			$sheldonCondition->setOperator(DreamTextCondition::CONDITION_OR);
			$listCondition->addCondition($sheldonCondition);
			$dreamQuery->setCondition($listCondition->toList());

			$flyingConcept = Concept::find()->name('Flying')->one();
			$flyingCondition = new ConceptCondition($flyingConcept->id, QueryCondition::OPERATOR_NOT_EQUALS);
			$dreamQuery->addCondition($flyingCondition);

			ob_start();
			$dreams = $dreamQuery->query();
			print "<b>Dreams</b><br>";
			foreach ($dreams as $dream)
			{
				print '<a href="/dream/view/' . $dream->getId() . '">' . $dream->getTitle() . '</a><br>';
			}
			$data = ob_get_clean();
			return $data;
		}
		catch(\Exception $e)
		{
			die($e->getMessage());
		}
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