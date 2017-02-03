<?php

/**
 * Copyright (c) 2015, Marcel Hauri
 * All rights reserved.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright 2015, Marcel Hauri (https://github.com/mhauri/magento-slack/)
 *
 * @category Notification
 * @package mhauri-slack
 * @author Marcel Hauri <marcel@hauri.me>
 */
class Intelivemetrics_Slack_Model_Notification extends Intelivemetrics_Slack_Model_Abstract {

    public function send() {
        if (!$this->isEnabled()) {
            Mage::log('Slack Notifications are not enabled!', Zend_Log::ERR, self::LOG_FILE, true);
            return false;
        }

        $params = array(
            'channel' => $this->getChannel(),
            'username' => $this->getUsername(),
            'text' => $this->getMessage(),
            'icon_emoji' => $this->getIcon(),
            'mrkdwn' => true,
            'mrkdwn_in' => '["text"]',
            'attachments' => array(
                array(
                    'pretext' => $this->getPretext(),
                    'author_name' => $this->getAuthor(),
                    'author_link' => $this->getAuthorLink(),
                    'author_icon' => $this->getAuthorIcon()
                )
            )
        );

        if (Mage::getStoreConfig(self::USE_QUEUE, 0)) {
            Mage::getModel('intelivemetrics_slack/queue')->addMessageToQueue($params);
        } else {
            try {
                $this->sendMessage($params);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), Zend_Log::ERR, Intelivemetrics_Slack_Model_Abstract::LOG_FILE);
            }
        }
        return true;
    }

}
