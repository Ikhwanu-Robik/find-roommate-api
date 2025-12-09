<?php

namespace Tests\Util\Auth\Signup;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class InvalidSignupAttributes
{
    private $name;
    private $phone;
    private $password;
    private $birthdate;
    private $gender;
    private $address;
    private $bio;
    private $profilePhoto;
    private $invalidAttributes;

    public function __construct()
    {
        $this->name = null;
        $this->phone = fake()->regexify('/^\+62-08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/');
        $this->password = null;
        $this->birthdate = now()->toDateString();
        $this->gender = 'transgender';
        $this->address = null;
        $this->bio = null;
        $this->profilePhoto = UploadedFile::fake()->create(
            'not-image.json',
            20,
            'application/json'
        );
    }

    public function only(array $keys): array
    {
        $invalidAttributes = $this->collectAttributes();
        return $invalidAttributes->only($keys)->toArray();
    }

    private function collectAttributes(): Collection
    {
        return collect([
            'name' => $this->name,
            'phone' => $this->phone,
            'password' => $this->password,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'address' => $this->address,
            'bio' => $this->bio,
            'profile_photo' => $this->profilePhoto,
        ]);
    }
}