<?php
namespace app\models\search;

use Yii;
use app\models\Consumer;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RepresentativeReportSearch extends Model
{
    public $period;
    public $user;
    public $consumer_representative_id;
    public $city_id;
    public $xinativos;

    public $month;
    public $year;

    public function rules()
    {
        return [
            [['user', 'period', 'city_id'], 'required'],
            [['period', 'user', 'consumer_representative_id', 'city_id', 'month', 'year', 'xinativos'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'period' => Yii::t('app', 'Month'),
            'user' => Yii::t('app', 'User'),
            'consumer_representative_id' => Yii::t('app', 'Representative'),
            'city_id' => Yii::t('app', 'City'),
            'xinativos' => Yii::t('app', 'show all inativos')
        ];
    }

    public function search($params)
    {
        $query = Consumer::find()->affiliationPaid();
        $query->innerJoin('legal_persons', 'legal_persons.id = consumers.legal_person_id');
        $query->innerJoin('physical_persons', 'physical_persons.id = legal_persons.person_id
                                               AND legal_persons.person_class=\'PhysicalPerson\'');

        $dataProvider = new ActiveDataProvider([
           'query' => $query,
        ]);

        $this->load($params);

        if ($this->user->authenticable_type == 'Consumer' && !Yii::$app->user->can('receptionist')) {
            $this->consumer_representative_id = $this->user->consumer->id;
        }

        $query->orderBy('physical_persons.name ASC');
        if(isset($this->xinativos) && $this->xinativos == 1){
            $query->andWhere("consumers.is_disabled = :disabled", [':disabled' => 1]);
        }else{
            $query->andWhere("consumers.is_disabled = :disabled", [':disabled' => 0]);
        }
        
        if (($this->user->authenticable_type == 'Consumer' && !Yii::$app->user->can('receptionist')) ||
           (($this->user->authenticable_type == 'Admin' || Yii::$app->user->can('receptionist')) && isset($this->consumer_representative_id) && $this->consumer_representative_id != '' )) {
            $query->andWhere(
                'legal_persons.city_id IN (
                    SELECT city_id
                    FROM sales_representative_cities
                    WHERE sales_representative_id = :representativeId
                )',
                [':representativeId' => $this->consumer_representative_id]
            );
        }

        if (isset($this->city_id) && $this->city_id != 'ALL' && $this->city_id != '') {
            $query->andWhere(
                    'legal_persons.city_id = :cityId',
                    [':cityId' => $this->city_id]
                );
        }

        return $dataProvider;
    }
}
