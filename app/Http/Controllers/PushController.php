<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Throwable;

class PushController extends Controller {
    protected WebPush $webPush;


    // ─────────────────────────────
    // Helpers
    // ─────────────────────────────
    public static function getPushIds(int $userId): array {
        $subscription = PushSubscription::where('user_id', $userId)->first();
        return $subscription?->push_ids ?? [];
    }

    public static function setPushIds(int $userId, array $pushIds): void {
        $subscription = PushSubscription::firstOrCreate(['user_id' => $userId]);
        $subscription->push_ids = $pushIds;
        $subscription->save();
    }

    public static function sendPush(array $subscription, string $payload) {
        $pushAuth = [
            'VAPID' => [
                'subject'   => 'mailto:' . env('PUSH_EMAIL'),
                'publicKey' => env('PUSH_PUBLIC_KEY'),
                'privateKey' => env('PUSH_PRIVATE_KEY'),
            ],
        ];

        $webPush = new WebPush($pushAuth);

        $sub = Subscription::create($subscription);
        return $webPush->sendOneNotification($sub, $payload);
    }

    // ─────────────────────────────
    // Main handler
    // ─────────────────────────────
    public function handle(Request $request) {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $method = $request->query('method');

        // IMPORTANT: decode raw JSON if not wrapped in `subscription`
        $subscription = $request->json()->all();
        if (isset($subscription['subscription'])) {
            $subscription = $subscription['subscription']; // supports both formats
        }

        if (empty($subscription)) {
            return response()->json(['error' => 'Invalid subscription data'], 400);
        }

        try {
            switch ($method) {
                case 'create':
                    $pushIds = $this->getPushIds($user->id);

                    // Replace existing if same auth key
                    $found = false;
                    foreach ($pushIds as $i => $pushId) {
                        if (($pushId['keys']['auth'] ?? null) === ($subscription['keys']['auth'] ?? null)) {
                            $pushIds[$i] = $subscription;
                            $found = true;
                        }
                    }

                    if (!$found) {
                        $pushIds[] = $subscription;
                    }

                    $this->setPushIds($user->id, $pushIds);

                    $payload = json_encode([
                        'title' => "Push Notification",
                        'body'  => __('Notification is enabled'),
                        'url'   => config('app.url'),
                    ]);

                    $this->sendPush($subscription, $payload);

                    return response()->json([
                        'exist'   => true,
                        'element' => "<span class='mr-1'>" . __('disable_notifications') . "</span> <i class='fa fa-bell-slash'></i>"
                    ]);

                case 'check':
                    $pushIds = $this->getPushIds($user->id);

                    foreach ($pushIds as $i => $pushId) {
                        if (($pushId['keys']['auth'] ?? null) === ($subscription['keys']['auth'] ?? null)) {
                            $pushIds[$i] = $subscription;
                            $this->setPushIds($user->id, $pushIds);

                            return response()->json([
                                'exist'   => true,
                                'element' => "<span class='mr-1'>" . __('disable_notifications') . "</span> <i class='fa fa-bell-slash'></i>"
                            ]);
                        }
                    }

                    return response()->json([
                        'exist'   => false,
                        'element' => "<span class='mr-1'>" . __('enable_notifications') . "</span> <i class='fa fa-bell'></i>"
                    ]);

                case 'delete':
                    $pushIds = $this->getPushIds($user->id);

                    $pushIds = array_filter($pushIds, function ($pushId) use ($subscription) {
                        return ($pushId['keys']['auth'] ?? null) !== ($subscription['keys']['auth'] ?? null);
                    });

                    $this->setPushIds($user->id, array_values($pushIds));

                    $payload = json_encode([
                        'title' => "Push Notification",
                        'body'  => __('notification_is_disabled'),
                        'url'   => config('app.url'),
                    ]);

                    $this->sendPush($subscription, $payload);

                    return response()->json([
                        'exist'   => false,
                        'element' => "<span class='mr-1'>" . __('enable_notifications') . "</span> <i class='fa fa-bell'></i>"
                    ]);

                default:
                    return response()->json(['error' => 'Method not handled'], 400);
            }
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
