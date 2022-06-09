<?php

namespace app\models\form;

use app\models\Supplier;
use yii\base\Model;
use yii\db\ActiveQuery;

/**
 * ContactForm is the model behind the contact form.
 */
class SupplierFilterForm extends Model
{
    public string $id = '';
    public string $name = '';
    public string $code = '';
    public string $status = '';

    public function formName(): string
    {
        return 'supplier';
    }

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            ['id', 'match', 'pattern' => '/^(>=|>|<|<=|=)?\s*\d+$/'],
            ['status', 'in', 'range' => ['ok', 'hold', 'all']],
            ['name', 'string', 'max' => 50],
            ['code', 'string', 'max' => 3],
        ];
    }

    /**
     * 获取 Filter Query
     */
    public function getQuery(): ActiveQuery
    {
        $query = Supplier::find();

        if ($this->validate()) {
            if (!empty($this->id)) {
                preg_match('/^(>=|>|<|<=|=)?\s*(\d+)$/', $this->id, $match);
                if (empty($match[1])) {
                    $query->andWhere(['id' => $match[2]]);
                } else {
                    $query->andWhere([$match[1], 'id', $match[2]]);
                }
            }

            if (!empty($this->name)) {
                $query->andWhere(['like', 'name', $this->name]);
            }

            if (!empty($this->code)) {
                $query->andWhere(['like', 'code', $this->code]);
            }

            if (!empty($this->status) && $this->status !== 'all') {
                $query->andWhere(['t_status' => $this->status]);
            }
        }

        return $query;
    }
}
