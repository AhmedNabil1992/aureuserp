<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineSystem;
use Webkul\SoftwareOnline\Models\OnlineTenantWebhookEvent;

class TenantWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'event_id'                          => ['required', 'string', 'max:100'],
            'event_type'                        => ['required', 'string', Rule::in([
                'tenant.provisioning_started',
                'tenant.provisioned',
                'tenant.provisioning_failed',
                'tenant.renewed',
                'tenant.suspended',
                'tenant.activated',
                'tenant.deletion_started',
                'tenant.deleted',
                'tenant.entitlements_updated',
            ])],
            'occurred_at'                       => ['required', 'date'],
            'data'                              => ['required', 'array'],
            'data.external_subscription_id'     => ['required', 'string', 'max:100', 'regex:/^online-instance:[1-9][0-9]*$/'],
            'data.instance_id'                  => ['nullable', 'integer'],
            'data.tenant_id'                    => ['required_if:event_type,tenant.provisioned', 'nullable', 'string', 'max:100'],
            'data.provisioning_request_id'      => ['nullable', 'string', 'max:100'],
            'data.domain'                       => ['nullable', 'string', 'max:255'],
            'data.login_url'                    => ['nullable', 'url', 'max:2048'],
            'data.ended_at'                     => ['nullable', 'date'],
            'data.error_code'                   => ['nullable', 'string', 'max:100'],
            'data.error_message'                => ['nullable', 'string', 'max:5000'],
        ]);

        /** @var OnlineSystem $system */
        $system = $request->attributes->get('onlineSystem');

        return DB::transaction(function () use ($payload, $system): JsonResponse {
            $existing = OnlineTenantWebhookEvent::query()
                ->where('event_id', $payload['event_id'])
                ->lockForUpdate()
                ->first();

            if ($existing?->status === 'processed') {
                return response()->json([
                    'message'  => 'Webhook already processed.',
                    'event_id' => $existing->event_id,
                    'status'   => 'duplicate',
                ]);
            }

            $event = $existing ?? OnlineTenantWebhookEvent::create([
                'system_id'   => $system->id,
                'event_id'    => $payload['event_id'],
                'event_type'  => $payload['event_type'],
                'status'      => 'received',
                'occurred_at' => $payload['occurred_at'],
                'payload'     => $payload,
            ]);

            $instance = $this->findInstance($system, $payload['data']);
            if (! $instance) {
                $event->update([
                    'status' => 'failed',
                    'error'  => 'No matching online instance was found.',
                ]);

                return response()->json([
                    'message'    => 'No matching online instance was found.',
                    'error_code' => 'ONLINE_INSTANCE_NOT_FOUND',
                ], 422);
            }

            $this->applyEvent($instance, $payload['event_type'], $payload['occurred_at'], $payload['data']);

            $event->update([
                'instance_id' => $instance->id,
                'status'      => 'processed',
                'error'       => null,
                'processed_at'=> now(),
            ]);

            return response()->json([
                'message'     => 'Webhook processed.',
                'event_id'    => $event->event_id,
                'instance_id' => $instance->id,
                'status'      => 'processed',
            ]);
        });
    }

    private function findInstance(OnlineSystem $system, array $data): ?OnlineInstance
    {
        $query = OnlineInstance::query()->where('system_id', $system->id)->lockForUpdate();

        if (filled($data['external_subscription_id'] ?? null)) {
            $reference = (string) $data['external_subscription_id'];
            $instanceId = str_starts_with($reference, 'online-instance:')
                ? substr($reference, strlen('online-instance:'))
                : $reference;

            if (ctype_digit($instanceId)) {
                return (clone $query)->whereKey((int) $instanceId)->first();
            }
        }

        if (filled($data['instance_id'] ?? null)) {
            return (clone $query)->whereKey((int) $data['instance_id'])->first();
        }

        if (filled($data['tenant_id'] ?? null)) {
            $instance = (clone $query)->where('remote_tenant_id', $data['tenant_id'])->first();
            if ($instance) {
                return $instance;
            }
        }

        if (filled($data['provisioning_request_id'] ?? null)) {
            return (clone $query)->where('provisioning_request_id', $data['provisioning_request_id'])->first();
        }

        return null;
    }

    private function applyEvent(OnlineInstance $instance, string $eventType, string $occurredAt, array $data): void
    {
        $eventTime = Carbon::parse($occurredAt)->utc();
        if ($instance->last_webhook_at?->gte($eventTime)) {
            return;
        }

        $attributes = [
            'last_webhook_at' => $eventTime,
            'last_api_sync_at'=> $eventTime,
            'remote_data'     => array_replace_recursive($instance->remote_data ?? [], $data),
        ];

        if (filled($data['tenant_id'] ?? null)) {
            $attributes['remote_tenant_id'] = $data['tenant_id'];
        }

        if (filled($data['provisioning_request_id'] ?? null)) {
            $attributes['provisioning_request_id'] = $data['provisioning_request_id'];
        }

        if (filled($data['login_url'] ?? null)) {
            $attributes['instance_url'] = $data['login_url'];
        }

        if (filled($data['ended_at'] ?? null)) {
            $attributes['expires_at'] = $data['ended_at'];
        }

        match ($eventType) {
            'tenant.provisioning_started' => $attributes['status'] = InstanceStatus::Provisioning,
            'tenant.provisioned'          => $attributes = array_merge($attributes, [
                'status'          => InstanceStatus::Active,
                'provisioned_at'  => now(),
                'last_api_error'  => null,
            ]),
            'tenant.provisioning_failed'  => $attributes = array_merge($attributes, [
                'status'         => InstanceStatus::Failed,
                'last_api_error' => trim(implode(': ', array_filter([
                    Arr::get($data, 'error_code'),
                    Arr::get($data, 'error_message'),
                ]))),
            ]),
            'tenant.renewed'              => $attributes = array_merge($attributes, [
                'status'             => InstanceStatus::Active,
                'last_api_error'     => null,
                'last_renewal_error' => null,
            ]),
            'tenant.suspended'            => $attributes['status'] = InstanceStatus::Suspended,
            'tenant.activated'            => $attributes['status'] = InstanceStatus::Active,
            'tenant.deletion_started'     => $attributes['status'] = InstanceStatus::Deleting,
            'tenant.deleted'              => $attributes['status'] = InstanceStatus::Deleted,
            'tenant.entitlements_updated' => null,
        };

        $instance->update($attributes);
    }
}
