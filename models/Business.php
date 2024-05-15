<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\models\Consumable;

/**
 * This is the model class for table "businesses".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $legal_person_type
 * @property integer $legal_person_id
 * @property string $representative_legal
 * @property string $representative_cpf
 * @property string $economic_activity
 */
class Business extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'businesses';
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
    public function rules()
    {
        return [
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['legal_person_id','representative_legal','representative_cpf'], 'required'],
            [['legal_person_id'], 'integer'],
            [['legal_person_id'], 'unique'],
            [['economic_activity'], 'string', 'max' => 255],
            [['representative_legal'], 'string', 'max' => 150],
            [['representative_cpf'], 'string', 'max' => 14],
            ['is_disabled', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'default' => ['legal_person_id', 'logoempresa', 'economic_activity', 'representative_legal', 'representative_cpf', 'is_disabled'],
            'insert' => ['legal_person_id', 'logoempresa', 'economic_activity', 'representative_legal', 'representative_cpf', 'is_disabled'],
            'update' => ['economic_activity', 'logoempresa', 'representative_legal', 'representative_cpf', 'is_disabled'],
        ];
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
            'legal_person_id' => Yii::t('app', 'Legal Person'),
            'economic_activity' => Yii::t('app', 'Economic Activity'),
            'representative_legal' => Yii::t('app', 'Representative Legal'),
            'representative_cpf' => Yii::t('app', 'CPF'),
            'is_disabled' => Yii::t('app', 'Disabled'),
            'whatsapp' => Yii::t('app', 'WhatsApp'),

            // Search
            'legalPersonType' => Yii::t('app', 'Legal Person Type'),
            'name' => Yii::t('app', 'Name'),
            'cell_number' => Yii::t('app', 'Phone Business'),
            'nationalIdentifier' => Yii::t('app', 'National Identifier'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    public static function getDisabled()
    {
        return [
            'Yes' => Yii::t('app', 'Yes'),
            'No' => Yii::t('app', 'No'),
        ];
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getLegalPerson()
    {
        return $this->hasOne(LegalPerson::className(), ['id' => 'legal_person_id']);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->legalPerson ? $this->legalPerson->getType() : null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->legalPerson ? $this->legalPerson->getName() : null;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->legalPerson ? $this->legalPerson->getPhoneNumber() : null;
    }

    /**
     * @return string
     */
    public function getEconomicActivity()
    {
        return $this->economic_activity;
    }

    /**
     * @return string
     */
    public function getNationalIdentifier()
    {
        return $this->legalPerson ? $this->legalPerson->getNationalIdentifier() : null;
    }

    /**
     * @return boolean
     */
    public function isPhysicalPerson()
    {
        return ($this->legalPerson && $this->legalPerson->isPhysicalPerson());
    }

    /**
     * @return boolean
     */
    public function isJuridicalPerson()
    {
        return ($this->legalPerson && $this->legalPerson->isJuridicalPerson());
    }

    public function getConsumables()
    {
        return $this->hasMany(Consumable::className(), ['business_id' => 'id']);
    }

    public function getDataContract(){
        $legalPerson = $this->legalPerson;
        $person = $legalPerson->person;
        $cidade = $legalPerson->city;
        $dateTime = strtotime($this->created_at);

        $antes = array(
            '@con-razaosocial', 
            '@con-nomefantasia', 
            '@con-nomeproprietario', 
            '@con-telefone', 
            '@con-celular', 
            '@con-documento', 
            '@con-inscricaoest', 
            '@con-email', 
            '@con-site', 
            '@con-endereco', 
            '@con-cep', 
            '@con-bairro', 
            '@con-cidade', 
            '@con-estado', 
            '@con-responsavellegal', 
            '@con-cpfresponsavel', 
            '@con-atividades', 
            '@con-repasse', 
            '@datacreate', 
            '@nomepresidente', 
            '@cpfpresidente'
        );
        $depois = array(
            ($legalPerson->isJuridicalPerson() ? $person->company_name : $legalPerson->name), 
            $legalPerson->name, 
            ($legalPerson->isJuridicalPerson() ? $person->contact_name : ''),
            '############', 
            $legalPerson->cell_number, 
            $legalPerson->nationalIdentifier,
            ($legalPerson->isJuridicalPerson() ? $person->ie : ''),
            $legalPerson->email,
            ($legalPerson->website)? $legalPerson->website : '##########',
            $legalPerson->address,
            $legalPerson->zip_code,
            $legalPerson->district,
            $cidade->name,
            $cidade->state->name,
            $this->representative_legal,
            $this->representative_cpf,
            $this->economic_activity,
            $this->getConsumablesString(),
            $cidade->name.', '.date('d',$dateTime).' de '.Yii::t('app',date('F',$dateTime)).' de '.date('Y', $dateTime),
            'José Domingos Tolfo',
            '649.358.680-15'
        );

        return array('antes'=>$antes, 'depois'=>$depois);
    }

    public function getConsumablesString(){
        $repasse = '';
        foreach($this->consumables as $dado){
            $repasse .= ' ';
            $repasse .= $dado->description .' - '.Yii::$app->formatter->asPercent($dado->shared_percentage / 100, 6);
        }
        return $repasse;
    }

    public function updateImagem(){
        $transaction = $this->getDb()->beginTransaction();
        try {
            $connection = \Yii::$app->db;
            $connection->createCommand()
            ->update('public.businesses', ['logoempresa'=> $this->logoempresa], 'id = '.$this->id)
            ->execute(); 

            $transaction->commit();
            return true;

        }catch (\Exception $e) {
            $transaction->rollback();
            echo $e->getMessage();
        }
    }

    public function updateComplementos(){
        $transaction = $this->getDb()->beginTransaction();
        try {
            $connection = \Yii::$app->db;
            $connection->createCommand()
            ->update('public.businesses', ['whatsapp'=> $this->whatsapp], 'id = '.$this->id)
            ->execute(); 

            $connection->createCommand()
            ->update('public.legal_persons', ['comercial_phone'=> $this->legalPerson->comercial_phone], 'id = '.$this->legal_person_id)
            ->execute(); 

            $transaction->commit();
            return true;

        }catch (\Exception $e) {
            $transaction->rollback();
            echo $e->getMessage();
        }
    }
}
