<?php

namespace App\Services;

use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Factories\Protocol;
use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\Legacy\FilesFolders;
use stdClass;

class NfeService{

    private $config;
    private $tools;

    public function __construct($config){
        // $config = [
        //     "atualizacao" => "2015-10-02 06:01:21",
        //     "tpAmb" => 2,
        //     "razaosocial" => "Fake Materiais de construção Ltda",
        //     "siglaUF" => "SP",
        //     "cnpj" => "00716345000119",
        //     "schemes" => "PL_008i2",
        //     "versao" => "3.10",
        //     "tokenIBPT" => "AAAAAAA",
        //     "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
        //     "CSCid" => "000002"            
        // ];
        
        // $json = json_encode($config);

        $this->config = $config;

        $certificadoDigital = file_get_contents("certificado/CERTITIFCADO_CAESP-C123456789.pfx");

        $this->tools = new Tools(json_encode($config),Certificate::readPfx($certificadoDigital, 'C123456789'));
          
    }

    public function gerarNfe(){
        $nfe = new Make();

        //Inf Nfe
        $stdInNFe = new stdClass();
        $stdInNFe->versao = "4.00"; //versão do layout
        // $stdInNFe->Id = 'NFe35150271780456000160550010000000021800700082';//se o Id de 44 digitos não for passado será gerado automaticamente
        $stdInNFe->pk_nItem = null; //deixe essa variavel sempre como NULL

        $infNfe = $nfe->taginfNFe($stdInNFe);

        //Tag Ide
        $stdIde = new stdClass();
        $stdIde->cUF = 31;
        $stdIde->cNF = rand(11111111, 99999999);
        $stdIde->natOp = "VENDA";

        //$stdIde->indPag = 0; //NÃO EXISTE MAIS NA VERSÃO 4.00

        $stdIde->mod = 55;
        $stdIde->serie = 1;
        $stdIde->nNF = 13;
        $stdIde->dhEmi = date("Y-m-d\TH:i:sP");
        $stdIde->dhSaiEnt = date("Y-m-d\TH:i:sP");
        $stdIde->tpNF = 1; //Entrada ou saida
        $stdIde->idDest = 1;// Dentro ou fora do estado
        $stdIde->cMunFG = 3141108;
        $stdIde->tpImp = 1;//1-Retrato / 2-Paisagem
        $stdIde->tpEmis = 1;//Contingencia ou normal etc...
        $stdIde->cDV = 2;//Digito verificador
        $stdIde->tpAmb = 2;//1-Produção / 2-Homologação
        $stdIde->finNFe = 1;//1-Nfe normal / 2-Nfe comeplementar / 3- Nfes de ajuste
        $stdIde->indFinal = 0;
        $stdIde->indPres = 0;
        $stdIde->procEmi = 0;
        $stdIde->verProc = "2.4.0";
        $stdIde->dhCont = null;//Data e hoira da entrada em contigencia
        $stdIde->xJust = null;//Justificativa a contigencia

        $stdide = $nfe->tagide($stdIde);

        //Tag Emitente
        $stdEmit = new stdClass();
        $stdEmit->xNome = "CAESP CALDEIRARIA ESTRUTURAS E PROJETOS LTDA";
        $stdEmit->xFant = "CAESP";
        $stdEmit->IE = "0015174300047";      
        $stdEmit->CRT = "3";
        $stdEmit->CNPJ = "11399702000106"; //indicar apenas um CNPJ ou CPF      

        $emit = $nfe->tagemit($stdEmit);

        //Endereço do Emitente
        $stdEnderEmit = new stdClass();
        $stdEnderEmit->xLgr = "R JOAO MACHADO NETO";
        $stdEnderEmit->nro = "100";
        $stdEnderEmit->xCpl = "";
        $stdEnderEmit->xBairro = "Distrito Industrial";
        $stdEnderEmit->cMun = "3141108";
        $stdEnderEmit->xMun = "Matozinhos";
        $stdEnderEmit->UF = "MG";
        $stdEnderEmit->CEP = "35720000";
        $stdEnderEmit->cPais = "1058";
        $stdEnderEmit->xPais = "Brasil";
        $stdEnderEmit->fone = "3137125058";

        $enderEmit = $nfe->tagenderEmit($stdEnderEmit);

        //Destinatário
        $stdDest = new stdClass();
        $stdDest->xNome = "VIACAO MARVIN LTDA";
        $stdDest->indIEDest = "1";      
        $stdDest->IE = "0025955190082";
        $stdDest->ISUF = "";
        $stdDest->IM = "";
        $stdDest->email = "teste@gmail.com";
        $stdDest->CNPJ = "22891614000143"; //indicar apenas um CNPJ ou CPF ou idEstrangeiro
        $stdDest->CPF = "";  
        $stdDest->idEstrangeiro ="";    

        $dest = $nfe->tagdest($stdDest);

        //Endereço do Destinatário
        $stdEnderDest = new stdClass();
        $stdEnderDest->xLgr = "Rua Caetano Pirri";
        $stdEnderDest->nro = "834";
        $stdEnderDest->xCpl = "";
        $stdEnderDest->xBairro = "CIC";
        $stdEnderDest->cMun = "3106200";
        $stdEnderDest->xMun = "Belo Horizonte";
        $stdEnderDest->UF = "MG";
        $stdEnderDest->CEP = "30620070";
        $stdEnderDest->cPais = "1058";
        $stdEnderDest->xPais = "Brasil";
        $stdEnderDest->fone = "3133361454";

        $enddest = $nfe->tagenderDest($stdEnderDest);

        //Produtos
        $stdProd = new stdClass();
        $stdProd->item = 1; //item da NFe
        $stdProd->cProd = "4450";
        $stdProd->cEAN = "7897534826649";
        $stdProd->xProd = "LIMPA TELAS 120ML";
        $stdProd->NCM = "44170010";

        $stdProd->cBenef = ""; //incluido no layout 4.00
      
        $stdProd->CFOP = "5102";
        $stdProd->uCom = "UN";
        $stdProd->qCom = "10";
        $stdProd->vUnCom = $this->format(6.99);
        $stdProd->cEANTrib = "7897534826649";
        $stdProd->uTrib = "UN";
        $stdProd->qTrib = "10";
        $stdProd->vUnTrib = $this->format(6.99);
        $stdProd->vProd = $this->format($stdProd->qTrib * $stdProd->vUnTrib);
        $stdProd->vFrete = "";
        $stdProd->vSeg = "";
        // $stdProd->vDesc = "";
        $stdProd->vOutro = "";
        $stdProd->indTot = "1";
        // $stdProd->xPed = "";
        // $stdProd->nItemPed = "";
        // $stdProd->nFCI = "";

        $prod = $nfe->tagprod($stdProd);

        //Informações adicionais do Produto
        $stdAdicional = new stdClass();
        $stdAdicional->item = 1; //item da NFe

        $stdAdicional->infAdProd = "informacao adicional do item";

        $nfe->taginfAdProd($stdAdicional);

        //Imposto
        $stdImposto = new stdClass();
        $stdImposto->item = 1; //item da NFe
        $stdImposto->vTotTrib = 4.00;

        $imposto = $nfe->tagimposto($stdImposto);

        //ICMS
        $stdICMS = new stdClass();
        $stdICMS->item = 1; //item da NFe
        $stdICMS->orig = 0;
        $stdICMS->CST = "00";
        $stdICMS->modBC = "0";
        $stdICMS->vBC = $this->format($stdProd->vProd);
        $stdICMS->pICMS = 18.00;
        $stdICMS->vICMS = $this->format($stdICMS->vBC * ($stdICMS->pICMS / 100));
        // $stdICMS->pFCP;
        // $stdICMS->vFCP;
        // $stdICMS->vBCFCP;
        // $stdICMS->modBCST;
        // $stdICMS->pMVAST;
        // $stdICMS->pRedBCST;
        // $stdICMS->vBCST;
        // $stdICMS->pICMSST;
        // $stdICMS->vICMSST;
        // $stdICMS->vBCFCPST;
        // $stdICMS->pFCPST;
        // $stdICMS->vFCPST;
        // $stdICMS->vICMSDeson;
        // $stdICMS->motDesICMS;
        // $stdICMS->pRedBC;
        // $stdICMS->vICMSOp;
        // $stdICMS->pDif;
        // $stdICMS->vICMSDif;
        // $stdICMS->vBCSTRet;
        // $stdICMS->pST;
        // $stdICMS->vICMSSTRet;
        // $stdICMS->vBCFCPSTRet;
        // $stdICMS->pFCPSTRet;
        // $stdICMS->vFCPSTRet;
        // $stdICMS->pRedBCEfet;
        // $stdICMS->vBCEfet;
        // $stdICMS->pICMSEfet;
        // $stdICMS->vICMSEfet;
        // $stdICMS->vICMSSubstituto; //NT2018.005_1.10_Fevereiro de 2019

        $icms = $nfe->tagICMS($stdICMS);

        //PIS
        $stdPIS = new stdClass();
        $stdPIS->item = 1; //item da NFe
        $stdPIS->CST = "01";
        $stdPIS->vBC = $this->format($stdProd->vProd);
        $stdPIS->pPIS = 1.65;
        $stdPIS->vPIS = $this->format($stdPIS->vBC * ($stdPIS->pPIS / 100));
        // $stdPIS->qBCProd = null;
        // $stdPIS->vAliqProd = null;

        $PIS = $nfe->tagPIS($stdPIS);

        //COFINS
        $stdCOFINS = new stdClass();
        $stdCOFINS->item = 1; //item da NFe
        $stdCOFINS->CST = "01";
        $stdCOFINS->vBC = $this->format($stdProd->vProd);
        $stdCOFINS->pCOFINS = 0.65;
        $stdCOFINS->vCOFINS = $this->format($stdCOFINS->vBC * ($stdCOFINS->pCOFINS / 100));
        // $stdCOFINS->qBCProd = null;
        // $stdCOFINS->vAliqProd = null;

        $cofins = $nfe->tagCOFINS($stdCOFINS);

        //Totais
        $stdICMSTot = new stdClass();
        $stdICMSTot->vBC = "";
        $stdICMSTot->vICMS = "";
        $stdICMSTot->vICMSDeson = "";
        $stdICMSTot->vFCP = ""; //incluso no layout 4.00
        $stdICMSTot->vBCST = "";
        $stdICMSTot->vST = "";
        $stdICMSTot->vFCPST = ""; //incluso no layout 4.00
        $stdICMSTot->vFCPSTRet = ""; //incluso no layout 4.00
        $stdICMSTot->vProd = "";
        $stdICMSTot->vFrete = "";
        $stdICMSTot->vSeg = "";
        $stdICMSTot->vDesc = "";
        $stdICMSTot->vII = "";
        $stdICMSTot->vIPI = "";
        $stdICMSTot->vIPIDevol = ""; //incluso no layout 4.00
        $stdICMSTot->vPIS = "";
        $stdICMSTot->vCOFINS = "";
        $stdICMSTot->vOutro = "";
        $stdICMSTot->vNF = "";
        $stdICMSTot->vTotTrib = "";

        $ICMSTot = $nfe->tagICMSTot($stdICMSTot);

        //Transportadora
        $stdTransp = new stdClass();
        $stdTransp->modFrete = 1;

        $transp = $nfe->tagtransp($stdTransp);

        //Volumes
        $stdVol = new stdClass();
        $stdVol->item = 1; //indicativo do numero do volume
        $stdVol->qVol = 1;
        $stdVol->esp = "caixa";      

        $vol = $nfe->tagvol($stdVol);

        //Pagamento
        $stdPag = new stdClass();
        $stdPag->vTroco = 0.00; //incluso no layout 4.00, obrigatório informar para NFCe (65)

        $pag = $nfe->tagpag($stdPag);

        //Detalhe do Pagamento
        $stdDetPag = new stdClass();
        $stdDetPag->tPag = "14";
        $stdDetPag->vPag = $this->format($stdProd->vProd); //Obs: deve ser informado o valor pago pelo cliente       
        $stdDetPag->indPag = "0"; //0= Pagamento à Vista 1= Pagamento à Prazo

        $detPag = $nfe->tagdetPag($stdDetPag);

        //Informação Adicional
        $stdAdicional = new stdClass();
        $stdAdicional->infAdFisco = "informacoes para o fisco";
        $stdAdicional->infCpl = "informacoes complementares";

        $infAdic = $nfe->taginfAdic($stdAdicional);

        //Monta a Nfe
        if($nfe->montaNFe()){
            return $nfe->getXML();
        }else{
            throw new Exception("Erro ao gerar Nfe");
        }      
    } 

    public function sign($xml){
        return $this->tools->signNFe($xml);
    }

    public function transmitir($signed_xml){

        $resp = $this->tools->sefazEnviaLote([$signed_xml], 1, 1);

        $st = new Standardize();
        $std = $st->toStd($resp);

        if ($std->cStat != 103) {
            //erro registrar e voltar
            exit("[$std->cStat] $std->xMotivo");
        }
        $xmlResp = $std->infRec->nRec; // Vamos usar a variável $recibo para consultar o status da nota
 
        
        
        if($std->cStat=='104'){ //lote processado (tudo ok)
            if($std->protNFe->infProt->cStat=='100'){ //Autorizado o uso da NF-e
                $return = ["situacao"=>"autorizada",
                           "numeroProtocolo"=>$std->protNFe->infProt->nProt,
                           "xmlProtocolo"=>$xmlResp];
            }elseif(in_array($std->protNFe->infProt->cStat,["302"])){ //DENEGADAS
                $return = ["situacao"=>"denegada",
                           "numeroProtocolo"=>$std->protNFe->infProt->nProt,
                           "motivo"=>$std->protNFe->infProt->xMotivo,
                           "cstat"=>$std->protNFe->infProt->cStat,
                           "xmlProtocolo"=>$xmlResp];
            }else{ //não autorizada (rejeição)
                $return = ["situacao"=>"rejeitada",
                           "motivo"=>$std->protNFe->infProt->xMotivo,
                           "cstat"=>$std->protNFe->infProt->cStat];
            }
        } else { //outros erros possíveis
            $return = ["situacao"=>"rejeitada",
                       "motivo"=>$std->xMotivo,
                       "cstat"=>$std->cStat];
        }



        
        
        file_put_contents('nfes/nota.xml',$resp);


       return $return;
      
    }
    
    public function format($number, $desc = 2){
        return number_format((float) $number,$desc,'.','');
    }
}