<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "suppliers".
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string $t_status
 */
class Supplier extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'suppliers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['t_status'], 'string'],
            [['name'], 'string', 'max' => 50],
            ['code', 'string', 'max' => 3],
            ['code', 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            't_status' => 'Status',
        ];
    }
}