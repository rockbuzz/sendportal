<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Models\Campaign;
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
     * Show campaign report view.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function index(int $id, Request $request)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        $this->statusCheck($campaign);

        $presenter = new CampaignReportPresenter($campaign, Sendportal::currentWorkspaceId(), (int) $request->get('interval', 24));
        $presenterData = $presenter->generate();

        $data = [
            'campaign' => $campaign,
            'campaignUrls' => $presenterData['campaignUrls'],
            'campaignStats' => $presenterData['campaignStats'],
            'chartLabels' => json_encode(Arr::get($presenterData['chartData'], 'labels', [])),
            'chartData' => json_encode(Arr::get($presenterData['chartData'], 'data', [])),
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Show campaign recipients.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function recipients(int $id)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        $this->statusCheck($campaign);

        $messages = $this->messageRepo->recipients(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages
            ]
        ]);
    }

    /**
     * Show campaign opens.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function opens(int $id)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);
        $averageTimeToOpen = $this->campaignRepo->getAverageTimeToOpen($campaign);

        $this->statusCheck($campaign);

        $messages = $this->messageRepo->opens(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages,
                'averageTimeToOpen' => $averageTimeToOpen
            ]
        ]);
    }

    /**
     * Show campaign clicks.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function clicks(int $id)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);
        $averageTimeToClick = $this->campaignRepo->getAverageTimeToClick($campaign);

        $this->statusCheck($campaign);

        $messages = $this->messageRepo->clicks(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages,
                'averageTimeToClick' => $averageTimeToClick
            ]
        ]);
    }

    /**
     * Show campaign bounces.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function bounces(int $id)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        $this->statusCheck($campaign);

        $messages = $this->messageRepo->bounces(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages
            ]
        ]);
    }

    /**
     * Show campaign unsubscribes.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function unsubscribes(int $id)
    {
        $campaign = $this->campaignRepo->find(Sendportal::currentWorkspaceId(), $id);

        $this->statusCheck($campaign);

        $messages = $this->messageRepo->unsubscribes(Sendportal::currentWorkspaceId(), Campaign::class, $id);

        return response()->json([
            'data' => [
                'campaign' => $campaign,
                'messages' => $messages
            ]
        ]);
    }

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
