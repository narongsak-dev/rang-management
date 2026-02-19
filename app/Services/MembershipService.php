<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MembershipService
{
    public function create(Customer $customer): Membership
    {
        $now = Carbon::now();

        return Membership::create([
            'customer_id' => $customer->id,
            'member_no'   => $this->generateMemberNo(),
            'started_at'  => $now->toDateString(),
            'expires_at'  => $now->addYear()->toDateString(),
            'status'      => 'active',
        ]);
    }

    public function renew(Membership $membership): Membership
    {
        $base = $membership->expires_at->isFuture()
            ? $membership->expires_at
            : Carbon::now();

        $membership->update([
            'expires_at' => $base->addYear()->toDateString(),
            'status'     => 'active',
        ]);

        return $membership->fresh();
    }

    public function syncExpired(): void
    {
        Membership::where('status', 'active')
            ->where('expires_at', '<', Carbon::today())
            ->update(['status' => 'expired']);
    }

    private function generateMemberNo(): string
    {
        do {
            $no = 'MBR-' . strtoupper(Str::random(8));
        } while (Membership::where('member_no', $no)->exists());

        return $no;
    }
}
