<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\PorcentagemInvestimento;
use app\models\InvestimentoDetail;
use app\models\Consumer;
use app\models\InvestimentoDetailReport;
use app\models\PlanoInvestimento;
use app\models\Investimento;
use app\models\Configuration;
use app\models\search\PorcentagemSearch;
use app\models\asaas\Payments;
use app\models\financeiro\ContasReceber;
use app\models\financeiro\ContasReceberParcelas;
use app\models\bling\ContaReceberBling;
use app\models\Faturamento;
use kartik\mpdf\Pdf;
use app\models\InvestimentoForm;

/**
 * SalesController implements the CRUD actions for Sale model.
 */
class InvestimentoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['report'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create-porcentagem', 'gerar-juros', 'porcentagem', 'update', 'view', 'preview', 'extract'],
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['contrato-investimento', 'visualizar-contrato'],
                        'roles' => ['manageConsumers'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
   }

   public function actionPorcentagem()
   {
        $model = new PorcentagemSearch();
        $dataSearch = $model->search(Yii::$app->request->queryParams);

        return $this->render('porcentagem', [
            'model' => $model,
            'dados' => $dataSearch
        ]);
   }

    public function actionCreatePorcentagem(){
        $model = new PorcentagemInvestimento();
        $model->isNewRecord = true;
        $planoinvestimento = new PlanoInvestimento;

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->Post();
            if($post){
                $post['PorcentagemInvestimento']['porcentagem'] = str_replace(['% ', ','], ['', '.'], $post['PorcentagemInvestimento']['porcentagem']);
            }
            if($post != null && $post['PlanoInvestimento']['id'] != ''){
                $model->plane_investiment_id = $post['PlanoInvestimento']['id'];
            }
            try{
                $transaction = Yii::$app->db->beginTransaction();
                if($model->load($post)){
                    if($model->save()){
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', Yii::t('app', 'percentage was successfully registered'));
                        return $this->redirect(['porcentagem']);
                    }
                }else{
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', Yii::t('app', 'An unknown error occorred while activating the percentage.'));    
                }
            }catch(Exception $ex){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'An unknown error occorred while activating the percentage.'));
            }
        }

        return $this->render('create', [
            'model' => $model,
            'planoinvestimento' => $planoinvestimento
        ]);   
    }

    /**
     * Updates an existing Business model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $porcentagem = $this->findModel($id);
        $planoinvestimento = new PlanoInvestimento;

        if($porcentagem->plane_investiment_id > 0){
            $planoinvestimento = $planoinvestimento->findOne($porcentagem->plane_investiment_id);
        }

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->Post();
            if($post){
                $post['PorcentagemInvestimento']['porcentagem'] = str_replace(['% ', ','], ['', '.'], $post['PorcentagemInvestimento']['porcentagem']);
            }
            if($post != null && $post['PlanoInvestimento']['id'] != ''){
                $porcentagem->plane_investiment_id = $post['PlanoInvestimento']['id'];
            }
            try{
                $transaction = Yii::$app->db->beginTransaction();
                if($porcentagem->load($post)){
                    if($porcentagem->save()){
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', Yii::t('app', 'percentage was successfully edited'));
                        return $this->redirect(['porcentagem']);
                    }
                }else{
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', Yii::t('app', 'An unknown error occorred while activating the percentage.'));    
                }
            }catch(Exception $ex){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'An unknown error occorred while activating the percentage.'));
            }
        }

        return $this->render('update', [
            'model' => $porcentagem,
            'planoinvestimento' => $planoinvestimento
        ]);
    }

    public function actionPreview($id){
        $searchInvestimento = new PorcentagemSearch();
        $consumer = new Consumer();
        $porcentagem = new PorcentagemInvestimento();
        $ModelDetails = new InvestimentoDetail();

        $details = InvestimentoDetail::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $details,
        ]);
        
        $dadosPorcentagem = $porcentagem->find()->where(['=', 'id', $id])->one();
        $lista = $consumer->find()
        ->select('consumers.*')
        ->join('INNER JOIN', 'investimento_details', 'investimento_details.consumer_id = consumers.id')
        ->where(['>=', "AGE('".$dadosPorcentagem->data_referencia."', investiment_at)", '30 days'])
        ->andWhere(['=', 'consumers.plane_investiment_id', $dadosPorcentagem->plane_investiment_id])->all();

        $conditionin = "";
        $dadosretorno = [];
        foreach($lista as $codigo){
            $somaTotal = $ModelDetails->find()
            ->where(['=', 'consumer_id', $codigo->id])
            ->andWhere(['>=', "AGE('".$dadosPorcentagem->data_referencia."', investiment_at)", '30 days'])
            ->sum('total');
            $jurosTotal = $ModelDetails->find()
            ->where(['=', 'consumer_id', $codigo->id])
            ->andWhere(['>=', "AGE('".$dadosPorcentagem->data_referencia."', investiment_at)", '30 days'])
            ->sum('(total*('.number_format($dadosPorcentagem->porcentagem, 2, '.', '').'/100))');
            $saldoTotal = $ModelDetails->find()
            ->where(['=', 'consumer_id', $codigo->id])
            ->andWhere(['>=', "AGE('".$dadosPorcentagem->data_referencia."', investiment_at)", '30 days'])
            ->sum('total+(total*('.number_format($dadosPorcentagem->porcentagem, 2, '.', '').'/100))');
            $dadosretorno[] = array(
                'codigo' => $codigo->identifier,
                'name' => (isset($codigo->legalPerson->name)) ? $codigo->legalPerson->name : "",
                'total' => $somaTotal,
                'juros' => $jurosTotal,
                'saldo' => $saldoTotal,
            );
        }

        return $this->render('preview', [
            'codigo'=> $id,
            'dados' => $dadosretorno,
            'model' => $ModelDetails
        ]);
    }

    public function actionExtract(){

        $model = new InvestimentoDetailReport;
        $model->load($_GET);
        $model->user = \Yii::$app->user->getIdentity();
        if($_GET){
            if(isset($_GET['InvestimentoDetailReport']['inicio_periodo'])){
                $model->inicio_periodo = $_GET['InvestimentoDetailReport']['inicio_periodo'];
            }
            if(isset($_GET['InvestimentoDetailReport']['fim_periodo'])){
                $model->fim_periodo = $_GET['InvestimentoDetailReport']['fim_periodo'];
            }
        }

        return $this->render('extract', [
            'model' => $model
        ]);
    }

    public function actionExtractExport(){

        $model = new InvestimentoDetailReport;
        $model->load($_GET);
        $model->user = \Yii::$app->user->getIdentity();
        if($_GET){
            if(isset($_GET['InvestimentoDetailReport']['inicio_periodo'])){
                $model->inicio_periodo = $_GET['InvestimentoDetailReport']['inicio_periodo'];
            }
            if(isset($_GET['InvestimentoDetailReport']['fim_periodo'])){
                $model->fim_periodo = $_GET['InvestimentoDetailReport']['fim_periodo'];
            }
        }

        $dataProvider = $model->getTransactionReport();

        $dataProvider->pagination  = false;
        $dataProvider->sort = false;
        
        $content = $this->renderPartial('extract-export', [
            'model' => $model,
            'dataInicial' => Yii::$app->formatter->asDate($model->inicio_periodo,'dd/MM/yyyy'),
            'dataFinal' => Yii::$app->formatter->asDate($model->fim_periodo,'dd/MM/yyyy'),
            'totalinvestimento' => Yii::$app->formatter->asCurrency(InvestimentoDetailReport::getTotalInvestimento($model->inicio_periodo, $model->fim_periodo,$model->consumer_id)),
            'totaljuros' => Yii::$app->formatter->asCurrency(InvestimentoDetailReport::getTotalJuros($model->inicio_periodo, $model->fim_periodo,$model->consumer_id)),
            'total' => Yii::$app->formatter->asCurrency(InvestimentoDetailReport::getTotal($model->inicio_periodo, $model->fim_periodo, $model->consumer_id)),
            'numerototal' => InvestimentoDetailReport::getTotalRows($model->inicio_periodo, $model->fim_periodo, $model->consumer_id),
            'dataProvider' => $dataProvider
        ]);

        $pdf = Yii::$app->pdf;
        $pdf->filename = Yii::t('app', 'Sales Report') . ' - TBest.pdf';
        $pdf->options = ['title'=> Yii::t('app', 'Sales Report') . ' - TBest'];
        $pdf->content = $content;
        $pdf->methods = [
            'SetHeader'=>[
                '<div style="text-align: left">
                    <img width="25px" src="' . Url::base('http') . '/images/newlogotipo-tbest-login.png" alt="Tbest">' . 'TBest - ' . Yii::t('app', 'Smart Consumption') .
                '</div>'
            ],
            'SetFooter'=>['{PAGENO}']
        ];

        return $pdf->render();
    }

    public function actionGerarJuros($id){
        $porcentagem = new PorcentagemInvestimento();
        $ModelDetails = new InvestimentoDetail();

        $dadosPorcentagem = $porcentagem->find()->where(['=', 'id', $id])->one();
        
        $somaTotal = $ModelDetails->find()
            ->join('INNER JOIN', 'consumers', 'investimento_details.consumer_id = consumers.id')
            ->where(['>=', "AGE('".$dadosPorcentagem->data_referencia."', investiment_at)", '30 days'])
            ->andWhere(['<=', 'investiment_at', $dadosPorcentagem->data_referencia])
            ->andWhere(['investimento_details.processed' => 0])
            ->andWhere(['consumers.plane_investiment_id'=>$dadosPorcentagem->plane_investiment_id])
            ->orderBy(['investimento_details.consumer_id'=>SORT_ASC, 'investiment_at'=>SORT_ASC])
            ->all();
        $transacaoOld[] = 0;
        //echo '<pre>';
        $contador = 0;
        foreach($somaTotal as $dados){
            if(!isset($dados->balance) || $dados->balance <= 0) {
                $dados->balance = $ModelDetails->find()
                ->leftJoin('investimento_details inv2', 'inv2.sold_id = investimento_details.sold_id AND inv2.interest = 1')
                ->where(['<', 'investimento_details.investiment_at', $dados->investiment_at])
                ->andWhere(['!=','investimento_details.id',$dados->id])
                ->andWhere(['=','investimento_details.consumer_id', $dados->consumer_id])
                ->sum('inv2.total+investimento_details.total')+$dados->total;
                $dados->save();
            }
            //echo 'Transação:';
            //echo $dados->id . ' - ' . $dados->consumer_id . ' - ' . $dados->consumer->legalPerson->name . ' - ' . $dados->balance .'<br/>';
            //echo 'Verifica Juros <br/>';
            if(isset($transacaoOld[$dados->consumer_id]) && $transacaoOld[$dados->consumer_id] > 0){
                $balanceJuros = $ModelDetails->find()->where(['=','sold_id',$transacaoOld[$dados->consumer_id]])->andWhere(['=', 'interest', 1])->one();
                if($balanceJuros){
                    $dados->balance = $balanceJuros->balance+$dados->total;
                }
            }
            $insert = new InvestimentoDetail();
            //echo 'Saldo Para Calculo: ';
            $insert->balance = $dados->balance;
            //echo $insert->balance.'<br />';
            //echo 'Juros: ';
            $insert->total = ($insert->balance*($dadosPorcentagem->porcentagem/100));
            $insert->balance = $insert->balance + $insert->total;
            $insert->interest = 1;
            $insert->consumer_id = $dados->consumer_id;
            $tempo = str_pad($contador, 6, "0", STR_PAD_LEFT);
            $insert->investiment_at = $dadosPorcentagem->data_referencia." ".substr($tempo, 0, 2).":".substr($tempo, 2, 2).":".substr($tempo, 4, 2);
            $contador++;
            $insert->sold_id = $dados->sold_id;
            
            //echo $insert->total.'<br />';
            //echo "Documento: ";
            $name = strtolower(substr($dados->consumer->legalPerson->name, 0, strpos($dados->consumer->legalPerson->name, " ") ));
            $insert->invoice_code = "juros-".$name. preg_replace("/[^0-9]/", "", $dados->invoice_code);
            //echo $insert->invoice_code.'<br />';
            //echo "Saldo Novo: ";
            //echo $insert->balance.'<br/>';
            //echo "Data: ";
            //echo $insert->investiment_at.'<br />';
            //echo $insert->consumer_id.'<br />';
            //echo '<br/>';
            $insert->processed = 1;
            $transacaoOld[$dados->consumer_id] = $dados->sold_id;
            $verifica = true;
            $verifica = $insert->save();
            if($verifica){
                $dados->processed = 1;
                $dados->save();
                //echo "deu certo <br />";
            }
        }
        //echo '</pre>';
        return $this->render('gerarjuros', ['model'=> $dadosPorcentagem]);
    }

    public function actionContratoInvestimento(){
        $investimento = new Investimento();
        $consumer = new Consumer();
        $investimentoForm = new InvestimentoForm();

        if($investimento->validarContratoInvestimento(Yii::$app->user->identity->authenticable_id) && !Yii::$app->user->can('admin')){
            return $this->redirect(['visualizar-contrato']);
        }

        $investimentoForm->data_contrato = date("d/m/Y");
        $investimentoForm->valor_contrato = Configuration::getConfigurationValue(Configuration::VALOR_INVESTIMENTO);

        $investimento->dia_vencimento = date("d");
        $parcelasTituloInvestimento = explode(",", Configuration::getConfigurationValue(Configuration::CONFIGURACAO_PARCELA_TITULO_INVESTIMENTO));
        $valores = Investimento::Valores();
        
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $valorTela = preg_replace("/[^0-9]/", "", $valores[$post['Investimento']['valor']]);
            $investimento->dia_vencimento = $post['Investimento']['dia_vencimento'];
            $investimento->prazo = (intval($parcelasTituloInvestimento[$post['Investimento']['prazo']]) * 12);
            $investimento->valor = (float)substr($valorTela, 0, -2).".".substr($valorTela, -2);
            $investimento->consumer_id = $post['Investimento']['consumer_id'];
            if($investimento->validarContratoInvestimento($post['Investimento']['consumer_id'])){
                Yii::$app->session->setFlash('error', Yii::t('app', 'Contrato para o consumidor já foi gerado!'));
                return $this->redirect(['contrato-investimento']);
            }

            if(isset($post['InvestimentoForm'])){
                $investimentoForm->data_contrato = $post['InvestimentoForm']['data_contrato'];
                $investimentoForm->valor_contrato = $post['InvestimentoForm']['valor_contrato'];
            }
            $consumer = $consumer->find()->Where(['id' => $post['Investimento']['consumer_id']])->one();
            $error = 0;
            if(!$consumer->id_asaas || !$consumer->id_bling){
                Yii::$app->session->setFlash('error', Yii::t('app', 'Por favor, acione o suporte para o cadastro no Asaas!'));
                $error = 1;
            }
            if(!$error && $investimento->save()){
                $pagamentoApi = new Payments();
                $pagamentoApi->customer = $consumer->id_asaas;
                $pagamentoApi->value = $investimento->valor;
                $pagamentoApi->installmentCount = 1;
                $pagamentoApi->description = "Pagamento do Investimento ".$consumer->legalPerson->name;
                $pagamentoApi->externalReference = $consumer->id.' - '.$investimento->id;
                $retornoPagamento = $pagamentoApi->CadastrarPagamento();
                $dadosRetorno = json_decode($retornoPagamento->response);
                if(isset($dadosRetorno->netValue)){
                    $faturamento = new Faturamento();
                    $faturamento->prepareSave($consumer, $investimento->valor, $dadosRetorno);
                    if($faturamento->save()){
                        $contas = new ContasReceber();
                        $contas->prepareSave($faturamento, $consumer, $dadosRetorno->invoiceNumber, 1);
                        if($contas->save()){

                            $contaReceberBling = new ContaReceberBling();
                            $contaReceberBling->generateXmlInvestimento($consumer, $contas, "Conta a Pagar Investimento Cliente", $investimento);

                            $retornoConta = $contaReceberBling->CadastrarContaReceber();
                            
                            $contaParcela = new ContasReceberParcelas();
                            $contaParcela->vencimento = date('Y-m-d H:i:s', strtotime('+3 days'));
                            $contaParcela->prepareSave($contas, 1);
                            if(!$contaParcela->save())
                            {
                                $error = 1;
                                Yii::$app->session->setFlash('error', Yii::t('app', 'Sua cobrança foi criada, verifique seu e-mail.'));
                            }
                            $investimento->GerarContratoInvestimento(Pdf::DEST_FILE, Yii::$app->user->can('admin') ? $investimentoForm : null);
                            $investimento->sendContratoInvestimento($dadosRetorno->invoiceUrl);
                            if(Yii::$app->user->can('admin'))
                                Yii::$app->session->setFlash('success', Yii::t('app', 'Contrato de investimento gerado com Sucesso!'));
                            
                            return (!Yii::$app->user->can('admin')) ? $this->redirect(['visualizar-contrato']) : $this->redirect(['contrato-investimento']);
                        }else{
                            $error = 1;
                            Yii::$app->session->setFlash('error', Yii::t('app', 'Sua cobrança foi criada, verifique seu e-mail.'));
                        }
                    }else{
                        Yii::$app->session->setFlash('error', Yii::t('app', 'Aconteceu algum erro na criação do faturamento, porfavor contacte o administrativo.'));
                        foreach ($faturamento->errors as $key => $value) {
                            if(strlen($value[0]) > 0){
                                Yii::$app->session->setFlash('error', Yii::t('app', $value[0]));
                            }
                        }
                    }
                }else{
                    $error = 1;
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Aconteceu algum erro na criação do pagamento, porfavor contacte o administrativo.'));
                }
            }else{
                foreach ($consumer->errors as $key => $value) {
                    if(strlen($value[0]) > 0){
                        Yii::$app->session->setFlash('error', Yii::t('app', $value[0]));
                    }
                }
            }
        }

        return $this->render('contrato-investimento', [
            'investimento' => $investimento,
            'consumer' => $consumer,
            'listParcelas' => $parcelasTituloInvestimento,
            'valores' => $valores,
            'investimentoForm' => $investimentoForm
        ]);
    }

    public function actionVisualizarContrato(){
        $investimento = new Investimento();
        $investimento = $investimento->find()->Where(['consumer_id' => Yii::$app->user->identity->authenticable_id])->one();
        $investimento->GerarContratoInvestimento(Pdf::DEST_BROWSER);
    }

    /**
     * Finds the Business model based on its primary key value.
     * If the model is not found, a 404 HTTP exzip_codetion will be thrown.
     * @param integer $id
     * @return Business the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PorcentagemInvestimento::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}