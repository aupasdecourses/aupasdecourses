<?php
/**
 * Observer interface
 *
 * @category    Intelivemetrics
 * @package     Intelivemetrics_Slack
 * @author      Sander Mangel <https://github.com/sandermangel>
 */
interface Intelivemetrics_Slack_Model_Observers_Interface
{
    /**
     * Send a notification to slack
     * @param $observer
     */
    public function notify($observer);
}