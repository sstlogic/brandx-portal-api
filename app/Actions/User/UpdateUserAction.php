<?php

namespace App\Actions\User;

use App\Actions\BaseAction;
use App\Booked\Models\BookedUser;
use App\Booked\Repositories\AttributeRepository;
use App\Booked\Repositories\UserRepository;
use App\Mail\UserUpdatedMail;
use App\Models\User;
use Mail;

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

        return $user;
    }

    private function attributes(): array
    {
        $attributes = [
            'art_form' => $this->data->get('artform'),
            'address' => $this->data->get('address'),
            'suburb' => $this->data->get('suburb'),
            'state' => $this->data->get('state'),
            'postcode' => $this->data->get('postcode'),
            'org_type' => $this->data->get('organisation_type'),
            'org_abn' => $this->data->get('organisation_abn'),
            'insurance' => $this->data->get('insurance'),
            'country' => $this->data->get('country'),
            'wk_ph' => $this->data->get('wk_ph'),
            'account_type' =>  $this->data->get('account_type'),
            'promo' =>  $this->data->get('promo'),
            'role_in_org' =>  $this->data->get('role_in_org'),
            'accurate' =>  $this->data->get('accurate'),
            'website' =>  $this->data->get('website'),
        ];

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
