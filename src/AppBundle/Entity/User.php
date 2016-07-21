<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user", indexes={
 *   @ORM\Index(name="index_date_salt_for_hash", columns={"restore_link_date","salt"}),
 *   @ORM\Index(name="index_username", columns={"username"}),
 *   @ORM\Index(name="index_email", columns={"email"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
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
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var integer, timestamp
     * was selected integer(timestamp), instead datetime to be easy with DQL
     * @ORM\Column(name="restore_link_date", type="integer")
     */
    private $restoreDate;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get role of user
     * @todo make possible to get roles from table UserRole
     *
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }


    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @todo develop method to escape credentials
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        // TODO: Implement serialize() method.
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        // TODO: Implement unserialize() method.
    }


    /**
     * Set restoreDate
     *
     * @param \DateTime $restoreDate
     *
     * @return User
     */
    public function setRestoreDate(\DateTime $restoreDate)
    {
        $this->restoreDate = $restoreDate->getTimestamp();

        return $this;
    }

    /**
     * Get restoreDate
     *
     * @return \DateTime
     */
    public function getRestoreDate()
    {
        $datetime = new \DateTime();
        $datetime->setTimestamp($this->restoreDate);

        return $datetime;
    }
}
