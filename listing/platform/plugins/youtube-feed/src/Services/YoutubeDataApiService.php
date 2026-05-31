<?php

namespace Botble\YoutubeFeed\Services;

use Illuminate\Support\Facades\Http;

class YoutubeDataApiService
{
    private const BASE = 'https://www.googleapis.com/youtube/v3';

    public function __construct(
        protected string $apiKey,
        protected ?string $channelId,
        protected ?string $channelHandle
    ) {}

    /**
     * @return array{videos: array<int, array<string, mixed>>, nextPageToken: string|null, channelId: string|null, channelTitle: string|null, channelThumbUrl: string|null, subscriberCount: int|null}
     */
    public function fetchPage(?string $pageToken, int $maxResults): array
    {
        $maxResults = min(50, max(1, $maxResults));

        $channel = $this->fetchChannel();
        $uploadsPlaylistId = $channel['uploadsPlaylistId'];
        $channelMeta = $channel['meta'];

        $plUrl = self::BASE . '/playlistItems';
        $plQuery = [
            'part' => 'snippet,contentDetails',
            'playlistId' => $uploadsPlaylistId,
            'maxResults' => $maxResults,
            'key' => $this->apiKey,
        ];
        if ($pageToken) {
            $plQuery['pageToken'] = $pageToken;
        }

        $plRes = Http::timeout(25)
            ->acceptJson()
            ->get($plUrl, $plQuery);

        if (! $plRes->successful()) {
            $this->throwFromGoogle($plRes->json(), $plRes->status());
        }

        $plData = $plRes->json();
        $items = $plData['items'] ?? [];
        $videos = $this->normalizePlaylistItems($items);
        $videos = $this->enrichVideos($videos);

        return [
            'videos' => $videos,
            'nextPageToken' => $plData['nextPageToken'] ?? null,
            'channelId' => $channelMeta['channelId'],
            'channelTitle' => $channelMeta['channelTitle'],
            'channelThumbUrl' => $channelMeta['channelThumbUrl'],
            'subscriberCount' => $channelMeta['subscriberCount'],
        ];
    }

    /**
     * @return array{uploadsPlaylistId: string, meta: array{channelId: string|null, channelTitle: string|null, channelThumbUrl: string|null, subscriberCount: int|null}}
     */
    protected function fetchChannel(): array
    {
        $params = [
            'part' => 'snippet,contentDetails,statistics',
            'key' => $this->apiKey,
        ];
        if ($this->channelId) {
            $params['id'] = $this->channelId;
        } elseif ($this->channelHandle) {
            $params['forHandle'] = ltrim($this->channelHandle, '@');
        } else {
            throw new \InvalidArgumentException('Configure channel ID or handle in YouTube Feed settings.');
        }

        $res = Http::timeout(25)->acceptJson()->get(self::BASE . '/channels', $params);

        if (! $res->successful()) {
            $this->throwFromGoogle($res->json(), $res->status());
        }

        $data = $res->json();
        $ch = $data['items'][0] ?? null;
        if (! $ch) {
            throw new \RuntimeException('Channel not found. Check channel ID or handle.');
        }

        $uploadsPlaylistId = $ch['contentDetails']['relatedPlaylists']['uploads'] ?? null;
        if (! $uploadsPlaylistId) {
            throw new \RuntimeException('Could not resolve uploads playlist for this channel.');
        }

        $thumbs = $ch['snippet']['thumbnails'] ?? [];
        $channelThumbUrl = $thumbs['high']['url'] ?? $thumbs['medium']['url'] ?? $thumbs['default']['url'] ?? null;
        $sc = $ch['statistics']['subscriberCount'] ?? null;
        $subscriberCount = null;
        if ($sc !== null && $sc !== '') {
            $n = (int) $sc;
            $subscriberCount = $n > 0 || (string) $sc === '0' ? $n : null;
        }

        return [
            'uploadsPlaylistId' => $uploadsPlaylistId,
            'meta' => [
                'channelId' => $ch['id'] ?? null,
                'channelTitle' => $ch['snippet']['title'] ?? null,
                'channelThumbUrl' => $channelThumbUrl,
                'subscriberCount' => $subscriberCount,
            ],
        ];
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array<string, mixed>>
     */
    protected function normalizePlaylistItems(array $items): array
    {
        $out = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $sn = $item['snippet'] ?? [];
            $rid = is_array($sn['resourceId'] ?? null) ? $sn['resourceId'] : [];
            $vid = $rid['videoId'] ?? ($item['contentDetails']['videoId'] ?? null);
            if (! is_string($vid) || $vid === '') {
                continue;
            }
            $thumbs = is_array($sn['thumbnails'] ?? null) ? $sn['thumbnails'] : [];
            $thumb = $thumbs['maxres']['url'] ?? $thumbs['high']['url'] ?? $thumbs['medium']['url'] ?? $thumbs['default']['url'] ?? '';

            $out[] = [
                'id' => $vid,
                'title' => is_string($sn['title'] ?? null) ? $sn['title'] : 'Untitled',
                'description' => is_string($sn['description'] ?? null) ? $sn['description'] : '',
                'publishedAt' => is_string($sn['publishedAt'] ?? null) ? $sn['publishedAt'] : '',
                'thumbnail' => is_string($thumb) ? $thumb : '',
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, array<string, mixed>>  $videos
     * @return array<int, array<string, mixed>>
     */
    protected function enrichVideos(array $videos): array
    {
        $ids = array_values(array_filter(array_map(fn ($v) => $v['id'] ?? '', $videos)));
        if ($ids === []) {
            return $videos;
        }

        $res = Http::timeout(25)->acceptJson()->get(self::BASE . '/videos', [
            'part' => 'snippet,contentDetails,statistics',
            'id' => implode(',', $ids),
            'key' => $this->apiKey,
        ]);

        if (! $res->successful()) {
            return array_map(fn ($v) => array_merge($v, [
                'durationSeconds' => null,
                'viewCount' => null,
                'liveBroadcastContent' => 'none',
            ]), $videos);
        }

        $data = $res->json();
        $byId = [];
        foreach ($data['items'] ?? [] as $item) {
            if (is_array($item) && ! empty($item['id'])) {
                $byId[$item['id']] = $item;
            }
        }

        return array_map(function ($v) use ($byId) {
            $id = $v['id'] ?? '';
            $item = $byId[$id] ?? null;
            if (! is_array($item)) {
                return array_merge($v, [
                    'durationSeconds' => null,
                    'viewCount' => null,
                    'liveBroadcastContent' => 'none',
                ]);
            }

            $cd = $item['contentDetails'] ?? [];
            $st = $item['statistics'] ?? [];
            $sn = $item['snippet'] ?? [];
            $vc = $st['viewCount'] ?? null;

            return array_merge($v, [
                'durationSeconds' => $this->parseIso8601Duration(is_string($cd['duration'] ?? null) ? $cd['duration'] : null),
                'viewCount' => $vc !== null && $vc !== '' ? (int) $vc : null,
                'liveBroadcastContent' => is_string($sn['liveBroadcastContent'] ?? null) ? $sn['liveBroadcastContent'] : 'none',
            ]);
        }, $videos);
    }

    protected function parseIso8601Duration(?string $iso): ?int
    {
        if (! $iso || ! is_string($iso)) {
            return null;
        }
        if (! preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/', $iso, $m)) {
            return null;
        }
        $h = (int) ($m[1] ?? 0);
        $min = (int) ($m[2] ?? 0);
        $s = (int) ($m[3] ?? 0);

        return $h * 3600 + $min * 60 + $s;
    }

    protected function throwFromGoogle(mixed $json, int $status): void
    {
        $msg = 'YouTube API error';
        if (is_array($json)) {
            $err = $json['error'] ?? null;
            if (is_array($err) && isset($err['message'])) {
                $msg = is_string($err['message']) ? $err['message'] : $msg;
            } elseif (isset($json['error_description']) && is_string($json['error_description'])) {
                $msg = $json['error_description'];
            }
        }

        throw new \RuntimeException($msg . ' (HTTP ' . $status . ')');
    }
}
