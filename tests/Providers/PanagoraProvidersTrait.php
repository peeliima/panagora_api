<?php

namespace Tests\Providers;

trait PanagoraProvidersTrait
{
    public function sucessCaseWithNotPdf()
	{
		return [
			'Espera o retorno da funcao index sem criacao de pdf da seguinte maneira' => [
                [
                    'message',
                    'response' => [
                        '*' => [
                            'id',
                            'nome',
                            'email',
                            'celular',
                            'cpf',
                            'dataNascimento',
                            'matriculaFuncional',
                            'urna',
                            'demaisDados',
                            'aprovado',
                            'votoSeparado',
                            'listaInicial',
                            'motivo',
                            'fingerprint'
                        ]
                    ]
                ]
            ]
		];
	}

    public function sucessCaseCreatePdf()
	{
		return [
			'Espera o retorno da funcao index com a criacao de pdf da seguinte maneira' => [
                [
                    'urls' => [
                        '*' => [
                            'votante',
                            'pdf_path'
                        ]
                    ]
                ]
            ]
		];
	}

    public function sucessCaseCreatePdfPagineted()
	{
		return [
			'Espera o retorno da funcao index com a criacao de pdf e enviado paginacao' => [
                [
                    'urls' => [
                        '*' => [
                            'votante',
                            'pdf_path'
                        ]
                    ]
                ]
            ]
		];
	}

    public function voteIds()
	{
		return [
            'Envia alguns votes_ids e aguarda segunte retorno' =>
            [
                [
                    'post_fields' => [
                        'votes_ids' => [
                            5444527, 
                            5444852 
                        ]
                    ],
    
                    'return' => [
                        '*' => [
                            'votante',
                            'pdf_path'
                        ]
                    ]
                ]
            ]
        ];
	}
}
