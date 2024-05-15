<?php
namespace app\widgets;

use Yii;

class Select2 extends \kartik\select2\Select2
{
    public $templateResult;
    public $templateSelection;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->options['placeholder'])) {
            $this->options['placeholder'] = Yii::t('app', 'Type to search');
        }

        $this->theme = self::THEME_BOOTSTRAP;

        $this->pluginOptions['allowClear'] = true;
        $this->pluginOptions['minimumInputLength'] = 0;

        if ($this->templateResult) {
            $this->pluginOptions['templateResult'] = $this->templateResult;
        }

        if ($this->templateSelection) {
            $this->pluginOptions['templateSelection'] = $this->templateSelection;
        }
    }
}
