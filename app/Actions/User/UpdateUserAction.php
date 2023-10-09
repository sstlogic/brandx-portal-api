<?php

namespace App\Actions\User;

use App\Actions\BaseAction;
use App\Booked\Models\BookedUser;
use App\Booked\Repositories\AttributeRepository;
use App\Booked\Repositories\UserRepository;
use App\Mail\NewRegistrationMail;
use App\Mail\UserUpdatedMail;
use App\Models\User;
use Arr;
use Mail;
use Mockery\Undefined;

class UpdateUserAction extends BaseAction
{
    private User $user;

    public function __construct(
        private UserRepository      $userRepository,
        private AttributeRepository $attributeRepository
    ) {
    }

    public function execute(User $user, array $data): ?BookedUser
    {
        $this->setData($data);
        $this->user = $user;

        return $this->updateBookedUser();
    }



    private function updateBookedUser(): ?BookedUser
    {
        $data = [
            "language" => "en_us",
            "firstName" => $this->data->get('first_name'),
            "lastName" => $this->data->get('last_name'),
            "emailAddress" => $this->data->get('email'),
            "userName" => $this->data->get('email'),
            "timezone" => "Australia/Sydney",
            "phone" => $this->data->get('wk_ph'),
            'customAttributes' => $this->attributes(),
            'organization' => $this->data->get('organisation_name'),
            'groups' => $this->getGroups(),
        ];

        $user = $this->userRepository->update($this->user->external_id, $data);

        if (!$user) {
            return null;
        }

        $this->user->update([
            'email' => $user->emailAddress,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'external_id' => $user->id,
            'phone' => $user->phoneNumber,
            'existing_membership_expiry' => $user->existingMemberExpiry(),
            'organisation' => (bool) $user->organization,
        ]);

        if ($this->data->get('update_type') == 'profile') {
            Mail::to($this->user->email)->send(new UserUpdatedMail($this->user));
        }

        if ($this->data->get('account_type') == "General_Public") {
            Mail::to($this->user->email)->send(new NewRegistrationMail($this->user));
        }

        return $user;
    }

    private function attributes(): array
    {
        $attributes = [];
        if ($this->data->get('artform')) {
            $attributes['art_form'] = $this->data->get('artform');
        }
        if ($this->data->get('address')) {
            $attributes['address'] = $this->data->get('address');
        }
        if ($this->data->get('suburb')) {
            $attributes['suburb'] = $this->data->get('suburb');
        }
        if ($this->data->get('state')) {
            $attributes['state'] = $this->data->get('state');
        }
        if ($this->data->get('postcode')) {
            $attributes['postcode'] = $this->data->get('postcode');
        }
        if ($this->data->get('organisation_type')) {
            $attributes['org_type'] = $this->data->get('organisation_type');
        }
        if ($this->data->get('organisation_abn')) {
            $attributes['org_abn'] = $this->data->get('organisation_abn');
        }
        if ($this->data->get('insurance')) {
            $attributes['insurance'] = $this->data->get('insurance');
        }
        if ($this->data->get('country')) {
            $attributes['country'] = $this->data->get('country');
        }
        if ($this->data->get('wk_ph')) {
            $attributes['wk_ph'] = $this->data->get('wk_ph');
        }
        if ($this->data->get('account_type')) {
            $attributes['account_type'] = $this->data->get('account_type');
        }
        if ($this->data->get('promo')) {
            $attributes['promo'] = $this->data->get('promo');
        }
        if ($this->data->get('role_in_org')) {
            $attributes['role_in_org'] = $this->data->get('role_in_org');
        }
        if ($this->data->get('accurate')) {
            $attributes['accurate'] = $this->data->get('accurate');
        }
        if ($this->data->get('website')) {
            $attributes['website'] = $this->data->get('website');
        }
        if ($this->data->get('member')) {
            $attributes['member'] = $this->data->get('member');
        }
        if ($this->data->get('member_date')) {
            $attributes['member_date'] = $this->data->get('member_date');
        }
        if ($this->data->get('expiry_date')) {
            $attributes['expiry_date'] = $this->data->get('expiry_date');
        }

        return collect($attributes)->map(function ($attribute, $key) {
            return [
                'attributeId' => $this->attributeRepository->userAttributeId($key),
                'attributeValue' => $attribute,
            ];
        })->reject(function ($array) {
            return !$array['attributeId'];
        })->all();
    }

    private function getGroups()
    {
        $bookedUser = $this->userRepository->find($this->user->external_id);

        return collect($bookedUser->groups ?? [])
            ->map(fn (array $group) => $group['id'])
            ->all();
    }
}