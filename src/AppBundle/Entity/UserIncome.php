<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserIncome
 *
 * @ORM\Table(name="user_income")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserIncomeRepository")
 */
class UserIncome
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="msoa_code", type="string", length=255)
     */
    private $msoaCode;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=255, nullable=true)
     */
    private $postcode;


    /**
     * @var int
     *
     * @ORM\Column(name="total_income", type="integer")
     */
    private $totalIncome;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set msoaCode
     *
     * @param string $msoaCode
     *
     * @return UserIncome
     */
    public function setMsoaCode($msoaCode)
    {
        $this->msoaCode = $msoaCode;

        return $this;
    }

    /**
     * Get msoaCode
     *
     * @return string
     */
    public function getMsoaCode()
    {
        return $this->msoaCode;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     *
     * @return UserIncome
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set totalIncome
     *
     * @param integer $totalIncome
     *
     * @return UserIncome
     */
    public function setTotalIncome($totalIncome)
    {
        $this->totalIncome = $totalIncome;

        return $this;
    }

    /**
     * Get totalIncome
     *
     * @return integer
     */
    public function getTotalIncome()
    {
        return $this->totalIncome;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return UserIncome
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
