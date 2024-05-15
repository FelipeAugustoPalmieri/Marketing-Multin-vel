<?php
namespace app\widgets;

use Yii;
use yii\web\JsExpression;

class AjaxSelect2 extends Select2
{
    public $ajaxUrl;
    public $ajaxData;
    public $templateResult;
    public $templateSelection;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->pluginOptions['minimumInputLength'] = 3;
        $this->pluginOptions['ajax'] = [
            'url' => $this->ajaxUrl,
            'dataType' => 'json',
            'data' => $this->ajaxData,
            'processResults' => new JsExpression('function (data, params) {
                var page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (page * data.perPage) < data.totalCount
                    }
                };
            }'),
        ];
    }
}
