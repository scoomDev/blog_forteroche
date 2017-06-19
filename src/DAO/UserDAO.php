<?php
namespace forteroche\DAO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use forteroche\Domain\User;

class UserDAo extends DAO implements UserProviderInterface {

    /**
     * Returns a user matching the supplied id.
     *
     * @param integer $id The user id.
     *
     * @return \forteroche\Domain\User|throws an exception if no matching user is found
     */
    public function find($id) {
        $sql = "SELECT * FROM jf_user where usr_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("Aucun utilisateur ne correspond " . $id);
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $sql = "SELECT * FROM jf_user WHERE usr_name=?";
        $row = $this->getDb()->fetchAssoc($sql, array($username));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new UsernameNotFoundException(sprintf('Utilisateur "%s" pas trouvé.', $username));
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Les instances de "%s" ne sont pas pris en charge.', $class));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return 'forteroche\Domain\User' === $class;
    }

    /**
     * Creates a User object based on a DB row.
     *
     * @param array $row The DB row containing User data.
     * @return \forteroche\Domain\User
     */
    protected function buildDomainObject(array $row) {
        $user = new User();
        $user->setId($row['usr_id']);
        $user->setEmail($row['usr_email']);
        $user->setUsername($row['usr_name']);
        $user->setPassword($row['usr_password']);
        $user->setSalt($row['usr_salt']);
        $user->setRole($row['usr_role']);
        return $user;
    }

    // Recovery password
    public function mailExist($mail) {
        $sql = 'SELECT usr_id FROM jf_user WHERE usr_email = ?';
        $row = $this->getDb()->prepare($sql);
        $row->execute([$mail]);

        return $row->rowCount();
    }

    public function insertCode($mail, $code) {
        $sql = 'SELECT recovery_id FROM jf_recovery WHERE recovery_mail=?';
        $row = $this->getDb()->prepare($sql);
        $row->execute([$mail]);
        $recovery_mail_exist = $row->rowCount();

        if($recovery_mail_exist == 1) {
            $sql_update = 'UPDATE jf_recovery SET recovery_code=? WHERE recovery_mail=?';
            $row_update = $this->getDb()->prepare($sql_update);
            $row_update->execute([$code, $mail]);
        } else {
            $sql_update = 'INSERT INTO jf_recovery(recovery_mail, recovery_code) VALUES (?,?)';
            $row_update = $this->getDb()->prepare($sql_update);
            $row_update->execute([$mail, $code]);
        }
    }

    public function recoveryFind($mail) {
        $sql = "SELECT * FROM jf_user where usr_email = ?";
        $row = $this->getDb()->fetchAssoc($sql, array($mail));
        return $row;
    }

    public function checkValid($check_mail, $code) {
        $check_code = intval(htmlspecialchars($code));
        $sql_valid = 'SELECT recovery_id FROM jf_recovery WHERE recovery_code = ? AND recovery_mail = ?';
        $row_valid = $this->getDb()->prepare($sql_valid);
        $row_valid->execute([$check_code, $check_mail]);

        return $row_valid->rowCount();
    }

    public function delRecovery($check_mail) {
        $del_req = 'DELETE FROM jf_recovery WHERE recovery_mail = ?';
        $del_row = $this->getDb()->prepare($del_req);
        $del_row->execute([$check_mail]);
    }

    public function updatePwd($mail, $pwd) {
        $sql = 'UPDATE jf_user SET usr_password = ? WHERE usr_email = ?';
        $row = $this->getDb()->prepare($sql);
        $row->execute([$pwd, $mail]);
    }
}