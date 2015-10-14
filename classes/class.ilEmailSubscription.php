<?php

require_once 'Modules/Course/classes/class.ilCourseParticipants.php';


class ilEmailSubscription

{


    /**
     * @var ilObjCourse
     */
    protected $courseObject;

    /** @var  string[] */
    protected $emailsFound;

    /** @var  string[] */
    protected $emailsNotFound;

    /** @var  ilCourseParticipants */
    protected $courseParticipants;

    public function __construct($ref_id)
    {
        $this->courseObject = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
        $this->courseParticipants = new ilCourseParticipants($this->courseObject->getId());

        $this->emailsFound = array();
        $this->emailsNotFound = array();
    }

    public function getEmailsFromString($emailString)
    {
        preg_match_all("/[A-Za-z0-9_.-]+@[A-Za-z0-9_.-]+\\.[A-Za-z0-9_-][A-Za-z0-9_]+/uismx", $emailString, $matches);
        return $matches[0];

    }

    public function subscribeEmail($email)
    {
        $usr_id = $this->getUserIdByEmail($email);
        if($usr_id)
        {
            $this->courseParticipants->add($usr_id, IL_CRS_MEMBER);
            $this->emailsFound[] = $email;

        }
        else
        {
            $this->emailsNotFound[] = $email;
        }

    }

    public function getUserIdByEmail($email)
    {
        global $ilDB;
        $query = "SELECT * FROM usr_data WHERE usr_data.email LIKE ".$ilDB->quote($email, 'text');

        $result = $ilDB->query($query);

        while($row = $ilDB->fetchAssoc($result))
        {
            return $row['usr_id'];
        }
        return false;
    }

    public function getEmailsFound() {
        return $this->emailsFound;
    }

    /**
     * @return array
     */
    public function getEmailsNotFound() {
        return $this->emailsNotFound;
    }

}

?>