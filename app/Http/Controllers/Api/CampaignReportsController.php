<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Arr;
use Sendportal\Base\Models\Campaign;
use Sendportal\Base\Facades\Sendportal;
use Illuminate\Http\{Request, JsonResponse};
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Presenters\CampaignReportPresenter;
use Sendportal\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Sendportal\Base\Repositories\Messages\MessageTenantRepositoryInterface;

class CampaignReportsController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    protected $campaignRepo;

    /** @var MessageTenantRepositoryInterface */
    protected $messageRepo;

    public function __construct(
        CampaignTenantRepositoryInterface $campaignRepository,
        MessageTenantRepositoryInterface $messageRepo
    ) {
        $this->campaignRepo = $campaignRepository;
        $this->messageRepo = $messageRepo;
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index(int $id, Request $request): JsonResponse
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        if ($response = $this->statusCheck($campaign)) {
            return $response;
        }

        $presenterData = (new CampaignReportPresenter(
            $campaign,
            Sendportal::currentWorkspaceId(),
            (int)$request->get('interval', 24)
        ))->generate();

        $data = [
            'campaign' => $campaign,
            'campaign_urls' => $presenterData['campaignUrls'],
            'campaign_stats' => $presenterData['campaignStats'],
            'chart_labels' => json_encode(Arr::get($presenterData['chartData'], 'labels', [])),
            'chart_data' => json_encode(Arr::get($presenterData['chartData'], 'data', [])),
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function recipients(int $id): JsonResponse
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        if ($response = $this->statusCheck($campaign)) {
            return $response;
        }

        $messages = $this->messageRepo->recipients(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages
            ]
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function opens(int $id): JsonResponse
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);
        $averageTimeToOpen = $this->campaignRepo->getAverageTimeToOpen($campaign);

        if ($response = $this->statusCheck($campaign)) {
            return $response;
        }

        $messages = $this->messageRepo->opens(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        $campaign->append(['unique_open_count', 'total_open_count']);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages,
                'average_time_to_open' => $averageTimeToOpen
            ]
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function clicks(int $id): JsonResponse
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);
        $averageTimeToClick = $this->campaignRepo->getAverageTimeToClick($campaign);

        if ($response = $this->statusCheck($campaign)) {
            return $response;
        }

        $messages = $this->messageRepo->clicks(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        $campaign->append(['unique_click_count', 'total_click_count']);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages,
                'average_time_to_click' => $averageTimeToClick
            ]
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function bounces(int $id): JsonResponse
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        if ($response = $this->statusCheck($campaign)) {
            return $response;
        }

        $messages = $this->messageRepo->bounces(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages
            ]
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function unsubscribes(int $id): JsonResponse
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        if ($response = $this->statusCheck($campaign)) {
            return $response;
        }

        $messages = $this->messageRepo->unsubscribes(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages
            ]
        ]);
    }

    /**
     * @param Campaign $campaign
     * @return JsonResponse
     */
    private function statusCheck(Campaign $campaign)
    {
        if ($campaign->draft) {
            return response()->json(['message' => 'Campaign is draft'], 401);
        }

        if ($campaign->queued || $campaign->sending) {
            return response()->json(['message' => 'Campaign is queued or sending'], 401);
        }
    }
}
