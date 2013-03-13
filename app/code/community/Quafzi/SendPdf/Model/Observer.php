<?php
/**
 * @category  Sales
 * @package   Quafzi_SendPdf
 * @author    Thomas Birke <tbirke@netextreme.de>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Quafzi_SendPdf_Model_Observer
 * 
 * @package   Quafzi_SendPdf
 * @copyright 2013
 * @author    Thomas Birke <tbirke@netextreme.de>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class Quafzi_SendPdf_Model_Observer
{
    public function attachInvoicePdf($event)
    {
        $mailer = $event->getMailer();
        $params = $mailer->getTemplateParams();
        $invoice = $params['invoice'];

        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice));
        $mailer->addPdf($pdf, $invoice->getIncrementId() . '.pdf');
    }
}
