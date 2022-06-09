<?php

namespace app\controllers;

use app\models\form\SupplierFilterForm;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\Response;

class SupplierController extends Controller
{

    /**
     * Displays Suppliers.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $form = new SupplierFilterForm();
        $form->load($this->request->get());

        $dataProvider = new ActiveDataProvider([
            'query' => $form->getQuery(),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider, 'model' => $form]);
    }

    /**
     * Export suppliers as CSV file
     * @return \yii\web\Response
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionExport(): Response
    {
        // Export columns
        $columns = explode(',', str_replace('status', 't_status', $this->request->get('columns')));
        $columns = array_intersect(['name', 'code', 't_status'], $columns);
        $columns[] = 'id'; // Id is required

        // CSV Title
        $content = 'Id';
        if (in_array('name', $columns, true)) {
            $content .= ',Name';
        }
        if (in_array('code', $columns, true)) {
            $content .= ',Code';
        }
        if (in_array('t_status', $columns, true)) {
            $content .= ',Status';
        }

        // Prepare Query
        $form = new SupplierFilterForm();
        $form->load($this->request->get());
        $query = $form->getQuery()->select($columns);
        if (($keys = $this->request->get('keys')) && $keys !== 'all') {
            $query->andWhere(['in', 'id', explode(',', $keys)]);
        }

        // CSV Content
        foreach ($query->asArray()->each(500) as $item) {
            $content .= "\n{$item['id']}";
            if (isset($item['name'])) {
                $content .= ",{$item['name']}";
            }
            if (isset($item['code'])) {
                $content .= ",{$item['code']}";
            }
            if (isset($item['t_status'])) {
                $content .= ",{$item['t_status']}";
            }
        }
        // Export
        $fileName = date('Ymd') . '.csv';
        return $this->response->sendContentAsFile($content, $fileName, ['mimeType' => FileHelper::getMimeTypeByExtension($fileName)]);
    }
}
