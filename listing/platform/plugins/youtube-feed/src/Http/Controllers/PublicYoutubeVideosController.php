<?php

namespace Botble\YoutubeFeed\Http\Controllers;

use Botble\YoutubeFeed\Services\YoutubeDataApiService;
use Botble\YoutubeFeed\Supports\YoutubeFeedSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PublicYoutubeVideosController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (! is_plugin_active('haft/youtube-feed')) {
            return response()->json(['error' => 'YouTube Feed plugin is not active.'], 503);
        }

        $apiKey = YoutubeFeedSettings::getApiKey();
        if ($apiKey === null || $apiKey === '') {
            $apiKey = trim((string) config('haft-youtube.api_key', ''));
        }
        if ($apiKey === '') {
            return response()->json([
                'error' => 'YouTube Data API key is not configured. Set Admin → YouTube Feed → Settings or YOUTUBE_API_KEY in .env.',
            ], 503);
        }

        $channelId = trim((string) YoutubeFeedSettings::get('channel_id', ''));
        $channelHandle = trim((string) YoutubeFeedSettings::get('channel_handle', ''));
        if ($channelId === '') {
            $channelId = trim((string) config('haft-youtube.channel_id', ''));
        }
        if ($channelHandle === '') {
            $channelHandle = trim((string) config('haft-youtube.channel_handle', ''));
        }
        if ($channelId === '' && $channelHandle === '') {
            return response()->json([
                'error' => 'Set a channel ID or handle in YouTube Feed settings, or YOUTUBE_CHANNEL_ID / YOUTUBE_CHANNEL_HANDLE in .env.',
            ], 503);
        }

        $pageToken = $request->query('pageToken');
        $pageToken = is_string($pageToken) && $pageToken !== '' ? $pageToken : null;

        $max = YoutubeFeedSettings::maxResultsForRequest();
        $reqMax = $request->query('maxResults');
        if (is_numeric($reqMax)) {
            $max = min(50, max(1, (int) $reqMax));
        }

        try {
            $service = new YoutubeDataApiService($apiKey, $channelId !== '' ? $channelId : null, $channelHandle !== '' ? $channelHandle : null);
            $payload = $service->fetchPage($pageToken, $max);

            return response()->json($payload);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => $e->getMessage()], 502);
        }
    }
}
