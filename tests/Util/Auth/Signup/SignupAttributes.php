<?php

namespace Tests\Util\Auth\Signup;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class SignupAttributes
{
    private $name;
    private $phone;
    private $password;
    private $birthdate;
    private $gender;
    private $address;
    private $bio;
    private $profilePhoto;

    public function __construct()
    {
        $this->name = fake()->name();
        $this->phone = fake()->regexify('/^08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/');
        $this->password = fake()->password();
        $this->birthdate = fake()->date();
        $this->gender = fake()->randomElement(['male', 'female']);
        $this->address = fake()->address();
        $this->bio = fake()->realText();
        $this->profilePhoto = UploadedFile::fake()->image('profile_photo.jpg');
    }

    public function exclude(array $keys): Collection
    {
        $attributes = $this->collectAttributes();
        $filteredAttributes = $attributes->except($keys);
        
        return $filteredAttributes;
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
    
    public function replaceWith(array $data): Collection
    {   
        $attributes = $this->collectAttributes();
        $replacedAttributes = $attributes->replace($data);

        return $replacedAttributes;
    }
}
