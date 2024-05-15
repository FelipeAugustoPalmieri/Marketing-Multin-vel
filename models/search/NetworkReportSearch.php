<?php
namespace app\models\search;

use Yii;
use app\models\Consumer;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class NetworkReportSearch extends Model
{
    public $period;
    public $user;

    public $month;
    public $year;

    public function rules()
    {
        return [
            [['user', 'period'], 'required'],
            [['period', 'user', 'consumer', 'month', 'year'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'period' => Yii::t('app', 'Month'),
            'user' => Yii::t('app', 'User'),
        ];
    }

    public function search($params)
    {
        $query = Consumer::find()->andWhere('consumers.id IN (' . implode(',', $this->user->consumer->getTreeIds()) . ')');
        $query->innerJoin('legal_persons', 'legal_persons.id = consumers.legal_person_id');
        $query->innerJoin('physical_persons', 'physical_persons.id = legal_persons.person_id
                                               AND legal_persons.person_class=\'PhysicalPerson\'');

        $dataProvider = new ActiveDataProvider([
           'query' => $query,
        ]);

        $this->load($params);

        $query->orderBy('physical_persons.name ASC');

        return $dataProvider;
    }
}
