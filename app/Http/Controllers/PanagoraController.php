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

        switch ($http_status_code) {
            case 401:
                $message = 'Token inválido!';
                break;

            case 404:
                $message = 'Evento não encontrado';
                break;
        }

        return response([
            'message' => $message,
            'response' => $response
        ], $http_status_code);
    }
}
