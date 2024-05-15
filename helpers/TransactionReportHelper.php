<?php
namespace app\helpers;

use Yii;
use app\models\TransactionDetail;

class TransactionReportHelper
{
    public static function getTransactionType(TransactionDetail $model)
    {
        return $model->object_type == 'Consumer' ? Yii::t('app', 'Activation') : ($model->object_type == 'SaleComission' ? Yii::t('app', 'SaleComission') : Yii::t('app', 'SaleReport'));
    }

    public static function getTransactionOrigin(TransactionDetail $model)
    {
        return $model->transaction_origin == TransactionDetail::TRANSACTION_ORIGIN_HIM ? Yii::t('app', 'Own') : ($model->transaction_origin == TransactionDetail::SALE_COMISSION ? Yii::t('app', 'Trade') : Yii::t('app', 'Network'));
    }

    public static function getSaleValue(TransactionDetail $model)
    {   
        if($model->object_type == 'Sale'){
            return $model->object->total;
        }else if($model->object_type == 'SaleComission'){
            return $model->object ? $model->object->total : null;
        }else{
            return null;
        }
    }

    public static function getValue(TransactionDetail $model)
    {
        if( $model->object_type == 'Consumer'){
            return $model->object_type == 'Consumer' ? $model->object->plane->value : null;
        } else {
            return $model->object_type == 'Sale' ? $model->object->total : null;
        }
    }

    public static function getTransactionDescription(TransactionDetail $model)
    {
        $html = '';

        if ($model->object_type == 'Consumer') {
            $html = $model->object ? ($model->object->identifier . ' - ' . $model->object->legalPerson->getName()) : '';
        } else if ($model->object_type == 'SaleComission'){
            $html = $model->object ? ($model->object->consumer->identifier . ' - ' . $model->object->consumer->legalPerson->getName()) : '';
        } else if ($model->object_type == 'Sale') {
            if ($model->transaction_origin == TransactionDetail::TRANSACTION_ORIGIN_NET) {
                $html = $model->object ? ($model->object->consumer->identifier . ' - ' . $model->object->consumer->legalPerson->getName()) : '';
            } else {
                $html = $model->object ? ($model->object->business->legalPerson->getName()) : '';
            }
        }


        //se for habilitacao ou pontuacao da rede, mostra código e nome do consumidor
        //se for venda
            //se for propria mostra o nome do convênio
            //se for da rede mostra código e nome do consumidor
        return $html;
    }
}
