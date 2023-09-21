<?php

namespace App\Actions\User;

use App\Actions\BaseAction;
use App\Booked\Models\BookedUser;
use App\Booked\Repositories\AttributeRepository;
use App\Booked\Repositories\UserRepository;

class CreateUserAction extends BaseAction
{
    public function __construct(
        private UserRepository      $userRepository,
        private AttributeRepository $attributeRepository
    ) {
    }

    public function execute(array $data)
    {
        $this->setData($data);

        return $this->createBookedUser();
    }

    // private function createBookedUser(): ?array
    private function createBookedUser(): ?BookedUser
    {
        $data = [
            "password" => $this->data->get('password'),
            "language" => "en_us",
            "firstName" => $this->data->get('first_name'),
            "lastName" => $this->data->get('last_name'),
            "emailAddress" => $this->data->get('email'),
            "userName" => $this->data->get('email'),
            "timezone" => "Australia/Sydney",
            // "phone" => $this->data->get('phone'),
            'customAttributes' => $this->attributes(),
            'organization' => $this->data->get('organisation_name'),
        ];
        // return $data;
        $user = $this->userRepository->create($data);

        if (!$user) {
            return null;
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
            'terms' => $this->data->get('tcs'),
            'marketing' => $this->data->get('updates'),
            'insurance' => $this->data->get('insurance'),
            'country' => $this->data->get('country'),
            'wk_ph' => $this->data->get('wk_ph'),
            'account_type' =>  $this->data->get('account_type'),
            'promo' =>  $this->data->get('promo'),
            'role_in_org' =>  $this->data->get('role_in_org'),
            'accurate' =>  $this->data->get('accurate'),
            'website' =>  $this->data->get('website'),
        ];
        // return $attributes;
        return collect($attributes)
            ->map(function ($attribute, $key) {
                return [
                    'attributeId' => $this->attributeRepository->userAttributeId($key),
                    'attributeValue' => $attribute,
                ];
            })
            ->reject(function ($array) {
                return !$array['attributeId'];
            })
            ->values()
            ->all();
    }
}
