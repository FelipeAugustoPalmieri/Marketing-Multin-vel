<?php

namespace app\models;

use Yii;
use app\models\TransactionDetail;
use app\models\ConsumerUserForm;
use app\models\query\ConsumerQuery;
use Hashids\Hashids;
use yii\behaviors\TimestampBehavior;
use app\util\clsTexto;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "consumers".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $legal_person_id
 * @property integer $parent_consumer_id
 * @property string $position
 * @property string $bank_name
 * @property string $bank_agency
 * @property string $bank_account
 * @property boolean $is_business_representative
 * @property boolean $paid_affiliation_fee
 * @property integer $identifier
 * @property integer $x_paid_billet
 * @property integer $maximum_amount
 * @property float $percentage_plot
 * @property integer $plane_investiment_id
 * @property string $id_asaas
 * @property decimal $soma
 *
 * @property Consumer $parentConsumer
 * @property Consumer[] $consumers
 * @property LegalPerson $legalPerson
 * @property PlanoInvestimento $planoinvestimento
 */
class Consumer extends ActiveRecord
{

    const EVENT_SET_REPRESENTATIVE_PERMISSION = 'set-representative-permission';

    public function init()
    {
        $this->on(self::EVENT_SET_REPRESENTATIVE_PERMISSION, [$this, 'setRepresentativePermission']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'consumers';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new ConsumerQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['!legal_person_id'], 'required'],
            [['!legal_person_id'], 'unique'],
            [['!identifier'], 'unique'],
            [['!legal_person_id', 'parent_consumer_id', '!identifier', 'operation'], 'integer'],
            [
                ['parent_consumer_id', 'sponsor_consumer_id', 'position'],
                'required',
                'when' => function($model) {
                    $query = Consumer::find()->andWhere('parent_consumer_id IS NULL');
                    if ($model->id) {
                        $query->andWhere('id <> :id', [':id' => $model->id]);
                    }
                    return $query->count() > 0;
                },
                'whenClient' => 'function(attribute, value) { return false; }',
            ],
            [
                ['plane_id'],
                'required',
                'when' => function($model) {
                    return $model->paid_affiliation_fee == true;
                },
                'whenClient' => 'function(attribute, value) { return false; }',
            ],
            ['position', 'in', 'range' => ['left', 'right'], 'skipOnEmpty' => true],
            ['parent_consumer_id', 'unique', 'targetAttribute' => ['parent_consumer_id', 'position'], 'skipOnEmpty' => true],
            [['parent_consumer_id'], 'validateTree', 'skipOnEmpty' => true],
            [['is_business_representative', '!paid_affiliation_fee'], 'boolean'],
            [['bank_name', 'bank_agency', 'bank_account', 'bank_number'], 'string', 'max' => 255],
            [['id_asaas'], 'string', 'max'=>50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'default' => self::OP_INSERT,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getNextFreeIdentifier()
    {
        $id = 1;

        if ($ultimoRegistro = static::find()->where('identifier is not null')
                                            ->orderBy('identifier desc')->one()) {
            $id += $ultimoRegistro->identifier;
        }

        return $id;
    }

    public function beforeDelete()
    {
        $parent = parent::beforeDelete();

        $this->_clearRelationships();

        return $parent;
    }

    private function _clearRelationships()
    {
        $user = User::findOne(['authenticable_id' => $this->id, 'authenticable_type' => 'Consumer']);

        if (!is_null($user)) {
            Yii::$app->authManager->revokeAll(intval($user->id));
            $user->delete();
        }
    }

    /**
     * Valida a árvore de consumers.
     * @param string $attribute
     * @param array $params
     */
    public function validateTree($attribute, $params = [])
    {
        $parent = self::findOne($this->parent_consumer_id);

        // Parent cannot have more than 2 direct children
        $childrenQuery = $parent->getChildrenConsumers();
        if ($this->id) {
            $childrenQuery->andWhere('id <> :current_id', [':current_id' => $this->id]);
        }
        if ($childrenQuery->count() >= 2) {
            return $this->addError(
                'parent_consumer_id',
                Yii::t('app/error', 'This consumer reached the limit of possible children. Please choose another parent consumer.')
            );
        }

        // Cannot create a loop between ancestors and descendants
        while ($parent->parentConsumer) {
            if ($parent->parent_consumer_id == $this->id) {
                $this->addError(
                    'parent_consumer_id',
                    Yii::t('app/error', 'This consumer is a direct or indirect child of the current record, therefore cannot be a parent.')
                );
                break;
            }
            $parent = $parent->parentConsumer;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'legal_person_id' => Yii::t('app', 'Person ID'),
            'parent_consumer_id' => Yii::t('app', 'Parent Consumer ID'),
            'sponsor_consumer_id' => Yii::t('app', 'Sponsor Consumer ID'),
            'identifier' => Yii::t('app', 'Identifier'),
            'bank_name' => Yii::t('app', 'Bank Name'),
            'bank_agency' => Yii::t('app', 'Bank Agency'),
            'bank_account' => Yii::t('app', 'Bank Account'),
            'is_business_representative' => Yii::t('app', 'Is Business Representative?'),
            'paid_affiliation_fee' => Yii::t('app', 'Paid Affiliation Fee?'),
            'position' => Yii::t('app', 'Position'),
            'plane_id' => Yii::t('app', 'Name Plane'),
            'bank_number' => Yii::t('app', 'Bank Number'),
            'operation' => Yii::t('app', 'Operation'),
            'x_paid_billet' => Yii::t('app', 'paid_billet'),
            'id_asaas' => Yii::t('app', 'Id Asaas'),
            'plane_investiment_id' => Yii::t('app', 'plane_investiment_id'),

            // Search
            'name' => Yii::t('app', 'Name'),
            'soma' => Yii::t('app', 'Soma'),
            'parentName' => Yii::t('app', 'Parent Name'),
            'nationalIdentifier' => Yii::t('app', 'National Identifier'),
            'phoneNumber' => Yii::t('app', 'Cell Phone')
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'identifier',
            'name' => function($model) {
                return $model->legalPerson->getName();
            },
            'national_identifier' => function($model) {
                return $model->legalPerson->getNationalIdentifier();
            },
            'position',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentConsumer()
    {
        return $this->hasOne(Consumer::className(), ['id' => 'parent_consumer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSponsorConsumer()
    {
        return $this->hasOne(Consumer::className(), ['id' => 'sponsor_consumer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildrenConsumers()
    {
        return $this->hasMany(Consumer::className(), ['parent_consumer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLegalPerson()
    {
        return $this->hasOne(LegalPerson::className(), ['id' => 'legal_person_id']);
    }

    /**
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getPlane()
    {
        return $this->hasOne(Plane::className(), ['id' => 'plane_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaneInvestimet()
    {
        return $this->hasOne(PlanoInvestimento::className(), ['id' => 'plane_investiment_id']);
    }

    /**
     * @return integer
     */
    public function getMonthPoints($month, $year, $ids = [])
    {
        $query = Sale::find();
        $testarr = (is_array($ids)?count($ids):0);
        $ids = $testarr == 0 ? [$this->id] : $ids;
        $query->andWhere(['in','consumer_id', $ids]);

        $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $year . '-' . $month . '-01 00:00:01');

        $endDate = $startDate->format('Y-m-t 23:59:59');
        $startDate = $startDate->format('Y-m-d H:i:s');

        $query->andFilterWhere(['between', 'sold_at', $startDate, $endDate]);

        $sum = $query->sum('points');
        return $sum ? $sum : 0;
    }

    public function getMonthIndications($month, $year, $ids = [])
    {
        $query = TransactionDetail::find();
        $testarr = (is_array($ids)?count($ids):0);

        $ids = $testarr == 0 ? [$this->id] : $ids;
        $query->andWhere(['in','consumer_id', $ids]);

        $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $year . '-' . $month . '-01 00:00:01');

        $endDate = $startDate->format('Y-m-t 23:59:59');
        $startDate = $startDate->format('Y-m-d H:i:s');

        $query->andFilterWhere(['between', 'created_at', $startDate, $endDate]);
        $query->andWhere(['transaction_origin' => TransactionDetail::TRANSACTION_ORIGIN_HIM]);
        $query->andWhere("object_type = 'Consumer'");

        $count = $query->count();
        return $count ? $count : 0;    }
    /**
     * @return integer
     */
    public function getTreeMonthPoints($month, $year, $selfInclude = true)
    {
        $query = TransactionDetail::find();

        $query->innerJoin('sales', 'transaction_details.object_id = sales.id');

        $query->andWhere('transaction_details.consumer_id = ' . $this->id);

        $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $year . '-' . $month . '-01 00:00:01');

        $endDate = $startDate->format('Y-m-t 23:59:59');
        $startDate = $startDate->format('Y-m-d H:i:s');

        $query->andFilterWhere(['between', 'sold_at', $startDate, $endDate]);

        $query->andWhere('transaction_origin = ' . TransactionDetail::TRANSACTION_ORIGIN_NET);

        $query->andWhere("object_type = 'Sale'");

        $sum = $query->sum('points');
        return $sum ? $sum : 0;
    }

    /**
     * @return array
     */
    public function getTreeIds(Consumer $consumer = null, $selfInclude = true)
    {
        $consumer = $consumer ? $consumer : $this;

        $tree = [];

        if ($selfInclude) {
            $tree[] = $consumer->id;
        }

        $childrens = $consumer->getChildrenConsumers()->all();
        if ($childrens) {

            foreach ($childrens as $children) {

                $childrenTree = $consumer->getTreeIds($children);
                if ($childrenTree) {
                    $tree = array_merge($tree, $childrenTree);
                }
            }
        }

        return $tree;
    }

    /**
     * @return array
     */
    public function getTree(Consumer $consumer = null)
    {
        $consumer = $consumer ? $consumer : $this;

        $tree = [];

        $item = [
            'consumer' => $consumer,
            'childs' => [],
        ];

        $childrens = $consumer->getChildrenConsumers()->leftFirst()->all();
        if ($childrens) {
            foreach ($childrens as $children) {
                $item['childs'][] = $consumer->getTree($children);
            }
        }

        $tree[] = $item;

        return $tree;
    }

    /**
     * @return array
     */
    public function getFatherTree(Consumer $consumer = null)
    {
        $consumer = $consumer ? $consumer : $this;

        $tree = [$consumer];

        $parent = $consumer->parentConsumer;
        if ($parent) {
            $item = $consumer->getFatherTree($parent);
            $tree = array_merge($tree, $item);
        }

        return $tree;
    }

    public function getCurrentQualification()
    {
        $key = 'current_qualification_consumer_' . $this->id;
        $data = \Yii::$app->cache->get($key);

        if ($data === false) {

            $currentTreeLevel = $this->getCompletedTreeLevels();

            if (!$currentTreeLevel) {
                return false;
            }

            $data = Qualification::find()
                ->where(['<=', 'completed_levels', $currentTreeLevel])
                ->orderBy(['position' => SORT_ASC])
                ->one();

            $dependency = new \yii\caching\DbDependency(['sql' => 'SELECT MAX(id) FROM consumers']);

            \Yii::$app->cache->set($key, $data, 86400, $dependency);
        }

        return $data;
    }

    public function getNextQualification()
    {
        $key = 'next_qualification_consumer_' . $this->id;
        $data = \Yii::$app->cache->get($key);

        if ($data === false) {
            $currentQualification = $this->getCurrentQualification();

            if (!$currentQualification) {
                return false;
            }

            $data = Qualification::find()
                ->where(['position' => $currentQualification->position -1])
                ->orderBy(['position' => SORT_ASC])
                ->one();

            $dependency = new \yii\caching\DbDependency(['sql' => 'SELECT MAX(id) FROM consumers']);

            \Yii::$app->cache->set($key, $data, 86400, $dependency);
        }

        return $data;
    }

    public function getCompletedTreeLevels()
    {
        $key = 'completed_tree_levels_consumer_' . $this->id;
        $completedTreeLevels = \Yii::$app->cache->get($key);

        if ($completedTreeLevels === false) {

            $completedTreeLevels = 1;

            $allChildrensHaveTwoChildrens = true;

            $childrens = [$this];

            while ($allChildrensHaveTwoChildrens) {

                $newChildrens = [];

                foreach ($childrens as $children) {

                    if ($children->getChildrenConsumers()->count() != 2) {
                        $allChildrensHaveTwoChildrens = false;
                        break 2;
                    }

                    $newChildrens = array_merge($newChildrens, $children->getChildrenConsumers()->all());
                }

                $completedTreeLevels++;

                $childrens = $newChildrens;
            }

            $dependency = new \yii\caching\DbDependency(['sql' => 'SELECT MAX(id) FROM consumers']);

            \Yii::$app->cache->set($key, $completedTreeLevels, 86400, $dependency);
        }

        return $completedTreeLevels;
    }

    public function getTotalConsumersTree()
    {
        return count($this->getTreeIds()) - 1;
    }

    public function calculateRepresentativeActivateProfit()
    {
        return round((($this->plane->value * Configuration::getConfigurationValue(Configuration::PERCENTUAL_REPASSE_REPRESENTANTE)) / 100), 2);
    }

    public static function getRepresentativeOfCity(City $model)
    {
        return Consumer::find()
        ->businessRepresentatives()
        ->innerJoin('sales_representative_cities', 'sales_representative_cities.sales_representative_id = consumers.id')
        ->andWhere('sales_representative_cities.city_id = :representativeCityId', [':representativeCityId' => $model->id])
        ->one();
    }

    private function calculateValuePaidBillet() {
        if ($this->sponsorConsumer && $this->sponsorConsumer->plane) {
            $valorTotalRepasse = $this->plane->calculateProfitValue($this->sponsorConsumer->plane);
            return $valorTotalRepasse / $this->maximum_amount;
        } else {
            Yii::warning('Plane object is null for sponsor consumer.', 'application');
            return 0; // ou outra ação apropriada, como lançar uma exceção ou retornar um valor padrão
        }
    }
    

    private function calculatePaidBillet(){
        return $this->calculateValuePaidBillet();
    }

    private function calculatePaidBilletRepresentative(){
        $valorTotalRepasse = $this->calculateRepresentativeActivateProfit();
        return $valorTotalRepasse/$this->maximum_amount;
    }

    public function activateUser(){
        $transaction = Yii::$app->db->beginTransaction();
        $user = new ConsumerUserForm;
        $user->scenario = 'insert';
        $user->consumer = $this;
        $user->authManager = Yii::$app->authManager;
        $user->name = $this->legalPerson->name;
        $user->email = $this->legalPerson->email;
        $user->identifier = (int) $this->identifier;
        $hashids = new Hashids(
            $chaveSecreta = 'consumer_random_password',
            $minimoDeCaracteres = 6,
            $alfabeto = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        );

        $password = $hashids->encode(rand(0, 10000));

        $user->password = $password;
        $user->passwordConfirmation = $password;
        $userSalved = $user->save();

        if ($userSalved) {
            $transaction->commit();
            return true;
        }

        $transaction->rollBack();
        return false;
    }

    public function activate()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $userVerificar = User::findOne(['login' => $this->identifier]);
            if (!$userVerificar) {
                $user = new ConsumerUserForm;
                $user->scenario = 'insert';
                $user->consumer = $this;
                $user->authManager = Yii::$app->authManager;
                $user->name = $this->legalPerson->name;
                $user->email = $this->legalPerson->email;
                $user->identifier = (int) $this->identifier;
                $hashids = new Hashids(
                    $chaveSecreta = 'consumer_random_password',
                    $minimoDeCaracteres = 6,
                    $alfabeto = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
                );

                $password = $hashids->encode(rand(0, 10000));
                $user->password = $password;
                $user->passwordConfirmation = $password;
                $userSalved = $user->save();
            } else {
                $userSalved = true;
            }

            $numeroParcelas = 1;
            if ($this->maximum_amount > 1) {
                $numeroParcelas = $this->maximum_amount;
                $profitValue = $this->calculatePaidBillet();
                $profitRepresentative = $this->calculatePaidBilletRepresentative();
            } else {
                // Adicionando logs para depuração
                Yii::info('Verificando plane e sponsorConsumer->plane', __METHOD__);
                Yii::info('plane: ' . print_r($this->plane, true), __METHOD__);
                Yii::info('sponsorConsumer: ' . print_r($this->sponsorConsumer, true), __METHOD__);
                Yii::info('sponsorConsumer->plane: ' . print_r($this->sponsorConsumer->plane, true), __METHOD__);

                if (!($this->plane instanceof \app\models\Plane)) {
                    Yii::error("O objeto plane do consumidor atual não é uma instância de Plane.", __METHOD__);
                    $transaction->rollBack();
                    return false;
                }
                if (!($this->sponsorConsumer->plane instanceof \app\models\Plane)) {
                    Yii::error("O objeto plane do consumidor patrocinador não é uma instância de Plane.", __METHOD__);
                    $transaction->rollBack();
                    return false;
                }

                $profitValue = $this->plane->calculateProfitValue($this->sponsorConsumer->plane);
                $profitRepresentative = $this->calculateRepresentativeActivateProfit();
            }

            $detailsSaved = false;
            $comissionSaved = false;

            $adiantamentoMes = ($this->maximum_amount <= 1 ? $this->maximum_amount : 0);

            for ($i = 0; $i < $numeroParcelas; $i++) {
                $dia = ($i == 0 ? mktime(date('H'), date('i'), date('s'), date('m') + $adiantamentoMes, date('d'), date('Y')) : mktime(date('H'), date('i'), date('s'), date('m') + $i, 1, date('Y')));
                $start = date("Y-m-d H:i:s", $dia);

                $details = new TransactionDetail;
                $details->object_type = 'Consumer';
                $details->object_id = $this->id;
                $details->consumer_id = $this->sponsorConsumer->id;
                $details->plane_id = $this->sponsorConsumer->plane->id;
                $details->profit_percentage = $this->sponsorConsumer->plane->bonus_percentage;
                $details->profit = $profitValue;
                $details->transaction_origin = TransactionDetail::TRANSACTION_ORIGIN_HIM;
                $details->created_at = $start;
                $details->updated_at = $start;

                $detailsSaved = $details->save();

                $representative = self::getRepresentativeOfCity($this->legalPerson->city);

                if ($representative) {
                    $comission = new TransactionDetail;
                    $comission->object_type = 'Consumer';
                    $comission->object_id = $this->id;
                    $comission->consumer_id = $representative->id;
                    $comission->plane_id = $representative->plane_id;
                    $comission->profit_percentage = Configuration::getConfigurationValue(Configuration::PERCENTUAL_REPASSE_REPRESENTANTE);
                    $comission->profit = $profitRepresentative;
                    $comission->transaction_origin = TransactionDetail::REPRESENTATIVE_COMISSION;
                    $comission->created_at = $start;
                    $comission->updated_at = $start;

                    $comissionSaved = $comission->save();
                } else {
                    $comissionSaved = true;
                }
            }

            if ($userSalved && $comissionSaved && $detailsSaved) {
                $transaction->commit();
                return true;
            }

            $transaction->rollBack();
            return false;
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @return int
     */
    public function getMissingConsumers(Qualification $nextQualification)
    {
        $completedLevelsNextQualification = $nextQualification->completed_levels;
        $totalConsumersNextQualification = $nextQualification->getRequiredConsumers();

        $currentConsumers = 0;
        $treeLevels = 0;

        $childrens = [$this];

        while (count($childrens)) {

            $newChildrens = [];

            foreach ($childrens as $children) {

                if ($treeLevels >= $completedLevelsNextQualification - 1) {
                    break 2;
                }

                $currentConsumers += $children->getChildrenConsumers()->count();

                $newChildrens = array_merge($newChildrens, $children->getChildrenConsumers()->all());
            }

            $childrens = $newChildrens;

            $treeLevels++;
        }

        return $totalConsumersNextQualification - $currentConsumers;
    }

    /**
     * @return void
     */
    public function setRepresentativePermission($event)
    {

        $user = User::findOne(['authenticable_id' => $this->id, 'authenticable_type' => 'Consumer']);

        if ($user) {
            $authManager = \Yii::$app->authManager;
            $role = $authManager->getRole('representative');

            $userRoles = \Yii::$app->authManager->getRolesByUser($user->getId());

            if ($this->is_business_representative == true) {
                if (array_key_exists('representative', $userRoles) == false)
                    $authManager->assign($role, $user->getId());
            } else {
                if (array_key_exists('representative', $userRoles))
                    $authManager->revoke($role, $user->getId());
            }
        }
    }

    /**
     * @return boolean
     */
    public function hasConsumerInQualificationNetwork(Consumer $consumer)
    {
        $consumidoresDoConsumidor = $this->getIdsQualificationNetworkConsumers();
        return in_array($consumer->id, $consumidoresDoConsumidor);
    }

    public function UltimoConsumersNetWork(){
        $childrens = [$this];
        while (count($childrens)) {
            $newChildrens = [];
            foreach ($childrens as $children) {
                $testeChildrens = $children->getChildrenConsumers()->all();
                if(count($testeChildrens) <= 0)
                    $consumersIds[] = $children->id;
                foreach ($testeChildrens as $child) {
                    $consumersIds[] = $child->id;
                }
                $newChildrens = array_merge($newChildrens, $children->getChildrenConsumers()->all());
            }
            $childrens = $newChildrens;
        }
        return $consumersIds;
    }

    /**
     * @return array
     */
    public function getIdsQualificationNetworkConsumers()
    {
        $currentQualification = $this->getCurrentQualification();
        $completedLevelsQualification = $currentQualification->completed_levels;

        $treeLevels = 0;
        $consumersIds = [];

        $childrens = [$this];
        //echo 'Consumer Net: '.$this->id;
        //echo '<br>complete leval '.$completedLevelsQualification.'<br>';
        while (count($childrens)) {

            $newChildrens = [];
            //echo 'Filhos<br>';
            foreach ($childrens as $children) {

                if ($treeLevels >= $completedLevelsQualification) {
                    break 2;
                }
                //echo 'filho: '.$children->id.'<br>';

                //$newChildrens = $children->getChildrenConsumers()->all();
                foreach ($children->getChildrenConsumers()->all() as $child) {
                    $consumersIds[] = $child->id;
                }

                $newChildrens = array_merge($newChildrens, $children->getChildrenConsumers()->all());
            }

            $childrens = $newChildrens;

            $treeLevels++;
        }

        return $consumersIds;
    }

    public function save($runValidation = true, $attributes = null)
    {
        $transaction = $this->getDb()->beginTransaction();

        try {

            $result = parent::save($runValidation, $attributes);

            if ($result) {
               if ($this->is_business_representative == false) {
                    foreach (SalesRepresentativeCity::find()->where('sales_representative_id = :representative', [':representative' => $this->id])->all() as $representative) {
                        $representative->delete();
                    }
                }

                $transaction->commit();
                return true;
            }

        } catch (\Exception $e) {
        }

        $transaction->rollback();
        return false;
    }

    public function getMonths($onlyLabel = false)
    {
        $meses = [
            1 => Yii::t('app', 'January'),
            2 => Yii::t('app', 'February'),
            3 => Yii::t('app', 'March'),
            4 => Yii::t('app', 'April'),
            5 => Yii::t('app', 'May'),
            6 => Yii::t('app', 'June'),
            7 => Yii::t('app', 'July'),
            8 => Yii::t('app', 'August'),
            9 => Yii::t('app', 'September'),
            10 => Yii::t('app', 'October'),
            11 => Yii::t('app', 'November'),
            12 => Yii::t('app', 'December'),
        ];

        return !$onlyLabel ? $meses : array_values($meses);
    }

    public function getPoints()
    {
        $series = [
            'points' => [],
        ];

        foreach ($this->getMonths() as $id => $mes) {

            $start = new \DateTime;
            $start->setDate(date('Y'), $id, 1);

            $end = new \DateTime;
            $end->setDate(date('Y'), $id, date("t", strtotime(date('Y') . '-' . $id . '-01')));

            $endDate = $end->format('Y-m-t 23:59:59');
            $startDate = $start->format('Y-m-d H:i:s');
            $series['points'][] = $this->getPointsBetweenDate($startDate, $endDate);
        }

        return $series;
    }

    public function getPointsBetweenDate($startDate, $endDate)
    {
        $query = Sale::find();
        $query->andWhere("consumer_id = " . $this->id);
        $query->andFilterWhere(['between', 'sold_at', $startDate, $endDate]);
        $sum = (float) $query->sum('points');
        return  $sum ? $sum : 0;
    }

    public function getConsumersPlane($dataA, $dataB, $plane_id, $valorMinimo = 0, $codigoConsumer = 0){
        
        $startDate = $dataA->format('Y-m-d H:i:s');
        $endDate = $dataB->format('Y-m-t 23:59:59');

        $consumerModel = consumer::find()
                        ->select("consumers.id");
        if ($plane_id > 0) {
            $consumerModel = $consumerModel->andWhere("consumers.plane_id = :planoid", [':planoid' => $plane_id]);
        }

        $consumerModel = $consumerModel->innerJoin("public.transaction_details as t","t.consumer_id = consumers.id");

        if ($valorMinimo > 0) {
            
            $consumerModel = $consumerModel->andWhere('
                                (select sum(profit) from public.transaction_details where transaction_details.created_at between :dataA and :dataB and transaction_origin = :origintransaction and object_type = :objecttype and transaction_details.consumer_id = consumers.id) >= :pontuacaominima',[':pontuacaominima' => $valorMinimo, ':objecttype'=>'Sale', ':dataA'=> $startDate, ':dataB' => $endDate, ':origintransaction'=>TransactionDetail::TRANSACTION_ORIGIN_HIM]);
        }
        
        if($codigoConsumer > 0){
            $consumerModel = $consumerModel->andWhere('consumers.id = :consumerid',[':consumerid'=>$codigoConsumer]);
        }

        $consumerModel = $consumerModel->groupBy(["consumers.id"])->all();

        $conditionin = "";
        foreach ($consumerModel as $key => $value) {
            $conditionin = empty($conditionin) ? $value->id : $conditionin.", ".$value->id;
        }
        if (!empty($conditionin)){
            $retornoConsumer = consumer::find()->andWhere("id in (".$conditionin.")")->orderBy(['identifier'=>SORT_ASC])->all();
        }else{
            $retornoConsumer = null;
        }

        return $retornoConsumer;
    }

    public function disabled(){

        $transaction = $this->getDb()->beginTransaction();
        try {
            $connection = \Yii::$app->db;
            $connection->createCommand()
            ->update('public.consumers', ['sponsor_consumer_id'=>0, 'parent_consumer_id'=> 0, 'position'=> '', 'is_disabled'=>1], 'id = '.$this->id)
            ->execute(); 

            $transaction->commit();
            return true;

        }catch (\Exception $e) {
            $transaction->rollback();
            echo $e->getMessage();
        }
    }

    public function removeDad(){
        $transaction = $this->getDb()->beginTransaction();
        try {
            $connection = \Yii::$app->db;
            $connection->createCommand()
            ->update('public.consumers', ['parent_consumer_id'=> 0, 'position'=> ''], 'id = '.$this->id)
            ->execute(); 
            
            $transaction->commit();
            return true;
        }catch (\Exception $e) {
            print_r($e->getMessage());
            exit();
            $transaction->rollback();
            echo $e->getMessage();
        }
    }

    public function updateIdAsaas(){
        $transaction = $this->getDb()->beginTransaction();
        try {
            $connection = \Yii::$app->db;
            $connection->createCommand()
            ->update('public.consumers', ['id_asaas'=> $this->id_asaas], 'id = '.$this->id)
            ->execute(); 

            $transaction->commit();
            return true;

        }catch (\Exception $e) {
            $transaction->rollback();
            echo $e->getMessage();
        }
    }

    public function updateIdBling(){
        $transaction = $this->getDb()->beginTransaction();
        try {
            $connection = \Yii::$app->db;
            $connection->createCommand()
            ->update('public.consumers', ['id_bling'=> $this->id_bling], 'id = '.$this->id)
            ->execute(); 

            $transaction->commit();
            return true;

        }catch (\Exception $e) {
            $transaction->rollback();
            echo $e->getMessage();
        }
    }

    public function updateMaximumAmount(){
        $transaction = $this->getDb()->beginTransaction();
        try {
            $connection = \Yii::$app->db;
            $connection->createCommand()
            ->update('public.consumers', ['maximum_amount'=> $this->maximum_amount], 'id = '.$this->id)
            ->execute(); 

            $transaction->commit();
            return true;

        }catch (\Exception $e) {
            $transaction->rollback();
            echo $e->getMessage();
        }
    }

    public function atualizarIdentifierPlano(){
        $transaction = $this->getDb()->beginTransaction();
        try {
            $connection = \Yii::$app->db;
            $connection->createCommand()
            ->update('public.consumers', ['identifier'=> $this->identifier, 'plane_id' => $this->plane_id], 'id = '.$this->id)
            ->execute(); 

            $transaction->commit();
            return true;

        }catch (\Exception $e) {
            $transaction->rollback();
            echo $e->getMessage();
        }
    }

    public function getDataContract($data = "", $investimentoForm = null){
        $legalPerson = $this->legalPerson;
        $person = $legalPerson->person;
        $cidade = $legalPerson->city;
        if($data == ""){
            $dateTime = strtotime($this->created_at);
        }else if($investimentoForm != null){
            $dataformat = substr($investimentoForm->data_contrato, 6, 4).'/'.substr($investimentoForm->data_contrato, 3, 2).'/'.substr($investimentoForm->data_contrato, 0, 2);
            $dateTime = strtotime($dataformat);
        }else{
            $dateTime = strtotime($data);
        }

        $valorInvestimento = ($investimentoForm == null) ? Configuration::getConfigurationValue(Configuration::VALOR_INVESTIMENTO) : $investimentoForm->valor_contrato;

        $antes = Contratos::InvestimentoContract();
        $texto = new clsTexto();
        $depois = array(
            $legalPerson->name,
            $legalPerson->nationalIdentifier,
            $person->rg, 
            $legalPerson->zip_code,
            $person->occupation->name,
            $legalPerson->address,
            preg_replace("/[^0-9]/", "", $legalPerson->address),
            $legalPerson->district,
            $cidade->name,
            $cidade->state->name,
            Yii::$app->formatter->asCurrency($valorInvestimento),
            $this->maximum_amount . " Meses",
            $texto->valorPorExtenso($this->maximum_amount, false) . " Meses",
            $texto->valorPorExtenso($valorInvestimento, false) . " Reais",
            $cidade->name.', '. date('d',$dateTime).' de '.Yii::t('app',date('F',$dateTime)).' de '.date('Y', $dateTime),
            'José Domingos Tolfo',
            '649.358.680-15'
        );

        return array('antes'=>$antes, 'depois'=>$depois);
    }

}