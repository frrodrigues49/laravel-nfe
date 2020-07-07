<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NfeService;

class NfeController extends Controller
{    
    public function index()
    {
        return view('danfe');
    } 

   
    public function store(Request $request)
    {
        $nfe_service = new NfeService([
            "atualizacao" => "2019-06-0132 08:36:21",
            "tpAmb" => 2,
            "razaosocial" => "CAESP CALDEIRARIA ESTRUTURAS E PROJETOS LTDA",
            "siglaUF" => "MG",
            "cnpj" => "11399702000106",
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
            "tokenIBPT" => "AAAAAAA",
            "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
            "CSCid" => "000002"        
        ]);

        header('Content-type: text/xml; charset-UTF-8');

        //Gera o XML
        $xml = $nfe_service->gerarNfe();

        //Assinar o XML
        $signed_xml = $nfe_service->sign($xml);

        //Transmitir
        $resultado = $nfe_service->transmitir($signed_xml);

        return $resultado;
    }

}
