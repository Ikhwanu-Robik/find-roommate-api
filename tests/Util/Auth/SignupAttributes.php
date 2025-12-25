<?php

namespace Tests\Util\Auth;

use InvalidArgumentException;
use Tests\Util\IDataProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class SignupAttributes implements IDataProvider
{
    private $name;
    private $phone;
    private $password;
    private $birthdate;
    private $gender;
    private $address;
    private $bio;
    private $profilePhoto;
    private $publicAttributes;

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
        $this->publicAttributes = [
        'name',
        'phone',
        'password',
        'birthdate',
        'gender',
        'address',
        'bio',
        'profile_photo',
        ];
    }

    public function toArray(): array
    {
        return $this->collectAttributes()
            ->only($this->publicAttributes)->toArray();
    }

    public function exclude(array $keys): static
    {
        foreach ($keys as $key) {
            $idx = array_search($key, $this->publicAttributes);
            unset($this->publicAttributes[$idx]);
        }

        return $this;
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

    public function replace(array $data): static
    {
        $attributes = $this->collectAttributes();
        $replacedAttributes = $attributes->replace($data);
        $this->replaceAttributes($replacedAttributes->toArray());

        return $this;
    }

    private function replaceAttributes(array $replacers): void
    {
        foreach ($replacers as $key => $value) {
            switch ($key) {
                case 'name':
                    $this->name = $value;
                    break;
                case 'phone':
                    $this->phone = $value;
                    break;
                case 'password':
                    $this->password = $value;
                    break;
                case 'birthdate':
                    $this->birthdate = $value;
                    break;
                case 'gender':
                    $this->gender = $value;
                    break;
                case 'address':
                    $this->address = $value;
                    break;
                case 'bio':
                    $this->bio = $value;
                    break;
                case 'profile_photo':
                    $this->profilePhoto = $value;
                    break;
                default:
                    throw new InvalidArgumentException;
            }
        }
    }
}
