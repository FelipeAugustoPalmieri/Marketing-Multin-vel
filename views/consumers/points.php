<?php

use miloschuman\highcharts\Highcharts;
use yii\helpers\Html;

?>

<div class="points">
<?php

    $month = $model->getMonths(true);
    $points = $model->getPoints();

    echo Highcharts::widget([
       'options' => [
          'title' => ['text' => Yii::t('app', 'Point Graph')],
          'colors' => ['#F58634'],
          'xAxis' => [
                'categories' => $month,
                'title' => ['text' => Yii::t('app', 'Month')]
          ],
          'yAxis' => [
              'max' => 100,
              'title' => ['text' => Yii::t('app', 'Points')],
              'plotLines' => [[
                  'value' => 35,
                  'color' => 'red',
                  'width' => 1,
                  'label' => [
                      'text' => Yii::t('app', 'Goal points: ') . 35,
                      'align' => 'center',
                      'style' => [
                          'color' => 'gray'
                      ]
                  ]
              ]]
          ],
          'plotOptions' => [
              'column' => ['stacking' => 'number']
          ],
          'series' => [
                [
                  'name' => Yii::t('app', 'Points'),
                  'data' => $points['points'],
                  'type' => 'column',
                ]
          ]
       ]
    ]);
?>
</div>
