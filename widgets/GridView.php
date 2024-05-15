<?php
namespace app\widgets;

use Yii;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView as YiiGridView;
use yii\helpers\Html;

class GridView extends YiiGridView
{
    const FORMATO_CSV = 'csv';

    /**
     * @var boolean
     */
    public $exportable = true;

    /**
     * @inheritdoc
     */
    public $layout = "{buttons}\n{summary}\n{items}\n{pager}";

    /**
     * @var string
     */
    public $parametroExportacao = 'export';

    /**
     * @var string
     */
    public $exportedFileName = 'report';

    /**
     * Registros por página para cada iteração da exportação
     * @var integer
     */
    public $recordsLoadingStep = 100;

    /**
     * @return array
     */
    public $buttons;

    /**
     * @var string label do botão de exportar
     */
    public $exportButtonLabel;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->exportButtonLabel = Yii::t('app', 'Export');

        if ($this->estaExportando()) {
            return $this->exportar();
        } else {
            return parent::run();
        }
    }

    /**
     * @inheritdoc
     */
    public function renderSection($name)
    {
        if ($name == '{buttons}') {
            return $this->renderButtons();
        } else {
            return parent::renderSection($name);
        }
    }

    /**
     * @return boolean
     */
    protected function estaExportando()
    {
        return $this->exportable && Yii::$app->request->get($this->parametroExportacao);
    }

    /**
     * Stream do arquivo
     */
    public function exportar()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        $formato = Yii::$app->request->get($this->parametroExportacao);
        $mimeType = $this->getMimeType($formato);

        header('Content-Encoding: ' . Yii::$app->charset);
        header('Content-Type: ' . $mimeType . '; charset=' . Yii::$app->charset);

        if (YII_DEBUG) {
            file_put_contents('php://output', '<pre>');
        } else {
            header('Content-Disposition: attachment; filename="' . $this->exportedFileName . '_' . date('YmdHis') . '.csv"');
        }

        $this->streamCsv();
    }

    /**
     * @return array
     */
    protected function getExportableColumns()
    {
        $columns = [];

        foreach ($this->columns as $column) {

            if ($column instanceof ActionColumn || $column instanceof SerialColumn) {
                continue;
            }

            if (isset($column->exportable) && $column->exportable === false) {
                continue;
            }

            $columns[] = $column;
        }
        return $columns;
    }

    /**
     * @return array
     */
    protected function _getHeader()
    {
        $header = [];

        foreach ($this->getExportableColumns() as $column) {

            $name = null;

            if ($column->header) {
                $name = $column->header;
            }
            elseif ($this->dataProvider instanceof ActiveDataProvider) {
                $modelClass = $this->dataProvider->query->modelClass;
                $object = new $modelClass;
                $name = $object->getAttributeLabel($column->attribute);
            }
            elseif ($column->attribute) {
                $name = $column->attribute;
            }

            $header[] = $name;
        }

        // Corrige bug com o Excel quando a primeira coluna se chama ID
        if (isset($header[0]) && strtoupper($header[0]) == 'ID') {
            $header[0] = ' ' . $header[0];
        }

        return $header;
    }

    /**
     * @return array
     */
    protected function _getFooter()
    {
        $footer = array();

        $qtde = 0;

        foreach ($this->getExportableColumns() as $column) {

            $name = null;
            if ($column->footer) {
                $name = $column->footer;
                $qtde++;
            }
            else {
                $name = ' ';
            }

            $footer[] = $name;
        }

        return $qtde > 0 ? $footer : null;
    }

    /**
     * Gera um arquivo CSV na saída atual
     */
    protected function streamCsv()
    {
        // Sem limite de tempo
        set_time_limit(0);

        // Monta header
        $header = $this->_getHeader();

        // Desativa os logs até o final do script, evitando que consumam memória do PHP
        $enabledLogs = array();
        $previousFlushInterval = Yii::$app->log->flushInterval;
        Yii::$app->log->flushInterval = 0;

        if (!empty(Yii::$app->log) && !empty(Yii::$app->log->routes)) {
            foreach (Yii::$app->log->routes as $route) {
                if ($route->enabled)
                    $route->enabled = false;
            }
        }

        // Abre pra escrever na tela
        $handle = fopen('php://output', 'w');

        if (Yii::$app->charset == 'UTF-8') {

            // BOM
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        }

        // Imprime cabeçalho
        fputcsv($handle, $header, ';');

        // Seta em quantos registros por vez ele carrega (pra não estourar a memória)
        $pagination = $this->dataProvider->getPagination();
        $pagination->pageSize = $this->recordsLoadingStep;

        $this->dataProvider->prepare();

        $steps = $pagination->getPageCount();

        for ($currentStep = 0; $currentStep < $steps; $currentStep++) {

            // Muda bloco atual
            $pagination->setPage($currentStep);

            $this->dataProvider->prepare(true);

            // Obtém dados
            $rows = $this->dataProvider->getModels();

            // Monta o CSV
            foreach ($rows as $index => $model) {

                $row = array();

                foreach ($this->getExportableColumns() as $column) {

                    $value = null;

                    if ($column->value !== null) {

                        $function = $column->value;
                        $value = $function($model, $index, $this);

                    } elseif ($column->attribute !== null) {

                        if(is_array($model)) {
                            $value = $model[$column->attribute];
                        } else {
                            $value = Html::getAttributeValue($model, $column->attribute);
                        }
                    }

                    $strippedValue = strip_tags(($value === null) ? '' : Yii::$app->getFormatter()->format($value, $column->format));

                    $row[] = $strippedValue;
                }

                // Imprime na tela
                fputcsv($handle, $row, ';');
            }
        }

        // Monta footer, se houver
        if ($footer = $this->_getFooter()) {
            fputcsv($handle, $footer, ';');
        }

        // Encerra
        fclose($handle);

        // Reativa logs
        foreach ($enabledLogs as $route) {
            $route->enabled = true;
        }

        Yii::$app->log->flushInterval = $previousFlushInterval;

        // Finish application
        exit;
    }

    /**
     * @return string
     */
    protected function renderButtons()
    {
        if ($this->exportable) {

            if ($this->buttons === null) {
                $this->buttons = [];
            }

            if (!isset($this->buttons['export'])) {

                $this->buttons['export'] = function() {

                    $url = $_SERVER['REQUEST_URI'];

                    if (false === strpos($url, '?')) {
                        $url .= '?';
                    } else {
                        $url .= '&';
                    }

                    return Html::a(
                        $this->exportButtonLabel,
                        $url . 'export=csv',
                        [
                            'class' => 'btn btn-secondary default',
                            'data-role' => 'export',
                        ]
                    );
                };
            }
        }

        if ($this->buttons == null) {
            return '';
        }

        $html = '<div class="grid-buttons btn-group">';

        foreach ($this->buttons as $key => $button) {
            if (is_callable($button)) {
                $button = $button();
            }
            $html .= $button;
        }

        return $html . '</div>';
    }

    /**
     * @param string $formato
     * @return string
     */
    protected function getMimeType($formato)
    {
        if (YII_DEBUG) {
            return 'text/html';
        } else {
            return 'text/csv';
        }
    }
}
