<?php
namespace app\widgets;

use yii\helpers\Html;

class LinkPager extends \yii\widgets\LinkPager
{
    /**
     * @inheritdoc
     */
    public $linkOptions = ['class' => 'page-link'];

    /**
     * @inheritdoc
     */
    protected function renderPageButtons()
    {
        return Html::tag('nav', parent::renderPageButtons());
    }

    /**
     * @inheritdoc
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $class = (empty($class) ? $this->pageCssClass : $class);
        $class = (empty($class) ? 'page-item' : 'page-item ' . $class);

        if ($disabled) {
            $options = ['class' => empty($class) ? $this->pageCssClass : $class];
            Html::addCssClass($options, $this->disabledPageCssClass);

            $linkOptions = $this->linkOptions;
            $linkOptions['data-page'] = $page;

            return Html::tag('li', Html::a($label, 'javascript:void(0)', $linkOptions), $options);
        }

        return parent::renderPageButton($label, $page, $class, $disabled, $active);


        $options = ['class' => empty($class) ? $this->pageCssClass : $class];
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);

            return Html::tag('li', Html::tag('span', $label), $options);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        return Html::tag('li', Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
    }
}
