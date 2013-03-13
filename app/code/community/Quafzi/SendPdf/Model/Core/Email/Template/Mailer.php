<?php
/**
 * @category  Sales
 * @package   Quafzi_SendPdf
 * @author    Thomas Birke <tbirke@netextreme.de>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Quafzi_SendPdf_Model_Core_Email_Template_Mailer
 * 
 * @package   Quafzi_SendPdf
 * @copyright 2013
 * @author    Thomas Birke <tbirke@netextreme.de>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class Quafzi_SendPdf_Model_Core_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{
    protected $attachments = array();

    public function addPdf(Zend_Pdf $pdf, $filename='invoice.pdf')
    {
        $file = $pdf->render();
        $this->attachments[] = array(
            'file'        => $file,
            'type'        => 'application/pdf',
            'encoding'    => Zend_Mime::ENCODING_BASE64,
            'disposition' => Zend_Mime::DISPOSITION_ATTACHMENT,
            'name'        => $filename
        );
    }

    /**
     * override parent method to add attachments
     *
     * @see Mage_Core_Model_Email_Template_Mailer::send()
     */
    public function send()
    {
        $emailTemplate = Mage::getModel('core/email_template');
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // add attachments
            foreach ($this->attachments as $attachment) {
                $emailTemplate->getMail()->createAttachment(
                    $attachment['file'],
                    $attachment['type'],
                    $attachment['disposition'],
                    $attachment['encoding'],
                    $attachment['name']
                );
            }
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->sendTransactional(
                $this->getTemplateId(),
                $this->getSender(),
                $emailInfo->getToEmails(),
                $emailInfo->getToNames(),
                $this->getTemplateParams(),
                $this->getStoreId()
            );
        }
        return $this;
    }
}
