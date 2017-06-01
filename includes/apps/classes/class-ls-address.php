<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Address
{
    protected $address;

    public function __construct($address)
    {
        if(!empty($address)){

            if (!is_array($address)) {
                $this->address = json_decode($address, true);
            } else {
                $this->address = $address;
            }
        }
    }

    /**
     * @param $key
     * @return null|mixed
     */
    public function __get($key)
    {
        // TODO: Implement __get() method.
        return isset($this->address[$key]) ? $this->address[$key] : null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }


    public function getPhone()
    {
        return $this->phone;
    }

    public function getStreetOne()
    {
        return $this->street1;
    }

    public function getStreetTwo()
    {
        return $this->street2;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getAddressArray()
    {
        return $this->address;
    }
}