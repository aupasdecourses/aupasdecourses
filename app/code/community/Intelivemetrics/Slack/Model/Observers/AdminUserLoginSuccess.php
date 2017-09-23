<?php
/**
 * Copyright (c) 2015, Intelive Metrics SRL
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
 * @copyright Copyright 2015, Intelive Metrics SRL (http://mageslack.com/)
 */

class Intelivemetrics_Slack_Model_Observers_AdminUserLoginSuccess
    extends Intelivemetrics_Slack_Model_Observers_Abstract
{
    /**
     * Send a notification when admin user login failed
     * @param $observer
     */
    public function notify($observer)
    {
        if($this->_getConfig(Intelivemetrics_Slack_Model_Notification::ADMIN_USER_LOGIN_SUCCESS_PATH)) {
            $this->_notificationModel
                ->setMessage($this->_helper->__("Admin user %s %s logged in", $observer->getUser()->getFirstname(),$observer->getUser()->getLastname()))
                ->setIcon(':unlock:')
                ->setUsername($this->_helper->__('Login'))
                ->send();
        }
    }
}
