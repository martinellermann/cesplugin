<?php

require_once "Services/Tracking/classes/object_statistics/class.ilLPObjectStatisticsTableGUI.php";
require_once('Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('Services/Form/classes/class.ilTextAreaInputGUI.php');


/**
 * Class ilCourseEmailSubscriptionGUI
 * @ilCtrl_isCalledBy ilCourseEmailSubscriptionGUI : ilUIPluginRouterGUI
 */
class ilCourseEmailSubscriptionGUI {

    /** @var  ilObjCourse */
    protected $course;

    protected $tpl;

    protected $ctrl;

    protected $tabs;


    public function __construct() {
        global $tpl, $ilCtrl, $ilTabs;
        $this->tpl = $tpl;
        $tpl->getStandardTemplate();
        $this->course = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
        $this->ctrl = $ilCtrl;
        $this->tabs = $ilTabs;
    }

    function executeCommand() {
        global $ilCtrl, $tpl;

        $command = $ilCtrl->getCmd();

        switch($command) {
            case 'show':
                $this->showMe();
                break;
            case 'save';
                $this->save();
                break;
        }
        $tpl->show();
    }

    function showMe() {
        global $ilLocator, $ilAccess;

        if(!$ilAccess->checkAccess('write', '', $_GET['ref_id'])){
            ilUtil::sendFailure("Access Denied!");
            return;
        }

        $this->buildheader($ilLocator);
        $form = $this->buildform();
        $this->tpl->setContent($form->getHTML());

        $obj_id = $this->course->getId();
        //echo "Hello World";
        // $lp_table = new ilLPObjectStatisticsTableGUI($this, "access", array($obj_id));
        //$this->tpl->setContent($lp_table->getGraph(array($obj_id)));
        // $this->tpl->setContent("Hello World");
        //$this->tpl->setContent($lp_table->getGraph(array($obj_id)));

    }

    /**
     * @param $ilLocator
     */
    public function buildheader($ilLocator)
    {
        $this->tpl->setTitle($this->course->getTitle()); // Der Titel soll der Titel des Kurses sein
        $this->tpl->setDescription($this->course->getDescription()); // Die Beschreibung soll die Beschreibung des Kurses sein.
        $this->tpl->setTitleIcon(ilObject::_getIcon($this->course->getId(), 'big')); // Das Bild soll ein Kurs Icon sein.

        // Wir fügen einen Zurückknopf ein. Dieser soll die Members des Kurses anzeigen
        $this->ctrl->saveParameterByClass('ilObjCourseGUI', 'ref_id'); //Wir müssen die ref_id speichern, damit der Link zum richtigen Kurs zeigt
        $this->tabs->setBackTarget('Zurück', $this->ctrl->getLinkTargetByClass(array(
            'ilRepositoryGUI',
            'ilObjCourseGUI'
        ), 'members'));

        // Wir fügen in den folgenden zwei Zeilen den Locator hinzu. (Breadcrumbs über dem Titel).
        $ilLocator->addRepositoryItems($this->course->getRefId());
        $this->tpl->setLocator($ilLocator->getHTML());

        // Tabs anzeigen
    }

    /**
     * @return ilPropertyFormGUI
     */
    public function buildform()
    {
        $form = new ilPropertyFormGUI();

        $form->setTitle("Mitglieder einschreiben");
        $form->setDescription("Bitte geben Sie eine kommagetrennte Liste von E-Mail Adressen an!");
        $textarea = new ilTextAreaInputGUI('E-Mail Adressen', 'emails');
        $textarea->setRequired(true);
        $textarea->setRows(20);

        $form->addItem($textarea);

        $this->ctrl->saveParameter($this, 'ref_id');
        $form->addCommandButton('save','Speichern');
        $form->setFormAction($this->ctrl->getFormAction($this));

        return $form;
    }

    protected function save()
    {
        global $tpl;


        global $ilLocator;


        $this->buildheader($ilLocator);

        $form = $this->buildform();

        if ($form->checkInput())
        {
            //ilUtil::sendSuccess("Benutzer erfolgreich eingetragen!");
            $form->setValuesByPost();
            $emails = $form->getInput('emails');

            require_once ("Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CourseEmailSubscription/classes/class.ilEmailSubscription.php");

            $subscriber = new ilEmailSubscription($_GET['ref_id']);

            $emails = $subscriber->getEmailsFromString($emails);



            foreach ($emails as $email) {

                $subscriber->subscribeEmail($email);

            }


                ilUtil::sendSuccess('Es wurden folgende Mitglieder eingeschrieben: ' . (implode(', ', $subscriber->getEmailsFound())), true);
                ilUtil::sendInfo('Folgende E-Mail adressen konnten nicht gefunden werden: '.(implode(', ', $subscriber->getEmailsNotFound())), true);
            //$tpl->setContent("Du hast folgende E-Mails eingetragen: ".$email);
            //$tpl->setContent($form->getHTML());
            $this->ctrl->redirect($this, 'show');
        }
        else{
            $tpl->setContent($form->getHTML());
        }

    }

}

?>