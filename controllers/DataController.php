<?php

namespace app\controllers;

use app\components\gui\Breadcrumb;
use app\components\gui\ActionItem;
use app\models\data\ExportForm;
use app\models\data\ImportForm;
use app\models\dj\Dream;
use app\models\dj\DreamCategory;
use app\models\dj\DreamType;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

/**
 * Class DataController
 *
 * Imports and exports dream data.
 *
 * @package app\controllers
 */
class DataController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [];
	}

	public function beforeAction($action)
	{
		$this->addBreadcrumb(new Breadcrumb('Data', '/data'));
		return parent::beforeAction($action); // TODO: Change the autogenerated stub
	}

	public function actionExport()
	{
		$this->getView()->title = 'Export Dreams';
		$this->addBreadcrumb(new Breadcrumb('Export', '', true));

		$exportForm = new ExportForm();

		$request = \Yii::$app->request;
		if($request->getIsPost())
		{
			$exportForm->load($request->post());

			if($exportForm->format == 'json')
			{
				//Send JSON data
				$dreams = $exportForm->getDreamData();
				$file = tmpfile();
				fwrite($file, Json::encode($dreams, JSON_PRETTY_PRINT));
				return \Yii::$app->response->sendStreamAsFile($file, 'dream-export-' . date('Y-m-d') . '.json');
			}
			else
			{
				//Render HTML data

				//Group dreams by date
				$dateToDreams = [];
				$dreamCount = 0;

				foreach($exportForm->getDreams() as $dream)
				{
					$dreamCount++;
					$dreamDate = $dream->getFormattedDate();
					if(!isset($dateToDreams[$dreamDate]))
					{
						$dateToDreams[$dreamDate] = [];
					}
					$dateToDreams[$dreamDate][] = $dream;
				}

				$data = [];
				$data['dateToDreams'] = $dateToDreams;
				$data['dreamCount'] = $dreamCount;
				$data['userName'] = 'Sheldon Juncker';
				$data['currentTime'] = date('l, F dS Y');
				return $this->renderPartial('export-html', $data);
			}
		}

		return $this->render('export', [
			'exportForm' => $exportForm
		]);
	}

	public function actionImport()
	{
		$this->getView()->title = 'Import Dreams';
		$this->addBreadcrumb(new Breadcrumb('Import', '', true));

		$importForm = new ImportForm();
		$request = \Yii::$app->request;
		if($request->getIsPost())
		{
			$importForm->load($request->post());
			$importForm->file = UploadedFile::getInstance($importForm, 'file');
			$fileContents = $importForm->readFile();

			if($fileContents)
			{
				if($importForm->format == 'json')
				{
					$dreamData = Json::decode($fileContents);
					foreach($dreamData as $dreamAttributes)
					{
						$dream = new Dream();
						$dream->attributes = $dreamAttributes;
						$dream->setId($dream->id);

						$dreamAlreadyExists = Dream::find()->andWhere('id = :id',[':id' => $dream->id])->exists();
						if($dreamAlreadyExists)
						{
							continue;
						}
						if(!$dream->save())
						{
							print "<pre>";
							var_dump($dream->getErrors());
							exit;
						}

						foreach($dreamAttributes['categories'] as $categoryName)
						{
							$category = DreamCategory::find()->andWhere('name = :name', [':name' => $categoryName])->one();
							if($category)
							{
								$dream->link('categories', $category);
							}
						}

						foreach($dreamAttributes['types'] as $typeName)
						{
							$type = DreamType::find()->andWhere('name = :name', [':name' => $typeName])->one();
							if($type)
							{
								$dream->link('types', $type);
							}
						}
						$dream->save();
					}
				}
				else
				{
					throw new BadRequestHttpException("Text import no longer supported.");
				}
			}
		}

		return $this->render('import', [
			'importForm' => $importForm
		]);
	}
}