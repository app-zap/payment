<?php
namespace AppZap\Payment\Model;

class CustomerData
{

    /**
     * @var string
     */
    protected $addressAdditionalInfo;

    /**
     * @var string
     */
    protected $addressCity;

    /**
     * @var string
     */
    protected $addressPostalCode;

    /**
     * @var string
     */
    protected $addressStreet;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string;
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $middleName;

    /**
     * @var string
     */
    protected $nameSuffix;

    /**
     * @var string
     */
    protected $payerToken;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $phoneType;

    /**
     * @var string
     */
    protected $salutation;

    /**
     * @return string
     */
    public function getAddressAdditionalInfo()
    {
        return $this->addressAdditionalInfo;
    }

    /**
     * @param string $addressAdditionalInfo
     */
    public function setAddressAdditionalInfo($addressAdditionalInfo)
    {
        $this->addressAdditionalInfo = $addressAdditionalInfo;
    }

    /**
     * @return string
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * @param string $addressCity
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
    }

    /**
     * @return string
     */
    public function getAddressPostalCode()
    {
        return $this->addressPostalCode;
    }

    /**
     * @param string $addressPostalCode
     */
    public function setAddressPostalCode($addressPostalCode)
    {
        $this->addressPostalCode = $addressPostalCode;
    }

    /**
     * @return string
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * @param string $addressStreet
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string
     */
    public function getNameSuffix()
    {
        return $this->nameSuffix;
    }

    /**
     * @param string $nameSuffix
     */
    public function setNameSuffix($nameSuffix)
    {
        $this->nameSuffix = $nameSuffix;
    }

    /**
     * @return string
     */
    public function getPayerToken()
    {
        return $this->payerToken;
    }

    /**
     * @param string $payerToken
     */
    public function setPayerToken($payerToken)
    {
        $this->payerToken = $payerToken;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhoneType()
    {
        return $this->phoneType;
    }

    /**
     * @param string $phoneType
     */
    public function setPhoneType($phoneType)
    {
        $this->phoneType = $phoneType;
    }

    /**
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * @param string $salutation
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $nameParts = [
            $this->salutation,
            $this->firstName,
            $this->middleName,
            $this->lastName,
            $this->nameSuffix,
        ];
        return join(' ', array_filter($nameParts));
    }

}
