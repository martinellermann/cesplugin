<?php

require_once 'Modules/Course/classes/class.ilCourseParticipants.php';

class ilEmailSubscriber {

    /**
     * @var ilObjCourse
     */
    protected $couseObject;

    /** @var  string[] */
    protected $emailsFound;

    /** @var  string[] */
    protected $emailsNotFound;

    /** @var  ilCourseParticipants */
    protected $courseParticipants;

    public function __construct($ref_id) {
        $this->couseObject = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
        $this->courseParticipants = new ilCourseParticipants($this->couseObject->getId());

        $this->emailsFound = array();
        $this->emailsNotFound = array();
    }

    /**
     * @param $email string
     */
    public function subscribeEmail($email) {
        if($user_id =  $this->getUserIdByEmail($email)) {
            $this->courseParticipants->add($user_id, IL_CRS_MEMBER);
            $this->emailsFound[] = $email;
        } else {
            $this->emailsNotFound[] = $email;
        }
    }

    /**
     * @param $string string a string with different email adresses separated by new lines or commatas
     */
    public function subscribeEmailStringList($string) {
        $emails = $this->separateEmails($string);


        foreach($emails as $email) {
            $this->subscribeEmail($email);
        }
    }

    /**
     * @param $email
     * @return bool|integer returns the user id to the email. if no user is found to the email then returns false
     */
    public function getUserIdByEmail($email) {
        global $ilDB;

        $query = "SELECT * FROM usr_data WHERE usr_data.email LIKE ".$ilDB->quote($email, 'text');
        $result = $ilDB->query($query);
        while($row = $ilDB->fetchAssoc($result)) {
            return $row['usr_id'];
        }
        return false;
    }

    /**
     * @param $string string
     * @return string[]
     */
    protected function separateEmails($string) {
        preg_match_all("/[A-Za-z0-9_.-]+@[A-Za-z0-9_.-]+\\.[A-Za-z0-9_-][A-Za-z0-9_]+/uismx", $string, $matches);
        return $matches[0];
    }

    /**
     * @return array
     */
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