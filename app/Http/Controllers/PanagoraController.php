<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Promise;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

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

            case 500:
                $message = 'Evento não encontrado, ou erro na assembleia_api';
                break;
        }

        foreach ($vote_people as $vote_person) {
            $vote_ids[] = $vote_person->id;
        }

        $page = $request->page ?? 1;
        $per_page = $request->per_page ?? 5;

        $vote_ids = collect($vote_ids)
            ->unique()
            ->skip(($page - 1) * $per_page)
            ->take($per_page);

        if ($request->getAllDocuments == 'true') {
            $urls = $this->votePersonPDF(
                new Request([
                    'votes_ids' => $vote_ids
                ]),
                $event_code
            );
           
            return response([
                'urls' => $urls,
            ], 200);
        }

        return response([
            'message' => $message,
            'response' => $vote_people
        ], $http_status_code);
    }

    public function votePersonPDF(Request $request, $event_code)
    {
        $validation = Validator::make($request->all(), [
            'votes_ids' => 'required'
        ]);

        if ($validation->fails()) {
            return response([
                'success' => false,
                'error' => $validation->errors()
            ], 400);
        }

        $vote_ids = $request->votes_ids;

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
        }

        $responses = Promise\Utils::unwrap($response);

        foreach ($responses as $response) {
            $voter = json_decode($response->getBody()->getContents());

            $encrypt_option = $voter->cpf ?? $voter->nome;
            $encrypt = md5($encrypt_option);

            $pdf = Pdf::loadView('pdf.voter', ['name' => $voter->nome]);

            $voter_name = str_replace(' ', '', $voter->nome);
            $path = storage_path('app/public/');
            $fileName =  $voter_name . '-' . $encrypt . '.pdf' ;
            $pdf->save($path . '/' . $fileName);

            $full_file_path = $path . $fileName;

            $current_person[] = [
                'votante' => $voter->nome,
                'pdf_path' => url($full_file_path)
            ];
        }

        return $current_person;
    }
}
