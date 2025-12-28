<?php

namespace Tests\Util\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\CustomerProfile;

trait SignupAssertions
{
    public function assertUserExistInDB(array $userWithProfile)
    {
        $userNoProfile = $userWithProfile;
        unset($userNoProfile['profile']);

        $userNoProfile['created_at'] = Carbon::create($userNoProfile['created_at'])
            ->toDateTimeString();
        $userNoProfile['updated_at'] = Carbon::create($userNoProfile['updated_at'])
            ->toDateTimeString();

        $this->assertDatabaseHas(User::class, $userNoProfile);
    }

    public function assertCustomerProfileExistInDB(array $customerProfile)
    {
        $customerProfile['created_at'] = Carbon::create($customerProfile['created_at'])
            ->toDateTimeString();
        $customerProfile['updated_at'] = Carbon::create($customerProfile['updated_at'])
            ->toDateTimeString();

        $this->assertDatabaseHas(CustomerProfile::class, $customerProfile);
    }
}