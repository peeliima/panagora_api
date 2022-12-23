<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Promise;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $vote_people = json_decode($response->getBody()->getContents());

        $message = 'Lista de votantes obtida com sucesso';

        // O retorno 404 esperado segundo a documentação não está funcionando, caso um evento não exista a api retorna um erro 500
        switch ($http_status_code) {
            case 401:
                $message = 'Token inválido!';
                break;

            case 404:
                $message = 'Evento não encontrado';
                break;
        }

        foreach ($vote_people as $vote_person) {
            $vote_ids[] = $vote_person->id;
        }

        if ($request->getAllDocuments == 'true') {
            $this->votePersonPDF($vote_ids, $event_code);

            die();
        }

        return response([
            'message' => $message,
            'response' => $vote_people
        ], $http_status_code);
    }

    public function votePersonPDF(Array $vote_ids, $event_code)
    {
        $panagora_api = new GuzzleClient();

        foreach ($vote_ids as $vote_id) {
            $response[] = $panagora_api->requestAsync(
                'GET',
                env('PANAGORA_API_ENDPOINT') . "/eventos/$event_code/votante/$vote_id",
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . env('PANAGORA_API_TOKEN'),
                        'Content-Type' => 'application/json',
                    ],
                    'http_errors' => false
                ]
            );

            break;
        }

        $responses = Promise\Utils::unwrap($response);

        foreach ($responses as $response) {
            $voters = json_decode($response->getBody()->getContents());

            $pdf = Pdf::loadView('pdf.voter', ['name' => $voters->nome]);
            dd($pdf->download('voter.pdf'));
        }
    }
}
