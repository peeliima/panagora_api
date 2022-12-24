<?php

namespace tests\Unit;

use Tests\TestCase;
use Tests\Providers\PanagoraProvidersTrait;

class PanagoraTest extends TestCase
{
	use PanagoraProvidersTrait;

    protected $event_id = 7747;

	/**
	 * @test
	*/
    public function checkBasicResponseApi()
	{
        $response = $this->json(
			'GET',
			"/",
		);

		$response->assertStatus(200);
	}

	/**
	 * @test
	*/
	public function checkResponseStatusHttpInIndexVotersNoCreatePdf()
	{
        $response = $this->json(
			'GET',
			"/api/index/$this->event_id/voters",
		);

		$response->assertStatus(200);
	}

	/**
	 * @test
     * @dataProvider sucessCaseWithNotPdf
	*/
    public function checkReturnPayloadInIndexFunctionNoCreatePdf($data_provider)
    {
        $response = $this->json(
			'GET',
			"/api/index/$this->event_id/voters",
		);

        $response->assertStatus(200);

        $response->assertJsonStructure($data_provider);
    }

    /**
	 * @test
     * @dataProvider sucessCaseCreatePdf
	*/
    public function checkReturnPayloadInIndexFunctionCreatePdf($data_provider)
    {
        $response = $this->json(
			'GET',
			"/api/index/$this->event_id/voters?getAllDocuments=true",
		);

        $response->assertStatus(200);

        $response->assertJsonStructure($data_provider);
    }

    /**
	 * @test
     * @dataProvider sucessCaseCreatePdfPagineted
	*/
    public function checkReturnPayloadInIndexPagineted($data_provider)
    {
        $response = $this->json(
			'GET',
			"/api/index/$this->event_id/voters?page=1&per_page=10&getAllDocuments=true",
		);

        $response->assertStatus(200);

        $response->assertJsonStructure($data_provider);
    }

    /**
	 * @test
     * @dataProvider voteIds
	*/
    public function checkReturnPayloadInVotePersonPDF($data_provider)
    {
        $response = $this->json(
			'POST',
            "/api/docs/$this->event_id/pdf",
            $data_provider['post_fields']
		);

        $response->assertStatus(200);

        $response->assertJsonStructure($data_provider['return']);
    }
}
