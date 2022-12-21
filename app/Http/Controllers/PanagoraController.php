<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

class PanagoraController extends Controller
{
    public function index(Request $request, $event_code)
    {
        $panagora_api = new GuzzleClient();

        $response = $panagora_api->request(
            'GET',
            env('PANAGORA_API_ENDPOINT') . "/eventos/$event_code/votante",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('PANAGORA_API_TOKEN'),
                    'Content-Type' => 'application/json',
                ],
                'http_errors' => false
            ]
        );

        $http_status_code = $response->getStatusCode();
        $response = json_decode($response->getBody()->getContents());

        $message = 'Lista de votantes obtida com sucesso';

        // O retorno 404 esperado segundo a documentação não está funcionando, caso um evento não existá a api retorna um erro 500
        switch ($http_status_code) {
            case 401:
                $message = 'Token inválido!';
                break;

            case 404:
                $message = 'Evento não encontrado';
                break;
        }

        if ($request->getAllVoters == 'true') {
            dd('k');
        }

        return response([
            'message' => $message,
            'response' => $response
        ], $http_status_code);
    }
}
