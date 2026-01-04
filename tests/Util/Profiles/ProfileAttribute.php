<?php

namespace Tests\Util\Profiles;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Tests\Util\IDataProvider;
use Illuminate\Support\Collection;

class ProfileAttribute implements IDataProvider
{
    private $fullName;
    private $gender;
    private $birthdate;
    private $address;
    private $bio;
    private $profilePhoto;
    private $publicAttributes;

    public function __construct()
    {
        $this->fullName = fake()->name();
        $this->gender = fake()->randomElement(['male', 'female']);
        $this->birthdate = fake()->date();
        $this->address = fake()->address();
        $this->bio = fake()->realText();
        $this->profilePhoto = UploadedFile::fake()->image('profile_photo.jpg');

        $this->publicAttributes = [
            'full_name',
            'gender',
            'birthdate',
            'address',
            'bio',
            'profile_photo',
        ];
    }

    public function toArray(): array
    {
        return $this->collectAttributes()
            ->only($this->publicAttributes)
            ->toArray();
    }

    private function collectAttributes(): Collection
    {
        return collect([
            'full_name' => $this->fullName,
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'address' => $this->address,
            'bio' => $this->bio,
            'profile_photo' => $this->profilePhoto,
        ]);
    }

    public function exclude(array $keys): static
    {
        foreach ($keys as $attr) {
            $idx = array_search($attr, $this->publicAttributes);
            unset($this->publicAttributes[$idx]);
        }

        return $this;
    }

    public function only(array $keys): static
    {
        $this->publicAttributes = $keys;
        return $this;
    }

    public function replace(array $data): static
    {
        $inputs = $this->collectAttributes();
        $replacedInputs = $inputs->replace($data);
        $this->replaceAttributes($replacedInputs->toArray());

        return $this;
    }

    private function replaceAttributes(array $replacers): void
    {
        foreach ($replacers as $key => $value) {
            switch ($key) {
                case 'full_name':
                    $this->fullName = $value;
                    break;
                case 'gender':
                    $this->gender = $value;
                    break;
                case 'birthdate':
                    $this->birthdate = $value;
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
