<?php
namespace app\helpers;

use Yii;
use yii\bootstrap\Progress;
use yii\helpers\Url;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;

class DashboardHelper
{
    public static function getArrayTree($tree = null)
    {
        $arrayTree = [];

        $children = [];

        $tree = $tree ? $tree : \Yii::$app->user->getIdentity()->consumer->getTree();
        foreach ($tree as $node) {

            $item = [
                'text' => [
                    'name' => $node['consumer']->legalPerson->getName(),
                    'contact' => [
                        'val' => Yii::t('app', 'Details'),
                        'href' => Url::to(['consumers/informations', 'id' => $node['consumer']->id],['data-modal' => 'popupModal'] ),
                    ],

                ],
            ];

            foreach ($node['childs'] as $child) {

                $children[] = self::getArrayTree($child);
            }
        }

        $item['children'] = $children;

        $arrayTree[] = $item;

        return $arrayTree[0];
    }

    public static function getQualificationBar()
    {
        $consumer = \Yii::$app->user->getIdentity()->consumer ?? null;
        $currentQualification = $consumer ? $consumer->getCurrentQualification() : 0;
        
        $nextQualification = $consumer ? $consumer->getNextQualification() : 0;


        $html = '';

        if ($nextQualification) {

            $consumersTotal = $nextQualification->getRequiredConsumers();
            $missingConsumers = \Yii::$app->user->getIdentity()->consumer
                ->getMissingConsumers($nextQualification);

            $progress = (($consumersTotal - $missingConsumers) * 100) / $consumersTotal;
            $percent = $progress > 100 ? 100 : round($progress, 0);
            $descriptionbar = (string)$consumersTotal;
            $descriptionbar = $descriptionbar == ''?$descriptionbar:$descriptionbar.' Consumidores';

            $html .= self::getAlertDiv($percent);

            $html .= '<p><strong>' . $currentQualification->description . '</strong></p>';

            $html .= self::getBarWidget($percent, $descriptionbar);

            $html .= '<p class="m-top-30"><strong>' . $missingConsumers . '</strong> '. Yii::t('app', 'consumers to grow up to') . ' <strong>' . $nextQualification->description . '</strong></p>';

            $html .= '</div>';

        } else {

            $html .= self::getAlertDiv(100);

            $html .= '<p><strong>' . $currentQualification->description . '</strong></p>';

            $html .= self::getBarWidget(100, $descriptionbar);

            $html .= '<p><strong>&nbsp;</strong></p>';

            $html .= '</div>';
        }

        return $html;
    }

    public static function getMonthPointsBar()
    {
        $consumer = \Yii::$app->user->getIdentity()->consumer ?? null;
        $consumerMonthPoints = $consumer ? $consumer->getMonthPoints(date('m'), date('Y')) : 0;
        
        $planePoints = \Yii::$app->user->getIdentity()->consumer->plane->goal_points;

        $progress = $consumerMonthPoints ? ($consumerMonthPoints * 100) / $planePoints : 0;
        $percent = $progress > 100 ? 100 : round($progress, 0);

        $html = self::getAlertDiv($percent);

        $html .= '<p><strong>' . Yii::t('app', 'Points') . '</strong></p>';

        $html .= self::getBarWidget($percent);

        $html .= '<div class="row m-top-30"><div class="col-sm-10"><strong>' . $consumerMonthPoints . '</strong> '. Yii::t('app', 'of') . ' <strong>' . $planePoints . '</strong> '.Yii::t('app', 'points').'</div><div class="col-sm-2" align="right">' . Html::a(FA::icon('bar-chart'), ['consumers/points', 'id' => \Yii::$app->user->getIdentity()->consumer->id], ['data-modal' => 'modal', 'title' => Yii::t('app', 'View monthly historic'), 'aria-label' => Yii::t('app', 'View monthly historic')]) . '</div></div>';

        $html .= '</div>';
        
        return $html;
    }

    public static function getAlertDiv($percent)
    {
        $alertCssClass = 'alert-success';
        if ($percent < 60) {
            $alertCssClass = 'alert-danger';
        } else if ($percent < 100) {
            $alertCssClass = 'alert-warning';
        }

        return '<div class="alert '.$alertCssClass.' alert-backgraound alturapadraoboxpontos" role="alert">';
    }

    public static function getBarWidget($percent, $description = '')
    {   
        $barCssClass = 'progress-bar-success';
        if ($percent < 60) {
            $barCssClass = 'progress-bar-danger';
        } else if ($percent < 100) {
            $barCssClass = 'progress-bar-warning';
        }
        
        return Progress::widget([
            'percent' => $percent,
            'options' => ['class' => $barCssClass . ' progress-striped'],
            'label' => '<span style="position:absolute; left: '.((((int)$percent - 35)+35) < 35 ? 35 : ((int)$percent - 35)+35).'px">'.$description.'</span>',
        ]);
    }
}